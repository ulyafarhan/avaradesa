<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AppButton from '../../../Components/AppButton.vue';
import { ArrowLeft, Calendar, User, Clock } from '@lucide/vue';
import DOMPurify from 'dompurify';

defineOptions({ layout: PublicLayout });
const props = defineProps({ informasi: Object });

import { stripHtml } from '../../../Utils/string';

const safeContent = (html) => DOMPurify.sanitize(html || '', { USE_PROFILES: { html: true } });
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

    <main class="bg-white min-h-screen py-24">
        <article class="mx-auto max-w-4xl px-6 sm:px-8">
            <AppButton href="/informasi" variant="ghost" class="mb-12 gap-2 px-0 text-slate-500 hover:text-slate-900 transition-colors font-bold text-[10px] uppercase tracking-[0.2em] font-sans">
                <ArrowLeft class="size-4" />
                Kembali ke daftar informasi
            </AppButton>

            <div v-if="informasi.cover_image" class="mb-12 overflow-hidden rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-900/5">
                <img :src="informasi.cover_image" :alt="informasi.judul" class="aspect-video w-full object-cover" loading="lazy" />
            </div>

            <div class="space-y-6">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase font-sans border-l-4 border-amber-600 pl-4">
                    Kategori: {{ informasi.kategori }}
                </span>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 tracking-tight leading-[1.1] font-heading">
                    {{ informasi.judul }}
                </h1>

                <div class="flex items-center gap-4 text-[10px] text-slate-500 font-bold uppercase tracking-widest border-y border-slate-200 py-4 mt-6 font-sans">
                    <span class="inline-flex items-center gap-1.5">
                        <User class="size-3.5" />
                        {{ informasi.author?.username || 'Admin' }}
                    </span>
                    <span class="size-1 rounded-full bg-slate-300"></span>
                    <span class="inline-flex items-center gap-1.5">
                        <Clock class="size-3.5" />
                        {{ new Date(informasi.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </span>
                </div>
            </div>

            <div class="mt-12 text-slate-700 text-base sm:text-lg leading-loose space-y-6 font-sans prose prose-slate max-w-none prose-headings:font-heading prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-amber-600" v-html="safeContent(informasi.konten)" />
        </article>
    </main>
</template>
