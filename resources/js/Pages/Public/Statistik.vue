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

    <header class="bg-white pt-32 pb-24 border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-4xl space-y-8">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-4 font-sans border-l-4 border-amber-600 pl-4">
                    Data Kependudukan Terbuka
                </span>
                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-slate-900 tracking-tight leading-[1.05] font-heading">
                    Statistik Desa<br/><span class="italic text-amber-600">{{ $page.props.settings.nama_desa }}.</span>
                </h1>
                
                <p class="text-xl text-slate-600 font-sans leading-relaxed max-w-2xl border-l-2 border-slate-200 pl-4">
                    Sajian informasi demografis, kepengurusan administrasi, dan mobilitas penduduk secara real-time. Transparansi data untuk pembangunan desa yang akuntabel.
                </p>
            </div>
        </div>
    </header>

    <div class="bg-white border-b border-slate-200 sticky top-[73px] z-20 shadow-sm backdrop-blur-xl bg-white/80">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex gap-12 font-sans">
                <button 
                    @click="activeTab = 'demografi'"
                    class="py-6 text-sm font-bold uppercase tracking-widest border-b-2 transition-all duration-300"
                    :class="activeTab === 'demografi' ? 'border-amber-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-900'"
                >
                    Profil Demografi
                </button>
                <button 
                    @click="activeTab = 'layanan'"
                    class="py-6 text-sm font-bold uppercase tracking-widest border-b-2 transition-all duration-300"
                    :class="activeTab === 'layanan' ? 'border-amber-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-900'"
                >
                    Kinerja Layanan Surat
                </button>
            </div>
        </div>
    </div>

    <main class="py-24 bg-slate-50 min-h-[500px]">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            
            <div v-if="activeTab === 'demografi'" class="space-y-16">
                
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[220px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Total Warga Aktif</span>
                            <span class="text-6xl font-heading font-bold text-slate-900 tracking-tighter">{{ totalPenduduk.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="text-xs font-semibold text-slate-500 flex items-center gap-2 pt-6 border-t border-slate-200 font-sans">
                            <Users class="size-4 text-amber-600" />
                            Terdaftar di sistem database desa
                        </div>
                    </div>
                    
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[220px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Rasio Jenis Kelamin</span>
                            <div class="flex gap-6 items-baseline mt-2">
                                <span class="text-4xl font-heading font-bold text-slate-900">{{ totalLakiLaki }} <span class="text-xs font-bold text-slate-500 font-sans uppercase tracking-widest">Laki</span></span>
                                <span class="text-4xl font-heading font-bold text-slate-900">{{ totalPerempuan }} <span class="text-xs font-bold text-slate-500 font-sans uppercase tracking-widest">Pr</span></span>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-slate-200 mt-auto">
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden flex">
                                <div class="bg-slate-900 h-full" :style="{ width: formatPercent(totalLakiLaki, totalPenduduk) }" />
                                <div class="bg-amber-600 h-full" :style="{ width: formatPercent(totalPerempuan, totalPenduduk) }" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[220px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Total Kepala Keluarga</span>
                            <span class="text-6xl font-heading font-bold text-slate-900 tracking-tighter">{{ demografi?.total_keluarga ?? 0 }}</span>
                        </div>
                        <div class="text-xs font-semibold text-slate-500 flex items-center gap-2 pt-6 border-t border-slate-200 font-sans">
                            <TrendingUp class="size-4 text-amber-600" />
                            Rata-rata 3-4 jiwa per keluarga
                        </div>
                    </div>
                </div>

                <div class="grid gap-12 lg:grid-cols-2">
                    <section class="bg-white border border-slate-200 rounded-[2rem] p-10 shadow-xl">
                        <div class="mb-10 flex items-center justify-between border-b border-slate-200 pb-6">
                            <h3 class="text-2xl font-heading font-bold text-slate-900 flex items-center gap-3">
                                <BarChart3 class="size-6 text-amber-600" /> Pekerjaan Warga
                            </h3>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest font-sans">10 Besar</span>
                        </div>
                        <div class="space-y-6">
                            <div v-for="job in pekerjaanSorted" :key="job.name" class="space-y-2">
                                <div class="flex justify-between text-xs font-bold text-slate-900 uppercase tracking-wider font-sans">
                                    <span>{{ job.name }}</span>
                                    <span class="text-amber-600">{{ job.value }} jiwa ({{ formatPercent(job.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-slate-900 h-full rounded-full transition-all duration-700" :style="{ width: formatPercent(job.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!pekerjaanSorted.length" class="text-center py-8 text-xs font-bold text-slate-500 uppercase tracking-widest font-sans">
                                Belum tersedia data pekerjaan
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-slate-200 rounded-[2rem] p-10 shadow-xl">
                        <div class="mb-10 flex items-center justify-between border-b border-slate-200 pb-6">
                            <h3 class="text-2xl font-heading font-bold text-slate-900 flex items-center gap-3">
                                <PieChart class="size-6 text-amber-600" /> Kelompok Usia
                            </h3>
                        </div>
                        <div class="space-y-6">
                            <div v-for="age in usiaSorted" :key="age.name" class="space-y-2">
                                <div class="flex justify-between text-xs font-bold text-slate-900 uppercase tracking-wider font-sans">
                                    <span>Usia {{ age.name }} tahun</span>
                                    <span class="text-amber-600">{{ age.value }} jiwa ({{ formatPercent(age.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-amber-600 h-full rounded-full transition-all duration-700" :style="{ width: formatPercent(age.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!usiaSorted.length" class="text-center py-8 text-xs font-bold text-slate-500 uppercase tracking-widest font-sans">
                                Belum tersedia data kelompok usia
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-slate-200 rounded-[2rem] p-10 shadow-xl">
                        <div class="mb-10 border-b border-slate-200 pb-6">
                            <h3 class="text-2xl font-heading font-bold text-slate-900 flex items-center gap-3">
                                <BarChart3 class="size-6 text-amber-600" /> Tingkat Pendidikan
                            </h3>
                        </div>
                        <div class="space-y-6">
                            <div v-for="edu in pendidikanSorted" :key="edu.name" class="space-y-2">
                                <div class="flex justify-between text-xs font-bold text-slate-900 uppercase tracking-wider font-sans">
                                    <span>{{ edu.name }}</span>
                                    <span class="text-amber-600">{{ edu.value }} jiwa ({{ formatPercent(edu.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-slate-900 h-full rounded-full transition-all duration-700" :style="{ width: formatPercent(edu.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!pendidikanSorted.length" class="text-center py-8 text-xs font-bold text-slate-500 uppercase tracking-widest font-sans">
                                Belum tersedia data pendidikan
                            </div>
                        </div>
                    </section>

                    <section class="bg-white border border-slate-200 rounded-[2rem] p-10 shadow-xl">
                        <div class="mb-10 border-b border-slate-200 pb-6">
                            <h3 class="text-2xl font-heading font-bold text-slate-900 flex items-center gap-3">
                                <PieChart class="size-6 text-amber-600" /> Keragaman Agama
                            </h3>
                        </div>
                        <div class="space-y-6">
                            <div v-for="rel in agamaSorted" :key="rel.name" class="space-y-2">
                                <div class="flex justify-between text-xs font-bold text-slate-900 uppercase tracking-wider font-sans">
                                    <span>{{ rel.name }}</span>
                                    <span class="text-amber-600">{{ rel.value }} jiwa ({{ formatPercent(rel.value, totalPenduduk) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-amber-600 h-full rounded-full transition-all duration-700" :style="{ width: formatPercent(rel.value, totalPenduduk) }" />
                                </div>
                            </div>
                            <div v-if="!agamaSorted.length" class="text-center py-8 text-xs font-bold text-slate-500 uppercase tracking-widest font-sans">
                                Belum tersedia data keragaman agama
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div v-else class="space-y-16">
                <div class="grid gap-8 md:grid-cols-4">
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[180px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Total Pengajuan</span>
                            <span class="text-5xl font-heading font-bold text-slate-900">{{ totalSurat }}</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 pt-4 border-t border-slate-200 font-sans">Sejak sistem diluncurkan</div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[180px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Selesai / Terbit</span>
                            <span class="text-5xl font-heading font-bold text-emerald-600">{{ suratSelesai }}</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 pt-4 border-t border-slate-200 font-sans">Penyelesaian: {{ formatPercent(suratSelesai, totalSurat) }}</div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[180px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Sedang Antre</span>
                            <span class="text-5xl font-heading font-bold text-amber-600">{{ suratPending }}</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 pt-4 border-t border-slate-200 font-sans">Butuh respon admin desa</div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl flex flex-col justify-between min-h-[180px]">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest block mb-4">Berkas Ditolak</span>
                            <span class="text-5xl font-heading font-bold text-red-600">{{ suratDitolak }}</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 pt-4 border-t border-slate-200 font-sans">Syarat tidak lengkap</div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-3">
                    <section class="bg-white border border-slate-200 rounded-[2rem] p-10 shadow-xl lg:col-span-2">
                        <div class="mb-8 border-b border-slate-200 pb-6">
                            <h3 class="text-2xl font-heading font-bold text-slate-900 flex items-center gap-3">
                                <FileText class="size-6 text-amber-600" /> Distribusi Jenis Surat
                            </h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div v-for="item in jenisSuratSorted" :key="item.name" class="py-5 flex justify-between items-center group">
                                <div class="flex items-center gap-4">
                                    <span class="size-2 rounded-full bg-amber-600 shadow-sm transition-transform group-hover:scale-150 duration-300" />
                                    <span class="text-sm font-bold text-slate-900 font-sans uppercase tracking-wider">{{ item.name }}</span>
                                </div>
                                <div class="flex items-center gap-6 font-sans">
                                    <span class="text-slate-900 font-bold text-sm">{{ item.value }} pengajuan</span>
                                    <span class="text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full text-[10px] font-bold tracking-widest">{{ formatPercent(item.value, totalSurat) }}</span>
                                </div>
                            </div>
                            <div v-if="!jenisSuratSorted.length" class="text-center py-12 text-[10px] font-bold text-slate-500 font-sans uppercase tracking-widest">
                                Belum ada pengajuan surat yang masuk
                            </div>
                        </div>
                    </section>

                    <section class="bg-slate-950 text-white rounded-[2rem] p-10 flex flex-col justify-between border border-slate-800 shadow-2xl relative overflow-hidden">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/10 rounded-full blur-[60px]"></div>
                        <div class="space-y-6 relative z-10">
                            <div class="flex size-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                                <Info class="size-6 text-white" />
                            </div>
                            <h4 class="text-3xl font-heading font-bold leading-tight">Pengajuan<br/><span class="italic text-amber-500">Mandiri Warga.</span></h4>
                            <p class="text-sm text-white/70 font-sans font-light leading-relaxed">
                                Warga Desa {{ $page.props.settings.nama_desa }} dapat mengajukan seluruh jenis surat keterangan di atas secara mandiri online dengan masuk menggunakan NIK dan kata sandi yang terdaftar di kantor desa.
                            </p>
                        </div>
                        
                        <div class="pt-8 mt-12 relative z-10 border-t border-white/20">
                            <a href="/login" class="inline-flex items-center justify-center w-full rounded-full bg-white text-slate-900 hover:bg-amber-50 transition-colors duration-300 px-8 py-4 text-[10px] font-bold font-sans uppercase tracking-[0.2em] shadow-xl">
                                Masuk Portal <ArrowRight class="size-4 ml-3" />
                            </a>
                        </div>
                    </section>
                </div>
            </div>

        </div>
    </main>
</template>
