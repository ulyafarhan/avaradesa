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

    <header class="bg-white pt-32 pb-24 border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-4xl space-y-8">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase font-sans border-l-4 border-amber-600 pl-4">
                    Keabsahan Tanda Tangan Elektronik
                </span>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 tracking-tight leading-[1.15] font-heading">
                    Verifikasi Dokumen <span class="text-amber-600 italic">Desa.</span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-600 font-sans leading-relaxed max-w-2xl border-l-2 border-slate-200 pl-4">
                    Verifikasi keaslian surat keterangan yang diterbitkan oleh Pemerintah Desa {{ $page.props.settings.nama_desa }} secara instan dan aman.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-4xl px-6 py-16 bg-slate-50 min-h-[500px]">
        
        <div class="bg-white border border-slate-200 p-8 sm:p-10 rounded-[2rem] mb-12 shadow-xl shadow-slate-900/5 relative overflow-hidden group">
            <div class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <h3 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-3">
                        <Search class="size-5 text-amber-600" />
                        Cari Kode Registrasi / Hash Surat
                    </h3>
                    <p class="text-sm text-slate-500 font-sans leading-relaxed">
                        Masukkan kode hash atau kode registrasi yang tertera di bagian bawah surat atau pada tautan QR Code surat cetak Anda.
                    </p>
                </div>

                <form @submit.prevent="handleVerify" class="flex flex-col sm:flex-row gap-4 font-sans">
                    <div class="grow relative rounded-xl overflow-hidden">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400">
                            <FileText class="size-5" />
                        </div>
                        <input 
                            type="text" 
                            v-model="hashInput"
                            placeholder="Contoh: 8a9b7c6d5e..." 
                            class="block w-full pl-14 pr-6 py-4 border border-slate-300 rounded-xl text-base text-slate-900 bg-slate-50 placeholder:text-slate-400 focus:border-amber-600 focus:bg-white !focus-visible:ring-2 !focus-visible:ring-amber-600/20 !focus-visible:ring-offset-0 transition-all duration-300"
                            required
                        />
                    </div>
                    <AppButton type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 text-white py-4 px-8 shrink-0 font-bold tracking-[0.2em] uppercase text-[10px] shadow-lg transition-colors">
                        Verifikasi
                    </AppButton>
                </form>
                <p v-if="errorMsg" class="text-[10px] text-red-600 font-bold font-sans uppercase tracking-widest">{{ errorMsg }}</p>
            </div>
        </div>

        <div v-if="result" class="animate-fade-in font-sans">
            
            <div v-if="result.valid" class="bg-white border border-slate-200 rounded-[2rem] overflow-hidden shadow-xl shadow-slate-900/5">
                <div class="bg-emerald-50 border-b border-emerald-100 p-6 flex items-center gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <ShieldCheck class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold font-heading text-emerald-900">Dokumen Sah & Terverifikasi</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">Surat ini resmi diterbitkan oleh Pemerintah Desa {{ $page.props.settings.nama_desa }}.</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6 bg-white">
                    <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2 text-sm border-b border-slate-100 pb-6">
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Jenis Surat</dt>
                            <dd class="font-bold font-heading text-slate-900">{{ result.jenis_surat }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Nomor Registrasi</dt>
                            <dd class="font-mono text-xs font-bold text-slate-900 bg-slate-50 px-2.5 py-1 rounded border border-slate-200 w-fit">{{ result.nomor_registrasi }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Nama Pemohon</dt>
                            <dd class="font-bold text-slate-900 flex items-center gap-1.5 font-heading">
                                <User class="size-4 text-slate-400" /> {{ result.nama_pemohon }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">NIK Pemohon</dt>
                            <dd class="font-mono text-xs text-slate-900 flex items-center gap-1.5 font-bold">
                                <Fingerprint class="size-4 text-slate-400" /> {{ result.nik_pemohon ? (result.nik_pemohon.length === 16 ? result.nik_pemohon.slice(0, 6) + '******' + result.nik_pemohon.slice(-4) : result.nik_pemohon.slice(0, 4) + '****' + result.nik_pemohon.slice(-4)) : '—' }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tanggal Terbit</dt>
                            <dd class="text-slate-900 flex items-center gap-1.5 font-bold">
                                <Calendar class="size-4 text-slate-400" /> {{ result.tanggal_terbit }}
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Diverifikasi Oleh</dt>
                            <dd class="font-bold text-slate-900">
                                Perangkat Desa / {{ result.diverifikasi_oleh }}
                            </dd>
                        </div>
                    </dl>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        <a href="/verifikasi" class="inline-flex items-center gap-1.5 text-[10px] text-amber-600 hover:text-slate-900 transition-colors font-bold uppercase tracking-[0.2em]">
                            <RefreshCw class="size-3.5" /> Verifikasi Dokumen Lain
                        </a>
                        <a href="/" class="inline-flex items-center gap-1 text-[10px] text-slate-500 hover:text-slate-900 transition-colors font-bold uppercase tracking-[0.2em]">
                            <ArrowLeft class="size-3.5" /> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white border border-slate-200 rounded-[2rem] overflow-hidden shadow-xl shadow-slate-900/5">
                <div class="bg-red-50 border-b border-red-100 p-6 flex items-center gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <ShieldAlert class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold font-heading text-red-900">Dokumen Tidak Valid</h4>
                        <p class="text-xs text-red-700 mt-0.5">Sistem tidak dapat menemukan keabsahan dokumen ini.</p>
                    </div>
                </div>

                <div class="p-8 text-center space-y-5 bg-white">
                    <p class="text-sm text-slate-600 leading-relaxed max-w-md mx-auto">
                        Kode registrasi yang Anda masukkan tidak terdaftar di sistem kependudukan kami, atau berkas pengajuan tersebut belum selesai diproses oleh perangkat desa.
                    </p>
                    <div class="flex items-center justify-center gap-4 pt-2">
                        <a href="/verifikasi" class="inline-flex items-center gap-1.5 text-[10px] text-amber-600 hover:text-slate-900 transition-colors font-bold uppercase tracking-[0.2em]">
                            <RefreshCw class="size-3.5" /> Ulangi Pencarian
                        </a>
                        <a href="/" class="inline-flex items-center gap-1 text-[10px] text-slate-500 hover:text-slate-900 transition-colors font-bold uppercase tracking-[0.2em]">
                            <ArrowLeft class="size-3.5" /> Kembali
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>
