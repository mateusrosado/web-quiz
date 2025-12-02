<template>
    <div class="container mx-auto p-6" v-if="quiz">
        <h1 class="text-2xl font-bold mb-4">Resultado da Partida</h1>
        
        <div class="bg-gray-100 p-4 rounded mb-6 flex justify-around">
            <div><strong>Pontos:</strong> {{ quiz.score }}</div>
            <div><strong>Acertos:</strong> <span class="text-green-600">{{ quiz.correct_count }}</span></div>
            <div><strong>Erros:</strong> <span class="text-red-600">{{ quiz.wrong_count }}</span></div>
            <div><strong>Tempo:</strong> {{ quiz.duration }}s</div>
        </div>

        <div class="space-y-4">
            <div v-for="(answer, index) in quiz.answers" :key="answer.id" class="border p-4 rounded bg-white shadow-sm">
                <p class="font-bold mb-2">{{ index + 1 }}. {{ answer.question.description }}</p>
                
                <div class="text-sm">
                    <span :class="answer.is_correct ? 'text-green-600 font-bold' : 'text-red-500 font-bold'">
                        {{ answer.is_correct ? '✅ Você acertou!' : '❌ Você errou.' }}
                    </span>
                    <p class="mt-1 text-gray-600">Sua escolha: {{ answer.option.text }}</p>
                </div>
            </div>
        </div>
        
        <button @click="$router.push('/')" class="mt-6 bg-gray-500 text-white px-4 py-2 rounded">Voltar</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const quiz = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get(`/quiz/${route.params.id}`);
        quiz.value = response.data;
    } catch (error) {
        alert('Não foi possível carregar o histórico.');
    }
});
</script>