<template>
    <header class="flex justify-between items-center p-4 md:p-6 max-w-4xl mx-auto w-full">
        <div class="bg-white rounded-full px-3 py-1 flex items-center gap-2 shadow-sm">
            <div class="bg-[#1E1E4B] rounded-full p-1">
                <CoinsIcon :size="14" class="text-white" />
            </div>
            <span class="font-bold text-sm text-[#1E1E4B]">{{ score }}</span>
        </div>

        <h1 class="font-bold text-lg md:text-xl text-[#1E1E4B] absolute left-1/2 -translate-x-1/2">
            Fantasy Quiz #{{ quizId }}
        </h1>

        <button @click="$emit('close')" class="bg-white p-2 rounded-full hover:bg-gray-200 transition text-[#1E1E4B]">
            <XIcon :size="20" />
        </button>
    </header>
</template>

<script setup>
import { CoinsIcon, XIcon } from 'lucide-vue-next';

defineProps({
    score: Number,
    quizId: [String, Number]
});

defineEmits(['close']);
</script>