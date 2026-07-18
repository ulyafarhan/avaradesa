<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

interface Kategori {
  id: number
  kode_surat: string
  nama_surat: string
  is_active: boolean
  syarat_dokumen?: string[]
}

const items = ref<Kategori[]>([])
const loading = ref(true)
const error = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const activeId = ref<number | null>(null)
const submitting = ref(false)
const formError = ref('')
const form = ref({ kode_surat: '', nama_surat: '', is_active: true, syaratInput: '' })

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const res: any = await api.get(endpoints.kategoriSurat.list)
    items.value = res.data ?? res ?? []
  } catch {
    error.value = 'Gagal memuat kategori surat'
  } finally {
    loading.value = false
  }
}

function openAdd() {
  isEdit.value = false
  activeId.value = null
  form.value = { kode_surat: '', nama_surat: '', is_active: true, syaratInput: '' }
  showModal.value = true
}

function openEdit(k: Kategori) {
  isEdit.value = true
  activeId.value = k.id
  form.value = {
    kode_surat: k.kode_surat,
    nama_surat: k.nama_surat,
    is_active: k.is_active,
    syaratInput: (k.syarat_dokumen ?? []).join(', '),
  }
  showModal.value = true
}

async function save() {
  formError.value = ''
  if (!form.value.kode_surat || !form.value.nama_surat) {
    formError.value = 'Kode surat dan Nama surat wajib diisi'
    return
  }

  const syarat = form.value.syaratInput ? form.value.syaratInput.split(',').map(s => s.trim()).filter(Boolean) : []
  submitting.value = true

  try {
    const payload = {
      kode_surat: form.value.kode_surat,
      nama_surat: form.value.nama_surat,
      is_active: form.value.is_active,
      syarat_dokumen: syarat,
    }

    if (isEdit.value && activeId.value) {
      await api.put(endpoints.kategoriSurat.update(String(activeId.value)), payload)
    } else {
      await api.post(endpoints.kategoriSurat.create, payload)
    }

    showModal.value = false
    fetch()
  } catch (e: any) {
    formError.value = e.message ?? 'Gagal menyimpan kategori surat'
  } finally {
    submitting.value = false
  }
}

function hapus(id: number) {
  if (!confirm('Hapus jenis kategori surat ini?')) return
  api.delete(endpoints.kategoriSurat.delete(String(id))).then(() => fetch())
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Kelola Kategori Surat</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Atur jenis surat Keterangan & syarat pengajuan warga</p>
      </div>
      <AppButton @click="openAdd" variant="primary" size="sm">+ Buat Kategori Surat</AppButton>
    </div>

    <!-- Error -->
    <div v-if="error" class="rounded-xl p-3 text-sm font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">
      {{ error }}
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-20 rounded-xl" />
    </div>

    <!-- List -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div
        v-for="k in items"
        :key="k.id"
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded" style="background: var(--clr-surface-dim); color: var(--clr-primary);">{{ k.kode_surat }}</span>
            <div class="flex items-center gap-1">
              <span class="badge" :class="k.is_active ? 'badge-tertiary' : 'badge-warning'">{{ k.is_active ? 'Aktif' : 'Non-Aktif' }}</span>
              <button @click="openEdit(k)" class="p-1.5 rounded text-blue-500 hover:bg-blue-500/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
              <button @click="hapus(k.id)" class="p-1.5 rounded text-red-500 hover:bg-red-500/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
            </div>
          </div>
          <h3 class="font-bold text-base" style="color: var(--clr-text);">{{ k.nama_surat }}</h3>
          <p v-if="k.syarat_dokumen?.length" class="text-xs mt-2" style="color: var(--clr-text-secondary);">
            Syarat: {{ k.syarat_dokumen.join(', ') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div class="rounded-2xl p-6 w-full max-w-md space-y-4" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
        <h3 class="text-lg font-bold" style="color: var(--clr-text);">{{ isEdit ? 'Edit Kategori Surat' : 'Tambah Kategori Surat' }}</h3>
        <div v-if="formError" class="rounded-xl p-3 text-xs font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">{{ formError }}</div>

        <FormInput v-model="form.kode_surat" label="Kode Surat (Singkatan)" placeholder="MISAL: SKU / SKTM" required />
        <FormInput v-model="form.nama_surat" label="Nama Surat Keterangan" placeholder="Surat Keterangan Usaha..." required />
        <FormInput v-model="form.syaratInput" label="Syarat Dokumen (Pisahkan dengan koma)" placeholder="KTP, KK, Pengantar RT" />

        <label class="flex items-center gap-2.5 cursor-pointer press">
          <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-blue-600" />
          <span class="text-sm font-semibold" style="color: var(--clr-text);">Status Layanan Aktif</span>
        </label>

        <div class="flex gap-2 pt-2">
          <AppButton @click="showModal = false" variant="ghost" class="flex-1">Batal</AppButton>
          <AppButton @click="save" :loading="submitting" variant="primary" class="flex-1">Simpan</AppButton>
        </div>
      </div>
    </div>
  </div>
</template>
