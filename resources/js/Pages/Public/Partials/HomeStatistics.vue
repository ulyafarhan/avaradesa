<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';

const props = defineProps({
    demografi: { type: Object, default: () => ({}) },
    layanan: { type: Object, default: () => ({}) }
});

const animatedStats = ref({ penduduk: 0, keluarga: 0, pengajuan: 0 });
const intervals = [];

const animateCount = (key, target) => {
    if (!target) return;
    const duration = 1500;
    const step = Math.max(1, Math.ceil(target / (duration / 16)));
    const interval = setInterval(() => {
        animatedStats.value[key] = Math.min(animatedStats.value[key] + step, target);
        if (animatedStats.value[key] >= target) clearInterval(interval);
    }, 16);
    intervals.push(interval);
};

onMounted(() => {
    animateCount('penduduk', props.demografi?.total_penduduk ?? 0);
    animateCount('keluarga', props.demografi?.total_keluarga ?? 0);
    animateCount('pengajuan', props.layanan?.pengajuan_surat?.total ?? 0);
});

onUnmounted(() => {
    intervals.forEach(clearInterval);
});
</script>

<template>
    <section class="bg-slate-50 py-24 md:py-32 border-t border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-24">
                
                <!-- Left Content: Editorial Header -->
                <div class="lg:w-1/3 flex flex-col justify-between">
                    <div>
                        <span class="block text-xs font-bold tracking-widest text-amber-600 uppercase mb-6 font-sans">
                            Data Kependudukan
                        </span>
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1] mb-6 font-heading">
                            Desa Dalam Angka.
                        </h2>
                        <p class="text-slate-600 text-lg leading-relaxed max-w-md mb-8 font-sans">
                            Transparansi data demografi dan layanan administrasi publik secara real-time untuk kemudahan pantauan warga.
                        </p>
                    </div>
                    
                    <div>
                        <Link href="/statistik" class="group inline-flex items-center text-sm font-bold tracking-widest text-slate-900 uppercase border-b-2 border-slate-900 pb-1 hover:text-amber-600 hover:border-amber-600 transition-colors font-sans">
                            Lihat Detail Statistik <ArrowUpRight class="size-4 ml-2 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1" />
                        </Link>
                    </div>
                </div>

                <!-- Right Content: Minimalist Stats Grid -->
                <div class="lg:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-12 border-t sm:border-t-0 sm:border-l border-slate-200 sm:pl-12 pt-12 sm:pt-0">
                    
                    <div class="flex flex-col">
                        <span class="text-6xl md:text-7xl font-bold text-slate-900 tracking-tighter mb-4 font-heading">
                            {{ animatedStats.penduduk }}
                        </span>
                        <div class="h-px w-12 bg-amber-600 mb-4"></div>
                        <h4 class="text-sm font-bold text-slate-900 tracking-widest uppercase mb-2 font-sans">
                            Warga Aktif
                        </h4>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-[200px] font-sans">
                            Penduduk terdata dengan hak pelayanan digital mandiri.
                        </p>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-6xl md:text-7xl font-bold text-slate-900 tracking-tighter mb-4 font-heading">
                            {{ animatedStats.keluarga }}
                        </span>
                        <div class="h-px w-12 bg-amber-600 mb-4"></div>
                        <h4 class="text-sm font-bold text-slate-900 tracking-widest uppercase mb-2 font-sans">
                            Kepala Keluarga
                        </h4>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-[200px] font-sans">
                            Jumlah kartu keluarga terdaftar di administrasi.
                        </p>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-6xl md:text-7xl font-bold text-slate-900 tracking-tighter mb-4 font-heading">
                            {{ animatedStats.pengajuan }}
                        </span>
                        <div class="h-px w-12 bg-amber-600 mb-4"></div>
                        <h4 class="text-sm font-bold text-slate-900 tracking-widest uppercase mb-2 font-sans">
                            Surat Terbit
                        </h4>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-[200px] font-sans">
                            Total pengajuan surat mandiri yang selesai diproses.
                        </p>
                    </div>

                </div>
                
            </div>
        </div>
    </section>
</template>
