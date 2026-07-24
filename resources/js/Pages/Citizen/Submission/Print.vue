<script setup>
import { onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';

const props = defineProps({
    pengajuan: Object,
    qrCodeSvg: String,
    bodyContent: String,
    tanggalSurat: String,
    settings: Object,
});

const documentTitle = computed(() => {
    const namaSurat = props.pengajuan.kategori?.nama_surat || 'Surat_Keterangan';
    const namaPemohon = props.pengajuan.pemohon?.nama_lengkap || 'Warga';
    const nomor = props.pengajuan.nomor_registrasi || 'REG';
    return `${namaSurat}_${namaPemohon}_${nomor}`.replace(/\s+/g, '_');
});

onMounted(() => {
    setTimeout(() => {
        window.print();
    }, 800);
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const sanitizeSvg = (svg) => DOMPurify.sanitize(svg || '', { USE_PROFILES: { svg: true } });
</script>

<template>
    <Head :title="documentTitle" />

    <div class="print-container bg-slate-100 min-h-screen py-8 flex flex-col items-center">
        <div class="no-print print-header-info w-[21cm] mb-6 bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-800">Pratinjau Cetak Dokumen</h1>
                    <p class="text-xs text-slate-500">Kertas diatur otomatis menjadi A4 (1 Halaman).</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow transition">
                        Cetak / Simpan PDF
                    </button>
                    <a href="/warga/dashboard?tab=pengajuan" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="letter-sheet relative bg-white">
            
            <div class="kop-surat flex items-center border-b-[3px] border-black pb-4 mb-6">
                <div class="logo-container pr-4 pl-2">
                    <img :src="'/images/logo-pidiejaya.png'" class="w-[75px] h-auto" alt="Logo">
                </div>
                <div class="text-center flex-1 pr-16">
                    <h2 class="text-[14pt] uppercase font-bold tracking-wide leading-snug">
                        PEMERINTAH KABUPATEN {{ settings.kabupaten?.toUpperCase() || 'PIDIE JAYA' }}
                    </h2>
                    <h2 class="text-[14pt] uppercase font-bold tracking-wide leading-snug">
                        KECAMATAN {{ settings.kecamatan?.toUpperCase() || 'BANDAR BARU' }}
                    </h2>
                    <h2 class="text-[14pt] uppercase font-bold tracking-wide leading-snug">
                        DESA {{ settings.nama_desa?.toUpperCase() || 'UDEUNG' }}
                    </h2>
                </div>
            </div>

            <div class="text-center mb-6">
                <h3 class="text-[12pt] font-bold underline uppercase tracking-wide">
                    {{ pengajuan.kategori?.nama_surat || 'SURAT KETERANGAN DOMISILI' }}
                </h3>
                <p class="text-[12pt] mt-1">
                    Nomor : {{ pengajuan.nomor_surat || '........ / ........ / ........ / ' + new Date().getFullYear() }}
                </p>
            </div>

            <div class="mb-3 text-justify leading-relaxed">
                Yang bertanda tangan di bawah ini,
            </div>
            <div class="mb-4 text-justify leading-relaxed">
                Kepala Desa {{ settings.nama_desa || 'Udeung' }} Kecamatan {{ settings.kecamatan || 'Bandar Baru' }} Kabupaten {{ settings.kabupaten || 'Pidie Jaya' }}, menerangkan bahwa:
            </div>

            <table class="biodata-table mb-5 ml-10">
                <tbody>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td class="val">{{ pengajuan.pemohon?.nama_lengkap || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="separator">:</td>
                        <td class="val">{{ pengajuan.pemohon?.nik || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat/Tgl. Lahir</td>
                        <td class="separator">:</td>
                        <td class="val">
                            {{ pengajuan.pemohon?.tempat_lahir || '-' }}, 
                            {{ formatDate(pengajuan.pemohon?.tanggal_lahir) || '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Agama</td>
                        <td class="separator">:</td>
                        <td class="val">{{ pengajuan.pemohon?.agama || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td class="separator">:</td>
                        <td class="val">{{ pengajuan.pemohon?.pekerjaan || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="separator">:</td>
                        <td class="val leading-relaxed">
                            Desa {{ settings.nama_desa || 'Udeung' }}, Kecamatan {{ settings.kecamatan || 'Bandar Baru' }},<br>
                            Kabupaten {{ settings.kabupaten || 'Pidie Jaya' }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-5 text-justify indent-10 leading-relaxed">
                {{ bodyContent || 'Bahwa yang bersangkutan merupakan benar warga yang berdomisili di Desa ' + (settings.nama_desa || 'Udeung') + ', Kecamatan ' + (settings.kecamatan || 'Bandar Baru') + ', Kabupaten ' + (settings.kabupaten || 'Pidie Jaya') + ', yang termasuk wilayah terdampak bencana hidrometeorologi pada tahun 2025.' }}
            </div>

            <div class="mb-6 text-justify indent-10 leading-relaxed">
                Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </div>

            <div class="mt-8 flex justify-between items-end relative">
                
                <div class="qr-block text-center flex flex-col items-center pl-4 pb-2">
                    <div class="qr-code-svg shadow-none p-0 bg-transparent" v-html="sanitizeSvg(qrCodeSvg)"></div>
                </div>

                <div class="sig-block text-center pr-8 min-w-[280px] relative">
                    <p class="text-[12pt] mb-1">Desa {{ settings.nama_desa || 'Udeung' }}, {{ tanggalSurat || '13 Mei 2026' }}</p>
                    <p class="text-[12pt]">Kepala Desa {{ settings.nama_desa || 'Udeung' }}</p>
                    
                    <div class="relative h-24 mt-1">
                        <img 
                             :src="'/images/signature.png'" 
                            class="ttd-img absolute left-[-5px] top-[10px] w-[1024px] z-40 pointer-events-none" 
                            style="transform: rotate(-10deg);"
                            alt="Tanda Tangan"
                        >
                        <img 
                            :src="'/images/stempel.png'" 
                            class="stempel-img absolute left-[-60px] top-[-30px] w-[190px] z-50 pointer-events-none" 
                            alt="Stempel"
                        >
                    </div>
                    
                    <p class="text-[12pt] font-bold underline relative z-10">
                        {{ settings.nama_kepala_desa || 'Muhammad Yunus, S.Sos' }}
                    </p>
                    <p v-if="settings.nip_kepala_desa" class="text-[12pt] relative z-10 mt-1">
                        NIP. {{ settings.nip_kepala_desa }}
                    </p>
                </div>
            </div>

            <div class="letter-footer absolute bottom-[1.5cm] left-[2cm] right-[2cm] border-t border-black pt-1.5 text-center text-[9px] text-slate-500 font-mono">
                Dokumen ini ditandatangani secara elektronik. Kode Registrasi: {{ pengajuan.qr_hash || 'TTE-VERIFIED' }} &middot; Nomor: {{ pengajuan.nomor_registrasi || '-' }}
            </div>

        </div>
    </div>
</template>

<style>
.print-container {
    font-family: 'Times New Roman', Times, serif;
    color: #000;
}

.letter-sheet {
    width: 21cm;
    height: 29.7cm;
    padding: 2cm 2cm 2cm 2cm;
    box-sizing: border-box;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    font-size: 12pt;
    line-height: 1.5;
    overflow: hidden;
}

.biodata-table {
    border-collapse: collapse;
}
.biodata-table td {
    padding: 3px 0;
    vertical-align: top;
    font-size: 12pt;
}
.biodata-table td.label {
    width: 140px; 
}
.biodata-table td.separator {
    width: 25px;
    text-align: center;
}

.qr-code-svg svg {
    width: 85px; 
    height: 85px;
}

.ttd-img,
.stempel-img {
    mix-blend-mode: multiply;
    opacity: 0.9;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 0 !important; 
    }

    body, html {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .no-print {
        display: none !important;
    }

    .print-container {
        padding: 0 !important;
        background: #fff !important;
    }

    .letter-sheet {
        width: 21cm !important;
        height: 29.7cm !important;
        margin: 0 !important;
        padding: 2cm 2cm 2cm 2cm !important;
        box-shadow: none !important;
        border: none !important;
        page-break-after: avoid !important;
        page-break-before: avoid !important;
        overflow: hidden !important;
    }
}
</style>