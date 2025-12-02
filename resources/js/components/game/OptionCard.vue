<template>
    <button 
        @click="$emit('select', option)"
        :disabled="disabled"
        class="w-full p-4 rounded-xl flex items-center justify-between transition-all duration-200 shadow-sm border-2"
        :class="containerClasses"
    >
        <div class="flex items-center gap-4">
            <div 
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shrink-0 transition-colors"
                :class="circleClasses"
            >
                <CheckIcon v-if="isSubmitted && isCorrect" :size="20" />
                <XIcon v-else-if="isSubmitted && isSelected && !isCorrect" :size="20" />
                <span v-else>{{ letter }}</span>
            </div>

            <div class="text-left">
                <p class="font-extrabold text-lg">{{ option.text }}</p>
            </div>
        </div>
    </button>
</template>

<script setup>
import { computed } from 'vue';
import { CheckIcon, XIcon } from 'lucide-vue-next';

const props = defineProps({
    option: Object,
    index: Number,
    isSelected: Boolean,
    isSubmitted: Boolean,
    correctId: Number,
    disabled: Boolean
});

defineEmits(['select']);

const letter = computed(() => ['A', 'B', 'C', 'D'][props.index] || '?');

const isCorrect = computed(() => props.option.id === props.correctId);

const containerClasses = computed(() => {
    if (props.isSubmitted) {
        if (isCorrect.value) return 'bg-green-100 border-green-500 text-green-800';
        if (props.isSelected) return 'bg-red-100 border-red-500 text-red-800 opacity-80';
        return 'bg-white border-transparent opacity-40';
    }

    if (props.isSelected) {
        // Alterado de bg-blue-50 para usar a cor da marca mais suave
        return 'bg-[#094789]/5 border-[#094789] text-[#094789] transform scale-[1.02] shadow-md';
    }
    
    // Texto padrão alterado para a cor da marca
    return 'bg-white border-transparent hover:border-gray-200 text-[#094789] hover:bg-gray-50';
});

const circleClasses = computed(() => {
    if (props.isSubmitted) {
        if (isCorrect.value) return 'bg-green-500 text-white';
        if (props.isSelected) return 'bg-red-500 text-white';
        return 'bg-gray-100 text-[#094789]';
    }

    if (props.isSelected) {
        // Azul da marca quando selecionado
        return 'bg-[#094789] text-white';
    }

    return 'bg-gray-100 text-[#094789]';
});
</script>