<script setup>
const props = defineProps({
    status: { type: String, required: true },
});

const classes = {
    Pending: 'bg-amber-100 text-amber-800',
    Diproses: 'bg-sky-100 text-sky-800',
    Disetujui: 'bg-emerald-100 text-emerald-800',
    Selesai: 'bg-emerald-100 text-emerald-800',
    Ditolak: 'bg-red-100 text-red-800',
};
</script>

<template>
    <span :class="['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', classes[status] || 'bg-slate-100 text-slate-700']">
        {{ status }}
    </span>
</template>
