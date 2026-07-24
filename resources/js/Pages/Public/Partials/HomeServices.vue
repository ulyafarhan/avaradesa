<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, ArrowUpRight, FileText } from '@lucide/vue';

defineProps({
    kategoriSurat: { type: Array, default: () => [] }
});
</script>

<template>
    <section class="bg-white py-24 md:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-16 lg:gap-24 items-start">
                
                <div class="lg:col-span-4 lg:sticky lg:top-32 space-y-8">
                    <span class="inline-flex items-center gap-2 text-xs font-bold tracking-[0.25em] text-slate-900 uppercase font-sans">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        Pelayanan Publik
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1] font-heading">
                        Layanan Surat Mandiri Digital.
                    </h2>
                    <p class="text-slate-600 text-lg leading-relaxed font-sans">
                        Urus dokumen administrasi langsung dari perangkat Anda. Lebih praktis, transparan, dan efisien tanpa perlu antre di balai desa.
                    </p>
                    <div class="pt-4">
                        <Link href="/login" class="group inline-flex items-center justify-center rounded-sm bg-slate-900 text-white hover:bg-slate-800 transition-all duration-300 h-14 px-8 text-sm uppercase tracking-wider font-bold shadow-sm hover:shadow-md focus:outline-none focus:ring-4 focus:ring-slate-200 font-sans">
                            Akses Portal <ArrowRight class="size-4 ml-2 group-hover:translate-x-1 transition-transform" />
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="flex flex-col gap-4">
                        <Link 
                            v-for="(kategori, index) in kategoriSurat" 
                            :key="kategori.id"
                            href="/login"
                            :class="[
                                index === 0 ? 'bg-slate-950 text-white shadow-xl shadow-slate-900/10' : 'bg-white border border-slate-200 hover:border-amber-300 hover:bg-amber-50/30 text-slate-900',
                                'group rounded-2xl p-6 sm:p-8 flex items-center justify-between transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-amber-200'
                            ]"
                        >
                            <div class="flex items-center gap-6">
                                <div :class="[
                                    index === 0 ? 'bg-white/10 text-white' : 'bg-slate-50 border border-slate-200 text-slate-700 group-hover:bg-amber-100 group-hover:text-amber-700',
                                    'hidden sm:flex size-14 rounded-xl items-center justify-center transition-all group-hover:scale-105 duration-300 shrink-0'
                                ]">
                                    <FileText class="size-6" />
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-3">
                                        <span :class="[
                                            index === 0 ? 'text-amber-400 border-amber-400/50' : 'text-slate-500 border-slate-300 group-hover:text-amber-600 group-hover:border-amber-400',
                                            'text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-widest border font-sans transition-colors'
                                        ]">
                                            {{ kategori.kode_surat }}
                                        </span>
                                    </div>
                                    <h3 :class="[
                                        index === 0 ? 'text-white' : 'text-slate-900',
                                        'text-xl sm:text-2xl font-bold tracking-tight font-heading group-hover:text-amber-600 transition-colors duration-300'
                                    ]">
                                        {{ kategori.nama_surat }}
                                    </h3>
                                    <p :class="[
                                        index === 0 ? 'text-slate-400' : 'text-slate-500',
                                        'text-sm max-w-md line-clamp-1 font-sans'
                                    ]">
                                        Pengajuan berkas online dengan validasi QR Code resmi.
                                    </p>
                                </div>
                            </div>
                            
                            <div :class="[
                                index === 0 ? 'bg-white/10 text-white group-hover:bg-amber-500 group-hover:text-slate-900' : 'bg-white border border-slate-200 text-slate-500 group-hover:bg-amber-500 group-hover:text-slate-900 group-hover:border-amber-500',
                                'size-12 rounded-full flex items-center justify-center shrink-0 transition-all duration-300'
                            ]">
                                <ArrowUpRight class="size-5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </div>
                        </Link>

                        <div v-if="!kategoriSurat?.length" class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center py-24 text-slate-500">
                            <FileText class="size-12 mb-4 text-slate-300" />
                            <span class="text-sm font-bold tracking-widest uppercase font-sans text-slate-400">Belum ada layanan tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
