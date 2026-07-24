<script setup>
import { ref, watch } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AppCard from '../../../Components/AppCard.vue';
import AppButton from '../../../Components/AppButton.vue';
import EmptyState from '../../../Components/EmptyState.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Newspaper, Search, Calendar, User, Clock, ArrowRight, X } from '@lucide/vue';

defineOptions({ layout: PublicLayout });
const props = defineProps({ 
    informasi: Object, 
    kategori: Array, 
    filters: Object 
});

const activeKategori = ref(props.filters?.kategori || '');
const searchQuery = ref(props.filters?.search || '');

import { stripHtml } from '../../../Utils/string';

const triggerFilter = () => {
    router.get('/informasi', {
        kategori: activeKategori.value || undefined,
        search: searchQuery.value || undefined
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const filterByKategori = (kategori) => {
    activeKategori.value = kategori;
    triggerFilter();
};

const clearSearch = () => {
    searchQuery.value = '';
    triggerFilter();
};
</script>

<template>
    <Head>
        <title>Pusat Informasi & Kabar Desa - {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}</title>
        <meta name="description" :content="'Temukan berita terbaru, pengumuman resmi, dan dokumentasi agenda pembangunan Desa ' + $page.props.settings.nama_desa" />
        <meta name="keywords" :content="'Berita, Pengumuman Desa, Agenda Desa, Kabar Desa, ' + $page.props.settings.nama_desa" />
        <meta property="og:title" :content="'Pusat Informasi & Kabar Desa - ' + $page.props.settings.nama_desa" />
        <meta property="og:description" :content="'Temukan berita terbaru, pengumuman resmi di Desa ' + $page.props.settings.nama_desa" />
    </Head>

    <header class="bg-white pt-32 pb-24 border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-4xl space-y-8">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-4 font-sans border-l-4 border-amber-600 pl-4">
                    Publikasi Kabar & Pengumuman
                </span>
                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-slate-900 tracking-tight leading-[1.05] font-heading">
                    Pusat Informasi<br/><span class="italic text-amber-600">Desa.</span>
                </h1>
                
                <p class="text-xl text-slate-600 font-sans leading-relaxed max-w-2xl border-l-2 border-slate-200 pl-4">
                    Dapatkan berita terupdate, agenda kegiatan desa, serta pengumuman transparansi pembangunan secara langsung dan terbuka.
                </p>

                <div class="max-w-lg pt-8">
                    <form @submit.prevent="triggerFilter" class="flex items-center bg-slate-50 rounded-xl p-2 border border-slate-200 focus-within:border-amber-600 shadow-sm transition-all duration-300">
                        <div class="pl-3 text-slate-400 shrink-0">
                            <Search class="size-5" />
                        </div>
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            placeholder="Cari berita..." 
                            class="bg-transparent text-slate-900 font-sans text-base grow px-4 py-2 placeholder:text-slate-400"
                            style="outline: none !important; box-shadow: none !important; border: none !important;"
                        />
                        <button 
                            v-if="searchQuery" 
                            type="button" 
                            @click="clearSearch"
                            class="p-2 text-slate-400 hover:text-slate-900 shrink-0 rounded-lg transition"
                        >
                            <X class="size-5" />
                        </button>
                        <AppButton 
                            type="submit" 
                            class="rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold px-6 py-3 text-[10px] tracking-[0.2em] uppercase transition shrink-0 ml-1 font-sans shadow-md"
                        >
                            Cari
                        </AppButton>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8 bg-slate-50 min-h-[500px]">
        
        <div class="space-y-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em] block font-sans">Filter Kategori</span>
            <div class="flex flex-wrap gap-3 font-sans">
                <button
                    class="rounded-full border px-5 py-2 text-[10px] font-bold tracking-widest uppercase transition-all duration-300"
                    :class="!activeKategori ? 'border-amber-600 bg-amber-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-600 hover:border-slate-900 hover:text-slate-900'"
                    @click="filterByKategori('')"
                >
                    Semua
                </button>
                <button
                    v-for="kat in kategori"
                    :key="kat"
                    class="rounded-full border px-5 py-2 text-[10px] font-bold tracking-widest uppercase transition-all duration-300"
                    :class="activeKategori === kat ? 'border-amber-600 bg-amber-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-600 hover:border-slate-900 hover:text-slate-900'"
                    @click="filterByKategori(kat)"
                >
                    {{ kat }}
                </button>
            </div>
        </div>

        <div v-if="informasi.data?.length" class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <article 
                v-for="item in informasi.data" 
                :key="item.id" 
                class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-2xl transition duration-500 flex flex-col justify-between"
            >
                <div>
                    <div class="relative h-64 w-full overflow-hidden bg-slate-100 flex items-center justify-center">
                        <img 
                            v-if="item.cover_image" 
                            :src="item.cover_image" 
                            :alt="item.judul"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-700"
                        />
                        <div v-else class="flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                            <Newspaper class="size-12 mb-3 stroke-[1]" />
                        </div>
                        
                        <div class="absolute bottom-4 left-4">
                            <span class="text-[9px] font-bold tracking-[0.2em] text-white uppercase bg-slate-900/80 backdrop-blur-md px-4 py-2 rounded-lg shadow-sm font-sans">
                                {{ item.kategori }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8 space-y-4">
                        <div class="flex items-center gap-3 text-[10px] text-slate-500 font-bold uppercase tracking-wider font-sans">
                            <span class="flex items-center gap-1.5">
                                <Clock class="size-3.5" />
                                {{ new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </span>
                        </div>

                        <h2 class="text-xl font-heading font-bold text-slate-900 leading-tight group-hover:text-amber-600 transition duration-300 line-clamp-2">
                            <a :href="`/informasi/${item.slug}`">{{ item.judul }}</a>
                        </h2>

                        <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed font-sans">
                            {{ stripHtml(item.konten) }}
                        </p>
                    </div>
                </div>

                <div class="px-8 pb-8 pt-2">
                    <AppButton 
                        :href="`/informasi/${item.slug}`" 
                        variant="ghost" 
                        class="text-amber-600 font-bold px-0 flex items-center gap-2 text-[10px] uppercase tracking-[0.2em] hover:text-amber-700 hover:gap-3 transition-all duration-300 font-sans"
                    >
                        Baca Selengkapnya <ArrowRight class="size-4" />
                    </AppButton>
                </div>
            </article>
        </div>

        <EmptyState 
            v-else 
            class="mt-10 bg-white" 
            title="Tidak ditemukan informasi" 
            message="Kami tidak menemukan berita atau pengumuman dengan kata kunci / kategori pencarian tersebut." 
            :icon="Newspaper" 
        >
            <AppButton @click="clearSearch" variant="outline" class="rounded-full font-sans">
                Reset Pencarian
            </AppButton>
        </EmptyState>

        <div class="mt-12">
            <Pagination v-if="informasi.meta" :links="informasi.links" :meta="informasi.meta" />
        </div>
    </section>
</template>
