<script setup>
defineProps({
    steps: { type: Array, required: true },
    current: { type: Number, required: true },
});
</script>

<template>
    <ol class="grid grid-cols-3 gap-2">
        <li v-for="(step, index) in steps" :key="step" class="rounded-md border p-3 text-sm" :class="index <= current ? 'border-teal-600 bg-teal-50 text-teal-900' : 'border-slate-200 text-slate-500'">
            <span class="block text-xs font-semibold">Langkah {{ index + 1 }}</span>
            {{ step }}
        </li>
    </ol>
</template>
