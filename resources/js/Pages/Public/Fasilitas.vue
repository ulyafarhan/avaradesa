<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { 
    Building, 
    MapPin, 
    Calendar, 
    Info, 
    Activity,
    Map
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });
const props = defineProps({ fasilitas: Array });

const getKondisiBadgeColor = (kondisi) => {
    switch (kondisi?.toLowerCase()) {
        case 'baik':
            return 'bg-emerald-50 text-emerald-700 border-emerald-250';
        case 'rusak ringan':
            return 'bg-amber-50 text-amber-700 border-amber-250';
        case 'rusak berat':
            return 'bg-rose-50 text-rose-700 border-rose-250';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getStatusBadgeColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'aktif':
        case 'digunakan':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'tidak aktif':
        case 'kosong':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        default:
            return 'bg-slate-50 text-slate-600 border-slate-200';
    }
};
</script>

<template>
    <Head>
        <title>Fasilitas Desa - {{ $page.props.settings.nama_desa }}</title>
        <meta name="description" :content="'Daftar inventaris sarana dan fasilitas umum di Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten" />
    </Head>

    <header class="bg-white border-b border-gray-200 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F0FE] text-[#1A73E8] uppercase tracking-wider">
                    <Building class="size-3.5" /> Sarana & Prasarana Publik
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-normal text-[#202124] tracking-tight leading-tight">
                    Fasilitas Desa {{ $page.props.settings.nama_desa }}
                </h1>
                
                <p class="text-base sm:text-lg text-[#5F6368] font-normal leading-relaxed">
                    Daftar sarana umum, kesehatan, pendidikan, keagamaan, dan infrastruktur sosial lainnya yang dibangun dan dirawat untuk melayani kebutuhan warga.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8 bg-white">
        <div v-if="!fasilitas || fasilitas.length === 0" class="text-center py-16 border border-dashed border-slate-200 rounded-3xl">
            <Building class="size-12 text-slate-300 mx-auto mb-4" />
            <h3 class="text-lg font-bold text-slate-800">Belum Ada Data Fasilitas</h3>
            <p class="text-slate-500 text-sm mt-1">Fasilitas publik desa belum terdaftar atau masih dalam proses pendataan.</p>
        </div>

        <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div 
                v-for="item in fasilitas" 
                :key="item.id"
                class="flex flex-col rounded-3xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 group"
            >
                <div class="h-48 w-full bg-slate-100 relative overflow-hidden shrink-0">
                    <img 
                        v-if="item.foto"
                        :src="item.foto" 
                        :alt="item.nama_fasilitas"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
                        <Building class="size-10 stroke-[1.5]" />
                    </div>

                    <div class="absolute top-4 right-4 flex flex-col gap-1.5 items-end">
                        <span 
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm"
                            :class="getKondisiBadgeColor(item.kondisi)"
                        >
                            Kondisi: {{ item.kondisi }}
                        </span>
                        <span 
                            v-if="item.status_penggunaan"
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm"
                            :class="getStatusBadgeColor(item.status_penggunaan)"
                        >
                            {{ item.status_penggunaan }}
                        </span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] font-bold text-[#1A73E8] bg-[#E8F0FE] px-2.5 py-1 rounded-full uppercase tracking-wider inline-block mb-2">
                                {{ item.jenis_fasilitas || 'Fasilitas Umum' }}
                            </span>
                            <h3 class="text-lg font-bold text-[#202124] group-hover:text-blue-650 transition duration-200 leading-snug">
                                {{ item.nama_fasilitas }}
                            </h3>
                        </div>

                        <p class="text-xs text-[#5F6368] leading-relaxed line-clamp-3">
                            {{ item.deskripsi || 'Tidak ada deskripsi tambahan mengenai fasilitas ini.' }}
                        </p>
                    </div>

                    <div class="border-t border-slate-100 pt-5 mt-6 space-y-2.5 text-xs text-[#5F6368] font-medium">
                        <div class="flex items-start gap-2">
                            <MapPin class="size-4 shrink-0 text-slate-400 mt-0.5" />
                            <span class="leading-normal">{{ item.lokasi || '-' }}</span>
                        </div>
                        <div v-if="item.tahun_dibangun" class="flex items-center gap-2">
                            <Calendar class="size-4 shrink-0 text-slate-400" />
                            <span>Tahun Dibangun: {{ item.tahun_dibangun }}</span>
                        </div>
                        <div v-if="item.latitude && item.longitude" class="flex items-center gap-2">
                            <Map class="size-4 shrink-0 text-slate-400" />
                            <a 
                                :href="'https://www.google.com/maps/place/' + item.latitude + ',' + item.longitude" 
                                target="_blank"
                                class="text-[#1A73E8] hover:underline"
                            >
                                Koordinat: {{ item.latitude }}, {{ item.longitude }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
