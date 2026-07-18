<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import { debounce } from '../../../utils/debounce'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

interface KeluargaItem {
  no_kk: string
  alamat: string
  rt?: string
  rw?: string
  dusun?: string
  kepala_keluarga?: { nama_lengkap: string }
}

const items = ref<KeluargaItem[]>([])
const loading = ref(true)
const error = ref('')
const search = ref('')
const page = ref(1)
const total = ref(0)

const showAddModal = ref(false)
const submitting = ref(false)
const formError = ref('')
const newKK = ref({ no_kk: '', alamat: '', rt: '', rw: '', dusun: '' })

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const params = `?search=${encodeURIComponent(search.value)}&page=${page.value}`
    const res: any = await api.get(endpoints.keluarga.list + params)
    items.value = res.data ?? []
    total.value = res.meta?.total ?? res.total ?? 0
  } catch {
    error.value = 'Gagal memuat data keluarga'
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => {
  page.value = 1
  fetch()
}, 350)

watch(search, () => {
  debouncedFetch()
})

async function handleAddKK() {
  formError.value = ''
  if (!newKK.value.no_kk || !newKK.value.alamat) {
    formError.value = 'No. KK dan Alamat wajib diisi'
    return
  }
  submitting.value = true
  try {
    await api.post(endpoints.keluarga.create, newKK.value)
    showAddModal.value = false
    newKK.value = { no_kk: '', alamat: '', rt: '', rw: '', dusun: '' }
    fetch()
  } catch (e: any) {
    formError.value = e.message ?? 'Gagal membuat KK baru'
  } finally {
    submitting.value = false
  }
}

function hapus(no_kk: string) {
  if (!confirm('Hapus data Kartu Keluarga ini?')) return
  api.delete(endpoints.keluarga.delete(no_kk)).then(() => fetch())
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Kelola Kartu Keluarga (KK)</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Data rumah tangga & nomor KK terdaftar di desa</p>
      </div>
      <AppButton @click="showAddModal = true" variant="primary" size="sm">+ Buat KK Baru</AppButton>
    </div>

    <!-- Search -->
    <div class="flex gap-2 items-center">
      <FormInput v-model="search" placeholder="Cari No. KK / Alamat..." class="flex-1" @keyup.enter="fetch" />
      <AppButton @click="fetch" :loading="loading" variant="primary" size="sm">Cari</AppButton>
    </div>

    <!-- Error -->
    <div v-if="error" class="rounded-xl p-3 text-sm font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">
      {{ error }}
    </div>

    <!-- Skeleton -->
    <div v-if="loading && items.length === 0" class="space-y-3">
      <div v-for="i in 4" :key="i" class="skeleton h-20 rounded-xl" />
    </div>

    <!-- Empty -->
    <div v-else-if="items.length === 0" class="text-center py-12 rounded-xl p-5" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);">
      <p class="font-bold text-sm" style="color: var(--clr-text);">Belum ada data KK</p>
    </div>

    <!-- Grid Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div
        v-for="k in items"
        :key="k.no_kk"
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="badge badge-primary font-mono text-xs">No. KK: {{ k.no_kk }}</span>
            <button @click="hapus(k.no_kk)" class="p-1 rounded text-red-500 hover:bg-red-500/10" title="Hapus KK">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
          </div>
          <p class="font-bold text-sm" style="color: var(--clr-text);">{{ k.kepala_keluarga?.nama_lengkap ?? 'Kepala Keluarga' }}</p>
          <p class="text-xs mt-1" style="color: var(--clr-text-secondary);">{{ k.alamat }} <template v-if="k.rt">RT {{ k.rt }}/RW {{ k.rw }}</template></p>
          <p v-if="k.dusun" class="text-[11px] mt-0.5" style="color: var(--clr-text-tertiary);">Dusun {{ k.dusun }}</p>
        </div>
      </div>
    </div>

    <!-- Modal Add KK -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div class="rounded-2xl p-6 w-full max-w-md space-y-4" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
        <h3 class="text-lg font-bold" style="color: var(--clr-text);">Tambah Kartu Keluarga Baru</h3>
        <div v-if="formError" class="rounded-xl p-3 text-xs font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">{{ formError }}</div>

        <FormInput v-model="newKK.no_kk" label="Nomor Kartu Keluarga (16 Digit)" maxlength="16" required />
        <FormInput v-model="newKK.alamat" label="Alamat Domisili" required />

        <div class="grid grid-cols-3 gap-2">
          <FormInput v-model="newKK.rt" label="RT" placeholder="001" />
          <FormInput v-model="newKK.rw" label="RW" placeholder="002" />
          <FormInput v-model="newKK.dusun" label="Dusun" placeholder="Dusun I" />
        </div>

        <div class="flex gap-2 pt-2">
          <AppButton @click="showAddModal = false" variant="ghost" class="flex-1">Batal</AppButton>
          <AppButton @click="handleAddKK" :loading="submitting" variant="primary" class="flex-1">Simpan KK</AppButton>
        </div>
      </div>
    </div>
  </div>
</template>
