<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { PengajuanSurat } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'

const router  = useRouter()
const items   = ref<PengajuanSurat[]>([])
const loading = ref(true)
const error   = ref('')

onMounted(async () => {
  try {
    const res: any = await api.get<PengajuanSurat[]>(endpoints.surat.list)
    items.value = res.data ?? []
  } catch {
    error.value = 'Gagal memuat daftar pengajuan'
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
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Pengajuan Saya</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Riwayat pengajuan surat keterangan</p>
      </div>
      <router-link
        to="/warga/surat"
        class="btn btn-primary btn-sm"
      >+ Buat Surat</router-link>
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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-sm font-medium">Belum ada pengajuan surat</p>
      <router-link to="/warga/surat" class="btn btn-ghost btn-sm mt-3 inline-flex">Ajukan Surat Pertama</router-link>
    </div>

    <!-- Items -->
    <div v-else class="space-y-3">
      <div
        v-for="item in items"
        :key="item.id"
        class="rounded-xl p-4 press cursor-pointer transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
        @click="router.push(`/warga/surat/pengajuan/${item.id}`)"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
              <h3 class="font-bold text-[15px] truncate" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? 'Surat Keterangan' }}</h3>
              <StatusBadge :status="item.status" />
            </div>
            <p class="text-[13px] line-clamp-1" style="color: var(--clr-text-secondary);">
              {{ item.data_isian?.keperluan ?? item.nomor_registrasi ?? 'Pengajuan Keterangan' }}
            </p>
            <p class="text-[11px] font-medium mt-2" style="color: var(--clr-text-tertiary);">
              Diajukan: {{ formatDate(item.created_at) }}
            </p>
          </div>
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
            style="background: var(--clr-surface-dim); color: var(--clr-text-tertiary);"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
