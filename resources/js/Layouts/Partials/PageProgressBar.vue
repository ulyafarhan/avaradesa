<script setup>
import { inject } from 'vue';

const progress = inject('pageProgress');
</script>

<template>
    <div v-if="progress" class="fixed left-0 top-0 z-50 h-[3px] w-full overflow-hidden bg-blue-100">
        <div class="h-full w-1/3 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 animate-progress" />
    </div>
</template>

<style scoped>
.animate-progress {
    animation: progress-indeterminate 1.5s ease-in-out infinite;
}

@keyframes progress-indeterminate {
    0% { transform: translateX(-100%) scaleX(0.5); }
    50% { transform: translateX(100%) scaleX(1); }
    100% { transform: translateX(300%) scaleX(0.5); }
}
</style>
