<template>
    <div class="p-8 max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6">🏆 Ranking Global</h1>
        
        <div v-if="loading" class="text-center">Carregando...</div>
        
        <table v-else class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Pos</th>
                    <th class="border p-2">Jogador</th>
                    <th class="border p-2">Pontos</th>
                    <th class="border p-2">Tempo (s)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rank, index) in rankings" :key="rank.id" class="text-center">
                    <td class="border p-2">{{ index + 1 }}º</td>
                    <td class="border p-2">{{ rank.user ? rank.user.name : 'Usuário Removido' }}</td>
                    <td class="border p-2 font-bold">{{ rank.score }}</td>
                    <td class="border p-2">{{ rank.duration }}s</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-8 text-center" v-if="authStore.isAuthenticated">
            <button @click="$router.push('/game')" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-xl shadow-lg hover:bg-blue-700">
                🎮 Jogar Agora
            </button>
        </div>
        <div class="mt-8 text-center" v-else>
            <router-link to="/login" class="text-blue-600 underline">Faça login para jogar</router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const rankings = ref([]);
const loading = ref(true);
const authStore = useAuthStore();

onMounted(async () => {
    try {
        const response = await window.axios.get('/ranking'); 
        rankings.value = response.data.data;
    } catch (error) {
        console.error('Erro ao carregar ranking', error);
    } finally {
        loading.value = false;
    }
});
</script>