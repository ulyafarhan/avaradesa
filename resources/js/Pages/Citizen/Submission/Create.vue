<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import CitizenLayout from '../../../Layouts/CitizenLayout.vue';
import AppButton from '../../../Components/AppButton.vue';
import AppCard from '../../../Components/AppCard.vue';
import FormInput from '../../../Components/FormInput.vue';
import FormSelect from '../../../Components/FormSelect.vue';
import StepIndicator from '../../../Components/StepIndicator.vue';
import { compressImageToWebP } from '../../../Utils/imageCompressor';

defineOptions({ layout: CitizenLayout });

const props = defineProps({
    kategori: Object,
    wargaData: Object,
    anggotaKeluarga: { type: Array, default: () => [] },
    isKepalaKeluarga: Boolean,
});

const showFamilySelector = computed(() => props.isKepalaKeluarga && props.anggotaKeluarga.length > 1);
const steps = computed(() => showFamilySelector.value
    ? ['Pemohon', 'Data Isian', 'Dokumen', 'Tinjauan']
    : ['Data Isian', 'Dokumen', 'Tinjauan']
);
const current = ref(0);
const fields = computed(() => props.kategori.schema_isian || []);
const documents = computed(() => props.kategori.syarat_dokumen || []);

const selectedNik = ref(props.wargaData.nik);
const selectedPemohon = computed(() => {
    if (showFamilySelector.value) {
        return props.anggotaKeluarga.find(a => a.nik === selectedNik.value) || props.wargaData;
    }
    return props.wargaData;
});

const initialData = {};
fields.value.forEach((field) => {
    initialData[field.field] = '';
});

const initialFiles = {};
documents.value.forEach((document) => {
    const key = document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    initialFiles[key] = '';
});

const form = useForm({
    kategori_surat_id: props.kategori.id,
    nik_pemohon: selectedNik.value,
    data_isian: reactive(initialData),
    file_syarat: reactive(initialFiles),
});

const populateDocsFromProfile = (pemohon) => {
    documents.value.forEach((document) => {
        const key = document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
        if ((key === 'ktp' || key === 'foto_ktp') && pemohon.foto_ktp) {
            form.file_syarat[key] = pemohon.foto_ktp;
        } else if ((key === 'kk' || key === 'kartu_keluarga' || key === 'foto_kk') && pemohon.foto_kk) {
            form.file_syarat[key] = pemohon.foto_kk;
        } else {
            if (!(form.file_syarat[key] instanceof File)) {
                form.file_syarat[key] = '';
            }
        }
    });
};

watch(selectedNik, (newNik) => {
    form.nik_pemohon = newNik;
    const pemohon = props.anggotaKeluarga.find(a => a.nik === newNik) || props.wargaData;
    
    populateDocsFromProfile(pemohon);

    const autoFillMap = {
        nama_lengkap: pemohon.nama_lengkap,
        nama: pemohon.nama_lengkap,
        nik: pemohon.nik,
        tempat_lahir: pemohon.tempat_lahir,
        tanggal_lahir: pemohon.tanggal_lahir,
        jenis_kelamin: pemohon.jenis_kelamin,
        agama: pemohon.agama,
        pekerjaan: pemohon.pekerjaan,
        pendidikan: pemohon.pendidikan,
        status_perkawinan: pemohon.status_perkawinan,
        alamat: pemohon.alamat,
        no_kk: pemohon.no_kk,
    };
    fields.value.forEach((field) => {
        const key = field.field.toLowerCase();
        if (autoFillMap[key] !== undefined && autoFillMap[key]) {
            form.data_isian[field.field] = autoFillMap[key];
        }
    });
});

const doAutoFill = () => {
    const pemohon = selectedPemohon.value;
    populateDocsFromProfile(pemohon);
    
    const autoFillMap = {
        nama_lengkap: pemohon.nama_lengkap,
        nama: pemohon.nama_lengkap,
        nik: pemohon.nik,
        tempat_lahir: pemohon.tempat_lahir,
        tanggal_lahir: pemohon.tanggal_lahir,
        jenis_kelamin: pemohon.jenis_kelamin,
        agama: pemohon.agama,
        pekerjaan: pemohon.pekerjaan,
        pendidikan: pemohon.pendidikan,
        status_perkawinan: pemohon.status_perkawinan,
        alamat: pemohon.alamat,
        no_kk: pemohon.no_kk,
    };
    fields.value.forEach((field) => {
        const key = field.field.toLowerCase();
        if (autoFillMap[key] !== undefined && autoFillMap[key]) {
            form.data_isian[field.field] = autoFillMap[key];
        }
    });
};

