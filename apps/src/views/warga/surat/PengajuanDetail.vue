<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { PengajuanSurat } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'

import { useAuthStore } from '../../../stores/authStore'

const downloading = ref(false)

const route   = useRoute()
const router  = useRouter()
const auth    = useAuthStore()
const item    = ref<PengajuanSurat | null>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res: any = await api.get(endpoints.surat.detail(route.params.id as string))
    item.value = res.data ?? res
  } catch {
    item.value = null
  } finally {
    loading.value = false
  }
})

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function handleDownloadPrint() {
  if (!item.value) return
  downloading.value = true
  try {
    const downloadUrl = `/api/v1/surat/pengajuan/${item.value.id}/download`
    const token = auth.token ?? ''
    const baseUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'
    
    const response = await fetch(`${baseUrl}${downloadUrl}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    if (!response.ok) {
      throw new Error('Gagal mengambil berkas PDF')
    }
    
    const blob = await response.blob()
    const blobUrl = URL.createObjectURL(blob)
    const filename = `Surat_${item.value.nomor_registrasi?.replace(/\//g, '_') ?? 'Selesai'}.pdf`
    
    const a = document.createElement('a')
    a.href = blobUrl
    a.download = filename
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(blobUrl)
  } catch (err: any) {
    alert(err?.message ?? 'Gagal mengunduh PDF surat')
  } finally {
    downloading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb back -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/warga/surat/pengajuan" class="hover:text-[var(--clr-primary)] transition-colors">Pengajuan</router-link>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-48 rounded-xl" />
      <div class="skeleton h-32 rounded-xl" />
    </div>

    <div v-else-if="item" class="space-y-4">
      <!-- Main Detail Card -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-start justify-between gap-3 mb-4 pb-3" style="border-bottom: 1px solid var(--clr-border-light);">
          <div>
            <h2 class="text-lg font-bold" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? 'Surat Keterangan' }}</h2>
            <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">No. Reg: {{ item.nomor_registrasi }}</p>
          </div>
          <StatusBadge :status="item.status" size="lg" />
        </div>

        <div class="space-y-3 text-[14px]">
          <div class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Jenis Surat</span>
            <span class="font-semibold text-right" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? '-' }}</span>
          </div>
          <div class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Keperluan</span>
            <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-text);">{{ item?.data_isian?.keperluan ?? '-' }}</span>
          </div>
          <div v-if="item.catatan_penolakan" class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Catatan Alasan</span>
            <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-error);">{{ item.catatan_penolakan }}</span>
          </div>
          <div class="flex justify-between py-1.5">
            <span style="color: var(--clr-text-secondary);">Tanggal Pengajuan</span>
            <span class="font-semibold text-right" style="color: var(--clr-text);">{{ formatDate(item.created_at) }}</span>
          </div>
        </div>

        <div v-if="item.status === 'Selesai'" class="mt-4 pt-3 border-t" style="border-color: var(--clr-border-light);">
          <button @click="handleDownloadPrint" class="w-full btn btn-primary py-3 flex items-center justify-center gap-2 font-bold shadow-md press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Unduh Surat
          </button>
        </div>
      </div>

      <!-- Data Isian Card -->
      <div
        v-if="item?.data_isian && Object.keys(item.data_isian).length > 1"
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-3" style="color: var(--clr-text);">Rincian Data Isian</h3>
        <div class="space-y-2.5 text-[14px]">
          <template v-for="(val, key) in item?.data_isian" :key="key">
            <div v-if="key !== 'keperluan'" class="flex justify-between py-1.5 border-b last:border-0" style="border-color: var(--clr-border-light);">
              <span class="capitalize" style="color: var(--clr-text-secondary);">{{ String(key).replace(/_/g, ' ') }}</span>
              <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-text);">{{ val }}</span>
            </div>
          </template>
        </div>
      </div>

      <!-- Timeline Tracking -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-4" style="color: var(--clr-text);">Riwayat Status Tracking</h3>

        <div v-if="!item?.tracking?.length" class="text-center py-6 text-xs" style="color: var(--clr-text-tertiary);">
          Belum ada pembaruan tracking
        </div>

        <div v-else class="space-y-4 relative pl-3">
          <div
            v-for="(t, i) in item?.tracking"
            :key="i"
            class="flex items-start gap-3 relative"
          >
            <!-- Line connector -->
            <div
              v-if="item?.tracking && i < item.tracking.length - 1"
              class="absolute left-[7px] top-[18px] bottom-[-16px] w-[2px]"
              style="background: var(--clr-border);"
            />
            <!-- Dot -->
            <div
              class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 z-10 mt-0.5"
              :style="{ background: (item?.tracking && i === item.tracking.length - 1) ? 'var(--clr-primary)' : 'var(--clr-border)' }"
            >
              <div class="w-1.5 h-1.5 rounded-full bg-white" />
            </div>
            <!-- Info -->
            <div class="min-w-0 flex-1">
              <p class="font-bold text-[14px]" style="color: var(--clr-text);">{{ t.status_baru }}</p>
              <p v-if="t.keterangan_update" class="text-[12px] mt-0.5" style="color: var(--clr-text-secondary);">{{ t.keterangan_update }}</p>
              <p class="text-[11px] mt-1 font-medium" style="color: var(--clr-text-tertiary);">
                {{ t.diupdate_oleh ?? 'Sistem' }} · {{ formatDate(t.created_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-16 text-sm" style="color: var(--clr-text-tertiary);">
      Pengajuan tidak ditemukan
    </div>
  </div>
</template>
