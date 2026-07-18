<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../../../api/client'

const router    = useRouter()
const stats     = ref<{ label: string; value: number; iconBg: string; iconClr: string; icon: string }[]>([])
const ageGroups = ref<{ label: string; value: number; percent: number }[]>([])
const loading   = ref(true)
const error     = ref('')

const statIcons = [
  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
  'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
  'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
]

onMounted(async () => {
  try {
    const demografi: any = await api.get<any>('/api/v1/statistik/demografi')
    const d: any = demografi.data ?? demografi

    stats.value = [
      { label: 'Total Penduduk',  value: d.total_penduduk ?? 0, iconBg: 'var(--clr-primary-bg)',   iconClr: 'var(--clr-primary)',   icon: statIcons[0] },
      { label: 'Laki-laki',       value: d.laki_laki ?? 0,       iconBg: 'var(--clr-tertiary-bg)',  iconClr: 'var(--clr-tertiary)',  icon: statIcons[1] },
      { label: 'Perempuan',       value: d.perempuan ?? 0,       iconBg: 'var(--clr-secondary-bg)', iconClr: 'var(--clr-secondary)', icon: statIcons[2] },
      { label: 'Kepala Keluarga', value: d.total_keluarga ?? 0,  iconBg: 'var(--clr-warning-bg)',   iconClr: 'var(--clr-warning)',   icon: statIcons[3] },
    ]

    const perUsia = d.per_usia ?? {}
    const maxVal = Math.max(...Object.values(perUsia).map((x: any) => Number(x) || 0), 1)

    ageGroups.value = Object.entries(perUsia).map(([label, value]) => {
      const v = Number(value) || 0
      return { label, value: v, percent: Math.round((v / maxVal) * 100) }
    })
  } catch {
    error.value = 'Gagal memuat data statistik'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <span style="color: var(--clr-text);">Statistik</span>
    </div>

    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Statistik Penduduk</h2>
      <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Demografi kependudukan desa secara umum</p>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="grid grid-cols-2 gap-3">
      <div v-for="i in 4" :key="i" class="skeleton h-28 rounded-xl" />
    </div>

    <template v-else>
      <!-- Stats 2x2 grid -->
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="s in stats"
          :key="s.label"
          class="rounded-xl p-4 flex flex-col justify-between"
          style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
        >
          <div class="flex items-center gap-3 mb-2">
            <div class="icon-box-sm" :style="{ background: s.iconBg }">
              <svg class="w-4.5 h-4.5" :style="{ color: s.iconClr }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"/>
              </svg>
            </div>
            <span class="text-xs font-medium truncate" style="color: var(--clr-text-tertiary);">{{ s.label }}</span>
          </div>
          <span class="text-2xl font-bold" style="color: var(--clr-text);">{{ s.value }}</span>
        </div>
      </div>

      <!-- Age Groups Distribution -->
      <div
        v-if="ageGroups.length"
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-4" style="color: var(--clr-text);">Distribusi Kelompok Usia</h3>
        <div class="space-y-3">
          <div
            v-for="g in ageGroups"
            :key="g.label"
            class="space-y-1.5"
          >
            <div class="flex justify-between text-xs font-medium">
              <span style="color: var(--clr-text-secondary);">{{ g.label }} tahun</span>
              <span style="color: var(--clr-text);">{{ g.value }} jiwa</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background: var(--clr-surface-dim);">
              <div
                class="h-full rounded-full transition-all duration-500"
                style="background: var(--clr-primary);"
                :style="{ width: `${g.percent}%` }"
              />
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
