<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'

interface Demografi {
  total_penduduk: number
  laki_laki: number
  perempuan: number
  total_keluarga: number
}

interface Layanan {
  pengajuan_surat: { total: number; pending: number; diproses: number; selesai: number; ditolak: number }
  mutasi_penduduk: { total: number; pending: number; disetujui: number; ditolak: number }
}

const demografi = ref<Demografi>({ total_penduduk: 0, laki_laki: 0, perempuan: 0, total_keluarga: 0 })
const layanan = ref<Layanan>({ pengajuan_surat: { total: 0, pending: 0, diproses: 0, selesai: 0, ditolak: 0 }, mutasi_penduduk: { total: 0, pending: 0, disetujui: 0, ditolak: 0 } })
const loading = ref(true)
const error = ref('')
const clearing = ref(false)

onMounted(async () => {
  try {
    const [d, l] = await Promise.all([
      api.get<{ data: Demografi }>(endpoints.statistik.demografi),
      api.get<{ data: Layanan }>(endpoints.statistik.layanan),
    ])
    demografi.value = d.data ?? demografi.value
    layanan.value = l.data ?? layanan.value
  } catch {
    error.value = 'Gagal memuat data statistik'
  } finally {
    loading.value = false
  }
})

async function clearCache() {
  clearing.value = true
  try {
    await api.post(endpoints.statistik.clearCache)
    const [d, l] = await Promise.all([
      api.get<{ data: Demografi }>(endpoints.statistik.demografi),
      api.get<{ data: Layanan }>(endpoints.statistik.layanan),
    ])
    demografi.value = d.data ?? demografi.value
    layanan.value = l.data ?? layanan.value
  } finally {
    clearing.value = false
  }
}
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Statistik Layanan & Demografi</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Data agregat sistem desa</p>
      </div>
      <AppButton @click="clearCache" :loading="clearing" size="sm" variant="ghost">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        Segarkan Cache
      </AppButton>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 gap-3">
      <div v-for="i in 6" :key="i" class="skeleton h-24 rounded-xl" />
    </div>

    <template v-else>
      <!-- Demografi Card -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-base mb-4" style="color: var(--clr-text);">Demografi Kependudukan</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-primary-bg);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-primary);">{{ demografi.total_penduduk.toLocaleString() }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-primary-text);">Total Penduduk</p>
          </div>

          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-tertiary-bg);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-tertiary);">{{ demografi.laki_laki.toLocaleString() }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-tertiary-text);">Laki-laki</p>
          </div>

          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-secondary-bg);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-secondary);">{{ demografi.perempuan.toLocaleString() }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-secondary-text);">Perempuan</p>
          </div>
        </div>
      </div>

      <!-- Layanan Card -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-base mb-4" style="color: var(--clr-text);">Statistik Permohonan Surat</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-surface-dim);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-text);">{{ layanan.pengajuan_surat?.total?.toLocaleString() ?? 0 }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-text-secondary);">Total Pengajuan</p>
          </div>

          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-warning-bg);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-warning);">{{ layanan.pengajuan_surat?.pending?.toLocaleString() ?? 0 }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-warning-text);">Menunggu Prosedur</p>
          </div>

          <div
            class="rounded-xl p-4 text-center"
            style="background: var(--clr-tertiary-bg);"
          >
            <p class="text-2xl font-bold" style="color: var(--clr-tertiary);">{{ layanan.pengajuan_surat?.selesai?.toLocaleString() ?? 0 }}</p>
            <p class="text-xs font-semibold mt-1" style="color: var(--clr-tertiary-text);">Selesai Diterbitkan</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
