import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAppStore = defineStore('app', () => {
  const isOnline = ref(navigator.onLine)
  const isDark = ref(false)
  const sidebarOpen = ref(false)

  const themeClass = computed(() => (isDark.value ? 'dark' : 'light'))

  function toggleDark() {
    isDark.value = !isDark.value
    document.documentElement.classList.toggle('dark', isDark.value)
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  }

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function initTheme() {
    const saved = localStorage.getItem('theme')
    isDark.value = saved === 'dark'
    document.documentElement.classList.toggle('dark', isDark.value)
  }

  window.addEventListener('online', () => { isOnline.value = true })
  window.addEventListener('offline', () => { isOnline.value = false })

  return { isOnline, isDark, sidebarOpen, themeClass, toggleDark, toggleSidebar, initTheme }
})
