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

const stripHtml = (html) => {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '');
};

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

    <header class="bg-white border-b border-gray-200 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F0FE] text-[#1A73E8] uppercase tracking-wider">
                    <Newspaper class="size-3.5" /> Publikasi Kabar & Pengumuman
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-normal text-[#202124] tracking-tight leading-tight">
                    Pusat Informasi Desa
                </h1>
                
                <p class="text-base sm:text-lg text-[#5F6368] font-normal leading-relaxed">
                    Dapatkan berita terupdate, agenda kegiatan desa, serta pengumuman transparansi pembangunan secara langsung dan terbuka.
                </p>

                <div class="max-w-md pt-4">
                    <form @submit.prevent="triggerFilter" class="flex items-center bg-[#F8F9FA] rounded-lg p-1.5 border border-gray-300 focus-within:border-[#1A73E8] focus-within:bg-white transition duration-200">
                        <div class="pl-2.5 text-[#5F6368] shrink-0">
                            <Search class="size-4" />
                        </div>
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            placeholder="Cari berita..." 
                            class="bg-transparent text-[#202124] text-sm grow px-2 py-2"
                            style="outline: none !important; box-shadow: none !important; border: none !important;"
                        />
                        <button 
                            v-if="searchQuery" 
                            type="button" 
                            @click="clearSearch"
                            class="p-1.5 text-[#5F6368] hover:text-[#202124] shrink-0 rounded"
                        >
                            <X class="size-4" />
                        </button>
                        <AppButton 
                            type="submit" 
                            class="rounded-md bg-[#1A73E8] hover:bg-[#155fa8] text-white font-medium px-4 py-2 text-xs transition shrink-0"
                        >
                            Cari
                        </AppButton>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-6 py-12 lg:px-8 bg-[#F8F9FA] min-h-[500px]">
        
        <div class="space-y-3">
            <span class="text-xs font-bold text-[#5F6368] uppercase tracking-wider block">Kategori Informasi:</span>
            <div class="flex flex-wrap gap-2">
                <button
                    class="rounded-full border px-4 py-1.5 text-xs font-semibold transition"
                    :class="!activeKategori ? 'border-[#1A73E8] bg-[#E8F0FE] text-[#1A73E8]' : 'border-gray-300 bg-white text-[#5F6368] hover:border-gray-400'"
                    @click="filterByKategori('')"
                >
                    Semua Kabar
                </button>
                <button
                    v-for="kat in kategori"
                    :key="kat"
                    class="rounded-full border px-4 py-1.5 text-xs font-semibold transition"
                    :class="activeKategori === kat ? 'border-[#1A73E8] bg-[#E8F0FE] text-[#1A73E8]' : 'border-gray-300 bg-white text-[#5F6368] hover:border-gray-400'"
                    @click="filterByKategori(kat)"
                >
                    {{ kat }}
                </button>
            </div>
        </div>

        <div v-if="informasi.data?.length" class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <article 
                v-for="item in informasi.data" 
                :key="item.id" 
                class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition duration-200 flex flex-col justify-between"
            >
                <div>
                    <div class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-teal-500/20 via-emerald-500/10 to-teal-600/20 flex items-center justify-center">
                        <img 
                            v-if="item.cover_image" 
                            :src="item.cover_image" 
                            :alt="item.judul"
                            class="w-full h-full object-cover group-hover:scale-102 transition duration-500"
                        />
                        <div v-else class="flex flex-col items-center justify-center text-teal-800/60 p-4 text-center">
                            <Newspaper class="size-10 mb-2 stroke-[1.5]" />
                            <span class="text-xs font-semibold tracking-wide uppercase opacity-70">AvaraDesa Kabar</span>
                        </div>
                        
                        <div class="absolute bottom-3 left-3">
                            <span class="text-[10px] font-bold tracking-wider text-teal-850 uppercase bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm">
                                {{ item.kategori }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-3">
                        <div class="flex items-center gap-3 text-[10px] text-[#5F6368] font-bold">
                            <span class="flex items-center gap-1">
                                <Clock class="size-3" />
                                {{ new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </span>
                            <span class="size-1 rounded-full bg-slate-300"></span>
                            <span class="flex items-center gap-1">
                                <User class="size-3" />
                                {{ item.author?.username || 'Admin' }}
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-[#202124] leading-snug group-hover:text-[#1A73E8] transition duration-200 line-clamp-2">
                            <a :href="`/informasi/${item.slug}`">{{ item.judul }}</a>
                        </h2>

                        <p class="text-xs text-slate-700 line-clamp-3 leading-relaxed font-medium">
                            {{ stripHtml(item.konten) }}
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-2">
                    <AppButton 
                        :href="`/informasi/${item.slug}`" 
                        variant="ghost" 
                        class="text-teal-700 font-bold px-0 flex items-center gap-1 text-xs hover:text-teal-850 hover:gap-2 transition-all duration-200"
                    >
                        Baca Selengkapnya <span>→</span>
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
            <AppButton @click="clearSearch" variant="outline" class="rounded-full">
                Reset Pencarian
            </AppButton>
        </EmptyState>

        <div class="mt-12">
            <Pagination v-if="informasi.meta" :links="informasi.links" :meta="informasi.meta" />
        </div>
    </section>
</template>
