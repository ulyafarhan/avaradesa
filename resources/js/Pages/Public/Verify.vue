<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import AppCard from '../../Components/AppCard.vue';
import AppButton from '../../Components/AppButton.vue';
import { 
    ShieldCheck, 
    ShieldAlert, 
    Search, 
    Calendar, 
    User, 
    FileText, 
    ArrowLeft,
    RefreshCw,
    Fingerprint
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });
const props = defineProps({ result: Object });

const hashInput = ref('');
const errorMsg = ref('');

const handleVerify = () => {
    errorMsg.value = '';
    const cleanHash = hashInput.value.trim();
    if (!cleanHash) {
        errorMsg.value = 'Kode registrasi / hash wajib diisi.';
        return;
    }
    router.visit(`/verifikasi/${cleanHash}`);
};
</script>

<template>
    <Head>
        <title>Verifikasi Dokumen - {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}</title>
        <meta name="description" :content="'Halaman verifikasi keaslian surat fisik Desa ' + $page.props.settings.nama_desa + ' menggunakan QR Code Tanda Tangan Elektronik (TTE) berbasis hash SHA-256.'" />
        <meta name="keywords" :content="'Verifikasi Surat, Cek Keaslian Surat, TTE QR Code, Tanda Tangan Elektronik Desa, ' + $page.props.settings.nama_desa" />
        <meta property="og:title" :content="'Verifikasi Dokumen - ' + $page.props.settings.nama_desa" />
        <meta property="og:description" :content="'Halaman verifikasi keaslian surat fisik Desa ' + $page.props.settings.nama_desa + ' menggunakan QR Code Tanda Tangan Elektronik (TTE).'" />
    </Head>

    <header class="bg-white border-b border-gray-200 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F0FE] text-[#1A73E8] uppercase tracking-wider">
                    <ShieldCheck class="size-3.5" /> Keabsahan Tanda Tangan Elektronik
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-normal text-[#202124] tracking-tight leading-tight">
                    Verifikasi Dokumen Desa
                </h1>
                
                <p class="text-base sm:text-lg text-[#5F6368] font-normal leading-relaxed">
                    Verifikasi keaslian surat keterangan yang diterbitkan oleh Pemerintah Desa {{ $page.props.settings.nama_desa }} secara instan dan aman.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-3xl px-6 py-12 bg-[#F8F9FA] min-h-[500px]">
        
        <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl mb-8">
            <div class="space-y-4">
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-[#202124] flex items-center gap-2">
                        <Search class="size-4.5 text-[#1A73E8]" />
                        Cari Kode Registrasi / Hash Surat
                    </h3>
                    <p class="text-xs text-[#5F6368] leading-relaxed font-medium">
                        Masukkan kode hash atau kode registrasi yang tertera di bagian bawah surat atau pada tautan QR Code surat cetak Anda.
                    </p>
                </div>

                <form @submit.prevent="handleVerify" class="flex flex-col sm:flex-row gap-3">
                    <div class="grow relative rounded-lg">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#5F6368]">
                            <FileText class="size-4.5" />
                        </div>
                        <input 
                            type="text" 
                            v-model="hashInput"
                            placeholder="Contoh: 8a9b7c6d5e..." 
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm text-[#202124] bg-white placeholder:text-gray-400 focus:border-[#1A73E8] !focus-visible:ring-2 !focus-visible:ring-[#1A73E8]/20 !focus-visible:ring-offset-0 transition"
                            required
                        />
                    </div>
                    <AppButton type="submit" class="rounded-lg bg-[#1A73E8] hover:bg-[#155fa8] text-white py-3 px-6 shrink-0 font-medium text-sm">
                        Verifikasi Dokumen
                    </AppButton>
                </form>
                <p v-if="errorMsg" class="text-xs text-[#D93025] font-semibold">{{ errorMsg }}</p>
            </div>
        </div>

        <div v-if="result" class="animate-fade-in">
            
            <div v-if="result.valid" class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-emerald-50 border-b border-gray-200 p-6 flex items-center gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
                        <ShieldCheck class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-[#202124]">Dokumen Sah & Terverifikasi</h4>
                        <p class="text-xs text-slate-700 mt-0.5 font-medium">Surat ini resmi diterbitkan oleh Pemerintah Desa {{ $page.props.settings.nama_desa }}.</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6 bg-white">
                    <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2 text-sm border-b border-gray-150 pb-6">
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">Jenis Surat</dt>
                            <dd class="font-bold text-[#202124]">{{ result.jenis_surat }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">Nomor Registrasi</dt>
                            <dd class="font-mono text-xs font-bold text-[#202124] bg-slate-50 px-2.5 py-1 rounded border border-gray-200 w-fit">{{ result.nomor_registrasi }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">Nama Pemohon</dt>
                            <dd class="font-bold text-[#202124] flex items-center gap-1.5">
                                <User class="size-4 text-[#5F6368]" /> {{ result.nama_pemohon }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">NIK Pemohon</dt>
                            <dd class="font-mono text-xs text-[#202124] flex items-center gap-1.5 font-bold">
                                <Fingerprint class="size-4 text-[#5F6368]" /> {{ result.nik_pemohon ? (result.nik_pemohon.length === 16 ? result.nik_pemohon.slice(0, 6) + '******' + result.nik_pemohon.slice(-4) : result.nik_pemohon.slice(0, 4) + '****' + result.nik_pemohon.slice(-4)) : '—' }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">Tanggal Terbit</dt>
                            <dd class="text-[#202124] flex items-center gap-1.5 font-bold">
                                <Calendar class="size-4 text-[#5F6368]" /> {{ result.tanggal_terbit }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-[#5F6368] font-bold uppercase tracking-wider">Diverifikasi Oleh</dt>
                            <dd class="font-bold text-[#202124]">
                                Perangkat Desa / {{ result.diverifikasi_oleh }}
                            </dd>
                        </div>
                    </dl>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        <a href="/verifikasi" class="inline-flex items-center gap-1.5 text-xs text-[#1A73E8] hover:text-[#155fa8] transition font-bold">
                            <RefreshCw class="size-3.5" /> Verifikasi Dokumen Lain
                        </a>
                        <a href="/" class="inline-flex items-center gap-1 text-xs text-slate-650 hover:text-slate-850 transition font-bold">
                            <ArrowLeft class="size-3.5" /> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-red-50 border-b border-gray-200 p-6 flex items-center gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-[#D93025]">
                        <ShieldAlert class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-[#202124]">Dokumen Tidak Valid</h4>
                        <p class="text-xs text-slate-700 mt-0.5 font-medium">Sistem tidak dapat menemukan keabsahan dokumen ini.</p>
                    </div>
                </div>

                <div class="p-8 text-center space-y-5 bg-white">
                    <p class="text-sm text-slate-700 leading-relaxed max-w-md mx-auto font-medium">
                        Kode registrasi yang Anda masukkan tidak terdaftar di sistem kependudukan kami, atau berkas pengajuan tersebut belum selesai diproses oleh perangkat desa.
                    </p>
                    <div class="flex items-center justify-center gap-4 pt-2">
                        <a href="/verifikasi" class="inline-flex items-center gap-1.5 text-xs text-[#1A73E8] hover:text-[#155fa8] transition font-bold">
                            <RefreshCw class="size-3.5" /> Ulangi Pencarian
                        </a>
                        <a href="/" class="inline-flex items-center gap-1 text-xs text-slate-650 hover:text-[#202124] transition font-bold">
                            <ArrowLeft class="size-3.5" /> Kembali
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>
