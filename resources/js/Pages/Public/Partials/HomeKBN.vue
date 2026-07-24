<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
const page = usePage();
</script>

<template>
    <section class="bg-slate-900 py-24 text-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16 max-w-3xl mx-auto">
                <span class="text-[11px] font-bold tracking-[0.25em] text-sky-400 uppercase">Kampung Bebas Narkoba (KBN)</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight leading-[1.2]">
                    Komitmen Lingkungan <span class="text-sky-400">Desa {{ page.props.settings.nama_desa }}.</span>
                </h2>
                <p class="text-slate-400 text-base leading-relaxed max-w-2xl mx-auto">
                    Sebagai wilayah percontohan anti-narkoba di Kabupaten {{ page.props.settings.kabupaten }}, kami bersinergi aktif bersama warga mempertahankan kebersihan dan keamanan lingkungan.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <Link href="/profil" class="group bg-slate-800/80 border border-slate-700/80 p-7 rounded-2xl hover:border-slate-600 hover:bg-slate-800 transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center text-[10px] font-bold text-sky-400 bg-sky-950/60 border border-sky-800/60 px-3 py-1 rounded-lg uppercase tracking-wider">
                            Pencegahan
                        </span>
                        <h3 class="text-xl font-bold flex items-center gap-2 group-hover:text-sky-400 transition-colors">
                            Sosialisasi Pemuda <ArrowUpRight class="size-4 group-hover:opacity-100" />
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Penyuluhan berkala mengenai bahaya zat terlarang yang menyasar kelompok pemuda desa.</p>
                    </div>
                </Link>
                <Link href="/profil" class="group bg-slate-800/80 border border-slate-700/80 p-7 rounded-2xl hover:border-slate-600 hover:bg-slate-800 transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center text-[10px] font-bold text-sky-400 bg-sky-950/60 border border-sky-800/60 px-3 py-1 rounded-lg uppercase tracking-wider">
                            Partisipasi
                        </span>
                        <h3 class="text-xl font-bold flex items-center gap-2 group-hover:text-sky-400 transition-colors">
                            Pengawasan Warga <ArrowUpRight class="size-4 group-hover:opacity-100" />
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Masyarakat aktif berperan sebagai lini pengaman utama dalam menjaga kondusivitas wilayah.</p>
                    </div>
                </Link>
                <Link href="/profil" class="group bg-slate-800/80 border border-slate-700/80 p-7 rounded-2xl hover:border-slate-600 hover:bg-slate-800 transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center text-[10px] font-bold text-sky-400 bg-sky-950/60 border border-sky-800/60 px-3 py-1 rounded-lg uppercase tracking-wider">
                            Sinergitas
                        </span>
                        <h3 class="text-xl font-bold flex items-center gap-2 group-hover:text-sky-400 transition-colors">
                            Mitra BNK & Polres <ArrowUpRight class="size-4 group-hover:opacity-100" />
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Kolaborasi terarah bersama jajaran Kepolisian Resor dan Badan Narkotika Kabupaten.</p>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>

