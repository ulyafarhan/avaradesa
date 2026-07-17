<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CitizenLayout from '../../Layouts/CitizenLayout.vue';
import AppButton from '../../Components/AppButton.vue';
import FormInput from '../../Components/FormInput.vue';
import FormSelect from '../../Components/FormSelect.vue';
import { compressImageToWebP } from '../../Utils/imageCompressor';

defineOptions({ layout: CitizenLayout });

const props = defineProps({
    warga: Object,
    completeness: Number,
});

const isEditing = ref(false);

const form = useForm({
    pendidikan: props.warga.pendidikan || '',
    pekerjaan: props.warga.pekerjaan || '',
    status_perkawinan: props.warga.status_perkawinan || '',
    telegram_chat_id: props.warga.telegram_chat_id || '',
    foto_profil: null,
    foto_ktp: null,
    foto_kk: null,
});

const photoPreview = ref(null);
const ktpFileName = ref('');
const kkFileName = ref('');

const onPhotoChange = async (e) => {
    const file = e.target.files[0];
    if (file) {
        const processedFile = await compressImageToWebP(file);
        form.foto_profil = processedFile;
        photoPreview.value = URL.createObjectURL(processedFile);
    }
};

const onKtpChange = async (e) => {
    const file = e.target.files[0];
    if (file) {
        const processedFile = await compressImageToWebP(file);
        form.foto_ktp = processedFile;
        ktpFileName.value = processedFile.name;
    }
};

const onKkChange = async (e) => {
    const file = e.target.files[0];
    if (file) {
        const processedFile = await compressImageToWebP(file);
        form.foto_kk = processedFile;
        kkFileName.value = processedFile.name;
    }
};

const pendidikanOptions = [
    'Tidak/Belum Sekolah', 'Belum Tamat SD/Sederajat', 'Tamat SD/Sederajat',
    'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma I/II', 'Akademi/Diploma III/Sarjana Muda',
    'Diploma IV/Strata I', 'Strata II', 'Strata III',
];

const statusPerkawinanOptions = [
    'Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati',
];

