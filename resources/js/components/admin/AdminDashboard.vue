<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Users, Gamepad2, FileQuestion, Layers, Loader2 } from 'lucide-vue-next';

const insights = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const res = await axios.get('/admin/insights');
        insights.value = res.data;
    } catch (e) {
        console.error("Erro", e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="space-y-8">
        <header>
            <p class="text-xs uppercase tracking-[0.3em] text-[#FBC209] font-bold">Visão Geral</p>
            <h1 class="text-3xl font-semibold text-[#094789] mt-2">Dashboard</h1>
        </header>

        <div v-if="loading" class="text-gray-500 flex items-center gap-2">
            <Loader2 class="animate-spin" /> Carregando dados...
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="relative rounded-3xl bg-gradient-to-br from-[#094789] to-[#0a62bc] px-6 py-7 text-white shadow-xl">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 text-white">
                        <Users :size="24" />
                    </span>
                    <div>
                        <h3 class="text-white text-lg font-semibold">Usuários</h3>
                        <p class="text-white/80 text-sm mt-1">Total registrados</p>
                        <p class="text-3xl font-bold mt-2">{{ insights.total_users }}</p>
                    </div>
                </div>
            </div>

            <div class="relative rounded-3xl border border-[#094789]/10 bg-white px-6 py-7 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#094789]/10 text-[#094789]">
                        <Gamepad2 :size="24" />
                    </span>
                    <div>
                        <h3 class="text-[#094789] text-lg font-semibold">Partidas</h3>
                        <p class="text-slate-500 text-sm mt-1">Quizzes finalizados</p>
                        <p class="text-3xl font-bold text-[#094789] mt-2">{{ insights.total_quizzes }}</p>
                    </div>
                </div>
            </div>

            <div class="relative rounded-3xl border border-[#094789]/10 bg-white px-6 py-7 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FBC209]/20 text-[#8f6a00]">
                        <FileQuestion :size="24" />
                    </span>
                    <div>
                        <h3 class="text-[#094789] text-lg font-semibold">Perguntas</h3>
                        <p class="text-slate-500 text-sm mt-1">Banco de dados</p>
                        <p class="text-3xl font-bold text-[#094789] mt-2">{{ insights.total_questions }}</p>
                    </div>
                </div>
            </div>

            <div class="relative rounded-3xl border border-[#094789]/10 bg-white px-6 py-7 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                        <Layers :size="24" />
                    </span>
                    <div>
                        <h3 class="text-[#094789] text-lg font-semibold">Combinações</h3>
                        <p class="text-slate-500 text-sm mt-1">Jogos possíveis</p>
                        <p class="text-xl font-bold text-[#094789] mt-2 truncate" :title="insights.possible_combinations">
                            {{ insights.possible_combinations }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>