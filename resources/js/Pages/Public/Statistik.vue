<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { 
    Users, 
    TrendingUp, 
    FileText, 
    ArrowRight,
    PieChart,
    BarChart3,
    Activity,
    Info
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    demografi: Object,
    layanan: Object,
});

const activeTab = ref('demografi');

const formatPercent = (val, total) => {
    if (!total) return '0%';
    return `${((val / total) * 100).toFixed(1)}%`;
};

const totalPenduduk = computed(() => props.demografi?.total_penduduk ?? 0);
const totalLakiLaki = computed(() => props.demografi?.laki_laki ?? 0);
const totalPerempuan = computed(() => props.demografi?.perempuan ?? 0);

const totalSurat = computed(() => props.layanan?.pengajuan_surat?.total ?? 0);
const suratSelesai = computed(() => props.layanan?.pengajuan_surat?.selesai ?? 0);
const suratPending = computed(() => props.layanan?.pengajuan_surat?.pending ?? 0);
const suratDitolak = computed(() => props.layanan?.pengajuan_surat?.ditolak ?? 0);

const agamaSorted = computed(() => {
    if (!props.demografi?.per_agama) return [];
    return Object.entries(props.demografi.per_agama)
        .map(([name, val]) => ({ name, value: val }))
        .sort((a, b) => b.value - a.value);
});

const pendidikanSorted = computed(() => {
    if (!props.demografi?.per_pendidikan) return [];
    return Object.entries(props.demografi.per_pendidikan)
        .map(([name, val]) => ({ name, value: val }))
        .sort((a, b) => b.value - a.value);
});

const pekerjaanSorted = computed(() => {
    if (!props.demografi?.per_pekerjaan) return [];
    return Object.entries(props.demografi.per_pekerjaan)
        .map(([name, val]) => ({ name, value: val }))
        .sort((a, b) => b.value - a.value);
});

const usiaSorted = computed(() => {
    if (!props.demografi?.per_usia) return [];
    return Object.entries(props.demografi.per_usia)
        .map(([name, val]) => ({ name, value: val }));
});

const jenisSuratSorted = computed(() => {
    if (!props.layanan?.per_jenis_surat) return [];
    return Object.entries(props.layanan.per_jenis_surat)
        .map(([name, val]) => ({ name, value: val }))
        .sort((a, b) => b.value - a.value);
});
</script>

