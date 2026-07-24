<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, ArrowUpRight } from '@lucide/vue';

const page = usePage();
const sebutanWilayah = page.props.settings.sebutan_desa || 'Pemerintah Desa';

defineProps({
    demografi: { type: Object, default: () => ({}) }
});
</script>

<template>
    <section class="relative bg-white pt-12 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            
            <!-- Text Content (Premium Editorial) -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center space-y-8 z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-sm bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-widest w-max font-sans">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    Portal Layanan Publik
                </div>
                
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight text-slate-900 leading-[1.05] font-heading">
                    Layanan<br/>
                    <span class="italic text-slate-500 font-medium">Mandiri</span> <br/>
                    <span class="text-amber-700">{{ page.props.settings.nama_desa }}.</span>
                </h1>
                
                <p class="text-lg text-slate-600 font-sans leading-relaxed max-w-lg border-l-2 border-amber-600 pl-4">
                    Akses kemudahan administrasi kependudukan secara daring. Proses yang lebih transparan, efisien, dan terintegrasi penuh dalam satu pintu.
                </p>
                
                <div class="flex flex-wrap items-center gap-4 pt-4">
                    <Link href="/login" class="inline-flex h-12 md:h-14 items-center justify-center rounded-sm bg-slate-900 hover:bg-slate-800 text-white font-bold transition-all px-8 text-sm uppercase tracking-wider shadow-md hover:shadow-lg focus:ring-4 focus:ring-slate-200 outline-none font-sans">
                        Ajukan Surat <ArrowRight class="size-4 ml-2" />
                    </Link>
                    <Link href="/profil" class="inline-flex h-12 md:h-14 items-center justify-center rounded-sm bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 hover:text-amber-700 font-bold transition-all px-8 text-sm uppercase tracking-wider focus:ring-4 focus:ring-slate-100 outline-none font-sans">
                        Profil Desa <ArrowUpRight class="size-4 ml-2 text-slate-400" />
                    </Link>
                </div>
            </div>
            
            <!-- Image/Banner Section (Institutional Classic) -->
            <div class="w-full lg:w-1/2 relative h-[450px] md:h-[550px] rounded-sm overflow-hidden bg-slate-100 group">
                <template v-if="page.props.settings.banner_desa">
                    <img 
                        :src="page.props.settings.banner_desa"
                        alt="Pemandangan Wilayah" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-[4000ms] ease-out group-hover:scale-105"
                    />
                </template>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                
                <!-- Info Float (Classic) -->
                <div class="absolute bottom-6 left-6 right-6 md:bottom-8 md:left-8 bg-white/95 backdrop-blur-sm border-t-2 border-amber-600 p-6 shadow-xl flex justify-between items-center text-slate-900 font-sans">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Populasi</p>
                        <p class="text-3xl font-bold font-heading">{{ (demografi?.total_penduduk ?? 0).toLocaleString('id-ID') }} <span class="text-sm font-sans font-medium text-slate-500">Warga</span></p>
                    </div>
                    <div class="h-12 w-px bg-slate-200"></div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Kabupaten</p>
                        <p class="text-xl font-bold font-heading">{{ page.props.settings.kabupaten }}</p>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</template>
