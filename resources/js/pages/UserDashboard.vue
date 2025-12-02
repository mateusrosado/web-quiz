<template>
    <div class="min-h-screen bg-gray-50 font-sans pb-20">
        <div class="bg-white shadow-sm border-b border-gray-100 relative z-10">
            <div class="max-w-4xl mx-auto px-6 py-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="w-16 h-16 rounded-full bg-[#094789]/5 border-2 border-[#094789]/10 flex items-center justify-center text-[#094789] shrink-0">
                        <User :size="32" stroke-width="1.5" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Meu Perfil</p>
                        <h1 class="text-2xl md:text-3xl font-black text-[#094789] truncate max-w-[250px] md:max-w-md">
                            Olá, {{ form.name || 'Jogador' }}!
                        </h1>
                    </div>
                </div>

                <button 
                    @click="router.push('/game')"
                    class="w-full md:w-auto bg-[#FBC209] text-[#094789] px-8 py-4 rounded-xl font-bold text-lg hover:bg-yellow-400 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center gap-3"
                >
                    <Gamepad2 :size="24" />
                    Jogar Agora
                </button>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 md:px-6 mt-8">
            
            <div v-if="loading" class="text-center py-20">
                <div class="animate-spin w-10 h-10 border-4 border-[#094789] border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-400 font-medium animate-pulse">Carregando estatísticas...</p>
            </div>

            <div v-else>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
                    <div class="bg-gradient-to-br from-[#094789] to-[#073666] text-white p-6 rounded-3xl shadow-lg relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-6 -mt-6 transition-transform duration-500 group-hover:scale-110"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 text-white/70 mb-3 text-xs font-bold uppercase tracking-wider">
                                <Zap :size="16" class="text-[#FBC209]" /> Total XP
                            </div>
                            <p class="text-4xl md:text-5xl font-black tracking-tight">{{ stats?.stats?.total_xp || 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-md border border-gray-100 group hover:border-[#094789]/20 transition-colors">
                        <div class="flex items-center gap-2 text-gray-400 mb-3 text-xs font-bold uppercase tracking-wider group-hover:text-[#094789] transition-colors">
                            <Gamepad2 :size="16" /> Partidas Completas
                        </div>
                        <p class="text-4xl md:text-5xl font-black text-[#094789]">{{ stats?.stats?.total_quizzes || 0 }}</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-md border border-gray-100 group hover:border-green-500/20 transition-colors">
                        <div class="flex items-center gap-2 text-gray-400 mb-3 text-xs font-bold uppercase tracking-wider group-hover:text-green-600 transition-colors">
                            <Target :size="16" /> Precisão Média
                        </div>
                        <p class="text-4xl md:text-5xl font-black text-[#094789]">
                            {{ stats?.stats?.accuracy || 0 }}<span class="text-2xl text-gray-400">%</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden min-h-[500px]">
                    <div class="flex border-b border-gray-100">
                        <button 
                            @click="activeTab = 'history'" 
                            class="flex-1 py-5 text-sm font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 relative overflow-hidden group"
                            :class="activeTab === 'history' ? 'text-[#094789] bg-blue-50/50' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
                        >
                            <History :size="18" /> Histórico
                            <div v-if="activeTab === 'history'" class="absolute bottom-0 left-0 w-full h-1 bg-[#094789]"></div>
                        </button>
                        <button 
                            @click="activeTab = 'edit'" 
                            class="flex-1 py-5 text-sm font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 relative overflow-hidden group"
                            :class="activeTab === 'edit' ? 'text-[#094789] bg-blue-50/50' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
                        >
                            <Settings :size="18" /> Meus Dados
                            <div v-if="activeTab === 'edit'" class="absolute bottom-0 left-0 w-full h-1 bg-[#094789]"></div>
                        </button>
                    </div>

                    <div v-if="activeTab === 'history'" class="p-0">
                        <div v-if="!stats?.history?.length" class="text-center py-24 px-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                                <Ghost :size="40" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-600">Nenhuma partida encontrada</h3>
                            <p class="text-gray-400 text-sm mt-1">Jogue agora para começar a construir seu histórico!</p>
                            <button @click="router.push('/game')" class="mt-6 text-[#094789] font-bold text-sm hover:underline">Ir para o jogo</button>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead class="bg-gray-50/80 text-[10px] uppercase font-bold text-gray-500 tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4">Data</th>
                                        <th class="px-6 py-4 text-center">Pontuação</th>
                                        <th class="px-6 py-4 text-center">Desempenho</th>
                                        <th class="px-6 py-4 text-center">Tempo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="quiz in stats.history" :key="quiz.id" class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-700 text-sm">
                                                    {{ new Date(quiz.created_at).toLocaleDateString('pt-BR') }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ new Date(quiz.created_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'}) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 bg-[#FBC209]/10 text-[#d4a000] px-3 py-1.5 rounded-full font-bold text-sm border border-[#FBC209]/20">
                                                <Zap :size="14" fill="currentColor" /> {{ quiz.score }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-mono font-medium text-gray-600">
                                                <span class="text-green-600 font-bold">{{ quiz.correct_count }}</span>
                                                <span class="text-gray-300 mx-1">/</span>
                                                <span class="text-gray-400">{{ quiz.correct_count + quiz.wrong_count }}</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500 font-mono text-sm">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <Clock :size="14" class="text-gray-300" />
                                                {{ quiz.duration }}s
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'edit'" class="p-8 md:p-12">
                        <div class="max-w-lg mx-auto">
                            <h3 class="text-xl font-bold text-[#094789] mb-6 text-center">Atualizar Informações</h3>
                            
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-400 ml-1">Nome de Exibição</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                                            <User :size="20" />
                                        </div>
                                        <input 
                                            v-model="form.name" 
                                            type="text" 
                                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                                        >
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-100">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 ml-1">Segurança (Opcional)</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Nova Senha</label>
                                            <div class="relative group">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                                                    <Lock :size="18" />
                                                </div>
                                                <input 
                                                    v-model="form.password" 
                                                    type="password" 
                                                    placeholder="••••••"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                                                >
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Confirmar</label>
                                            <div class="relative group">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                                                    <CheckCircle :size="18" />
                                                </div>
                                                <input 
                                                    v-model="form.password_confirmation" 
                                                    type="password" 
                                                    placeholder="••••••"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6">
                                    <button 
                                        type="submit" 
                                        class="w-full bg-[#094789] text-white py-4 rounded-xl font-bold hover:bg-[#073666] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2"
                                    >
                                        <Save :size="20" />
                                        Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { 
    User, 
    Gamepad2, 
    Zap, 
    Target, 
    History, 
    Settings, 
    Ghost, 
    Lock, 
    CheckCircle, 
    Save,
    Clock,
    ArrowRight
} from 'lucide-vue-next';

const router = useRouter();
const stats = ref(null);
const activeTab = ref('history');
const loading = ref(true);
const form = reactive({ name: '', password: '', password_confirmation: '' });

onMounted(async () => {
    try {
        const response = await axios.get('/user/stats');
        stats.value = response.data;
        form.name = response.data.user.name;
    } catch (error) {
        console.error("Erro ao carregar dados:", error);
    } finally {
        loading.value = false;
    }
});

const updateProfile = async () => {
    try {
        await axios.post('/user/update', form);
        alert('Perfil atualizado com sucesso!');
        form.password = '';
        form.password_confirmation = '';
        
        // Atualiza o nome exibido localmente se mudou
        if (stats.value && stats.value.user) {
            stats.value.user.name = form.name;
        }
    } catch (e) {
        alert('Erro ao atualizar. Verifique os dados e tente novamente.');
    }
};
</script>