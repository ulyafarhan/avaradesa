<script setup>
import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import CitizenLayout from '../../../Layouts/CitizenLayout.vue';
import AppButton from '../../../Components/AppButton.vue';
import AppCard from '../../../Components/AppCard.vue';
import StatusBadge from '../../../Components/StatusBadge.vue';

defineOptions({ layout: CitizenLayout });
const props = defineProps({ pengajuan: Object });

let pollingTimeout = null;

const startPolling = () => {
    if (!['Selesai', 'Ditolak'].includes(props.pengajuan.status)) {
        const delay = Math.floor(Math.random() * (45000 - 30000 + 1)) + 30000;
        pollingTimeout = setTimeout(() => {
            router.reload({
                only: ['pengajuan'],
                onFinish: () => {
                    startPolling();
                }
            });
        }, delay);
    }
};

onMounted(() => {
    startPolling();
});

onUnmounted(() => {
    if (pollingTimeout) clearTimeout(pollingTimeout);
});

const getFileUrl = (path) => {
    if (!path) return '#';
    
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    if (path.startsWith('/warga/') || path.startsWith('/admin/') || path.startsWith('warga/') || path.startsWith('admin/')) {
        return path.startsWith('/') ? path : '/' + path;
    }
    
    const storageIdx = path.indexOf('/storage/');
    if (storageIdx !== -1) {
        return path.substring(storageIdx);
    }
    
    return path.startsWith('/') ? '/storage' + path : '/storage/' + path;
};

const getFileName = (path) => {
    if (!path) return '';
    return path.split('/').pop();
};

const getFileExtension = (path) => {
    if (!path) return '';
    const parts = path.split('.');
    return parts.length > 1 ? parts.pop().toUpperCase() : '';
};

