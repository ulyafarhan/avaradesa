<script setup>
import { ref, reactive } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import CitizenLayout from '../../Layouts/CitizenLayout.vue';
import AppButton from '../../Components/AppButton.vue';
import FormInput from '../../Components/FormInput.vue';
import FormSelect from '../../Components/FormSelect.vue';

defineOptions({ layout: CitizenLayout });

const props = defineProps({
    keluarga: Object,
    anggota: Array,
    isKepalaKeluarga: Boolean,
});

const selectedAnggota = ref(null);
const isEditing = ref(false);

const editForm = useForm({
    pendidikan: '',
    pekerjaan: '',
    status_perkawinan: '',
});

const pendidikanOptions = [
    'Tidak/Belum Sekolah', 'Belum Tamat SD/Sederajat', 'Tamat SD/Sederajat',
    'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma I/II', 'Akademi/Diploma III/Sarjana Muda',
    'Diploma IV/Strata I', 'Strata II', 'Strata III',
];

const statusPerkawinanOptions = [
    'Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati',
];

const openDetail = (anggota) => {
    selectedAnggota.value = anggota;
    isEditing.value = false;
};

const closeDetail = () => {
    selectedAnggota.value = null;
    isEditing.value = false;
};

const startEdit = (anggota) => {
    editForm.pendidikan = anggota.pendidikan || '';
    editForm.pekerjaan = anggota.pekerjaan || '';
    editForm.status_perkawinan = anggota.status_perkawinan || '';
    isEditing.value = true;
};

const saveEdit = () => {
    if (!selectedAnggota.value) return;
    editForm.put(`/warga/keluarga/${selectedAnggota.value.nik}`, {
        onSuccess: () => {
            isEditing.value = false;
            selectedAnggota.value = null;
        },
    });
};

const formatDate = (d) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const getGenderLabel = (g) => g === 'L' ? 'Laki-laki' : g === 'P' ? 'Perempuan' : '-';

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
};

const maskNik = (nik) => {
    if (!nik || nik.length < 8) return nik;
    return nik.slice(0, 4) + '****' + nik.slice(-4);
};

const getStatusColor = (s) => {
    const map = { 'Kepala Keluarga': 'bg-teal-100 text-teal-800', 'Istri': 'bg-purple-100 text-purple-800', 'Anak': 'bg-sky-100 text-sky-800' };
    return map[s] || 'bg-slate-100 text-slate-700';
};

const getGenderColor = (g) => g === 'L' ? 'bg-sky-100 text-sky-700' : 'bg-pink-100 text-pink-700';
</script>

