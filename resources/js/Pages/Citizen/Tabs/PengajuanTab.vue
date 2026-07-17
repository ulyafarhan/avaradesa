<script setup>
import { Link } from '@inertiajs/vue3';
import AppButton from '../../../Components/AppButton.vue';
import EmptyState from '../../../Components/EmptyState.vue';
import StatusBadge from '../../../Components/StatusBadge.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    kategoriSurat: {
        type: Array,
        required: true,
    },
    pengajuan: {
        type: Object,
        required: true,
    },
    biodataComplete: {
        type: Boolean,
        required: true,
    },
    isKepalaKeluarga: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['set-active-tab']);

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
        <div>
            <h2 class="headline-lg">Layanan & Pengajuan Surat</h2>
            <p class="body-md text-secondary mt-1">Pilih jenis surat keterangan atau administrasi desa yang ingin Anda ajukan.</p>
        </div>

        <div v-if="!biodataComplete" class="alert-box error-alert">
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div class="flex-1">
                <h4 class="label-md text-error">Biodata & Berkas Belum Lengkap</h4>
                <p class="body-sm text-secondary mt-0.5">Harap lengkapi biodata Anda pada tab Biodata sebelum dapat mengakses formulir pengajuan surat.</p>
            </div>
            <button @click="emit('set-active-tab', 'biodata')" class="btn-primary py-1.5 px-4 h-auto text-xs shrink-0">Lengkapi Biodata</button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div v-for="kategori in kategoriSurat" :key="kategori.id" class="editorial-card service-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="category-label text-primary">{{ kategori.kode_surat }}</span>
                        <span class="badge tertiary-badge font-semibold">{{ (kategori.syarat_dokumen || []).length }} Syarat</span>
                    </div>
                    <h3 class="headline-sm mt-3 leading-snug">{{ kategori.nama_surat }}</h3>
                </div>
                <AppButton
                    :href="biodataComplete ? `/warga/surat/ajukan/${kategori.id}` : undefined"
                    class="btn-primary mt-6 w-full justify-center"
                    :class="{ 'opacity-50 cursor-not-allowed pointer-events-none': !biodataComplete }"
                >
                    Ajukan Surat
                </AppButton>
            </div>
        </div>

        <div class="pt-4 space-y-4">
            <div>
                <h2 class="headline-sm">Riwayat Pengajuan Surat</h2>
                <p class="body-sm text-secondary mt-0.5">
                    {{ isKepalaKeluarga ? 'Daftar pengajuan surat dari seluruh anggota keluarga Anda.' : 'Daftar pengajuan surat administrasi Anda.' }}
                </p>
            </div>

            <div v-if="pengajuan.data?.length" class="space-y-3">
                <div
                    v-for="item in pengajuan.data" :key="item.id"
                    class="editorial-card list-item-card"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <Link :href="`/warga/pengajuan/${item.id}`" class="headline-sm font-medium hover:text-primary transition-colors block leading-snug">
                                {{ item.kategori?.nama_surat }}
                            </Link>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-secondary">
                                <span class="font-mono">{{ item.nomor_registrasi }}</span>
                                <span>·</span>
                                <span v-if="isKepalaKeluarga && item.pemohon" class="success-chip">{{ item.pemohon.nama_lengkap }}</span>
                            </div>
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
            <EmptyState v-else title="Belum ada pengajuan" message="Mulai dari katalog pengajuan untuk membuat surat pertama Anda." />

            <Pagination v-if="pengajuan.meta" :links="pengajuan.links" :meta="pengajuan.meta" class="mt-6" />
        </div>
    </div>
</template>
