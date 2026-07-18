<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import type { MutasiPenduduk } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'

const items   = ref<MutasiPenduduk[]>([])
const loading = ref(true)
const error   = ref('')

onMounted(async () => {
  try {
    const res: any = await api.get<any>('/api/v1/mutasi')
    items.value = res.data?.data ?? res.data ?? []
  } catch {
    error.value = 'Gagal memuat daftar mutasi'
  } finally {
    loading.value = false
  }
})

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Mutasi Penduduk</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Riwayat laporan pindah masuk / keluar</p>
      </div>
      <router-link
        to="/warga/mutasi/buat"
        class="btn btn-primary btn-sm"
      >+ Laporkan Mutasi</router-link>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-24 rounded-xl" />
    </div>

    <!-- Empty state -->
    <div v-else-if="items.length === 0" class="text-center py-16" style="color: var(--clr-text-tertiary);">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
      </svg>
      <p class="text-sm font-medium">Belum ada riwayat mutasi</p>
      <router-link to="/warga/mutasi/buat" class="btn btn-ghost btn-sm mt-3 inline-flex">Laporkan Mutasi</router-link>
    </div>

    <!-- Items -->
    <div v-else class="space-y-3">
      <div
        v-for="m in items"
        :key="m.id"
        class="rounded-xl p-4 transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-bold text-[15px]" style="color: var(--clr-text);">
                {{ m.jenis_mutasi === 'masuk' ? 'Mutasi Masuk' : m.jenis_mutasi === 'keluar' ? 'Mutasi Keluar' : 'Pindah Alamat' }}
              </span>
              <StatusBadge :status="m.status_verifikasi" />
            </div>
            <p class="text-[13px] line-clamp-2" style="color: var(--clr-text-secondary);">{{ m.keterangan || 'Tidak ada keterangan tambahan' }}</p>
            <p class="text-[11px] font-medium mt-2" style="color: var(--clr-text-tertiary);">{{ formatDate(m.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
