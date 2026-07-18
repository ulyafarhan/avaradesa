<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { Mutasi } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'
import AppButton from '../../../components/AppButton.vue'

interface Paginated<T> { current_page: number; data: T[]; total: number; per_page: number }

const items = ref<Mutasi[]>([])
const loading = ref(true)
const error = ref('')
const filter = ref('')
const activeAction = ref<{ id: number; type: 'approve' | 'reject' } | null>(null)

const filters = [
  { label: 'Semua Status', value: '' },
  { label: 'Menunggu',    value: 'Pending' },
  { label: 'Disetujui',   value: 'Disetujui' },
  { label: 'Ditolak',     value: 'Ditolak' },
]

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const params = filter.value ? `?status_verifikasi=${filter.value}` : ''
    const res = await api.get<Paginated<Mutasi>>(endpoints.mutasi.list + params)
    items.value = res.data ?? []
  } catch {
    error.value = 'Gagal memuat daftar mutasi'
  } finally {
    loading.value = false
  }
}

async function proses(id: number, action: 'approve' | 'reject') {
  activeAction.value = { id, type: action }
  try {
    if (action === 'approve') await api.post(endpoints.mutasi.approve(String(id)))
    else await api.post(endpoints.mutasi.reject(String(id)))
    fetch()
  } finally {
    activeAction.value = null
  }
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Kelola Mutasi Penduduk</h2>
      <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Verifikasi laporan perpindahan warga desa</p>
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
    <div v-if="loading && items.length === 0" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-28 rounded-xl" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="items.length === 0"
      class="text-center py-16 rounded-xl p-5"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);"
    >
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
      </svg>
      <p class="text-sm font-bold" style="color: var(--clr-text);">Tidak ada data mutasi</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <div
        v-for="m in items"
        :key="m.id"
        class="rounded-xl p-4 transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="badge badge-secondary capitalize">{{ m.jenis_mutasi }}</span>
              <StatusBadge :status="m.status_verifikasi" />
            </div>
            <p class="text-sm font-mono font-semibold" style="color: var(--clr-text);">NIK: {{ m.nik }}</p>
            <p class="text-xs mt-1" style="color: var(--clr-text-secondary);">{{ m.keterangan || 'Tanpa keterangan' }}</p>
            <p class="text-[11px] font-medium mt-2" style="color: var(--clr-text-tertiary);">
              {{ new Date(m.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
            </p>
          </div>
        </div>

        <!-- Action buttons if pending -->
        <div v-if="m.status_verifikasi === 'Pending'" class="mt-3 pt-3 flex gap-2 border-t" style="border-color: var(--clr-border-light);">
          <AppButton
            @click="proses(m.id, 'approve')"
            :loading="activeAction?.id === m.id && activeAction?.type === 'approve'"
            :disabled="activeAction?.id === m.id"
            variant="success"
            size="sm"
          >
            Setujui
          </AppButton>
          <AppButton
            @click="proses(m.id, 'reject')"
            :loading="activeAction?.id === m.id && activeAction?.type === 'reject'"
            :disabled="activeAction?.id === m.id"
            variant="danger"
            size="sm"
          >
            Tolak
          </AppButton>
        </div>
      </div>
    </div>
  </div>
</template>
