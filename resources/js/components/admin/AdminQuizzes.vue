<script setup>
import { onMounted } from 'vue';
import { usePagination } from '../../composables/usePagination';
import { Gamepad2, ChevronLeft, ChevronRight, Check, X, Clock } from 'lucide-vue-next';

const { 
    data: quizzesHistory, 
    pagination: quizzesPagination, 
    loadData: loadQuizzesHistory 
} = usePagination('/admin/quizzes');

onMounted(() => loadQuizzesHistory());

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('pt-BR');
};
</script>

<template>
    <div class="space-y-6">
        <header>
            <p class="text-xs uppercase tracking-[0.3em] text-[#FBC209] font-bold">Monitoramento</p>
            <h1 class="text-2xl font-semibold text-[#094789] mt-1">Histórico de Partidas</h1>
        </header>

        <div class="bg-white rounded-3xl border border-[#094789]/10 shadow-sm overflow-hidden">
            <div v-if="quizzesHistory.length === 0" class="text-center py-12 text-slate-500">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#094789]/5 text-[#094789] mb-4">
                    <Gamepad2 :size="32" />
                </div>
                <p>Nenhuma partida registrada ainda.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider">Data</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider">Jogador</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider text-center">Pontuação</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider text-center">Desempenho</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider text-center">Tempo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="quiz in quizzesHistory" :key="quiz.id" class="hover:bg-[#094789]/5 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-500 font-mono">{{ formatDate(quiz.created_at) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-[#094789]/10 flex items-center justify-center text-[#094789] text-xs font-bold">
                                        {{ quiz.user?.name?.charAt(0).toUpperCase() || '?' }}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700">{{ quiz.user?.name || 'Anônimo' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-[#FBC209]/10 text-[#8f6a00]">
                                    {{ quiz.score }} XP
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium flex items-center justify-center gap-3">
                                    <span class="text-emerald-600 flex items-center gap-1">
                                        <Check :size="14" /> {{ quiz.correct_count }}
                                    </span>
                                    <span class="text-rose-500 flex items-center gap-1">
                                        <X :size="14" /> {{ quiz.wrong_count }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-slate-500 font-mono bg-slate-100 px-2 py-1 rounded-lg flex items-center justify-center gap-1 w-fit mx-auto">
                                    <Clock :size="12" /> {{ quiz.duration }}s
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="quizzesPagination && quizzesPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-2">
                <button 
                    @click="loadQuizzesHistory(quizzesPagination.prev_page_url)" 
                    :disabled="!quizzesPagination.prev_page_url" 
                    class="flex items-center px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <ChevronLeft :size="16" class="mr-1" /> Anterior
                </button>
                
                <span class="text-sm text-slate-500 font-medium px-2">
                    {{ quizzesPagination.current_page }} / {{ quizzesPagination.last_page }}
                </span>
                
                <button 
                    @click="loadQuizzesHistory(quizzesPagination.next_page_url)" 
                    :disabled="!quizzesPagination.next_page_url" 
                    class="flex items-center px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Próxima <ChevronRight :size="16" class="ml-1" />
                </button>
            </div>
        </div>
    </div>
</template>