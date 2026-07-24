<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PublicLayout from "../../Layouts/PublicLayout.vue";
import {
    Building,
    MapPin,
    Calendar,
    ArrowRight
} from "@lucide/vue";

defineOptions({ layout: PublicLayout });
const props = defineProps({ fasilitas: Array });

const getKondisiBadgeColor = (kondisi) => {
    switch (kondisi?.toLowerCase()) {
        case "baik":
            return "bg-emerald-50 text-emerald-700 border-emerald-200";
        case "rusak ringan":
            return "bg-blue-50 text-blue-700 border-blue-200";
        case "rusak berat":
            return "bg-red-50 text-red-700 border-red-200";
        default:
            return "bg-slate-50 text-slate-700 border-slate-200";
    }
};

const getStatusBadgeColor = (status) => {
    switch (status?.toLowerCase()) {
        case "aktif":
        case "digunakan":
            return "bg-sky-50 text-sky-700 border-sky-200";
        case "tidak aktif":
        case "kosong":
            return "bg-slate-100 text-slate-600 border-slate-200";
        default:
            return "bg-slate-100 text-slate-600 border-slate-200";
    }
};
</script>

<template>
    <Head>
        <title>Fasilitas Desa - {{ $page.props.settings.nama_desa }}</title>
        <meta name="description" :content="'Daftar inventaris sarana dan fasilitas umum di Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten" />
    </Head>

    <header class="bg-white pt-32 pb-24 border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl space-y-8">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase font-sans border-l-4 border-amber-600 pl-4">
                    Sarana & Prasarana Publik
                </span>
                
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-slate-900 tracking-tight leading-[1.1] font-heading">
                    Fasilitas Desa.<br/>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 font-sans leading-relaxed max-w-2xl border-l-2 border-slate-200 pl-4">
                    Daftar inventaris sarana umum, kesehatan, pendidikan, dan infrastruktur sosial di wilayah {{ $page.props.settings.nama_desa }}.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 py-24 lg:px-8 bg-slate-50 min-h-[500px]">
        <div v-if="!fasilitas || fasilitas.length === 0" class="flex flex-col items-center justify-center text-center py-24 border-2 border-dashed border-slate-300 rounded-3xl bg-white shadow-sm">
            <Building class="size-12 text-slate-300 mb-6 stroke-[1.5]" />
            <h3 class="text-2xl font-bold font-heading text-slate-900">Belum Ada Data</h3>
            <p class="text-slate-500 font-sans mt-2">Fasilitas publik desa belum terdaftar atau masih dalam proses pendataan.</p>
        </div>

        <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="item in fasilitas"
                :key="item.id"
                :href="'/fasilitas/' + item.id"
                class="group flex flex-col rounded-3xl border border-slate-200 bg-white overflow-hidden focus:outline-none focus:ring-4 focus:ring-amber-200 transition-all hover:border-amber-300 hover:shadow-xl hover:shadow-amber-900/5"
            >
                <div class="aspect-[4/3] w-full bg-slate-100 relative overflow-hidden shrink-0 flex items-center justify-center">
                    <img
                        v-if="item.foto"
                        :src="item.foto"
                        :alt="item.nama_fasilitas"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50">
                        <Building class="size-12 stroke-[1.5]" />
                    </div>

                    <div class="absolute top-4 right-4 flex flex-col gap-2 items-end">
                        <span
                            class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border font-sans shadow-sm"
                            :class="getKondisiBadgeColor(item.kondisi)"
                        >
                            {{ item.kondisi }}
                        </span>
                        <span
                            v-if="item.status_penggunaan"
                            class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border font-sans shadow-sm"
                            :class="getStatusBadgeColor(item.status_penggunaan)"
                        >
                            {{ item.status_penggunaan }}
                        </span>
                    </div>
                </div>

                <div class="p-8 flex-1 flex flex-col justify-between">
                    <div class="space-y-3 mb-6">
                        <span class="block text-[10px] font-bold tracking-widest text-amber-600 uppercase font-sans">
                            {{ item.jenis_fasilitas || "Fasilitas Umum" }}
                        </span>
                        <h3 class="text-2xl font-bold font-heading text-slate-900 group-hover:text-amber-700 transition-colors leading-snug line-clamp-2">
                            {{ item.nama_fasilitas }}
                        </h3>
                        <p class="text-sm text-slate-600 font-sans leading-relaxed line-clamp-3">
                            {{ item.deskripsi || "Tidak ada deskripsi." }}
                        </p>
                    </div>

                    <div class="border-t border-slate-100 pt-6 space-y-3 text-xs text-slate-500 font-sans">
                        <div class="flex items-start gap-3">
                            <MapPin class="size-4 shrink-0 text-slate-400 mt-0.5" />
                            <span class="leading-relaxed line-clamp-1">{{ item.lokasi || "-" }}</span>
                        </div>
                        <div v-if="item.tahun_dibangun" class="flex items-center gap-3">
                            <Calendar class="size-4 shrink-0 text-slate-400" />
                            <span>{{ item.tahun_dibangun }}</span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </section>
</template>
