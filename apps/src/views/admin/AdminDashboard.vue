<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../api/client'
import { endpoints } from '../../api/endpoints'

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

onMounted(async () => {
  try {
    const [d, l] = await Promise.all([
      api.get<{ data: Demografi }>(endpoints.statistik.demografi),
      api.get<{ data: Layanan }>(endpoints.statistik.layanan),
    ])
    demografi.value = d.data ?? demografi.value
    layanan.value = l.data ?? layanan.value
  } catch {
    error.value = 'Gagal memuat data dashboard'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="space-y-5 max-w-6xl mx-auto">
    <!-- Title -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Dashboard Administrator</h2>
      <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Ringkasan data kependudukan dan layanan desa</p>
    </div>

    <!-- Error alert -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      <div v-for="i in 4" :key="i" class="skeleton h-24 rounded-xl" />
    </div>

    <!-- Stat cards -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      <!-- Penduduk -->
      <div
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3">
          <div class="icon-box-md shrink-0" style="background: var(--clr-primary-bg);">
            <svg class="w-5 h-5" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium truncate" style="color: var(--clr-text-tertiary);">Total Penduduk</p>
            <p class="text-xl font-bold mt-0.5 truncate" style="color: var(--clr-text);">{{ demografi.total_penduduk }}</p>
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t flex gap-2 text-xs font-medium" style="border-color: var(--clr-border-light); color: var(--clr-text-secondary);">
          <span>L: {{ demografi.laki_laki }}</span>
          <span>·</span>
          <span>P: {{ demografi.perempuan }}</span>
        </div>
      </div>

      <!-- Pengajuan -->
      <div
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3">
          <div class="icon-box-md shrink-0" style="background: var(--clr-tertiary-bg);">
            <svg class="w-5 h-5" style="color: var(--clr-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium truncate" style="color: var(--clr-text-tertiary);">Total Pengajuan</p>
            <p class="text-xl font-bold mt-0.5 truncate" style="color: var(--clr-text);">{{ layanan.pengajuan_surat?.total ?? 0 }}</p>
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t flex gap-2 text-xs font-medium" style="border-color: var(--clr-border-light);">
          <span style="color: var(--clr-tertiary);">Selesai: {{ layanan.pengajuan_surat?.selesai ?? 0 }}</span>
          <span>·</span>
          <span style="color: var(--clr-warning);">Pending: {{ layanan.pengajuan_surat?.pending ?? 0 }}</span>
        </div>
      </div>

      <!-- Mutasi -->
      <div
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3">
          <div class="icon-box-md shrink-0" style="background: var(--clr-warning-bg);">
            <svg class="w-5 h-5" style="color: var(--clr-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium truncate" style="color: var(--clr-text-tertiary);">Laporan Mutasi</p>
            <p class="text-xl font-bold mt-0.5 truncate" style="color: var(--clr-text);">{{ layanan.mutasi_penduduk?.total ?? 0 }}</p>
          </div>
        </div>
        <p class="mt-2.5 pt-2 border-t text-xs font-medium truncate" style="border-color: var(--clr-border-light); color: var(--clr-text-secondary);">Perpindahan warga</p>
      </div>

      <!-- Keluarga -->
      <div
        class="rounded-xl p-4 flex flex-col justify-between"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3">
          <div class="icon-box-md shrink-0" style="background: var(--clr-secondary-bg);">
            <svg class="w-5 h-5" style="color: var(--clr-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium truncate" style="color: var(--clr-text-tertiary);">Total Keluarga</p>
            <p class="text-xl font-bold mt-0.5 truncate" style="color: var(--clr-text);">{{ demografi.total_keluarga }}</p>
          </div>
        </div>
        <p class="mt-2.5 pt-2 border-t text-xs font-medium truncate" style="border-color: var(--clr-border-light); color: var(--clr-text-secondary);">Kepala Keluarga (KK)</p>
      </div>
    </div>

    <!-- Quick actions + Info layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div
        class="rounded-xl p-4.5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-3" style="color: var(--clr-text);">Aksi Pintar</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <router-link
            to="/admin/penduduk/tambah"
            class="flex items-center gap-3 p-3.5 rounded-xl press transition-colors"
            style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);"
          >
            <div class="icon-box-sm shrink-0" style="background: var(--clr-primary-bg);">
              <svg class="w-4.5 h-4.5" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </div>
            <span class="text-xs font-bold truncate" style="color: var(--clr-primary);">Tambah Penduduk</span>
          </router-link>

          <router-link
            to="/admin/surat"
            class="flex items-center gap-3 p-3.5 rounded-xl press transition-colors"
            style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);"
          >
            <div class="icon-box-sm shrink-0" style="background: var(--clr-tertiary-bg);">
              <svg class="w-4.5 h-4.5" style="color: var(--clr-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <span class="text-xs font-bold truncate" style="color: var(--clr-tertiary);">Proses Surat</span>
          </router-link>
        </div>
      </div>

      <div
        class="rounded-xl p-4.5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-3" style="color: var(--clr-text);">Status Layanan Surat</h3>
        <div class="space-y-2">
          <div class="flex justify-between items-center p-2.5 rounded-lg text-xs font-medium" style="background: var(--clr-surface-dim);">
            <span style="color: var(--clr-text-secondary);">Pengajuan Menunggu</span>
            <span class="font-bold" style="color: var(--clr-warning);">{{ layanan.pengajuan_surat?.pending ?? 0 }}</span>
          </div>
          <div class="flex justify-between items-center p-2.5 rounded-lg text-xs font-medium" style="background: var(--clr-surface-dim);">
            <span style="color: var(--clr-text-secondary);">Pengajuan Selesai</span>
            <span class="font-bold" style="color: var(--clr-tertiary);">{{ layanan.pengajuan_surat?.selesai ?? 0 }}</span>
          </div>
          <div class="flex justify-between items-center p-2.5 rounded-lg text-xs font-medium" style="background: var(--clr-surface-dim);">
            <span style="color: var(--clr-text-secondary);">Total Terdaftar</span>
            <span class="font-bold" style="color: var(--clr-primary);">{{ demografi.total_penduduk }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