const formatLabel = (str) => {
    if (!str) return '';
    return str.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <div class="google-editorial max-w-4xl mx-auto py-8 px-4">
        <div class="mb-8">
            <AppButton href="/warga/dashboard" variant="ghost" class="back-link inline-flex items-center gap-2">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Dashboard
            </AppButton>
        </div>

        <div class="editorial-card main-header-card mb-6">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="min-w-0">
                    <span class="category-label text-primary">{{ pengajuan.nomor_registrasi }}</span>
                    <h1 class="headline-lg mt-2">{{ pengajuan.kategori?.nama_surat }}</h1>
                </div>
                <div class="self-start">
                    <StatusBadge :status="pengajuan.status" />
                </div>
            </div>

            <div v-if="pengajuan.catatan_penolakan" class="alert-box error-alert mt-6">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <h4 class="label-md text-error">Catatan Penolakan</h4>
                    <p class="body-md mt-1 text-secondary">{{ pengajuan.catatan_penolakan }}</p>
                </div>
            </div>

            <div v-if="(pengajuan.status === 'Selesai' || pengajuan.status === 'Disetujui') && pengajuan.file_pdf_url" class="alert-box success-alert mt-6">
                <div class="flex items-start gap-4 flex-1">
                    <div class="alert-icon-wrapper bg-primary-soft text-primary shrink-0">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <div>
                        <h3 class="label-lg text-primary-dark">Surat Anda Telah Selesai</h3>
                        <p class="body-md mt-1 text-secondary">Dokumen PDF resmi bertanda tangan digital sudah siap diunduh dan dicetak.</p>
                    </div>
                </div>
                <a :href="getFileUrl(pengajuan.file_pdf_url)" target="_blank" class="btn-primary w-full md:w-auto text-center shrink-0">
                    Unduh PDF
                </a>
            </div>

            <div v-else-if="pengajuan.status === 'Disetujui' && !pengajuan.file_pdf_url" class="alert-box warning-alert mt-6">
                <svg class="size-5 text-warning shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2v4"/></svg>
                <div>
                    <h4 class="label-md text-warning-dark">Surat Sedang Diproses</h4>
                    <p class="body-md mt-1 text-secondary">Pengajuan telah disetujui. Sistem sedang menyiapkan dokumen PDF resmi Anda. Halaman akan memuat ulang otomatis ketika selesai.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3 mb-6">
            <div class="editorial-card md:col-span-2 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="avatar-circle">
                        {{ pengajuan.pemohon?.nama_lengkap?.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <span class="overline-label">Informasi Pemohon</span>
                        <h3 class="headline-sm mt-0.5 text-neutral">{{ pengajuan.pemohon?.nama_lengkap }}</h3>
                        <p class="body-sm text-secondary font-mono mt-0.5">NIK: {{ pengajuan.nik_pemohon }}</p>
                    </div>
                </div>
            </div>

            <div class="editorial-card flex flex-col justify-center">
                <span class="overline-label">Tanggal Pengajuan</span>
                <p class="body-lg text-neutral font-medium mt-1">{{ formatDate(pengajuan.created_at) }}</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 mb-6">
            <div class="editorial-card">
                <h3 class="label-md text-neutral mb-4 uppercase tracking-wider">Data Isian Formulir</h3>
                <div class="space-y-3">
                    <div v-for="(value, key) in pengajuan.data_isian" :key="key" class="data-item">
                        <span class="overline-label">{{ formatLabel(key) }}</span>
                        <p class="body-md text-neutral mt-0.5 font-medium">{{ value || '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="editorial-card">
                <h3 class="label-md text-neutral mb-4 uppercase tracking-wider">Dokumen Persyaratan</h3>
                <div class="space-y-3">
                    <div v-for="(value, key) in pengajuan.file_syarat" :key="key" class="file-item">
                        <div class="min-w-0 flex-1">
                            <span class="overline-label">{{ formatLabel(key) }}</span>
                            <div class="mt-1">
                                <a :href="getFileUrl(value)" class="file-download-link" target="_blank">
                                    {{ 'Lihat ' + formatLabel(key) + (getFileExtension(value) ? ' (' + getFileExtension(value) + ')' : '') }}
                                    <svg class="size-3.5 inline shrink-0 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>
                            </div>
                        </div>
                        <span class="success-chip">Terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="editorial-card">
            <h3 class="label-md text-neutral mb-6 uppercase tracking-wider">Riwayat Status Surat</h3>
            <div class="timeline-container pl-6">
                <div v-for="track in pengajuan.tracking" :key="track.id" class="timeline-step">
                    <div class="timeline-dot"></div>
                    
                    <div class="flex items-center gap-3">
                        <StatusBadge :status="track.status_baru" />
                        <span class="body-sm text-secondary">{{ formatDate(track.created_at) }}</span>
                    </div>
                    <p class="body-md text-neutral mt-2 font-medium">{{ track.keterangan_update }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>


.google-editorial {
    font-family: inherit;
    color: #202124;
    background-color: #F8F9FA;
    min-height: 100vh;
}


.headline-lg {
    font-size: 32px;
    font-weight: 400;
    line-height: 40px;
    letter-spacing: 0px;
    color: #202124;
}

.headline-sm {
    font-size: 20px;
    font-weight: 400;
    line-height: 28px;
    letter-spacing: 0px;
    color: #202124;
}

.body-lg {
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    letter-spacing: 0.25px;
}

.body-md {
    font-size: 14px;
    font-weight: 400;
    line-height: 22px;
    letter-spacing: 0.15px;
}

.body-sm {
    font-size: 12px;
    font-weight: 400;
    line-height: 18px;
    letter-spacing: 0.2px;
}

.label-lg {
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
    letter-spacing: 0px;
}

.label-md {
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    letter-spacing: 0px;
}

.category-label {
    font-size: 12px;
    font-weight: 500;
    line-height: 16px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.overline-label {
    font-size: 11px;
    font-weight: 500;
    line-height: 16px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #5F6368;
}


.text-primary { color: #1A73E8; }
.text-secondary { color: #5F6368; }
.text-neutral { color: #202124; }
.text-error { color: #D93025; }


.back-link {
    font-size: 14px;
    font-weight: 500;
    color: #1A73E8;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: opacity 0.2s;
}
.back-link:hover {
    opacity: 0.8;
    background: transparent !important;
}

.editorial-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,0.1), 0 1px 3px 1px rgba(60,64,67,0.05);
}

.main-header-card {
}

.alert-box {
    display: flex;
    gap: 16px;
    padding: 20px;
    border-radius: 12px;
    align-items: center;
}

.success-alert {
    background-color: #E8F0FE;
    border: 1px solid #D2E3FC;
    flex-wrap: wrap;
    justify-content: space-between;
}

.success-alert .text-primary-dark {
    color: #1A73E8;
}

.error-alert {
    background-color: #FCE8E6;
    border: 1px solid #FAD2CF;
    color: #D93025;
}

.warning-alert {
    background-color: #FEF7E0;
    border: 1px solid #FEEFC3;
    color: #B06000;
}

.warning-alert .text-warning-dark {
    color: #B06000;
}

.alert-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 9999px;
}

.bg-primary-soft {
    background-color: #D2E3FC;
}

.btn-primary {
    background-color: #1A73E8;
    color: #FFFFFF;
    font-size: 14px;
    font-weight: 500;
    border-radius: 9999px;
    padding: 11px 24px;
    transition: background-color 0.2s;
    border: none;
    cursor: pointer;
    display: inline-block;
}

.btn-primary:hover {
    background-color: #1557B0;
}

.avatar-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 9999px;
    background-color: #E8F0FE;
    color: #1A73E8;
    font-weight: 600;
    font-size: 16px;
}

.data-item {
    padding: 12px;
    background-color: #F8F9FA;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 16px;
    background-color: #F8F9FA;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
}

.file-download-link {
    color: #1A73E8;
    font-weight: 500;
    font-size: 13px;
    text-decoration: underline;
    transition: opacity 0.2s;
}

.file-download-link:hover {
    opacity: 0.8;
}

.success-chip {
    background-color: #E6F4EA;
    color: #137333;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.timeline-container {
    position: relative;
    border-left: 2px solid #E5E7EB;
    margin-left: 10px;
}

.timeline-step {
    position: relative;
    padding-bottom: 24px;
}

.timeline-step:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -32px;
    top: 6px;
    width: 12px;
    height: 12px;
    border-radius: 9999px;
    background-color: #1A73E8;
    border: 2px solid #FFFFFF;
    box-shadow: 0 0 0 2px #E8F0FE;
}
</style>

