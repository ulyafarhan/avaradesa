<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

const route = useRoute()
const router = useRouter()
const isEdit = !!route.params.id
const loading = ref(false)
const saving = ref(false)
const error = ref('')

const form = ref({
  nik: '', no_kk: '', nama_lengkap: '', jenis_kelamin: 'L' as 'L' | 'P',
  tempat_lahir: '', tanggal_lahir: '', agama: 'Islam', pendidikan: '',
  pekerjaan: '', status_perkawinan: 'Belum Kawin',
  status_keluarga: '', status_mutasi: 'Tetap',
})

const agamaList = ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']
const pendidikanList = ['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']
const statusPerkawinanList = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']

onMounted(async () => {
  if (isEdit) {
    loading.value = true
    try {
      const res: any = await api.get(endpoints.penduduk.detail(route.params.id as string))
      const d = res.data ?? {}
      form.value = {
        nik: d.nik ?? '', no_kk: d.no_kk ?? '', nama_lengkap: d.nama_lengkap ?? '',
        jenis_kelamin: d.jenis_kelamin ?? 'L',
        tempat_lahir: d.tempat_lahir ?? '', tanggal_lahir: d.tanggal_lahir ?? '',
        agama: d.agama ?? 'Islam', pendidikan: d.pendidikan ?? '',
        pekerjaan: d.pekerjaan ?? '', status_perkawinan: d.status_perkawinan ?? 'Belum Kawin',
        status_keluarga: d.status_keluarga ?? '', status_mutasi: d.status_mutasi ?? 'Tetap',
      }
    } catch (e: any) {
      error.value = e.message ?? 'Gagal memuat detail penduduk'
    } finally {
      loading.value = false
    }
  }
})

async function submit() {
  saving.value = true; error.value = ''
  try {
    if (isEdit) {
      await api.put(endpoints.penduduk.update(route.params.id as string), form.value)
    } else {
      await api.post(endpoints.penduduk.create, form.value)
    }
    router.push('/admin/penduduk')
  } catch (e: any) {
    error.value = e.message ?? 'Gagal menyimpan'
    if (e.errors) error.value = Object.values(e.errors as Record<string, string[]>).flat()[0] ?? error.value
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-4 max-w-4xl mx-auto">
    <!-- Breadcrumb back -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/admin/penduduk" class="hover:text-[var(--clr-primary)] transition-colors">Data Penduduk</router-link>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="skeleton h-64 rounded-xl" />

    <!-- Form Card -->
    <form
      v-else
      @submit.prevent="submit"
      class="rounded-xl p-5 sm:p-6 space-y-5"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
    >
      <div class="pb-3 border-b" style="border-color: var(--clr-border-light);">
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">{{ isEdit ? 'Edit Data Penduduk' : 'Tambah Penduduk Baru' }}</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Lengkapi formulir kependudukan di bawah ini</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FormInput v-model="form.nik" label="NIK" maxlength="16" required placeholder="16 digit NIK" />
        <FormInput v-model="form.no_kk" label="No. KK" maxlength="16" required placeholder="16 digit No. KK" />
      </div>

      <FormInput v-model="form.nama_lengkap" label="Nama Lengkap" required placeholder="Nama sesuai KTP" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FormInput v-model="form.tempat_lahir" label="Tempat Lahir" required placeholder="Kota / Kabupaten" />
        <FormInput v-model="form.tanggal_lahir" label="Tanggal Lahir" type="date" required />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Jenis Kelamin <span style="color: var(--clr-error);">*</span></label>
          <select v-model="form.jenis_kelamin" class="input-field">
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>

        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Agama <span style="color: var(--clr-error);">*</span></label>
          <select v-model="form.agama" class="input-field">
            <option v-for="a in agamaList" :key="a" :value="a">{{ a }}</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Pendidikan Terakhir</label>
          <select v-model="form.pendidikan" class="input-field">
            <option value="">Pilih Pendidikan</option>
            <option v-for="p in pendidikanList" :key="p" :value="p">{{ p }}</option>
          </select>
        </div>
        <FormInput v-model="form.pekerjaan" label="Pekerjaan" placeholder="Pekerjaan saat ini" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Status Perkawinan</label>
          <select v-model="form.status_perkawinan" class="input-field">
            <option v-for="s in statusPerkawinanList" :key="s" :value="s">{{ s }}</option>
          </select>
        </div>
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Status Dalam Keluarga <span style="color: var(--clr-error);">*</span></label>
          <select v-model="form.status_keluarga" class="input-field" required>
            <option value="">Pilih Status</option>
            <option value="Kepala Keluarga">Kepala Keluarga</option>
            <option value="Istri">Istri</option>
            <option value="Anak">Anak</option>
            <option value="Orang Tua">Orang Tua</option>
            <option value="Mertua">Mertua</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Status Mutasi</label>
          <select v-model="form.status_mutasi" class="input-field">
            <option value="Tetap">Tetap</option>
            <option value="Pendatang">Pendatang</option>
            <option value="Pindah">Pindah</option>
          </select>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="error"
        class="rounded-xl p-3 text-sm font-medium flex items-center gap-2"
        style="background: var(--clr-error-bg); color: var(--clr-error-text);"
      >
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ error }}
      </div>

      <div class="flex items-center gap-3 pt-2">
        <AppButton type="submit" variant="primary" :loading="saving">
          {{ isEdit ? 'Simpan Perubahan' : 'Tambah Penduduk' }}
        </AppButton>
        <router-link to="/admin/penduduk" class="btn btn-ghost">Batal</router-link>
      </div>
    </form>
  </div>
</template>
