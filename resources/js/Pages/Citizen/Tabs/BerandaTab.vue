<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import StatusBadge from '../../../Components/StatusBadge.vue';

const props = defineProps({
    warga: {
        type: Object,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
    recentPengajuan: {
        type: Array,
        required: true,
    },
    biodataComplete: {
        type: Boolean,
        required: true,
    },
    biodataCompleteness: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['set-active-tab']);

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
};

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
</script>

<template>
    <div class="space-y-6">
        <div class="editorial-card welcome-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="overline-label">Layanan Mandiri Warga</span>
                    <h1 class="headline-lg mt-1">Selamat datang kembali,</h1>
                    <p class="headline-md text-primary mt-1 font-medium">{{ warga.nama_lengkap }}</p>
                    <div class="mt-4 flex items-center gap-2 text-sm text-secondary">
                        <svg class="size-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ warga.keluarga?.alamat || 'Alamat belum tersedia' }}, {{ warga.keluarga?.dusun }} {{ warga.keluarga?.rt_rw }}</span>
                    </div>
                </div>
                <div class="avatar-circle-lg overflow-hidden shrink-0">
                    <img v-if="warga.foto_profil" :src="warga.foto_profil" alt="Profil" class="size-full object-cover" />
                    <span v-else>{{ getInitials(warga.nama_lengkap) }}</span>
                </div>
            </div>
        </div>

        <div v-if="!biodataComplete" class="alert-box warning-alert">
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div class="flex-1">
                <h4 class="label-md text-warning-dark">Biodata Belum Lengkap ({{ biodataCompleteness }}%)</h4>
                <p class="body-sm text-secondary mt-0.5">Lengkapi biodata and berkas Anda terlebih dahulu agar dapat mengajukan surat administrasi desa.</p>
            </div>
            <button @click="emit('set-active-tab', 'biodata')" class="btn-primary py-1.5 px-4 h-auto text-xs shrink-0">Lengkapi</button>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="editorial-card stat-card">
                <div class="icon-wrapper bg-warning-soft text-warning mb-2">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="overline-label">Menunggu</span>
                <p class="headline-md text-neutral mt-1 font-semibold">{{ summary.pending }}</p>
            </div>
            <div class="editorial-card stat-card">
                <div class="icon-wrapper bg-primary-soft text-primary mb-2">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2v4"/><path d="M12 18v4"/></svg>
                </div>
                <span class="overline-label">Diproses</span>
                <p class="headline-md text-neutral mt-1 font-semibold">{{ summary.diproses }}</p>
            </div>
            <div class="editorial-card stat-card">
                <div class="icon-wrapper bg-success-soft text-success mb-2">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span class="overline-label">Selesai</span>
                <p class="headline-md text-neutral mt-1 font-semibold">{{ summary.selesai }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <button @click="emit('set-active-tab', 'pengajuan')" class="editorial-card action-card">
                <div class="action-icon bg-primary-soft text-primary mb-3">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <span class="label-sm text-neutral font-medium">Ajukan Surat</span>
            </button>
            <button @click="emit('set-active-tab', 'keluarga')" class="editorial-card action-card">
                <div class="action-icon bg-accent-soft text-primary mb-3">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <span class="label-sm text-neutral font-medium">Keluarga Saya</span>
            </button>
            <button @click="emit('set-active-tab', 'biodata')" class="editorial-card action-card">
                <div class="action-icon bg-warning-soft text-warning mb-3">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="label-sm text-neutral font-medium">Biodata Saya</span>
            </button>
        </div>

        <div class="space-y-4">
            <h2 class="headline-sm">Pengajuan Terakhir</h2>
            <div v-if="recentPengajuan.length" class="space-y-3">
                <div
                    v-for="item in recentPengajuan" :key="item.id"
                    class="editorial-card list-item-card"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <Link :href="`/warga/pengajuan/${item.id}`" class="headline-sm font-medium hover:text-primary transition-colors block leading-snug">
                                {{ item.kategori?.nama_surat }}
                            </Link>
                            <p class="body-sm text-secondary font-mono mt-1">
                                {{ item.nomor_registrasi }} · {{ item.pemohon?.nama_lengkap }}
                            </p>
                        </div>
                        <StatusBadge :status="item.status" class="shrink-0" />
                    </div>
                    
                    <div v-if="(item.status === 'Selesai' || item.status === 'Disetujui') && item.file_pdf_url" class="attachment-footer mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="success-label flex items-center gap-1.5">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Surat siap diunduh
                        </span>
                        <a :href="getFileUrl(item.file_pdf_url)" target="_blank" class="btn-primary py-1.5 px-4 h-auto text-xs">
                            Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
            <div v-if="!recentPengajuan.length" class="editorial-card empty-card text-center py-10">
                <svg class="mx-auto size-12 text-secondary mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <p class="body-md text-secondary font-medium">Belum ada pengajuan surat.</p>
                <button @click="emit('set-active-tab', 'pengajuan')" class="btn-secondary mt-4">Ajukan sekarang</button>
            </div>
        </div>
    </div>
</template>
