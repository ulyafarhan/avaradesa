<script setup>
import { computed } from 'vue';
import AppButton from '../../../Components/AppButton.vue';

const props = defineProps({
    warga: {
        type: Object,
        required: true,
    },
    isKepalaKeluarga: {
        type: Boolean,
        required: true,
    },
    anggotaKeluarga: {
        type: Array,
        required: true,
    },
    biodataComplete: {
        type: Boolean,
        required: true,
    },
    kategoriSurat: {
        type: Array,
        required: true,
    },
});

const totalAnggota = computed(() => (props.anggotaKeluarga || []).length);

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
};

const maskNik = (nik) => {
    if (!nik) return '—';
    if (nik.length === 16) return nik.slice(0, 6) + '******' + nik.slice(-4);
    if (nik.length >= 8) return nik.slice(0, 4) + '****' + nik.slice(-4);
    return nik;
};

const getStatusColor = (s) => {
    const map = { 
        'Kepala Keluarga': 'bg-teal-100 text-teal-800', 
        'Istri': 'bg-purple-100 text-purple-800', 
        'Anak': 'bg-sky-100 text-sky-800' 
    };
    return map[s] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="headline-lg">Keluarga Saya</h2>
            <p class="body-md text-secondary mt-1">Informasi KK dan daftar anggota keluarga yang terdaftar dalam satu KK.</p>
        </div>

        <div class="editorial-card kk-card p-0 overflow-hidden">
            <div class="bg-neutral p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="icon-wrapper bg-white/10 text-white shrink-0">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="8" x2="22" y2="8"/></svg>
                    </div>
                    <div>
                        <span class="overline-label text-slate-350">Nomor Kartu Keluarga</span>
                        <p class="headline-sm tracking-wider mt-0.5 text-white">{{ warga.keluarga?.no_kk || '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="grid gap-6 p-6 grid-cols-3 text-xs bg-white border-t border-slate-200">
                <div>
                    <span class="overline-label">Alamat KK</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ warga.keluarga?.alamat || '-' }}</p>
                </div>
                <div>
                    <span class="overline-label">Dusun</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ warga.keluarga?.dusun || '-' }}</p>
                </div>
                <div>
                    <span class="overline-label">RT/RW</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ warga.keluarga?.rt_rw || '-' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="headline-sm">Anggota Keluarga ({{ totalAnggota }})</h3>
                <span v-if="isKepalaKeluarga" class="success-chip">Kepala Keluarga</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div v-for="anggota in anggotaKeluarga" :key="anggota.nik" class="editorial-card member-card flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="avatar-circle-sm shrink-0 font-bold"
                                    :class="anggota.jenis_kelamin === 'L' ? 'bg-sky-50 text-sky-700' : 'bg-pink-50 text-pink-700'"
                                >
                                    {{ getInitials(anggota.nama_lengkap) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="label-md text-neutral font-medium truncate">{{ anggota.nama_lengkap }}</p>
                                    <p class="body-sm text-secondary font-mono mt-0.5">NIK: {{ maskNik(anggota.nik) }}</p>
                                </div>
                            </div>
                            <span class="badge px-2.5 py-0.5 shrink-0" :class="getStatusColor(anggota.status_keluarga)">
                                {{ anggota.status_keluarga || 'Anggota' }}
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4 text-xs border-t border-slate-100 pt-3">
                            <div><span class="text-secondary font-medium">Umur:</span> <span class="font-medium text-neutral ml-1">{{ anggota.umur }} tahun</span></div>
                            <div><span class="text-secondary font-medium">Gender:</span> <span class="font-medium text-neutral ml-1">{{ anggota.jenis_kelamin }}</span></div>
                        </div>
                    </div>
                    
                    <div v-if="isKepalaKeluarga && biodataComplete" class="mt-4 pt-3 border-t border-slate-100">
                        <AppButton :href="`/warga/surat/ajukan/${kategoriSurat[0]?.id || 1}`" variant="outline" class="btn-secondary w-full text-xs py-2 h-auto justify-center">
                            Ajukan Surat
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center pt-2">
            <AppButton href="/warga/keluarga" variant="outline" class="btn-secondary w-full sm:w-auto">
                Kelola Anggota Keluarga
            </AppButton>
        </div>
    </div>
</template>
