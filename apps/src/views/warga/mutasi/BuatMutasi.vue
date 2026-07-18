<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/authStore'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

const router        = useRouter()
const auth          = useAuthStore()
const jenis_mutasi  = ref('Kedatangan')
const tanggal_mutasi = ref(new Date().toISOString().split('T')[0])
const keterangan     = ref('')
const dokumen_bukti  = ref('')
const loading        = ref(false)
const error          = ref('')

const jenisOptions   = ['Kelahiran', 'Kematian', 'Kedatangan', 'Kepindahan']

async function submit() {
  if (!keterangan.value) { error.value = 'Keterangan wajib diisi'; return }
  loading.value = true; error.value = ''
  try {
    await api.post(endpoints.mutasi.create, {
      nik:            auth.user?.nik,
      jenis_mutasi:   jenis_mutasi.value,
      tanggal_mutasi: tanggal_mutasi.value,
      keterangan:     keterangan.value,
      dokumen_bukti:  dokumen_bukti.value || '-'
    })
    router.push('/warga/mutasi')
  } catch (e: any) {
    error.value = e.errors ? Object.values(e.errors).flat()[0] as string : (e.message ?? 'Gagal mengajukan mutasi')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/warga/mutasi" class="hover:text-[var(--clr-primary)] transition-colors">Mutasi</router-link>
    </div>

    <!-- Form card -->
    <div
      class="rounded-xl p-5 sm:p-6"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
    >
      <div class="mb-5 pb-3 border-b" style="border-color: var(--clr-border-light);">
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Laporan Mutasi Penduduk</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Laporkan perubahan data mutasi anggota keluarga</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Jenis Mutasi</label>
          <select
            v-model="jenis_mutasi"
            class="input-field"
          >
            <option v-for="j in jenisOptions" :key="j" :value="j">{{ j }}</option>
          </select>
        </div>

        <FormInput v-model="tanggal_mutasi" label="Tanggal Peristiwa / Mutasi" type="date" required />

        <div>
          <label class="input-label block" style="margin-bottom: 6px;">Keterangan & Alasan</label>
          <textarea
            v-model="keterangan"
            rows="3"
            class="input-field"
            style="resize: vertical;"
            placeholder="Jelaskan rincian mutasi..."
            required
          />
        </div>

        <FormInput v-model="dokumen_bukti" label="Nomor / Link Dokumen Bukti (Opsional)" placeholder="Surat keterangan pindah / akta..." />

        <!-- Error -->
        <div
          v-if="error"
          class="rounded-xl p-3 text-sm font-medium"
          style="background: var(--clr-error-bg); color: var(--clr-error-text);"
        >{{ error }}</div>

        <AppButton type="submit" variant="primary" class="w-full" :loading="loading">
          Kirim Laporan Mutasi
        </AppButton>
      </form>
    </div>
  </div>
</template>
