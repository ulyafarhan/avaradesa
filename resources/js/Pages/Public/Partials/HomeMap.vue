<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const mapActive = ref(false);
</script>

<template>
    <section class="bg-slate-50/50 border-b border-slate-200/80 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 items-center">
                <div class="lg:col-span-5 space-y-6">
                    <div class="space-y-3">
                        <span class="block text-[11px] font-bold tracking-[0.25em] text-sky-600 uppercase">
                            Orbitasi Wilayah
                        </span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-[1.2]">
                            Letak & <span class="text-sky-600">Akses Desa.</span>
                        </h2>
                    </div>
                    
                    <p class="text-slate-600 text-base leading-relaxed">
                        Desa {{ page.props.settings.nama_desa }} terletak di Kecamatan {{ page.props.settings.kecamatan }}, berjarak sekitar 3.5 km dari simpang pusat kecamatan. Berbatasan langsung dengan wilayah sekitarnya yang strategis.
                    </p>
                    
                    <div class="space-y-3 pt-2 text-sm text-slate-800 font-medium">
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                            <div class="size-2 rounded-full bg-sky-600 animate-pulse"></div> 
                            <p class="text-xs">Koordinat Wilayah: 5.277732, 96.102347</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                            <div class="size-2 rounded-full bg-emerald-600"></div> 
                            <p class="text-xs">Sektor Unggulan: Padi Sawah, Jagung, Kakao</p>
                        </div>
                    </div>
                </div>

                <div @mouseleave="mapActive = false" class="lg:col-span-7 rounded-2xl border border-slate-200/80 overflow-hidden relative h-[420px] shadow-sm bg-white p-2 group/map">
                    <iframe 
                        src="https://www.openstreetmap.org/export/embed.html?bbox=96.0900%2C5.2650%2C96.1150%2C5.2900&amp;layer=mapnik&amp;marker=5.2777%2C96.1023" 
                        class="w-full h-full border border-slate-200/80 rounded-xl transition-all duration-500 filter grayscale-[20%]"
                        :class="{ 'pointer-events-none': !mapActive }"
                        allowfullscreen="" 
                        loading="lazy"
                    ></iframe>
                    <div v-if="!mapActive" @click="mapActive = true" class="absolute inset-0 bg-slate-900/10 cursor-pointer flex items-center justify-center transition-all duration-300 hover:bg-slate-900/20 backdrop-blur-[1px]">
                        <span class="bg-white text-slate-900 text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-xl shadow-md border border-slate-200/80">
                            Klik Untuk Interaksi Peta
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