doAutoFill();

const stepOffset = computed(() => showFamilySelector.value ? 1 : 0);
const dataStep = computed(() => 0 + stepOffset.value);
const docStep = computed(() => 1 + stepOffset.value);
const reviewStep = computed(() => 2 + stepOffset.value);

const next = () => { current.value = Math.min(current.value + 1, steps.value.length - 1); };
const previous = () => { current.value = Math.max(current.value - 1, 0); };

const submit = () => form.post('/warga/surat/pengajuan');

const handleFileSelect = async (e, key) => {
    const file = e.target.files[0];
    if (file) {
        const processedFile = await compressImageToWebP(file);
        form.file_syarat[key] = processedFile;
    }
};

const isFile = (val) => val instanceof File;

const getGenderLabel = (g) => g === 'L' ? 'Laki-laki' : g === 'P' ? 'Perempuan' : '-';
const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
};
</script>

<template>
    <div class="google-editorial max-w-3xl mx-auto py-8 px-4">
        <div class="mb-8">
            <AppButton href="/warga/dashboard" variant="ghost" class="back-link inline-flex items-center gap-2">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Dashboard
            </AppButton>
            
            <div class="mt-4 flex items-start gap-4">
                <div class="header-icon-box text-primary shrink-0">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <span class="category-label text-primary">{{ kategori.kode_surat }}</span>
                    <h1 class="headline-lg mt-1">{{ kategori.nama_surat }}</h1>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <StepIndicator :steps="steps" :current="current" />
        </div>

        <div class="editorial-card">
            <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">

                <div v-if="showFamilySelector && current === 0" class="space-y-6">
                    <div>
                        <h2 class="headline-sm">Pilih Pemohon</h2>
                        <p class="body-md text-secondary mt-1">Silakan tentukan anggota keluarga yang mengajukan surat keterangan ini.</p>
                    </div>
                    
                    <div class="grid gap-3">
                        <button
                            v-for="member in anggotaKeluarga" :key="member.nik"
                            type="button"
                            @click="selectedNik = member.nik"
                            class="selector-button"
                            :class="{ active: selectedNik === member.nik }"
                        >
                            <div class="avatar-circle-sm shrink-0"
                                :class="member.jenis_kelamin === 'L' ? 'bg-sky-50 text-sky-700' : 'bg-pink-50 text-pink-700'"
                            >
                                {{ getInitials(member.nama_lengkap) }}
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <p class="label-md text-neutral">{{ member.nama_lengkap }}</p>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-secondary">
                                    <span>{{ member.status_keluarga }}</span>
                                    <span>·</span>
                                    <span>{{ getGenderLabel(member.jenis_kelamin) }}</span>
                                </div>
                            </div>
                            <div v-if="selectedNik === member.nik" class="check-indicator bg-primary text-white">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div v-else class="check-circle" />
                        </button>
                    </div>

                    <div class="alert-box info-alert">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <p class="body-sm text-secondary">Biodata profil <strong>{{ selectedPemohon.nama_lengkap }}</strong> akan terisi secara otomatis di langkah berikutnya untuk kemudahan pengisian.</p>
                    </div>
                </div>

                <div v-if="current === dataStep" class="space-y-6">
                    <div>
                        <h2 class="headline-sm">Data Isian Formulir</h2>
                        <p class="body-md text-secondary mt-1">
                            Lengkapi informasi formulir pengajuan atas nama: <strong class="text-primary font-medium">{{ selectedPemohon.nama_lengkap }}</strong>
                        </p>
                    </div>
                    
                    <div class="grid gap-5">
                        <template v-for="field in fields" :key="field.field">
                            <div v-if="field.type === 'select'" class="input-wrapper">
                                <FormSelect
                                    :id="field.field"
                                    v-model="form.data_isian[field.field]"
                                    :label="field.label"
                                    :options="field.options || []"
                                    :required="field.required"
                                    :error="form.errors[`data_isian.${field.field}`]"
                                />
                            </div>
                            
                            <div v-else-if="field.type === 'textarea'" class="input-wrapper">
                                <label :for="field.field" class="input-label-wrapper">
                                    <span class="label-sm text-secondary mb-1.5 block">{{ field.label }} <span v-if="field.required" class="text-error">*</span></span>
                                    <textarea 
                                        :id="field.field" 
                                        v-model="form.data_isian[field.field]" 
                                        rows="4" 
                                        class="custom-textarea" 
                                        :required="field.required"
                                    />
                                </label>
                                <span v-if="form.errors[`data_isian.${field.field}`]" class="input-error-msg">{{ form.errors[`data_isian.${field.field}`] }}</span>
                            </div>
                            
                            <div v-else class="input-wrapper">
                                <FormInput
                                    :id="field.field"
                                    v-model="form.data_isian[field.field]"
                                    :type="field.type === 'number' ? (field.label.includes('Rp') ? 'currency' : 'number') : field.type === 'date' ? 'date' : 'text'"
                                    :label="field.label"
                                    :required="field.required"
                                    :error="form.errors[`data_isian.${field.field}`]"
                                />
                            </div>
                        </template>
                    </div>
                </div>

                <div v-if="current === docStep" class="space-y-6">
                    <div>
                        <h2 class="headline-sm">Berkas Persyaratan</h2>
                        <p class="body-md text-secondary mt-1">Unggah dokumen pendukung untuk verifikasi. Jika dokumen sudah tersedia di profil Anda, sistem akan melampirkannya secara otomatis.</p>
                    </div>

                    <div class="space-y-5">
                        <div v-for="document in documents" :key="document" class="document-upload-zone">
                            <span class="label-md text-neutral mb-2 block font-medium">{{ document }} <span class="text-error">*</span></span>
                            
                            <div v-if="typeof form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')] === 'string' && form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')]" class="attachment-status-card success-attached">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="avatar-circle-sm bg-primary-soft text-primary shrink-0">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </div>
                                    <div class="min-w-0 text-left">
                                        <p class="label-sm text-neutral font-medium">Terlampir Otomatis</p>
                                        <p class="body-sm text-secondary truncate">Menggunakan berkas resmi dari profil warga.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <a :href="form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')]" target="_blank" class="view-file-btn">Lihat</a>
                                    <button type="button" @click="form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')] = ''" class="change-file-btn">Ganti</button>
                                </div>
                            </div>

                            <div v-else-if="isFile(form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')])" class="attachment-status-card new-attached">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="avatar-circle-sm bg-accent-soft text-primary shrink-0">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                    </div>
                                    <div class="min-w-0 text-left">
                                        <p class="label-sm text-neutral font-medium">Unggahan Berkas Baru</p>
                                        <p class="body-sm text-secondary truncate">{{ form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')].name }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="form.file_syarat[document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')] = ''" class="remove-file-btn">Hapus</button>
                            </div>

                            <div v-else class="upload-dropzone">
                                <svg class="size-8 text-secondary mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span class="label-sm text-neutral font-medium">Seret atau Pilih Berkas {{ document }}</span>
                                <p class="body-sm text-secondary mt-0.5">Format: JPG, PNG, PDF, WebP (Maks. 2MB)</p>
                                <label class="btn-secondary mt-3 cursor-pointer inline-block">
                                    Pilih File
                                    <input type="file" @change="(e) => handleFileSelect(e, document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, ''))" class="hidden" accept="image/*,application/pdf" />
                                </label>
                            </div>
                            
                            <span v-if="form.errors[`file_syarat.${document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')}`]" class="input-error-msg mt-1">
                                {{ form.errors[`file_syarat.${document.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')}`] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="current === reviewStep" class="space-y-6">
                    <div>
                        <h2 class="headline-sm">Tinjau Permohonan</h2>
                        <p class="body-md text-secondary mt-1">Periksa kembali data Anda sebelum mengirimkan pengajuan surat.</p>
                    </div>

                    <div class="review-pemohon-card">
                        <span class="overline-label">Pemohon</span>
                        <h4 class="headline-sm mt-1 text-primary">{{ selectedPemohon.nama_lengkap }}</h4>
                        <p class="body-sm text-secondary mt-0.5">NIK: {{ selectedPemohon.nik }} · {{ selectedPemohon.status_keluarga }}</p>
                    </div>

                    <div class="space-y-3">
                        <h4 class="label-md text-neutral font-medium uppercase tracking-wider">Isian Formulir</h4>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div v-for="field in fields" :key="field.field" class="review-data-item">
                                <span class="overline-label">{{ field.label }}</span>
                                <p class="body-md text-neutral mt-0.5 font-medium">{{ form.data_isian[field.field] || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="documents.length" class="space-y-3 pt-2">
                        <h4 class="label-md text-neutral font-medium uppercase tracking-wider">Berkas Persyaratan</h4>
                        <div class="space-y-2.5">
                            <div v-for="doc in documents" :key="doc" class="review-doc-item">
                                <div class="min-w-0 flex-1">
                                    <p class="label-sm text-neutral font-medium">{{ doc }}</p>
                                    <p v-if="isFile(form.file_syarat[doc.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')])" class="body-sm text-secondary mt-0.5 truncate">
                                        Unggahan baru: <span class="font-medium text-neutral">{{ form.file_syarat[doc.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')].name }}</span>
                                    </p>
                                    <p v-else-if="typeof form.file_syarat[doc.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')] === 'string' && form.file_syarat[doc.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')]" class="body-sm text-primary mt-0.5 font-medium">
                                        Otomatis dilampirkan dari biodata profil.
                                    </p>
                                    <p v-else class="body-sm text-error mt-0.5 font-medium">
                                        Berkas belum dipilih.
                                    </p>
                                </div>
                                <span v-if="form.file_syarat[doc.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')]" class="badge success-badge">Siap</span>
                                <span v-else class="badge error-badge">Belum Ada</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="navigation-panel pt-6 mt-8">
                    <button v-if="current > 0" type="button" class="btn-secondary" @click="previous">
                        Kembali
                    </button>
                    <span v-else />
                    
                    <button v-if="current < steps.length - 1" type="button" class="btn-primary" @click="next">
                        Lanjut
                    </button>
                    <button v-else type="submit" class="btn-primary flex items-center justify-center gap-2" :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin size-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2v4"/></svg>
                        <svg v-else class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
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

.header-icon-box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background-color: #E8F0FE;
    border: 1px solid #D2E3FC;
}

.editorial-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,0.1), 0 1px 3px 1px rgba(60,64,67,0.05);
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

.selector-button {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px;
    width: 100%;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    background-color: #FFFFFF;
    transition: all 0.2s;
    cursor: pointer;
}
.selector-button.active {
    border-color: #1A73E8;
    background-color: #F4F8FF;
    box-shadow: 0 0 0 1px #1A73E8;
}

.avatar-circle-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 14px;
}

.check-circle {
    width: 20px;
    height: 20px;
    border-radius: 9999px;
    border: 2px solid #BDC1C6;
}

.check-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 9999px;
}

.alert-box {
    display: flex;
    gap: 14px;
    padding: 16px;
    border-radius: 12px;
    align-items: flex-start;
}

.info-alert {
    background-color: #E8F0FE;
    border: 1px solid #D2E3FC;
    color: #1A73E8;
}

.input-wrapper {
    margin-bottom: 4px;
}

.input-label-wrapper {
    display: block;
    width: 100%;
}

.custom-textarea {
    display: block;
    width: 100%;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    padding: 12px 16px;
    font-size: 15px;
    color: #202124;
    transition: border-color 0.2s;
    resize: vertical;
    outline: none;
    font-family: inherit;
}
.custom-textarea:focus {
    border-color: #1A73E8;
    box-shadow: 0 0 0 1px #1A73E8;
}

.input-error-msg {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #D93025;
    margin-top: 6px;
}

.document-upload-zone {
    padding: 4px 0;
}

.attachment-status-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
}

.success-attached {
    background-color: #E6F4EA;
    border-color: #CEEAD6;
}

.new-attached {
    background-color: #E8F0FE;
    border-color: #D2E3FC;
}

.view-file-btn {
    font-size: 13px;
    font-weight: 650;
    color: #1A73E8;
    text-decoration: underline;
}

.change-file-btn, .remove-file-btn {
    background: #FFFFFF;
    border: 1px solid #BDC1C6;
    font-size: 12px;
    font-weight: 500;
    color: #3C4043;
    padding: 6px 14px;
    border-radius: 9999px;
    cursor: pointer;
    transition: background-color 0.15s;
}
.change-file-btn:hover, .remove-file-btn:hover {
    background-color: #F8F9FA;
}

.upload-dropzone {
    display: flex;
    flex-col: col;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px dashed #BDC1C6;
    border-radius: 12px;
    padding: 24px;
    background-color: #F8F9FA;
    text-align: center;
    transition: border-color 0.2s;
}
.upload-dropzone:hover {
    border-color: #1A73E8;
}

.review-pemohon-card {
    background-color: #F4F8FF;
    border: 1px solid #D2E3FC;
    padding: 20px;
    border-radius: 12px;
}

.review-data-item {
    background-color: #F8F9FA;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 12px 16px;
}

.review-doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background-color: #F8F9FA;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 12px 16px;
}

.badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.success-badge {
    background-color: #E6F4EA;
    color: #137333;
}
.error-badge {
    background-color: #FCE8E6;
    color: #C5221F;
}

.navigation-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #E5E7EB;
}
</style>