const save = () => {
    form.post('/warga/profil', {
        onSuccess: () => {
            isEditing.value = false;
            photoPreview.value = null;
            ktpFileName.value = '';
            kkFileName.value = '';
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
</script>

<template>
    <div class="google-editorial max-w-4xl mx-auto py-8 px-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <AppButton href="/warga/dashboard" variant="ghost" class="back-link inline-flex items-center gap-2">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Dashboard
                </AppButton>
                
                <h1 class="headline-lg mt-4">Biodata Saya</h1>
                <p class="body-md text-secondary mt-1">Kelola data kependudukan resmi dan berkas identitas digital Anda.</p>
            </div>
            
            <div v-if="!isEditing" class="self-start md:self-center">
                <button @click="isEditing = true" class="btn-primary flex items-center gap-2">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Biodata
                </button>
            </div>
        </div>

        <div class="editorial-card mb-6">
            <div class="flex items-center justify-between text-sm font-medium mb-2">
                <span class="text-secondary">Kelengkapan Biodata</span>
                <span :class="completeness === 100 ? 'text-primary' : 'text-warning-dark'" class="font-semibold">{{ completeness }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="completeness === 100 ? 'bg-primary' : 'bg-warning'"
                    :style="{ width: completeness + '%' }"
                />
            </div>
            <p v-if="completeness < 100" class="mt-2.5 text-xs text-warning-dark font-medium">Lengkapi seluruh data pribadi & berkas identitas Anda agar dapat mengajukan surat.</p>
        </div>

        <div class="editorial-card mb-6">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h2 class="headline-sm">Data Kependudukan</h2>
                <p class="body-sm text-secondary mt-1">Data kependudukan resmi Anda yang terdaftar di sistem desa. Hubungi operator desa jika terdapat kekeliruan.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="data-item">
                    <span class="overline-label">Nomor Induk Kependudukan (NIK)</span>
                    <p class="body-md text-neutral font-mono font-medium mt-0.5">{{ warga.nik }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">No. Kartu Keluarga</span>
                    <p class="body-md text-neutral font-mono font-medium mt-0.5">{{ warga.no_kk }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Nama Lengkap</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.nama_lengkap }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Jenis Kelamin</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ getGenderLabel(warga.jenis_kelamin) }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Tempat Lahir</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.tempat_lahir || '-' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Tanggal Lahir</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ formatDate(warga.tanggal_lahir) }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Agama</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.agama || '-' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Status Hubungan Keluarga</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.status_keluarga || '-' }}</p>
                </div>
            </div>
        </div>

        <div v-if="!isEditing" class="editorial-card mb-6">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h2 class="headline-sm">Dokumen Identitas</h2>
                <p class="body-sm text-secondary mt-1">Dokumen yang terlampir di akun Anda untuk kebutuhan otomatisasi formulir pengajuan surat keterangan.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="file-display-card">
                    <div class="flex items-center gap-3">
                        <div class="icon-wrapper bg-primary-soft text-primary shrink-0">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M14 13c0-2-3-3-3-3s-3 1-3 3"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="label-sm text-neutral font-semibold">Kartu Tanda Penduduk (KTP)</p>
                            <p class="body-sm text-secondary mt-0.5">Lampiran KTP Warga</p>
                        </div>
                    </div>
                    <div>
                        <a v-if="warga.foto_ktp" :href="warga.foto_ktp" target="_blank" class="view-link inline-flex items-center gap-1">
                            Lihat Berkas
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/></svg>
                        </a>
                        <span v-else class="badge error-badge">Belum Ada</span>
                    </div>
                </div>

                <div class="file-display-card">
                    <div class="flex items-center gap-3">
                        <div class="icon-wrapper bg-primary-soft text-primary shrink-0">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="label-sm text-neutral font-semibold">Kartu Keluarga (KK)</p>
                            <p class="body-sm text-secondary mt-0.5">Lampiran KK Warga</p>
                        </div>
                    </div>
                    <div>
                        <a v-if="warga.foto_kk" :href="warga.foto_kk" target="_blank" class="view-link inline-flex items-center gap-1">
                            Lihat Berkas
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/></svg>
                        </a>
                        <span v-else class="badge error-badge">Belum Ada</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="editorial-card mb-6">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h2 class="headline-sm">Data Pribadi Mandiri</h2>
                <p class="body-sm text-secondary mt-1">Data pribadi ini dapat Anda perbarui secara berkala sesuai kondisi saat ini.</p>
            </div>

            <div v-if="!isEditing" class="grid gap-4 md:grid-cols-2">
                <div class="data-item">
                    <span class="overline-label">Pendidikan</span>
                    <p class="body-md mt-0.5 font-medium" :class="warga.pendidikan ? 'text-neutral' : 'text-error'">{{ warga.pendidikan || 'Belum diisi' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Pekerjaan</span>
                    <p class="body-md mt-0.5 font-medium" :class="warga.pekerjaan ? 'text-neutral' : 'text-error'">{{ warga.pekerjaan || 'Belum diisi' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Status Perkawinan</span>
                    <p class="body-md mt-0.5 font-medium" :class="warga.status_perkawinan ? 'text-neutral' : 'text-error'">{{ warga.status_perkawinan || 'Belum diisi' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Telegram Chat ID</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.telegram_chat_id || 'Belum terhubung' }}</p>
                </div>
            </div>

            <form v-else @submit.prevent="save" class="space-y-6">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="input-wrapper">
                        <FormSelect
                            id="pendidikan"
                            v-model="form.pendidikan"
                            label="Pendidikan"
                            :options="pendidikanOptions"
                            :error="form.errors.pendidikan"
                        />
                    </div>
                    <div class="input-wrapper">
                        <FormInput
                            id="pekerjaan"
                            v-model="form.pekerjaan"
                            label="Pekerjaan"
                            placeholder="Contoh: Petani, Wiraswasta, Pegawai Swasta"
                            :error="form.errors.pekerjaan"
                        />
                    </div>
                    <div class="input-wrapper">
                        <FormSelect
                            id="status_perkawinan"
                            v-model="form.status_perkawinan"
                            label="Status Perkawinan"
                            :options="statusPerkawinanOptions"
                            :error="form.errors.status_perkawinan"
                        />
                    </div>
                    <div class="input-wrapper">
                        <FormInput
                            id="telegram_chat_id"
                            v-model="form.telegram_chat_id"
                            label="Telegram Chat ID"
                            placeholder="Ketik Chat ID untuk menerima notifikasi pengajuan"
                            :error="form.errors.telegram_chat_id"
                        />
                    </div>
                </div>

                <div class="border-t border-slate-150 pt-6 mt-6">
                    <h3 class="label-lg text-neutral font-medium mb-4">Unggah Dokumen Pendukung & Foto Profil</h3>
                    
                    <div class="grid gap-6 sm:grid-cols-3">
                        <div class="upload-dropzone">
                            <span class="overline-label mb-2 block text-center">Foto Profil</span>
                            <div class="relative size-20 rounded-full overflow-hidden bg-slate-200 border-2 border-white ring-4 ring-slate-100 flex items-center justify-center mb-3">
                                <img v-if="photoPreview || warga.foto_profil" :src="photoPreview || warga.foto_profil" class="size-full object-cover" />
                                <span v-else class="text-secondary text-lg font-bold">{{ getInitials(warga.nama_lengkap) }}</span>
                            </div>
                            <label class="btn-secondary h-auto py-1.5 px-3.5 text-xs cursor-pointer inline-block">
                                Pilih Foto
                                <input type="file" @change="onPhotoChange" class="hidden" accept="image/*" />
                            </label>
                            <span v-if="form.errors.foto_profil" class="input-error-msg mt-2 text-center">{{ form.errors.foto_profil }}</span>
                        </div>

                        <div class="upload-dropzone justify-between text-center">
                            <div class="w-full">
                                <span class="overline-label mb-1 block">Foto KTP</span>
                                <p class="body-sm text-secondary mb-3">JPG, PNG, PDF, WebP (Maks. 2MB)</p>
                                
                                <div v-if="warga.foto_ktp && !ktpFileName" class="attachment-info-pilled">
                                    <svg class="size-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span class="truncate text-[10px] font-semibold flex-1 text-left">Berkas Tersimpan</span>
                                </div>
                                <div v-if="ktpFileName" class="attachment-info-pilled new-file">
                                    <svg class="size-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                    <span class="truncate text-[10px] font-semibold flex-1 text-left">{{ ktpFileName }}</span>
                                </div>
                            </div>
                            
                            <label class="btn-secondary h-auto py-1.5 px-3.5 text-xs cursor-pointer inline-block mt-3 w-full">
                                {{ warga.foto_ktp ? 'Ganti File KTP' : 'Pilih File KTP' }}
                                <input type="file" @change="onKtpChange" class="hidden" accept="image/*,application/pdf" />
                            </label>
                            <span v-if="form.errors.foto_ktp" class="input-error-msg mt-2 text-center">{{ form.errors.foto_ktp }}</span>
                        </div>

                        <div class="upload-dropzone justify-between text-center">
                            <div class="w-full">
                                <span class="overline-label mb-1 block">Foto Kartu Keluarga</span>
                                <p class="body-sm text-secondary mb-3">JPG, PNG, PDF, WebP (Maks. 2MB)</p>
                                
                                <div v-if="warga.foto_kk && !kkFileName" class="attachment-info-pilled">
                                    <svg class="size-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span class="truncate text-[10px] font-semibold flex-1 text-left">Berkas Tersimpan</span>
                                </div>
                                <div v-if="kkFileName" class="attachment-info-pilled new-file">
                                    <svg class="size-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                    <span class="truncate text-[10px] font-semibold flex-1 text-left">{{ kkFileName }}</span>
                                </div>
                            </div>
                            
                            <label class="btn-secondary h-auto py-1.5 px-3.5 text-xs cursor-pointer inline-block mt-3 w-full">
                                {{ warga.foto_kk ? 'Ganti File KK' : 'Pilih File KK' }}
                                <input type="file" @change="onKkChange" class="hidden" accept="image/*,application/pdf" />
                            </label>
                            <span v-if="form.errors.foto_kk" class="input-error-msg mt-2 text-center">{{ form.errors.foto_kk }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-100 pt-6 mt-6">
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="isEditing = false; form.reset(); photoPreview = null; ktpFileName = ''; kkFileName = '';" class="btn-secondary">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <div class="editorial-card">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h2 class="headline-sm">Informasi Alamat Tempat Tinggal</h2>
                <p class="body-sm text-secondary mt-1">Alamat domisili resmi sesuai dengan basis data keluarga terdaftar.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="data-item">
                    <span class="overline-label">Alamat Lengkap</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.keluarga?.alamat || '-' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">Dusun</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.keluarga?.dusun || '-' }}</p>
                </div>
                <div class="data-item">
                    <span class="overline-label">RT/RW</span>
                    <p class="body-md text-neutral font-medium mt-0.5">{{ warga.keluarga?.rt_rw || '-' }}</p>
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

.label-sm {
    font-size: 12px;
    font-weight: 500;
    line-height: 16px;
    letter-spacing: 0.04em;
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
.text-warning-dark { color: #B06000; }

.bg-primary { background-color: #1A73E8; }
.bg-warning { background-color: #F9AB00; }

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

.data-item {
    background-color: #F8F9FA;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 12px 16px;
}

.file-display-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 20px;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    background-color: #F8F9FA;
}

.icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 9999px;
}

.bg-primary-soft {
    background-color: #E8F0FE;
    color: #1A73E8;
}

.view-link {
    font-size: 13px;
    font-weight: 600;
    color: #1A73E8;
    text-decoration: underline;
}

.badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.error-badge {
    background-color: #FCE8E6;
    color: #C5221F;
}

.success-badge {
    background-color: #E6F4EA;
    color: #137333;
}

.upload-dropzone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px dashed #BDC1C6;
    border-radius: 12px;
    padding: 24px 16px;
    background-color: #F8F9FA;
    min-height: 180px;
}

.attachment-info-pilled {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #E6F4EA;
    border: 1px solid #CEEAD6;
    color: #137333;
    border-radius: 8px;
    max-width: 100%;
}
.attachment-info-pilled.new-file {
    background-color: #E8F0FE;
    border-color: #D2E3FC;
    color: #1A73E8;
}

.input-wrapper {
    margin-bottom: 4px;
}

.input-error-msg {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #D93025;
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
</style>