<template>
    <div class="google-editorial max-w-5xl mx-auto py-8 px-4">
        <div class="mb-8">
            <AppButton href="/warga/dashboard" variant="ghost" class="back-link inline-flex items-center gap-2">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Dashboard
            </AppButton>
            
            <h1 class="headline-lg mt-4">Keluarga Saya</h1>
            <p class="body-md text-secondary mt-1">Kelola data kartu keluarga dan pantau informasi anggota keluarga Anda.</p>
        </div>

        <div class="editorial-card kk-card p-0 overflow-hidden mb-8">
            <div class="bg-neutral p-6 text-white">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="icon-wrapper bg-white/10 text-white shrink-0">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="8" x2="22" y2="8"/></svg>
                        </div>
                        <div>
                            <span class="overline-label text-slate-350">Nomor Kartu Keluarga</span>
                            <p class="headline-sm tracking-wider mt-0.5 text-white">{{ keluarga?.no_kk || '-' }}</p>
                        </div>
                    </div>
                    <span v-if="isKepalaKeluarga" class="badge bg-primary-soft text-primary font-semibold py-1.5 px-4 rounded-full flex items-center gap-1">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Kepala Keluarga
                    </span>
                </div>
            </div>
            <div class="grid gap-6 p-6 sm:grid-cols-3 bg-white border-t border-slate-200">
                <div>
                    <span class="overline-label">Alamat KK</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ keluarga?.alamat || '-' }}</p>
                </div>
                <div>
                    <span class="overline-label">Dusun</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ keluarga?.dusun || '-' }}</p>
                </div>
                <div>
                    <span class="overline-label">RT/RW</span>
                    <p class="body-md text-neutral font-medium mt-1">{{ keluarga?.rt_rw || '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex items-center justify-between">
            <h2 class="headline-sm">Anggota Keluarga ({{ anggota.length }})</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <button
                v-for="member in anggota" :key="member.nik"
                @click="openDetail(member)"
                class="editorial-card member-button text-left"
            >
                <div class="flex items-start gap-4">
                    <div class="avatar-circle-sm shrink-0 font-bold" :class="getGenderColor(member.jenis_kelamin)">
                        {{ getInitials(member.nama_lengkap) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="label-md text-neutral font-semibold truncate">{{ member.nama_lengkap }}</p>
                        <p class="body-sm text-secondary font-mono mt-0.5">NIK: {{ maskNik(member.nik) }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="badge px-2.5 py-0.5" :class="getStatusColor(member.status_keluarga)">
                                {{ member.status_keluarga || 'Anggota' }}
                            </span>
                            <span class="badge tertiary-badge">
                                {{ member.umur }} tahun
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-slate-100 pt-3 text-[11px] text-secondary">
                    <div><span class="font-medium">Kelamin:</span> <span class="font-medium text-neutral ml-1">{{ getGenderLabel(member.jenis_kelamin) }}</span></div>
                    <div><span class="font-medium">Status:</span> <span class="font-medium text-neutral ml-1">{{ member.status_perkawinan || '-' }}</span></div>
                    <div class="col-span-2 truncate"><span class="font-medium">Pendidikan:</span> <span class="font-medium text-neutral ml-1">{{ member.pendidikan || '-' }}</span></div>
                </div>
            </button>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="selectedAnggota" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm" @click.self="closeDetail">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div v-if="selectedAnggota" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="avatar-circle-sm font-bold shrink-0" :class="getGenderColor(selectedAnggota.jenis_kelamin)">
                                    {{ getInitials(selectedAnggota.nama_lengkap) }}
                                </div>
                                <div>
                                    <h4 class="label-lg text-neutral font-medium">{{ selectedAnggota.nama_lengkap }}</h4>
                                    <span class="badge mt-1 inline-block" :class="getStatusColor(selectedAnggota.status_keluarga)">
                                        {{ selectedAnggota.status_keluarga || 'Anggota' }}
                                    </span>
                                </div>
                            </div>
                            <button @click="closeDetail" class="rounded-lg p-1.5 text-secondary hover:bg-slate-50 transition-colors">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>

                        <div v-if="!isEditing" class="max-h-[60vh] overflow-y-auto p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="data-item">
                                    <span class="overline-label">NIK</span>
                                    <p class="body-md text-neutral font-mono font-medium mt-0.5">{{ selectedAnggota.nik }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Jenis Kelamin</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ getGenderLabel(selectedAnggota.jenis_kelamin) }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Tempat Lahir</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.tempat_lahir || '-' }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Tanggal Lahir</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ formatDate(selectedAnggota.tanggal_lahir) }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Umur</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.umur }} tahun</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Agama</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.agama || '-' }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Pendidikan</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.pendidikan || '-' }}</p>
                                </div>
                                <div class="data-item">
                                    <span class="overline-label">Pekerjaan</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.pekerjaan || '-' }}</p>
                                </div>
                                <div class="data-item col-span-2">
                                    <span class="overline-label">Status Perkawinan</span>
                                    <p class="body-md text-neutral font-medium mt-0.5">{{ selectedAnggota.status_perkawinan || '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <form v-else class="max-h-[60vh] overflow-y-auto p-6 space-y-5" @submit.prevent="saveEdit">
                            <div class="input-wrapper">
                                <FormSelect
                                    id="edit-pendidikan"
                                    v-model="editForm.pendidikan"
                                    label="Pendidikan"
                                    :options="pendidikanOptions"
                                    :error="editForm.errors.pendidikan"
                                />
                            </div>
                            <div class="input-wrapper">
                                <FormInput
                                    id="edit-pekerjaan"
                                    v-model="editForm.pekerjaan"
                                    label="Pekerjaan"
                                    placeholder="Contoh: Pelajar, Mahasiswa, Wiraswasta"
                                    :error="editForm.errors.pekerjaan"
                                />
                            </div>
                            <div class="input-wrapper">
                                <FormSelect
                                    id="edit-status-perkawinan"
                                    v-model="editForm.status_perkawinan"
                                    label="Status Perkawinan"
                                    :options="statusPerkawinanOptions"
                                    :error="editForm.errors.status_perkawinan"
                                />
                            </div>
                        </form>

                        <div class="flex items-center justify-between border-t border-slate-100 px-6 py-4 bg-slate-50">
                            <div>
                                <button v-if="!isEditing && isKepalaKeluarga" @click="startEdit(selectedAnggota)" class="btn-secondary py-1.5 px-4 h-auto text-xs flex items-center gap-1.5">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit Data
                                </button>
                                <div v-else-if="isEditing" class="flex items-center gap-2">
                                    <button type="button" @click="saveEdit" class="btn-primary py-1.5 px-4 h-auto text-xs" :disabled="editForm.processing">
                                        Simpan
                                    </button>
                                    <button type="button" @click="isEditing = false" class="btn-secondary py-1.5 px-4 h-auto text-xs">
                                        Batal
                                    </button>
                                </div>
                            </div>
                            <button @click="closeDetail" class="btn-secondary py-1.5 px-4 h-auto text-xs bg-transparent border-none text-slate-500 hover:bg-slate-100">
                                Tutup
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
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

.kk-card {
}

.member-button {
    width: 100%;
    cursor: pointer;
    transition: all 0.2s;
}
.member-button:hover {
    border-color: #D1D5DB;
    box-shadow: 0 4px 12px 0 rgba(60,64,67,0.08);
}

.avatar-circle-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 9999px;
}

.icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 9999px;
}

.bg-primary-soft {
    background-color: #E8F0FE;
    color: #1A73E8;
}

.btn-primary {
    background-color: #1A73E8;
    color: #FFFFFF;
    font-size: 14px;
    font-weight: 500;
    border-radius: 9999px;
    padding: 11px 24px;
    height: 48px;
    transition: background-color 0.2s;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-primary:hover {
    background-color: #1557B0;
}
.btn-primary:disabled {
    background-color: #A1C2FA;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #FFFFFF;
    color: #1A73E8;
    font-size: 14px;
    font-weight: 500;
    border-radius: 9999px;
    padding: 11px 24px;
    height: 48px;
    border: 1px solid #D2E3FC;
    transition: background-color 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-secondary:hover {
    background-color: #F4F8FF;
}

.badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.tertiary-badge {
    background-color: #E8F0FE;
    color: #1A73E8;
}

.data-item {
    background-color: #F8F9FA;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 12px 16px;
}

.input-wrapper {
    margin-bottom: 4px;
}
</style>
