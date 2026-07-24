<script setup>
import { MessageCircle } from '@lucide/vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { settings } = usePage().props;

const fotoKantor = computed(() => settings.foto_kantor || 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=1000&auto=format&fit=crop');
const teleponWA = computed(() => settings.telepon?.replace(/[^0-9]/g, '') || '6281234567890');

const steps = [
    { num: '01', title: 'Autentikasi NIK', desc: 'Masuk ke portal layanan dengan NIK warga dan kata sandi Anda yang terdaftar.' },
    { num: '02', title: 'Input Data Isian', desc: 'Pilih layanan, isi formulir digital, serta unggah berkas kelengkapan pendukung.' },
    { num: '03', title: 'Verifikasi Berkas', desc: 'Operator memproses data isian dan memvalidasi keabsahan dokumen warga.' },
    { num: '04', title: 'Unduh PDF Resmi', desc: 'Surat terbit dalam format digital, dilengkapi QR Code TTE yang sah secara hukum.' }
];
</script>

<template>
    <section class="bg-slate-900 py-24 md:py-32 text-slate-300 border-t-4 border-amber-600">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">
                
                <div class="w-full lg:w-1/2 space-y-10 order-2 lg:order-1">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 text-xs font-bold tracking-[0.25em] text-amber-500 uppercase font-sans">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Proses Layanan
                        </span>
                        <h2 class="text-4xl md:text-5xl font-bold text-white tracking-tight leading-[1.1] font-heading">
                            4 Langkah Mudah.
                        </h2>
                        <p class="text-lg leading-relaxed text-slate-400 max-w-md font-sans">
                            Memprioritaskan efisiensi penuh, hindari antrean panjang. Dokumen Anda selesai dalam hitungan jam.
                        </p>
                    </div>

                    <div class="relative font-sans">
                        <!-- Connecting Line (Vertical) -->
                        <div class="absolute left-6 md:left-8 top-8 bottom-8 w-px bg-slate-800"></div>
                        
                        <div class="space-y-8 relative">
                            <div v-for="(step, index) in steps" :key="index" class="flex gap-6 group">
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-slate-950 border border-slate-800 text-slate-500 flex items-center justify-center font-bold text-lg md:text-xl font-heading transition-colors group-hover:bg-amber-500 group-hover:text-slate-900 group-hover:border-amber-500 z-10 relative">
                                        {{ step.num }}
                                    </div>
                                </div>
                                <div class="pt-2 md:pt-4">
                                    <h4 class="text-lg font-bold text-white mb-2 font-heading">{{ step.title }}</h4>
                                    <p class="text-sm text-slate-400 leading-relaxed font-sans">{{ step.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 relative order-1 lg:order-2">
                    <div class="relative overflow-hidden rounded-3xl shadow-2xl shadow-slate-950 aspect-[4/5] sm:aspect-square lg:aspect-[4/5] bg-slate-800">
                        <img
                            :src="fotoKantor"
                            alt="Kantor Desa"
                            class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80"
                        />
                        <!-- Overlay for contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        
                        <!-- Contact Card -->
                        <div class="absolute bottom-6 left-6 right-6 md:bottom-8 md:left-8 md:right-8 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest font-sans">Bantuan Cepat WhatsApp</p>
                                <p class="text-base font-bold text-white font-heading">{{ settings.nama_operator || 'Operator Bantuan' }}</p>
                                <p class="text-sm text-slate-300 font-sans">{{ settings.telepon || '0812-3456-7890' }}</p>
                            </div>
                            <a :href="'https://wa.me/' + teleponWA" target="_blank" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold py-3 px-6 transition-colors shadow-lg font-sans">
                                <MessageCircle class="size-4 mr-2" /> Hubungi
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
</template>
