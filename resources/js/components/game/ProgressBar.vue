<template>
    <div class="h-2 md:h-3 w-full bg-gray-100 rounded-full overflow-hidden shadow-inner">
        <div 
            class="h-full bg-[#FBC209] transition-all duration-500 ease-out"
            :style="{ width: percentage + '%' }"
        ></div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    current: Number,
    total: Number,
    answered: Boolean
});

const percentage = computed(() => {
    if (!props.total) return 0;
    return ((props.current + (props.answered ? 1 : 0)) / props.total) * 100;
});
</script>