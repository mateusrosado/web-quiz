<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import { 
    Trophy, 
    Crown, 
    Award, 
    Zap, 
    Clock, 
    Gamepad2
} from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(true);

const topUsers = ref([]);
const topQuizzes = ref([]);

onMounted(async () => {
    try {
        const response = await window.axios.get('/ranking');
        topUsers.value = response.data.top_users || [];
        topQuizzes.value = response.data.top_quizzes || [];
    } catch (error) {
        console.error('Erro ao carregar ranking', error);
    } finally {
        loading.value = false;
    }
});

const goToGame = () => {
    if (authStore.isAuthenticated) {
        router.push('/game');
    } else {
        router.push('/login');
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
};

const getCardClasses = (index) => {
    if (index === 0) {
        return 'from-[#FBC209]/30 via-white to-[#FBC209]/20 border-[#FBC209]/40 bg-gradient-to-br md:-mt-8 md:order-2 z-10 scale-105';
    }
    if (index === 1) {
        return 'from-slate-200 via-white to-slate-100 border-slate-300 bg-gradient-to-br md:order-1';
    }
    return 'from-[#CD7F32]/30 via-white to-[#CD7F32]/20 border-[#CD7F32]/40 bg-gradient-to-br md:order-3';
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 pb-20 relative">
        <div class="bg-gradient-to-b from-[#094789] via-[#094789]/80 to-[#0a4f96] text-white pt-8 pb-24 px-6 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-10 -left-10 h-48 w-48 rounded-full bg-[#FBC209]/20 blur-2xl"></div>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white/80 mb-4">
                    <Trophy :size="14" class="text-[#FBC209]" />
                    <span>Hall da Fama</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Mestres do Conhecimento</h1>
                <p class="text-white/80 text-lg max-w-2xl mx-auto">
                    Abaixo estão os jogadores que mais acumularam XP em toda a história do Web Quiz.
                </p>
            </div>
        </div>

        <div class="relative z-10 -mt-16 max-w-5xl mx-auto px-4">
            <div v-if="loading" class="text-center py-12 bg-white rounded-3xl shadow-lg">
                <p class="text-gray-500 animate-pulse">Carregando dados...</p>
            </div>

            <div v-else>
                <div v-if="topUsers.length > 0" class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <div v-for="(user, index) in topUsers" :key="user.id" 
                             :class="getCardClasses(index)"
                             class="rounded-3xl border p-6 text-center shadow-lg relative flex flex-col items-center transition transform hover:-translate-y-2 duration-300">
                            
                            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-white/80 text-[#094789] shadow-inner mb-4 text-3xl">
                                <Crown v-if="index === 0" :size="32" class="text-[#FBC209]" stroke-width="2.5" />
                                <Award v-else :size="32" />
                            </div>

                            <span class="text-xs font-bold uppercase tracking-widest opacity-70 mb-1">{{ index + 1 }}º Lugar</span>
                            <h3 class="text-xl font-bold text-[#094789] truncate w-full" :title="user.name">{{ user.name }}</h3>
                            
                            <div class="mt-4 flex flex-col items-center gap-1 text-[#094789]">
                                <span class="text-3xl font-black flex items-center gap-1"><Zap :size="24" class="text-[#FBC209]" /> {{ user.total_xp }}</span>
                                <span class="text-xs font-medium uppercase tracking-wide opacity-70">XP Total Acumulado</span>
                            </div>
                            
                            <div class="mt-3 px-3 py-1 bg-white/40 rounded-full text-xs font-bold text-[#094789]">
                                Nível {{ user.level }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#094789]/10 shadow-lg overflow-hidden">
                    <div class="p-8 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-[#094789] flex items-center gap-2">
                                <Zap :size="24" class="text-[#FBC209]" /> Melhores Partidas
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Ranking baseado na pontuação máxima e menor tempo.</p>
                        </div>
                    </div>

                    <div v-if="topQuizzes.length === 0" class="p-12 text-center">
                        <div class="text-5xl mb-4 text-gray-300"><Clock :size="32" class="mx-auto" /></div>
                        <p class="text-gray-500 text-lg">Nenhuma partida registrada ainda.</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 text-[#094789] text-xs uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Jogador</th>
                                    <th class="px-6 py-4 text-center">Pontuação</th>
                                    <th class="px-6 py-4 text-center">Tempo</th>
                                    <th class="px-6 py-4 text-right">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(quiz, index) in topQuizzes" :key="quiz.id" class="hover:bg-[#094789]/5 transition">
                                    <td class="px-6 py-4">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#094789]/10 text-xs font-bold text-[#094789]">
                                            {{ index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">{{ quiz.user ? quiz.user.name : 'Anônimo' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold text-sm">
                                            <Zap :size="14" /> {{ quiz.score }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600 font-mono text-sm">
                                        <Clock :size="14" class="inline text-[#094789]" /> {{ quiz.duration }}s
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500 text-xs">
                                        {{ formatDate(quiz.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-12 text-center pb-8">
                    <button @click="goToGame" class="bg-[#FBC209] text-[#094789] px-10 py-4 rounded-xl font-bold text-lg hover:bg-yellow-400 transition shadow-xl transform hover:scale-105 flex items-center gap-3 mx-auto">
                        <Gamepad2 :size="24" />
                        {{ authStore.isAuthenticated ? 'Jogar Agora' : 'Faça Login para Jogar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>