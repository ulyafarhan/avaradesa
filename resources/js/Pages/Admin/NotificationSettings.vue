<script setup>
import { Head } from '@inertiajs/vue3';
import WAGatewayCard from '../../Components/WAGatewayCard.vue';
import TelegramCard from '../../Components/TelegramCard.vue';

defineOptions({ layout: null });

defineProps({
    tgTargetCount: Number,
    tgBotInfo: Object,
    waConfig: Object,
});
</script>

<template>
    <Head title="Pengaturan Notifikasi" />

    <div class="min-h-screen bg-slate-50">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <a href="/admin" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-lg font-semibold text-slate-900">Pengaturan Notifikasi</h1>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content: Side-by-Side -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <WAGatewayCard :config="waConfig" />
                <TelegramCard :bot-info="tgBotInfo" :target-count="tgTargetCount" />
            </div>
        </main>
    </div>
</template>
