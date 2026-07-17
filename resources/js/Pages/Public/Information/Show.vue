<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AppButton from '../../../Components/AppButton.vue';
import { ArrowLeft, Calendar, User, Clock } from '@lucide/vue';

defineOptions({ layout: PublicLayout });
const props = defineProps({ informasi: Object });

const stripHtml = (html) => {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
};
</script>

<template>
    <Head>
        <title>{{ informasi.judul }} - {{ $page.props.settings.nama_desa }}</title>
        <meta name="description" :content="stripHtml(informasi.konten).substring(0, 150) + '...'" />
        <meta name="keywords" :content="`Berita, ${$page.props.settings.nama_desa}, ${informasi.kategori}, ${$page.props.settings.kabupaten}`" />
        <meta property="og:title" :content="informasi.judul" />
        <meta property="og:description" :content="stripHtml(informasi.konten).substring(0, 150) + '...'" />
        <meta property="og:type" content="article" />
    </Head>

    <main class="bg-white min-h-screen py-12">
        <article class="mx-auto max-w-3xl px-6 sm:px-8">
            <AppButton href="/informasi" variant="ghost" class="mb-8 gap-2 px-0 text-[#1A73E8] hover:text-[#155fa8] transition-colors font-medium text-xs">
                <ArrowLeft class="size-4" />
                Kembali ke informasi
            </AppButton>

            <div v-if="informasi.cover_image" class="mb-8 overflow-hidden rounded-2xl border border-gray-200">
                <img :src="informasi.cover_image" :alt="informasi.judul" class="aspect-video w-full object-cover" loading="lazy" />
            </div>

            <div class="space-y-4">
                <span class="text-[10px] font-bold text-[#1A73E8] bg-[#E8F0FE] px-3 py-1.5 rounded-lg uppercase tracking-wider block w-fit">
                    {{ informasi.kategori }}
                </span>
                
                <h1 class="text-3xl sm:text-4xl font-normal text-[#202124] tracking-tight leading-tight">
                    {{ informasi.judul }}
                </h1>

                <div class="flex items-center gap-4 text-xs text-[#5F6368] font-bold border-y border-gray-100 py-3.5 mt-4">
                    <span class="inline-flex items-center gap-1.5">
                        <User class="size-4 text-[#5F6368]" />
                        {{ informasi.author?.username || 'Admin' }}
                    </span>
                    <span class="size-1 rounded-full bg-slate-350"></span>
                    <span class="inline-flex items-center gap-1.5">
                        <Clock class="size-4 text-[#5F6368]" />
                        {{ new Date(informasi.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </span>
                </div>
            </div>

            <div class="mt-8 text-slate-800 text-sm sm:text-base leading-relaxed space-y-6 font-medium" v-html="informasi.konten" />
        </article>
    </main>
</template>
