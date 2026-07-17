<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    loading: Boolean,
});

const base = 'inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-60';
const variants = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-amber-600 text-white hover:bg-amber-700',
    outline: 'border border-blue-600 text-blue-700 hover:bg-blue-50',
    danger: 'bg-red-600 text-white hover:bg-red-700',
    ghost: 'text-slate-700 hover:bg-slate-100',
};
</script>

<template>
    <Link v-if="href" :href="href" :class="[base, variants[variant]]">
        <span v-if="loading" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />
        <slot />
    </Link>
    <button v-else :type="type" :disabled="loading" :class="[base, variants[variant]]">
        <span v-if="loading" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />
        <slot />
    </button>
</template>
