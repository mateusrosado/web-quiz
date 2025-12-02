<template>
    <div class="container mx-auto p-6 max-w-4xl">
        <h1 class="text-3xl font-bold text-[#1E1E4B] mb-6">Meu Perfil</h1>

        <!-- Stats Cards -->
        <div v-if="stats" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-600 text-white p-6 rounded-xl shadow-lg">
                <p class="text-sm opacity-80">Total XP</p>
                <p class="text-3xl font-bold">{{ stats.stats.total_xp }}</p>
            </div>
            <div class="bg-purple-600 text-white p-6 rounded-xl shadow-lg">
                <p class="text-sm opacity-80">Quizzes Completos</p>
                <p class="text-3xl font-bold">{{ stats.stats.total_quizzes }}</p>
            </div>
            <div class="bg-green-600 text-white p-6 rounded-xl shadow-lg">
                <p class="text-sm opacity-80">Precisão</p>
                <p class="text-3xl font-bold">{{ stats.stats.accuracy }}%</p>
            </div>
        </div>

        <!-- Abas -->
        <div class="flex border-b mb-6">
            <button @click="activeTab = 'history'" :class="{'border-b-2 border-blue-600 font-bold': activeTab === 'history'}" class="px-4 py-2">Histórico</button>
            <button @click="activeTab = 'edit'" :class="{'border-b-2 border-blue-600 font-bold': activeTab === 'edit'}" class="px-4 py-2">Editar Dados</button>
        </div>

        <!-- Aba Histórico -->
        <div v-if="activeTab === 'history' && stats">
            <table class="w-full bg-white shadow rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Data</th>
                        <th class="p-3 text-center">Pontos</th>
                        <th class="p-3 text-center">Acertos</th>
                        <th class="p-3 text-center">Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="quiz in stats.history" :key="quiz.id" class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ new Date(quiz.created_at).toLocaleDateString() }}</td>
                        <td class="p-3 text-center font-bold text-blue-600">{{ quiz.score }}</td>
                        <td class="p-3 text-center">{{ quiz.correct_count }}/{{ quiz.correct_count + quiz.wrong_count }}</td>
                        <td class="p-3 text-center">{{ quiz.duration }}s</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Aba Editar -->
        <div v-if="activeTab === 'edit'" class="bg-white p-6 shadow rounded-lg">
            <form @submit.prevent="updateProfile" class="space-y-4">
                <div>
                    <label class="block text-gray-700">Nome</label>
                    <input v-model="form.name" type="text" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Nova Senha (deixe em branco para manter)</label>
                    <input v-model="form.password" type="password" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Confirmar Senha</label>
                    <input v-model="form.password_confirmation" type="password" class="w-full border p-2 rounded">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar Alterações</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const stats = ref(null);
const activeTab = ref('history');
const form = reactive({ name: '', password: '', password_confirmation: '' });

onMounted(async () => {
    const response = await axios.get('/user/stats');
    stats.value = response.data;
    form.name = response.data.user.name;
});

const updateProfile = async () => {
    try {
        await axios.post('/user/update', form);
        alert('Perfil atualizado!');
        form.password = '';
        form.password_confirmation = '';
    } catch (e) {
        alert('Erro ao atualizar. Verifique os dados.');
    }
};
</script>