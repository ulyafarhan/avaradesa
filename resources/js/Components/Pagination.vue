<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

defineProps({
    links: { type: Object, required: true },
    meta: { type: Object, required: true },
});
</script>

<template>
    <nav v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-slate-200 px-1 pt-4">
        <p class="text-sm text-slate-500">
            Menampilkan <span class="font-semibold text-slate-700">{{ meta.from }}</span>
            sampai <span class="font-semibold text-slate-700">{{ meta.to }}</span>
            dari <span class="font-semibold text-slate-700">{{ meta.total }}</span> data
        </p>
        <div class="flex items-center gap-1">
            <Link
                v-if="links.prev"
                :href="links.prev"
                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                preserve-scroll
            >
                <ChevronLeft class="size-4" />
                <span class="hidden sm:inline">Sebelumnya</span>
            </Link>
            <span v-else class="inline-flex items-center gap-1 rounded-md border border-slate-100 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-400">
                <ChevronLeft class="size-4" />
                <span class="hidden sm:inline">Sebelumnya</span>
            </span>
            <Link
                v-if="links.next"
                :href="links.next"
                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                preserve-scroll
            >
                <span class="hidden sm:inline">Berikutnya</span>
                <ChevronRight class="size-4" />
            </Link>
            <span v-else class="inline-flex items-center gap-1 rounded-md border border-slate-100 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-400">
                <span class="hidden sm:inline">Berikutnya</span>
                <ChevronRight class="size-4" />
            </span>
        </div>
    </nav>
</template>
