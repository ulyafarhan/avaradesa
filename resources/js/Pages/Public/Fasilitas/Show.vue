<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PublicLayout from "../../../Layouts/PublicLayout.vue";
import { MapPin, ArrowLeft, Calendar, ChevronRight } from "@lucide/vue";

defineOptions({ layout: PublicLayout });
const props = defineProps({
    fasilitas: Object,
    fasilitasTerbaru: Array,
});

const stripHtml = (html) => {
    if (!html) return "";
    return html.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
};

const getKondisiBadgeColor = (kondisi) => {
    switch (kondisi?.toLowerCase()) {
        case "baik": return "bg-emerald-50 text-emerald-700 border-emerald-200";
        case "rusak ringan": return "bg-amber-50 text-amber-700 border-amber-200";
        case "rusak berat": return "bg-rose-50 text-rose-700 border-rose-200";
        default: return "bg-slate-50 text-slate-700 border-slate-200";
    }
};

const getStatusBadgeColor = (status) => {
    switch (status?.toLowerCase()) {
        case "aktif":
        case "digunakan": return "bg-blue-50 text-blue-700 border-blue-200";
        case "tidak aktif":
        case "kosong": return "bg-slate-100 text-slate-700 border-slate-200";
        default: return "bg-slate-50 text-slate-600 border-slate-200";
    }
};
</script>

<template>
    <Head>
        <title>{{ fasilitas.nama_fasilitas }} - {{ $page.props.settings.nama_desa }}</title>
        <meta name="description" :content="stripHtml(fasilitas.deskripsi).substring(0, 160)" />
        <meta name="keywords" :content="`Fasilitas Desa, ${fasilitas.jenis_fasilitas}, ${fasilitas.nama_fasilitas}`" />
        <meta property="og:title" :content="fasilitas.nama_fasilitas" />
        <meta property="og:description" :content="stripHtml(fasilitas.deskripsi).substring(0, 160)" />
        <meta v-if="fasilitas.foto" property="og:image" :content="fasilitas.foto" />
    </Head>

    <main class="bg-white min-h-screen py-12">
        <article class="mx-auto max-w-3xl px-6 sm:px-8">
            <Link href="/fasilitas" class="inline-flex items-center gap-2 mb-8 text-blue-600 hover:text-blue-700 transition-colors font-medium text-xs">
                <ArrowLeft class="size-4" />
                Kembali ke fasilitas
            </Link>

            <div v-if="fasilitas.foto" class="mb-8 overflow-hidden rounded-2xl border border-gray-200">
                <img :src="fasilitas.foto" :alt="fasilitas.nama_fasilitas" class="aspect-video w-full object-cover" loading="lazy" />
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg uppercase tracking-wider block w-fit">
                        {{ fasilitas.jenis_fasilitas || "Fasilitas Umum" }}
                    </span>
                    <span
                        class="text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider border"
                        :class="getKondisiBadgeColor(fasilitas.kondisi)"
                    >
                        Kondisi: {{ fasilitas.kondisi }}
                    </span>
                    <span
                        v-if="fasilitas.status_penggunaan"
                        class="text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider border"
                        :class="getStatusBadgeColor(fasilitas.status_penggunaan)"
                    >
                        {{ fasilitas.status_penggunaan }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-normal text-gray-900 tracking-tight leading-tight">
                    {{ fasilitas.nama_fasilitas }}
                </h1>

                <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                    <MapPin class="size-3.5 shrink-0" />
                    {{ fasilitas.lokasi || "Lokasi tidak tercatat" }}
                </div>

                <div class="flex items-center gap-4 text-xs text-gray-500 font-bold border-y border-gray-100 py-3.5">
                    <span v-if="fasilitas.tahun_dibangun" class="inline-flex items-center gap-1.5">
                        <Calendar class="size-4" />
                        Tahun Dibangun: {{ fasilitas.tahun_dibangun }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <MapPin class="size-4" />
                        {{ fasilitas.latitude && fasilitas.longitude ? "Tersedia Koordinat" : "Tidak ada koordinat" }}
                    </span>
                </div>

                <a
                    v-if="fasilitas.latitude && fasilitas.longitude"
                    :href="'https://www.google.com/maps/place/' + fasilitas.latitude + ',' + fasilitas.longitude"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline font-medium"
                >
                    <MapPin class="size-3.5" />
                    Lihat di Google Maps
                </a>
            </div>

            <div class="mt-8 text-slate-700 text-sm sm:text-base leading-relaxed space-y-6 font-medium">
                {{ fasilitas.deskripsi || "Tidak ada deskripsi tambahan mengenai fasilitas ini." }}
            </div>
        </article>

        <aside v-if="fasilitasTerbaru?.length" class="mx-auto max-w-5xl px-6 sm:px-8 mt-20">
            <h2 class="text-2xl font-normal text-gray-900 tracking-tight mb-6">
                Fasilitas Lainnya
            </h2>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="item in fasilitasTerbaru"
                    :key="item.id"
                    :href="'/fasilitas/' + item.id"
                    class="group block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition duration-200"
                >
                    <div class="h-36 overflow-hidden bg-gradient-to-br from-blue-500/20 via-cyan-500/10 to-blue-600/20 flex items-center justify-center">
                        <img
                            v-if="item.foto"
                            :src="item.foto"
                            :alt="item.nama_fasilitas"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <MapPin v-else class="size-8 text-blue-800/60 stroke-[1.5]" />
                    </div>
                    <div class="p-4 space-y-1.5">
                        <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition line-clamp-1">
                            {{ item.nama_fasilitas }}
                        </h3>
                        <p v-if="item.lokasi" class="text-xs text-gray-500 line-clamp-1">
                            {{ item.lokasi }}
                        </p>
                    </div>
                </Link>
            </div>

            <div class="mt-8 text-center">
                <Link href="/fasilitas" class="inline-flex items-center gap-1.5 text-xs rounded-full border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                    Lihat Semua Fasilitas <ChevronRight class="size-3.5" />
                </Link>
            </div>
        </aside>
    </main>
</template>
