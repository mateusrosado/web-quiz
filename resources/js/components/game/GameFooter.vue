<template>
    <footer class="fixed bottom-0 left-0 w-full bg-[#F3F0EA] border-t border-gray-200/50 p-4 md:p-6">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-4">
            
            <div class="hidden md:flex items-center gap-3 w-full">
                <ProgressBar :current="current" :total="total" :answered="isSubmitted" />
                <span class="text-gray-500 font-bold whitespace-nowrap">
                    {{ current + 1 }}/{{ total }}
                </span>
            </div>

            <button 
                @click="$emit('action')"
                :disabled="isDisabled"
                class="w-full md:w-auto md:min-w-[200px] py-4 rounded-xl font-bold text-white transition-all shadow-lg uppercase tracking-wide"
                :class="!isDisabled ? 'bg-[#4ADE80] hover:bg-green-400 translate-y-0' : 'bg-gray-300 cursor-not-allowed'"
            >
                {{ buttonLabel }}
            </button>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import ProgressBar from './ProgressBar.vue';

const props = defineProps({
    current: Number,
    total: Number,
    hasSelected: Boolean,
    isSubmitted: Boolean,
    isLast: Boolean
});

defineEmits(['action']);

const isDisabled = computed(() => {
    return !props.hasSelected && !props.isSubmitted;
});

const buttonLabel = computed(() => {
    if (!props.isSubmitted) return 'Confirmar';
    if (props.isLast) return 'Finalizar';
    return 'Próxima';
});
</script>