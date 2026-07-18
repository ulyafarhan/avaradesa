<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { KategoriSurat } from '../../../api/types'

const router     = useRouter()
const kategories = ref<KategoriSurat[]>([])
const loading    = ref(true)
const error      = ref('')

const colorPalette = [
  { bg: 'bg-[var(--ne-primary-container)]',   icon: 'text-[var(--ne-primary)]',   glow: 'ne-glow-primary' },
  { bg: 'bg-[var(--ne-secondary-container)]', icon: 'text-[var(--ne-secondary)]', glow: 'ne-glow-secondary' },
  { bg: 'bg-[var(--ne-tertiary-container)]',  icon: 'text-[var(--ne-tertiary)]',  glow: 'ne-glow-tertiary' },
  { bg: 'bg-amber-50 dark:bg-amber-900/20',   icon: 'text-amber-500',             glow: '' },
  { bg: 'bg-rose-50 dark:bg-rose-900/20',     icon: 'text-rose-500',              glow: '' },
]

onMounted(async () => {
  try {
    const res: any = await api.get(endpoints.surat.kategori)
    kategories.value = (res.data ?? res ?? []).filter((k: KategoriSurat) => k.is_active)
  } catch {
    error.value = 'Gagal memuat kategori surat'
  } finally {
    loading.value = false
  }
})

function go(cat: KategoriSurat) {
  const slug = cat.nama_surat.toLowerCase().replace(/\s+/g, '-')
  router.push(`/warga/surat/buat/${slug}`)
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="text-xl font-extrabold text-[var(--ne-on-bg)] tracking-tight">Pelayanan Surat Warga</h2>
        <p class="text-xs text-[var(--ne-on-surface-variant)] mt-1">
          Ajukan surat baru atau lihat riwayat & unduh PDF surat selesai.
        </p>
      </div>
      <router-link
        to="/warga/surat/pengajuan"
        class="px-3 py-2 rounded-xl text-xs font-bold text-white bg-[var(--clr-primary)] hover:opacity-90 transition-all shadow-sm shrink-0 flex items-center gap-1.5"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Surat Saya
      </router-link>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-2xl p-4 bg-[var(--ne-error-container)] text-[var(--ne-on-error-container)] text-sm font-semibold"
    >{{ error }}</div>

    <!-- Skeleton loader -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 4" :key="i" class="h-20 rounded-[20px] bg-[var(--ne-surface-low)] animate-pulse" />
    </div>

    <!-- Empty state -->
    <div
      v-else-if="kategories.length === 0"
      class="text-center py-16"
    >
      <div class="w-16 h-16 rounded-2xl bg-[var(--ne-surface-low)] flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-[var(--ne-outline)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <p class="text-sm font-semibold text-[var(--ne-on-surface-variant)]">Belum ada kategori surat tersedia</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <button
        v-for="(cat, i) in kategories"
        :key="cat.id"
        class="w-full ne-card-solid rounded-[20px] p-4 flex items-center gap-4 text-left
               hover:-translate-y-[2px] hover:shadow-[0_12px_32px_-8px_rgba(15,23,42,0.09)]
               dark:hover:shadow-[0_12px_32px_-8px_rgba(0,0,0,0.4)]
               transition-all ne-spring-press"
        :class="colorPalette[i % colorPalette.length].glow"
        :style="{ transitionTimingFunction: 'var(--spring-expressive)', transitionDuration: '0.3s' }"
        @click="go(cat)"
      >
        <!-- Icon -->
        <div
          class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
          :class="colorPalette[i % colorPalette.length].bg"
        >
          <svg
            class="w-6 h-6"
            :class="colorPalette[i % colorPalette.length].icon"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                     a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>

        <!-- Text -->
        <div class="flex-1 min-w-0">
          <p class="font-bold text-[var(--ne-on-bg)] tracking-tight truncate">{{ cat.nama_surat }}</p>
          <p class="text-[11px] text-[var(--ne-on-surface-variant)] mt-0.5 font-medium">
            {{ cat.syarat_dokumen?.length ?? 0 }} syarat dokumen diperlukan
          </p>
        </div>

        <!-- Arrow -->
        <div class="w-8 h-8 rounded-full bg-[var(--ne-surface-low)] flex items-center justify-center shrink-0">
          <svg class="w-3.5 h-3.5 text-[var(--ne-outline)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </button>
    </div>
  </div>
</template>
