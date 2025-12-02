<script setup>
import { onMounted, reactive } from 'vue';
import axios from 'axios';

const settingsForm = reactive({ quiz_question_limit: 10, score_per_question: 10 });

onMounted(async () => {
    try {
        const res = await axios.get('/admin/settings');
        if(res.data.quiz_question_limit) settingsForm.quiz_question_limit = res.data.quiz_question_limit;
        if(res.data.score_per_question) settingsForm.score_per_question = res.data.score_per_question;
    } catch (e) {}
});

async function saveSettings() {
    try {
        await axios.post('/admin/settings', settingsForm);
        alert('Salvo!');
    } catch (e) { alert('Erro'); }
}
</script>

<template>
    <div class="space-y-6">
        <header>
            <p class="text-xs uppercase tracking-[0.3em] text-[#FBC209] font-bold">Sistema</p>
            <h1 class="text-2xl font-semibold text-[#094789]">Configurações do Jogo</h1>
        </header>

        <div class="bg-white rounded-3xl border border-[#094789]/10 shadow-sm p-8 max-w-2xl">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-[#094789] mb-2">Perguntas por Partida</label>
                    <input v-model="settingsForm.quiz_question_limit" type="number" class="w-full rounded-2xl border border-[#094789]/20 px-4 py-3">
                    <p class="text-xs text-slate-500 mt-2">Quantidade de questões apresentadas em cada rodada.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#094789] mb-2">Pontos por Acerto</label>
                    <input v-model="settingsForm.score_per_question" type="number" class="w-full rounded-2xl border border-[#094789]/20 px-4 py-3">
                    <p class="text-xs text-slate-500 mt-2">XP ganho pelo usuário ao acertar.</p>
                </div>

                <div class="pt-4">
                    <button @click="saveSettings" class="inline-flex items-center gap-2 rounded-full bg-[#094789] px-8 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-[#0a5aa7]">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>