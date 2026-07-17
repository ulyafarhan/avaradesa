<script setup>
defineProps({
    variant: { type: String, default: 'text' },
    count: { type: Number, default: 1 },
});
</script>

<template>
    <div v-if="variant === 'text'" class="animate-pulse space-y-3">
        <div class="h-4 rounded bg-slate-200" />
        <div class="h-4 w-5/6 rounded bg-slate-200" />
        <div class="h-20 rounded bg-slate-200" />
    </div>

    <div v-else-if="variant === 'card'" class="grid gap-4" :class="count > 1 ? 'sm:grid-cols-2 lg:grid-cols-3' : ''">
        <div v-for="i in count" :key="i" class="animate-pulse rounded-lg border border-slate-200 bg-white p-5">
            <div class="h-3 w-1/3 rounded bg-slate-200" />
            <div class="mt-3 h-5 w-2/3 rounded bg-slate-200" />
            <div class="mt-4 h-3 w-full rounded bg-slate-200" />
            <div class="mt-2 h-3 w-4/5 rounded bg-slate-200" />
        </div>
    </div>

    <div v-else-if="variant === 'stat'" class="grid gap-4 sm:grid-cols-3">
        <div v-for="i in count" :key="i" class="animate-pulse rounded-lg border border-slate-200 bg-white p-5">
            <div class="h-3 w-1/2 rounded bg-slate-200" />
            <div class="mt-3 h-8 w-1/3 rounded bg-slate-200" />
        </div>
    </div>

    <div v-else-if="variant === 'table-row'" class="space-y-2">
        <div v-for="i in count" :key="i" class="flex animate-pulse items-center gap-4 rounded-lg border border-slate-200 bg-white px-4 py-3">
            <div class="h-4 w-1/4 rounded bg-slate-200" />
            <div class="h-4 w-1/3 rounded bg-slate-200" />
            <div class="h-4 w-1/6 rounded bg-slate-200" />
            <div class="ml-auto h-6 w-16 rounded-full bg-slate-200" />
        </div>
    </div>
</template>
