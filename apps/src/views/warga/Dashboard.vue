<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import { api } from '../../api/client'

const auth    = useAuthStore()
const loading = ref(true)
const error   = ref('')

interface Summary { pending: number; diproses: number; selesai: number }
const summary = ref<Summary>({ pending: 0, diproses: 0, selesai: 0 })

onMounted(async () => {
  try {
    const res: any = await api.get<any>('/api/v1/dashboard')
    summary.value = (res.data ?? res).summary ?? summary.value
  } catch {
    error.value = 'Gagal memuat data'
  } finally {
    loading.value = false
  }
})

const greeting = computed(() => {
  const h = new Date().getHours()
  if (h < 11) return 'Selamat pagi'
  if (h < 15) return 'Selamat siang'
  if (h < 18) return 'Selamat sore'
  return 'Selamat malam'
})

const initials = computed(() => {
  const name = auth.user?.nama_lengkap ?? ''
  return name.split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase() || '?'
})

const stats = computed(() => [
  {
    key:    'pending',
    label:  'Menunggu',
    value:  summary.value.pending,
    iconBg: 'var(--clr-warning-bg)',
    iconColor: 'var(--clr-warning)',
    icon:   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  {
    key:    'diproses',
    label:  'Diproses',
    value:  summary.value.diproses,
    iconBg: 'var(--clr-primary-bg)',
    iconColor: 'var(--clr-primary)',
    icon:   'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
  },
  {
    key:    'selesai',
    label:  'Selesai',
    value:  summary.value.selesai,
    iconBg: 'var(--clr-tertiary-bg)',
    iconColor: 'var(--clr-tertiary)',
    icon:   'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
])

const actions = [
  {
    label:   'Dokumen & Surat Saya',
    desc:    'Lihat riwayat status & unduh PDF surat selesai',
    to:      '/warga/surat/pengajuan',
    iconBg:  'var(--clr-primary-bg)',
    iconClr: 'var(--clr-primary)',
    icon:    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
  {
    label:   'Buat Surat Baru',
    desc:    'Keterangan domisili, SKTM, usaha',
    to:      '/warga/surat',
    iconBg:  'var(--clr-tertiary-bg)',
    iconClr: 'var(--clr-tertiary)',
    icon:    'M12 4v16m8-8H4',
  },
  {
    label:   'Informasi Desa',
    desc:    'Berita & pengumuman terkini',
    to:      '/warga/informasi',
    iconBg:  'var(--clr-secondary-bg)',
    iconClr: 'var(--clr-secondary)',
    icon:    'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
  },
  {
    label:   'Mutasi Penduduk',
    desc:    'Pindah masuk atau keluar',
    to:      '/warga/mutasi',
    iconBg:  'var(--clr-warning-bg)',
    iconClr: 'var(--clr-warning)',
    icon:    'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
  },
]
</script>

<template>
  <div class="space-y-4 max-w-screen-sm mx-auto">

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- ── Hero banner ──────────────────────────────────────────────────── -->
    <div class="rounded-xl overflow-hidden relative" style="min-height: 110px;">
      <div class="hero-gradient-blue absolute inset-0" />
      <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white opacity-[0.06]" />
      <div class="relative z-10 p-4.5 flex items-center justify-between gap-3">
        <div class="min-w-0">
          <p class="text-white/60 text-[11px] font-medium mb-0.5">{{ greeting }},</p>
          <h2 class="text-white text-lg font-bold truncate leading-tight">
            {{ auth.user?.nama_lengkap ?? 'Warga' }}
          </h2>
          <p class="text-white/50 text-[11px] font-medium mt-1 font-mono">NIK: {{ auth.user?.nik ?? '—' }}</p>
        </div>
        <!-- Avatar -->
        <div
          class="w-12 h-12 rounded-xl flex items-center justify-center text-base font-bold text-white shrink-0"
          style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);"
        >{{ initials }}</div>
      </div>
    </div>

    <!-- ── Status surat row (Compact horizontal / low padding) ──────────── -->
    <div class="grid grid-cols-3 gap-2.5">
      <div
        v-for="stat in stats"
        :key="stat.key"
        @click="$router.push('/warga/surat/pengajuan')"
        class="rounded-xl p-3 flex flex-col items-center justify-center gap-1 text-center press cursor-pointer transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="icon-box-sm" :style="{ background: stat.iconBg }">
          <svg class="w-4 h-4" :style="{ color: stat.iconColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"/>
          </svg>
        </div>

        <div v-if="loading" class="skeleton w-6 h-5 my-0.5" />
        <span v-else class="text-xl font-bold leading-tight" style="color: var(--clr-text);">{{ stat.value }}</span>

        <span class="text-[11px] font-medium" style="color: var(--clr-text-tertiary);">{{ stat.label }}</span>
      </div>
    </div>

    <!-- ── Quick actions (Modern list cards — no empty vertical gap!) ────── -->
    <div class="pt-1">
      <h3 class="text-[14px] font-bold mb-2.5" style="color: var(--clr-text);">Layanan Mandiri</h3>

      <div class="space-y-2.5">
        <router-link
          v-for="action in actions"
          :key="action.to"
          :to="action.to"
          class="block"
        >
          <div
            class="rounded-xl p-3.5 flex items-center gap-3.5 press transition-all duration-200"
            style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
          >
            <!-- Icon -->
            <div class="icon-box-md shrink-0" :style="{ background: action.iconBg }">
              <svg class="w-5 h-5" :style="{ color: action.iconClr }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon"/>
              </svg>
            </div>
            <!-- Text -->
            <div class="min-w-0 flex-1">
              <p class="text-[14px] font-semibold leading-tight" style="color: var(--clr-text);">{{ action.label }}</p>
              <p class="text-[12px] mt-0.5 truncate" style="color: var(--clr-text-tertiary);">{{ action.desc }}</p>
            </div>
            <!-- Arrow icon -->
            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: var(--clr-surface-dim);">
              <svg class="w-3.5 h-3.5" style="color: var(--clr-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </router-link>
      </div>
    </div>

  </div>
</template>
