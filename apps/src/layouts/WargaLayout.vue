<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import DarkModeToggle from '../components/DarkModeToggle.vue'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()

// ── Navigation tabs ─────────────────────────────────────────────────────────
const tabs = [
  {
    name:     'Beranda',
    path:     '/warga/dashboard',
    matchOn:  ['/warga/dashboard'],
    icon:     'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  },
  {
    name:     'Surat',
    path:     '/warga/surat',
    matchOn:  ['/warga/surat'],
    icon:     'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
  {
    name:     'Informasi',
    path:     '/warga/informasi',
    matchOn:  ['/warga/informasi'],
    icon:     'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
  },
  {
    name:     'Profil',
    path:     '/warga/profil',
    matchOn:  ['/warga/profil', '/warga/keluarga'],
    icon:     'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
  },
]

const activeIdx  = computed(() =>
  tabs.findIndex(t => t.matchOn.some(p => route.path.startsWith(p)))
)
const isDashboard = computed(() => route.path === '/warga/dashboard')

// Page title mapping
const pageTitle = computed(() => {
  const m: Record<string, string> = {
    '/warga/dashboard': 'AvaraDesa',
    '/warga/surat':     'Pengajuan Surat',
    '/warga/mutasi':    'Mutasi Penduduk',
    '/warga/informasi': 'Informasi Desa',
    '/warga/profil':    'Profil',
    '/warga/keluarga':  'Data Keluarga',
    '/warga/statistik': 'Statistik',
  }
  return Object.entries(m).find(([k]) => route.path.startsWith(k))?.[1] ?? 'AvaraDesa'
})

// Initials dari nama
const initials = computed(() => {
  const name = auth.user?.nama_lengkap ?? ''
  return name.split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase() || '?'
})
</script>

<template>
  <div class="min-h-screen flex flex-col" style="background: var(--clr-bg); color: var(--clr-text);">

    <!-- Ambient background -->
    <div class="app-ambient" />

    <!-- ── Header ────────────────────────────────────────────────────────── -->
    <header class="app-header px-4 max-w-screen-sm mx-auto w-full">
      <div class="flex items-center justify-between w-full gap-3">

        <!-- Back button atau logo -->
        <div class="flex items-center gap-3 min-w-0">
          <button
            v-if="!isDashboard"
            class="w-9 h-9 flex items-center justify-center rounded-xl press flex-shrink-0"
            style="background: var(--clr-surface-dim)"
            @click="router.back()"
            aria-label="Kembali"
          >
            <svg class="w-5 h-5" style="color: var(--clr-text-secondary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <!-- Title -->
          <span
            class="font-bold text-[17px] truncate"
            :style="isDashboard
              ? 'background: linear-gradient(90deg, var(--clr-primary), var(--clr-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;'
              : 'color: var(--clr-text)'"
          >{{ pageTitle }}</span>
        </div>

        <!-- Right actions -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <DarkModeToggle />

          <!-- Avatar -->
          <button
            class="w-9 h-9 rounded-xl flex items-center justify-center text-[13px] font-bold press"
            style="background: var(--clr-primary-bg); color: var(--clr-primary-text);"
            @click="router.push('/warga/profil')"
            aria-label="Profil saya"
          >{{ initials }}</button>
        </div>
      </div>
    </header>

    <!-- ── Content ───────────────────────────────────────────────────────── -->
    <main
      class="flex-1 z-10 relative max-w-screen-sm mx-auto w-full"
      style="padding: 16px 16px 100px;"
    >
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- ── Bottom Navigation ─────────────────────────────────────────────── -->
    <nav class="app-nav">
      <div class="flex justify-around items-center h-[60px] px-2 max-w-screen-sm mx-auto">
        <button
          v-for="(tab, i) in tabs"
          :key="tab.name"
          class="relative flex flex-col items-center justify-center pt-1.5 gap-[2px] flex-1 h-full press"
          @click="router.push(tab.path)"
          :aria-label="tab.name"
        >
          <!-- Active indicator bar at top -->
          <div
            class="absolute top-0 left-1/2 -translate-x-1/2 h-[3px] rounded-b-full transition-all duration-300"
            :style="{
              width: activeIdx === i ? '24px' : '0px',
              background: 'var(--clr-primary)',
              transitionTimingFunction: 'var(--ease-spring)',
            }"
          />

          <!-- Icon -->
          <svg
            class="w-[22px] h-[22px] transition-all duration-250"
            :style="{
              color: activeIdx === i ? 'var(--clr-primary)' : 'var(--clr-text-tertiary)',
              strokeWidth: activeIdx === i ? '2.2' : '1.8',
              transform: activeIdx === i ? 'scale(1.1)' : 'scale(1)',
              transitionTimingFunction: 'var(--ease-spring)',
            }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon"/>
          </svg>

          <!-- Label -->
          <span
            class="text-[11px] font-medium transition-all duration-250"
            :style="{ color: activeIdx === i ? 'var(--clr-primary)' : 'var(--clr-text-tertiary)' }"
          >{{ tab.name }}</span>
        </button>
      </div>
    </nav>
  </div>
</template>