<template>
    <Head>
        <title>Statistik Demografi & Layanan - {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}</title>
        <meta name="description" :content="'Visualisasi data demografi kependudukan secara real-time dan statistik aktivitas pelayanan surat di Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten" />
        <meta name="keywords" :content="'Statistik Kependudukan, Grafik Penduduk, Demografi, Transparansi Data Desa, ' + $page.props.settings.nama_desa" />
        <meta property="og:title" :content="'Statistik Demografi & Layanan - ' + $page.props.settings.nama_desa" />
        <meta property="og:description" :content="'Visualisasi data demografi kependudukan secara real-time di Desa ' + $page.props.settings.nama_desa" />
    </Head>

    <header class="bg-white border-b border-gray-200 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[#E8F0FE] text-[#1A73E8] uppercase tracking-wider mb-6">
                    <Activity class="size-3.5" /> Data Kependudukan Terbuka
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-normal text-[#202124] tracking-tight leading-tight mb-4">
                    Statistik Desa {{ $page.props.settings.nama_desa }}
                </h1>
                
                <p class="text-base sm:text-lg text-[#5F6368] font-normal leading-relaxed">
                    Sajian informasi demografis, kepengurusan administrasi, dan mobilitas penduduk secara real-time. Transparansi data untuk pembangunan desa yang akuntabel.
                </p>
            </div>
        </div>
    </header>

    <div class="bg-white border-b border-gray-200 sticky top-[73px] z-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex gap-8">
                <button 
                    @click="activeTab = 'demografi'"
                    class="py-4 text-sm font-medium border-b-2 transition-all"
                    :class="activeTab === 'demografi' ? 'border-[#1A73E8] text-[#1A73E8]' : 'border-transparent text-[#5F6368] hover:text-[#202124]'"
                >
                    Profil Demografi
                </button>
                <button 
                    @click="activeTab = 'layanan'"
                    class="py-4 text-sm font-medium border-b-2 transition-all"
                    :class="activeTab === 'layanan' ? 'border-[#1A73E8] text-[#1A73E8]' : 'border-transparent text-[#5F6368] hover:text-[#202124]'"
                >
                    Kinerja Layanan Surat
                </button>
            </div>
        </div>
    </div>

    <main class="py-12 bg-[#F8F9FA] min-h-[500px]">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            
            <div v-if="activeTab === 'demografi'" class="space-y-12">
                
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[160px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Total Warga Aktif</span>
                            <span class="text-4xl font-normal text-[#202124]">{{ totalPenduduk.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] flex items-center gap-1.5 pt-4 border-t border-gray-100">
                            <Users class="size-4 text-[#1A73E8]" />
                            Terdaftar di sistem database desa
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[160px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Rasio Jenis Kelamin</span>
                            <div class="flex gap-4 items-baseline mt-1">
                                <span class="text-2xl font-normal text-[#202124]">{{ totalLakiLaki }} <span class="text-xs text-[#5F6368]">Laki-laki</span></span>
                                <span class="text-2xl font-normal text-[#202124]">{{ totalPerempuan }} <span class="text-xs text-[#5F6368]">Perempuan</span></span>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-100">
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden flex">
                                <div class="bg-[#1A73E8] h-full" :style="{ width: formatPercent(totalLakiLaki, totalPenduduk) }" />
                                <div class="bg-emerald-500 h-full" :style="{ width: formatPercent(totalPerempuan, totalPenduduk) }" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[160px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Total Kepala Keluarga</span>
                            <span class="text-4xl font-normal text-[#202124]">{{ demografi?.total_keluarga ?? 0 }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] flex items-center gap-1.5 pt-4 border-t border-gray-100">
                            <TrendingUp class="size-4 text-emerald-600" />
                            Rata-rata 3-4 jiwa per keluarga
                        </div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
                    <section class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-normal text-[#202124] flex items-center gap-2">
                                <BarChart3 class="size-5 text-[#1A73E8]" /> Pekerjaan Warga
                            </h3>
                            <span class="text-xs text-[#5F6368]">10 Besar Terbanyak</span>
                        </div>
                        <div class="space-y-4">
                            <div v-for="job in pekerjaanSorted" :key="job.name" class="space-y-1">
                                <div class="flex justify-between text-xs font-medium text-[#202124]">
                                    <span>{{ job.name }}</span>
                                    <span>{{ job.value }} jiwa ({{ formatPercent(job.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#1A73E8] h-full rounded-full transition-all duration-500" :style="{ width: formatPercent(job.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!pekerjaanSorted.length" class="text-center py-8 text-xs text-[#5F6368]">
                                Belum tersedia data pekerjaan
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-normal text-[#202124] flex items-center gap-2">
                                <PieChart class="size-5 text-[#1A73E8]" /> Distribusi Kelompok Usia
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="age in usiaSorted" :key="age.name" class="space-y-1">
                                <div class="flex justify-between text-xs font-medium text-[#202124]">
                                    <span>Usia {{ age.name }} tahun</span>
                                    <span>{{ age.value }} jiwa ({{ formatPercent(age.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-teal-600 h-full rounded-full transition-all duration-500" :style="{ width: formatPercent(age.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!usiaSorted.length" class="text-center py-8 text-xs text-[#5F6368]">
                                Belum tersedia data kelompok usia
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-lg font-normal text-[#202124] flex items-center gap-2">
                                <BarChart3 class="size-5 text-[#1A73E8]" /> Tingkat Pendidikan Warga
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="edu in pendidikanSorted" :key="edu.name" class="space-y-1">
                                <div class="flex justify-between text-xs font-medium text-[#202124]">
                                    <span>{{ edu.name }}</span>
                                    <span>{{ edu.value }} jiwa ({{ formatPercent(edu.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#1A73E8] h-full rounded-full transition-all duration-500" :style="{ width: formatPercent(edu.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!pendidikanSorted.length" class="text-center py-8 text-xs text-[#5F6368]">
                                Belum tersedia data pendidikan
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-lg font-normal text-[#202124] flex items-center gap-2">
                                <PieChart class="size-5 text-[#1A73E8]" /> Keragaman Agama
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="rel in agamaSorted" :key="rel.name" class="space-y-1">
                                <div class="flex justify-between text-xs font-medium text-[#202124]">
                                    <span>{{ rel.name }}</span>
                                    <span>{{ rel.value }} jiwa ({{ formatPercent(rel.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-teal-600 h-full rounded-full transition-all duration-500" :style="{ width: formatPercent(rel.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!agamaSorted.length" class="text-center py-8 text-xs text-[#5F6368]">
                                Belum tersedia data keragaman agama
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div v-else class="space-y-12">
                <div class="grid gap-6 md:grid-cols-4">
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[140px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Total Pengajuan</span>
                            <span class="text-3xl font-normal text-[#202124]">{{ totalSurat }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] pt-2">Sejak sistem diluncurkan</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[140px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Selesai / Terbit</span>
                            <span class="text-3xl font-normal text-emerald-600">{{ suratSelesai }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] pt-2">Tingkat penyelesaian: {{ formatPercent(suratSelesai, totalSurat) }}</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[140px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Sedang Antre</span>
                            <span class="text-3xl font-normal text-[#1A73E8]">{{ suratPending }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] pt-2">Butuh respon admin desa</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[140px]">
                        <div>
                            <span class="text-xs font-semibold text-[#5F6368] uppercase tracking-wider block mb-1">Berkas Ditolak</span>
                            <span class="text-3xl font-normal text-red-600">{{ suratDitolak }}</span>
                        </div>
                        <div class="text-xs text-[#5F6368] pt-2">Syarat administrasi tidak lengkap</div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-3">
                    <section class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm lg:col-span-2">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-base font-normal text-[#202124] flex items-center gap-2">
                                <FileText class="size-5 text-[#1A73E8]" /> Pengajuan Berdasarkan Jenis Surat
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div v-for="item in jenisSuratSorted" :key="item.name" class="py-3.5 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <span class="size-2 rounded-full bg-[#1A73E8]" />
                                    <span class="text-xs font-semibold text-[#202124]">{{ item.name }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-xs">
                                    <span class="font-normal text-[#202124]">{{ item.value }} pengajuan</span>
                                    <span class="text-[#5F6368] bg-gray-100 px-2 py-0.5 rounded-md text-[10px]">{{ formatPercent(item.value, totalSurat) }}</span>
                                </div>
                            </div>
                            <div v-if="!jenisSuratSorted.length" class="text-center py-8 text-xs text-[#5F6368]">
                                Belum ada pengajuan surat yang masuk
                            </div>
                        </div>
                    </section>

                    <section class="bg-[#E8F0FE] rounded-2xl p-8 flex flex-col justify-between border border-[#D2E3FC]">
                        <div class="space-y-4">
                            <div class="flex size-10 items-center justify-center rounded-full bg-white text-[#1A73E8]">
                                <Info class="size-5" />
                            </div>
                            <h4 class="text-sm font-semibold text-[#202124]">Pengajuan Mandiri Warga</h4>
                            <p class="text-xs text-[#5F6368] leading-relaxed">
                                Warga Desa {{ $page.props.settings.nama_desa }} dapat mengajukan seluruh jenis surat keterangan di atas secara mandiri online dengan masuk menggunakan NIK dan kata sandi yang terdaftar di kantor desa.
                            </p>
                        </div>
                        
                        <div class="pt-6">
                            <a href="/login" class="inline-flex items-center gap-1.5 text-xs text-[#1A73E8] hover:underline font-semibold">
                                Masuk Portal Layanan Mandiri <ArrowRight class="size-4" />
                            </a>
                        </div>
                    </section>
                </div>
            </div>

        </div>
    </main>
</template>
