<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useAppStore } from '../stores/appStore'
import DarkModeToggle from '../components/DarkModeToggle.vue'
import { hapticLight } from '../api/native'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const app    = useAppStore()

const menu = [
  { name: 'Dashboard',      path: '/admin/dashboard',      icon: 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10' },
  { name: 'Penduduk',       path: '/admin/penduduk',       icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { name: 'Kartu Keluarga', path: '/admin/keluarga',       icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
  { name: 'Surat Warga',    path: '/admin/surat',          icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
  { name: 'Kategori Surat', path: '/admin/kategori-surat', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { name: 'Mutasi Penduduk',path: '/admin/mutasi',         icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
  { name: 'Informasi Desa', path: '/admin/informasi',      icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z' },
  { name: 'Fasilitas Desa', path: '/admin/fasilitas',      icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-4-8a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z' },
  { name: 'Statistik Desa', path: '/admin/statistik',      icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { name: 'Audit Log',      path: '/admin/audit-log',      icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
]

// 4 Menu Utama untuk Bottom Nav Mobile
const bottomNavItems = menu.slice(0, 4)

const active = computed(() => menu.findIndex(m => route.path.startsWith(m.path)))

const activeTitle = computed(() => {
  const current = menu.find(m => route.path.startsWith(m.path))
  return current?.name ?? 'Admin Portal'
})

function logout() {
  auth.logout()
  router.push('/auth/login-admin')
}

function navigate(path: string) {
  hapticLight()
  router.push(path)
  app.sidebarOpen = false
}
</script>

<template>
  <div class="h-screen w-full flex overflow-hidden relative" style="background: var(--clr-bg); color: var(--clr-text);">
    <div class="app-ambient" />

    <!-- Backdrop for mobile drawer -->
    <div
      v-if="app.sidebarOpen"
      @click="app.sidebarOpen = false"
      class="fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden"
    />

    <!-- Sidebar (Permanent on Desktop, Slide-over Drawer on Mobile) -->
    <aside
      class="w-64 flex flex-col shrink-0 transition-transform duration-300 z-50 h-full fixed md:relative top-0 bottom-0 left-0"
      :class="app.sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
      style="background: var(--clr-surface); border-right: 1px solid var(--clr-border-light);"
    >
      <!-- Fixed Logo Header -->
      <div class="h-16 flex items-center justify-between px-5 border-b shrink-0" style="border-color: var(--clr-border-light);">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-sm" style="background: linear-gradient(135deg, var(--clr-primary), var(--clr-secondary));">
            AD
          </div>
          <div class="min-w-0">
            <h1 class="font-bold text-sm leading-tight" style="color: var(--clr-text);">AvaraDesa</h1>
          </div>
        </div>
        <button
          @click="app.sidebarOpen = false"
          class="md:hidden p-1.5 rounded-lg press"
          style="color: var(--clr-text-tertiary);"
          title="Tutup Menu"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Nav Links (Scrollable Middle) -->
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <button
          v-for="(item, i) in menu"
          :key="item.name"
          @click="navigate(item.path)"
          class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all press text-left"
          :style="active === i
            ? 'background: var(--clr-primary-bg); color: var(--clr-primary); font-weight: 700;'
            : 'color: var(--clr-text-secondary);'"
        >
          <svg class="w-4.5 h-4.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
          </svg>
          <span class="truncate">{{ item.name }}</span>
        </button>
      </nav>

      <!-- Fixed User Info & Logout Footer -->
      <div class="p-3.5 border-t flex items-center justify-between gap-2 shrink-0" style="border-color: var(--clr-border-light); background: var(--clr-surface-dim);">
        <div class="min-w-0 flex-1">
          <p class="text-xs font-bold truncate" style="color: var(--clr-text);">{{ auth.user?.nama_lengkap ?? 'Administrator' }}</p>
          <p class="text-[10px] font-medium truncate" style="color: var(--clr-text-tertiary);">Perangkat Desa</p>
        </div>
        <button
          @click="logout"
          class="p-2 rounded-xl text-xs font-medium press shrink-0 transition-colors"
          style="background: var(--clr-error-bg); color: var(--clr-error-text);"
          title="Keluar"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden relative">
      <!-- Admin Desktop Top Bar -->
      <header
        class="h-16 flex items-center px-4 md:px-6 gap-3 shrink-0 z-20 justify-between"
        style="background: var(--clr-surface-overlay); backdrop-filter: blur(16px); border-bottom: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3">
          <div class="hidden md:flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
            <span class="font-semibold" style="color: var(--clr-text-secondary);">AvaraDesa</span>
            <span>/</span>
            <span class="font-bold text-sm" style="color: var(--clr-text);">{{ activeTitle }}</span>
          </div>

          <div class="md:hidden flex items-center gap-2">
            <span class="font-bold text-base" style="color: var(--clr-text);">AvaraDesa</span>
          </div>
        </div>

        <div class="ml-auto flex items-center gap-3">
          <DarkModeToggle />
        </div>
      </header>

      <!-- Main Body Content (Scrollable Container) -->
      <main class="flex-1 p-4 md:p-6 pb-20 md:pb-6 overflow-y-auto">
        <router-view v-slot="{ Component }">
          <transition name="page" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

      <!-- ── MOBILE BOTTOM NAVIGATION BAR (Khusus Admin pada layar HP < md) ── -->
      <nav
        class="md:hidden fixed bottom-0 left-0 right-0 z-30 h-16 flex items-center justify-around px-2 border-t backdrop-blur-lg"
        style="background: var(--clr-surface-overlay); border-color: var(--clr-border-light); box-shadow: 0 -4px 16px rgba(0,0,0,0.1);"
      >
        <button
          v-for="(item, i) in bottomNavItems"
          :key="item.name"
          @click="navigate(item.path)"
          class="flex flex-col items-center justify-center flex-1 py-1 press transition-colors"
          :style="active === i ? 'color: var(--clr-primary);' : 'color: var(--clr-text-tertiary);'"
        >
          <div
            class="p-1 rounded-xl transition-all"
            :style="active === i ? 'background: var(--clr-primary-bg);' : ''"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
            </svg>
          </div>
          <span class="text-[10px] font-semibold mt-0.5" :style="active === i ? 'font-weight: 700;' : ''">{{ item.name }}</span>
        </button>

        <button
          @click="hapticLight(); app.sidebarOpen = !app.sidebarOpen"
          class="flex flex-col items-center justify-center flex-1 py-1 press transition-colors"
          :style="app.sidebarOpen ? 'color: var(--clr-primary);' : 'color: var(--clr-text-tertiary);'"
        >
          <div
            class="p-1 rounded-xl transition-all"
            :style="app.sidebarOpen ? 'background: var(--clr-primary-bg);' : ''"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </div>
          <span class="text-[10px] font-semibold mt-0.5">Lainnya</span>
        </button>
      </nav>
    </div>
  </div>
</template>
