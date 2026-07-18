<script setup lang="ts">
import { ref } from 'vue'

// Baca preferensi awal dari localStorage
const saved  = localStorage.getItem('theme')
const isDark = ref(
  saved === 'dark'
    ? true
    : saved === 'light'
      ? false
      : window.matchMedia('(prefers-color-scheme: dark)').matches
)

// Terapkan segera
document.documentElement.classList.toggle('dark', isDark.value)

function toggle() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}
</script>

<template>
  <button
    @click="toggle"
    class="w-9 h-9 flex items-center justify-center rounded-xl press transition-colors"
    :style="{
      background: isDark ? 'var(--clr-primary-bg)' : 'var(--clr-surface-dim)',
      color: isDark ? 'var(--clr-primary)' : 'var(--clr-text-secondary)',
    }"
    :aria-label="isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
    :title="isDark ? 'Mode terang' : 'Mode gelap'"
  >
    <!-- Sun (light mode) -->
    <svg v-if="!isDark" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707
               m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>
    <!-- Moon (dark mode) -->
    <svg v-else class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
  </button>
</template>
