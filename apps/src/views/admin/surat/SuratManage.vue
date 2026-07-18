<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { SuratPengajuan } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'
const router  = useRouter()
const items   = ref<SuratPengajuan[]>([])
const loading = ref(true)
const error   = ref('')
const filter  = ref('')

const filters = [
  { label: 'Semua Status', value: '' },
  { label: 'Menunggu',    value: 'Pending' },
  { label: 'Diproses',    value: 'Diproses' },
  { label: 'Selesai',     value: 'Selesai' },
  { label: 'Ditolak',     value: 'Ditolak' },
]

async function fetch() {
  loading.value = true
  error.value   = ''
  try {
    const params = filter.value ? `?status=${filter.value}` : ''
    const res: any = await api.get(endpoints.surat.pengajuan + params)
    items.value = res.data ?? res.data?.data ?? []
  } catch (err: any) {
    error.value = err?.message ?? 'Gagal memuat pengajuan surat'
  } finally {
    loading.value = false
  }
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Kelola Pengajuan Surat</h2>
      <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Daftar permohonan surat keterangan dari warga</p>
    </div>

    <!-- Filter chips -->
    <div class="flex gap-2 overflow-x-auto pb-1">
      <button
        v-for="f in filters"
        :key="f.value"
        @click="filter = f.value; fetch()"
        class="badge press cursor-pointer transition-all"
        :class="filter === f.value ? 'badge-primary' : ''"
        :style="filter !== f.value ? 'background: var(--clr-surface); color: var(--clr-text-secondary); border: 1px solid var(--clr-border);' : ''"
      >
        {{ f.label }}
      </button>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading && items.length === 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div v-for="i in 4" :key="i" class="skeleton h-28 rounded-xl" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="items.length === 0"
      class="text-center py-16 rounded-xl p-5"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);"
    >
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-sm font-bold" style="color: var(--clr-text);">Tidak ada pengajuan surat</p>
      <p class="text-xs mt-1">Gunakan filter di atas untuk melihat status lain</p>
    </div>

    <!-- Grid items -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div
        v-for="item in items"
        :key="item.id"
        class="rounded-xl p-4 press cursor-pointer transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
        @click="router.push(`/admin/surat/${item.id}`)"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1">
              <h3 class="font-bold text-[15px] truncate" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? 'Surat' }}</h3>
              <StatusBadge :status="item.status" />
            </div>
            <p class="text-xs font-mono mb-1" style="color: var(--clr-text-tertiary);">{{ item.nomor_registrasi }}</p>
            <p class="text-sm font-semibold truncate" style="color: var(--clr-text);">{{ item.pemohon?.nama_lengkap || item.nik_pemohon }}</p>
            <p v-if="item.data_isian?.keperluan" class="text-xs mt-1 line-clamp-1" style="color: var(--clr-text-secondary);">{{ item.data_isian.keperluan }}</p>
          </div>
          <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: var(--clr-surface-dim); color: var(--clr-text-tertiary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
