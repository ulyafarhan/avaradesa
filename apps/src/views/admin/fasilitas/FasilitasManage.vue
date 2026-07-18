<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

interface Fasilitas {
  id: number
  nama_fasilitas: string
  kategori: string
  jumlah: number
  kondisi: string
  lokasi?: string
}

const items = ref<Fasilitas[]>([])
const loading = ref(true)
const error = ref('')

const showModal = ref(false)
const submitting = ref(false)
const formError = ref('')
const form = ref({ nama_fasilitas: '', kategori: 'Umum', jumlah: 1, kondisi: 'Baik', lokasi: '' })

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const res: any = await api.get(endpoints.fasilitas.list)
    items.value = res.data ?? []
  } catch {
    error.value = 'Gagal memuat inventaris fasilitas'
  } finally {
    loading.value = false
  }
}

async function handleSave() {
  formError.value = ''
  if (!form.value.nama_fasilitas) { formError.value = 'Nama fasilitas wajib diisi'; return }
  submitting.value = true
  try {
    await api.post(endpoints.fasilitas.create, form.value)
    showModal.value = false
    form.value = { nama_fasilitas: '', kategori: 'Umum', jumlah: 1, kondisi: 'Baik', lokasi: '' }
    fetch()
  } catch (e: any) {
    formError.value = e.message ?? 'Gagal menyimpan fasilitas'
  } finally {
    submitting.value = false
  }
}

function hapus(id: number) {
  if (!confirm('Hapus inventaris fasilitas ini?')) return
  api.delete(endpoints.fasilitas.delete(String(id))).then(() => fetch())
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Inventaris & Fasilitas Desa</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Pencatatan aset & sarana prasarana publik desa</p>
      </div>
      <AppButton @click="showModal = true" variant="primary" size="sm">+ Tambah Inventaris</AppButton>
    </div>

    <!-- Error -->
    <div v-if="error" class="rounded-xl p-3 text-sm font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-20 rounded-xl" />
    </div>

    <!-- Empty -->
    <div v-else-if="items.length === 0" class="text-center py-12 rounded-xl p-5" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);">
      <p class="font-bold text-sm" style="color: var(--clr-text);">Belum ada data inventaris</p>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
      <div
        v-for="f in items"
        :key="f.id"
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="badge badge-primary">{{ f.kategori }}</span>
            <button @click="hapus(f.id)" class="p-1 text-red-500 hover:bg-red-500/10 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
          </div>
          <h3 class="font-bold text-base" style="color: var(--clr-text);">{{ f.nama_fasilitas }}</h3>
          <p class="text-xs mt-1" style="color: var(--clr-text-secondary);">Jumlah: <span class="font-bold">{{ f.jumlah }} unit</span> · Kondisi: {{ f.kondisi }}</p>
          <p v-if="f.lokasi" class="text-[11px] mt-1 font-medium" style="color: var(--clr-text-tertiary);">📍 {{ f.lokasi }}</p>
        </div>
      </div>
    </div>

    <!-- Modal Add -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div class="rounded-2xl p-6 w-full max-w-md space-y-4" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
        <h3 class="text-lg font-bold" style="color: var(--clr-text);">Tambah Inventaris Fasilitas</h3>
        <div v-if="formError" class="rounded-xl p-3 text-xs font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">{{ formError }}</div>

        <FormInput v-model="form.nama_fasilitas" label="Nama Fasilitas / Aset" placeholder="Ambulans Desa / Balai Pertemuan" required />

        <div class="grid grid-cols-2 gap-2">
          <FormInput v-model="form.kategori" label="Kategori" placeholder="Umum / Kesehatan / Olahraga" />
          <FormInput v-model.number="form.jumlah" label="Jumlah Unit" type="number" min="1" required />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="input-label block mb-1">Kondisi</label>
            <select v-model="form.kondisi" class="input-field">
              <option value="Baik">Baik</option>
              <option value="Rusak Ringan">Rusak Ringan</option>
              <option value="Rusak Berat">Rusak Berat</option>
            </select>
          </div>
          <FormInput v-model="form.lokasi" label="Lokasi Sarana" placeholder="Dusun I..." />
        </div>

        <div class="flex gap-2 pt-2">
          <AppButton @click="showModal = false" variant="ghost" class="flex-1">Batal</AppButton>
          <AppButton @click="handleSave" :loading="submitting" variant="primary" class="flex-1">Simpan Aset</AppButton>
        </div>
      </div>
    </div>
  </div>
</template>
