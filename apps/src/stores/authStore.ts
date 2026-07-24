import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../api/client'
import { endpoints } from '../api/endpoints'
import type { User, AuthResponse } from '../api/types'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(null)
  const user = ref<User | null>(null)
  const loading = ref(false)

  const isLoggedIn = computed(() => !!token.value)
  const role = computed(() => user.value?.role ?? null)
  const isAdmin = computed(() => role.value === 'admin')

  async function loginWarga(nik: string, no_kk: string) {
    loading.value = true
    try {
      const res: any = await api.post<AuthResponse>(endpoints.auth.loginWarga, { nik, no_kk })
      token.value = res.token
      user.value = { ...res.user, role: 'warga' }
      // Simpan NIK terakhir untuk kemudahan login cepat
      localStorage.setItem('last_nik', nik)
      persist()
    } finally {
      loading.value = false
    }
  }

  async function loginAdmin(username: string, password: string) {
    loading.value = true
    try {
      const res: any = await api.post<AuthResponse>(endpoints.auth.loginAdmin, { username, password })
      token.value = res.token
      user.value = { ...res.user, role: 'admin' }
      persist()
    } finally {
      loading.value = false
    }
  }



  async function fetchMe() {
    if (!token.value) return
    try {
      const res: any = await api.get(endpoints.auth.me)
      user.value = res.user ?? res.data ?? res
    } catch {
      logout()
    }
  }

  async function loginPin(nik: string, pin: string) {
    loading.value = true
    try {
      const res: any = await api.post(endpoints.auth.loginPin, { nik, pin })
      token.value = res.token
      user.value = { ...res.user, role: 'warga' }
      persist()
    } finally {
      loading.value = false
    }
  }

  async function loginBiometric(nik: string, bioKey: string) {
    loading.value = true
    try {
      const res: any = await api.post(endpoints.auth.loginBiometric, { nik, biometric_key: bioKey })
      token.value = res.token
      user.value = { ...res.user, role: 'warga' }
      persist()
    } finally {
      loading.value = false
    }
  }

  async function registerPin(nik: string, no_kk: string, pin: string) {
    await api.post(endpoints.auth.registerPin, { nik, no_kk, pin, pin_confirmation: pin })
    user.value = { ...user.value!, has_pin: true } as any
  }

  async function resetPin(nik: string, no_kk: string, pin: string) {
    await api.post(endpoints.auth.resetPin, { nik, no_kk, pin, pin_confirmation: pin })
  }

  async function registerBiometric(bioKey: string) {
    await api.post(endpoints.auth.registerBiometric, { biometric_key: bioKey })
    user.value = { ...user.value!, has_biometric: true } as any
  }

  const hasPin = computed(() => !!(user.value as any)?.has_pin)
  const hasBiometric = computed(() => !!(user.value as any)?.has_biometric)

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('auth')
  }

  function persist() {
    localStorage.setItem('auth', JSON.stringify({
      state: { token: token.value, user: user.value }
    }))
  }

  function hydrate() {
    try {
      const raw = localStorage.getItem('auth')
      if (!raw) return
      const parsed = JSON.parse(raw)
      token.value = parsed.state?.token ?? null
      user.value = parsed.state?.user ?? null
    } catch {
      localStorage.removeItem('auth')
    }
  }

  hydrate()

  return {
    token, user, loading, isLoggedIn, role, isAdmin, hasPin, hasBiometric,
    loginWarga, loginAdmin, loginPin, loginBiometric,
    registerPin, resetPin, registerBiometric,
    fetchMe, logout
  }
})
