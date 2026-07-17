<script setup>
import AppButton from '../../../Components/AppButton.vue';

const props = defineProps({
    warga: {
        type: Object,
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

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
};

const getGenderLabel = (g) => g === 'L' ? 'Laki-laki' : g === 'P' ? 'Perempuan' : '-';

const formatDate = (d) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const requiredFields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'pendidikan', 'pekerjaan', 'status_perkawinan'];
const fieldLabels = {
    nama_lengkap: 'Nama Lengkap', tempat_lahir: 'Tempat Lahir', tanggal_lahir: 'Tanggal Lahir',
    jenis_kelamin: 'Jenis Kelamin', agama: 'Agama', pendidikan: 'Pendidikan',
    pekerjaan: 'Pekerjaan', status_perkawinan: 'Status Perkawinan',
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="headline-lg">Biodata Saya</h2>
                <p class="body-md text-secondary mt-1">Periksa data profil and kelengkapan administrasi Anda.</p>
            </div>
            <AppButton href="/warga/profil" class="btn-primary">
                Edit Biodata
            </AppButton>
        </div>

        <div class="editorial-card flex items-center gap-5">
            <div class="avatar-circle-lg overflow-hidden shrink-0">
                <img v-if="warga.foto_profil" :src="warga.foto_profil" alt="Foto Profil" class="size-full object-cover" />
                <span v-else>{{ getInitials(warga.nama_lengkap) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="headline-sm font-medium">{{ warga.nama_lengkap }}</h3>
                <p class="body-sm text-secondary font-mono mt-0.5">NIK: {{ warga.nik }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span v-if="warga.foto_ktp" class="badge success-badge">KTP OK</span>
                    <span v-else class="badge error-badge">KTP Kosong</span>

                    <span v-if="warga.foto_kk" class="badge success-badge">KK OK</span>
                    <span v-else class="badge error-badge">KK Kosong</span>
                </div>
            </div>
        </div>

        <div class="editorial-card">
            <div class="flex items-center justify-between text-sm font-medium mb-2">
                <span class="text-secondary">Kelengkapan Biodata</span>
                <span :class="biodataComplete ? 'text-primary' : 'text-warning-dark'">{{ biodataCompleteness }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="biodataComplete ? 'bg-primary' : 'bg-warning'"
                    :style="{ width: biodataCompleteness + '%' }"
                />
            </div>
        </div>

        <div class="grid gap-4 grid-cols-2">
            <div v-for="field in requiredFields" :key="field" class="editorial-card biodata-field-card">
                <span class="overline-label">{{ fieldLabels[field] }}</span>
                <p class="body-lg mt-1 font-medium leading-tight" :class="warga[field] ? 'text-neutral' : 'text-error'">
                    <template v-if="field === 'tanggal_lahir'">{{ warga[field] ? formatDate(warga[field]) : 'Belum diisi' }}</template>
                    <template v-else-if="field === 'jenis_kelamin'">{{ getGenderLabel(warga[field]) || 'Belum diisi' }}</template>
                    <template v-else>{{ warga[field] || 'Belum diisi' }}</template>
                </p>
            </div>
        </div>

        <div class="grid gap-4 grid-cols-2">
            <div class="editorial-card biodata-field-card">
                <span class="overline-label">Nomor Induk Kependudukan (NIK)</span>
                <p class="body-lg mt-1 font-mono text-neutral font-medium">{{ warga.nik }}</p>
            </div>
            <div class="editorial-card biodata-field-card">
                <span class="overline-label">Status Hubungan</span>
                <p class="body-lg mt-1 text-neutral font-medium">{{ warga.status_keluarga || '-' }}</p>
            </div>
        </div>
    </div>
</template>
