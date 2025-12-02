<template>
    <div class="min-h-screen bg-gray-50 text-[#094789] flex flex-col font-sans relative">
        
        <GameHeader 
            :score="currentScore" 
            :quiz-id="quizId" 
            @close="router.push('/')" 
        />

        <div class="px-4 mb-6 block md:hidden w-full">
            <ProgressBar :current="currentIndex" :total="totalQuestions" :answered="hasSubmitted" />
            <div class="text-right text-xs font-bold mt-1 text-gray-500">
                {{ currentIndex + 1 }}/{{ totalQuestions }}
            </div>
        </div>

        <main class="flex-1 flex flex-col items-center max-w-2xl mx-auto w-full px-4 mt-4 md:mt-10 pb-32">
            
            <div v-if="loading" class="text-xl animate-pulse">Carregando...</div>

            <div v-else-if="!gameFinished && currentQuestion" class="w-full">
                <h2 class="text-2xl md:text-3xl font-extrabold text-center mb-8 leading-tight">
                    {{ currentQuestion.description }}
                </h2>

                <div class="space-y-4">
                    <OptionCard 
                        v-for="(option, index) in currentQuestion.options"
                        :key="option.id"
                        :option="option"
                        :index="index"
                        :is-selected="selectedOptionId === option.id"
                        :is-submitted="hasSubmitted"
                        :correct-id="correctOptionId"
                        :disabled="hasSubmitted" 
                        @select="handleOptionSelect"
                    />
                </div>
            </div>

            <div v-else class="text-center mt-20">
                <h2 class="text-4xl font-bold text-[#094789] mb-4">Quiz Finalizado!</h2>
                <p class="text-xl mb-2">Sua pontuação final:</p>
                <div class="text-6xl font-black text-[#094789] mb-8">{{ currentScore }}</div>
                
                <button @click="router.push('/')" class="bg-[#FBC209] text-[#094789] px-8 py-3 rounded-xl font-bold hover:bg-yellow-400 transition shadow-lg">
                    Voltar ao Ranking
                </button>
            </div>
        </main>

        <GameFooter 
            v-if="!loading && !gameFinished"
            :current="currentIndex"
            :total="totalQuestions"
            :has-selected="!!selectedOptionId"
            :is-submitted="hasSubmitted"
            :is-last="isLastQuestion"
            @action="handleMainButton"
        />
    </div>
</template>

<script setup>
import { ref, shallowRef, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

import GameHeader from '../components/game/GameHeader.vue';
import GameFooter from '../components/game/GameFooter.vue';
import ProgressBar from '../components/game/ProgressBar.vue';
import OptionCard from '../components/game/OptionCard.vue';

const router = useRouter();

const questions = shallowRef([]);
const currentIndex = ref(0);
const loading = ref(true);
const quizId = ref(null);
const currentScore = ref(0);
const startTime = ref(Date.now());
const gameFinished = ref(false);

const selectedOptionId = ref(null);
const hasSubmitted = ref(false);
const correctOptionId = ref(null);

const currentQuestion = computed(() => questions.value[currentIndex.value]);
const totalQuestions = computed(() => questions.value.length);
const isLastQuestion = computed(() => currentIndex.value === totalQuestions.value - 1);

onMounted(async () => {
    try {
        const response = await axios.get('/quiz/start');
        questions.value = response.data.questions;
        quizId.value = response.data.quiz.id;
        startTime.value = Date.now();
    } catch (e) {
        alert('Erro ao iniciar. Verifique se há perguntas cadastradas.');
        router.push('/');
    } finally {
        loading.value = false;
    }
});

const handleOptionSelect = (option) => {
    if (hasSubmitted.value) return; 
    selectedOptionId.value = option.id;
};

const handleMainButton = async () => {
    if (!hasSubmitted.value) {
        await submitAnswer();
        return;
    }

    if (isLastQuestion.value) {
        await finishGame();
        return;
    }

    nextQuestion();
};

const submitAnswer = async () => {
    try {
        console.log("Enviando Quiz ID:", quizId.value);

        const response = await axios.post('/quiz/answer', {
            quiz_id: quizId.value,
            question_id: currentQuestion.value.id,
            option_id: selectedOptionId.value
        });

        const { is_correct, correct_option_id } = response.data;

        hasSubmitted.value = true;
        
        if (is_correct) {
            currentScore.value += 10;
            correctOptionId.value = selectedOptionId.value;
        } else {
            correctOptionId.value = correct_option_id;
        }

    } catch (e) {
        console.error(e);
        alert('Erro de conexão ao enviar resposta.');
    }
};

const nextQuestion = () => {
    currentIndex.value++;
    selectedOptionId.value = null;
    hasSubmitted.value = false;
    correctOptionId.value = null;
};

const finishGame = async () => {
    try {
        await axios.post(`/quiz/finish/${quizId.value}`);
        gameFinished.value = true;
    } catch (e) {
        console.error(e);
        gameFinished.value = true;
    }
};
</script>