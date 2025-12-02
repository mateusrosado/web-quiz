<template>
    <footer class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 md:p-6 shadow-lg z-10">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-4">
            
            <div class="hidden md:flex items-center gap-3 w-full">
                <ProgressBar :current="current" :total="total" :answered="isSubmitted" />
                <span class="text-gray-400 font-bold whitespace-nowrap text-xs tracking-wider">
                    {{ current + 1 }} / {{ total }}
                </span>
            </div>

            <button 
                @click="$emit('action')"
                :disabled="isDisabled"
                class="w-full md:w-auto md:min-w-[200px] py-4 rounded-xl font-bold transition-all shadow-lg uppercase tracking-wide"
                :class="!isDisabled ? 'bg-[#FBC209] text-[#094789] hover:bg-yellow-400 translate-y-0' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
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