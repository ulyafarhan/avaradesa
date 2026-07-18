import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../api/client'
import { endpoints } from '../api/endpoints'
import type { User, AuthResponse } from '../api/types'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(null)
  const user = ref<User | null>(null)
  const loading = ref(false)
  const hasPin = ref(false)
  const hasBiometric = ref(false)

  const isLoggedIn = computed(() => !!token.value)
  const role = computed(() => user.value?.role ?? null)
  const isAdmin = computed(() => role.value === 'admin')

  async function loginWarga(nik: string, no_kk: string) {
    loading.value = true
    try {
      const res: any = await api.post<AuthResponse>(endpoints.auth.loginWarga, { nik, no_kk })
      token.value = res.token
      user.value = { ...res.user, role: 'warga' }
      hasPin.value = !!res.has_pin
      hasBiometric.value = !!res.has_biometric
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

  async function registerPin(nik: string, no_kk: string, pin: string) {
    const res: any = await api.post(endpoints.auth.registerPin, { nik, no_kk, pin, pin_confirmation: pin })
    hasPin.value = true
    localStorage.setItem('last_nik', nik)
    return res
  }

  async function loginPin(nik: string, pin: string) {
    loading.value = true
    try {
      const res: any = await api.post(endpoints.auth.loginPin, { nik, pin })
      token.value = res.token ?? res.data?.token
      user.value = { ...(res.user ?? res.data?.user), role: 'warga' }
      hasPin.value = true
      hasBiometric.value = !!res.has_biometric
      localStorage.setItem('last_nik', nik)
      persist()
    } finally {
      loading.value = false
    }
  }

  async function registerBiometric(biometricKey: string) {
    const res: any = await api.post(endpoints.auth.registerBiometric, { biometric_key: biometricKey })
    hasBiometric.value = true
    if (user.value?.nik) {
      localStorage.setItem(`bio_key_${user.value.nik}`, biometricKey)
    }
    return res
  }

  async function loginBiometric(nik: string, biometricKey: string) {
    loading.value = true
    try {
      const res: any = await api.post(endpoints.auth.loginBiometric, { nik, biometric_key: biometricKey })
      token.value = res.token ?? res.data?.token
      user.value = { ...(res.user ?? res.data?.user), role: 'warga' }
      hasBiometric.value = true
      localStorage.setItem('last_nik', nik)
      persist()
    } finally {
      loading.value = false
    }
  }

  async function resetPin(nik: string, no_kk: string, pin: string) {
    return api.post(endpoints.auth.resetPin, { nik, no_kk, pin, pin_confirmation: pin })
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      const res: any = await api.get(endpoints.auth.me)
      user.value = res.user ?? res.data ?? res
      hasPin.value = !!res.has_pin
      hasBiometric.value = !!res.has_biometric
    } catch {
      logout()
    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('auth')
  }

  function persist() {
    localStorage.setItem('auth', JSON.stringify({
      state: { token: token.value, user: user.value, hasPin: hasPin.value, hasBiometric: hasBiometric.value }
    }))
  }

  function hydrate() {
    try {
      const raw = localStorage.getItem('auth')
      if (!raw) return
      const parsed = JSON.parse(raw)
      token.value = parsed.state?.token ?? null
      user.value = parsed.state?.user ?? null
      hasPin.value = parsed.state?.hasPin ?? false
      hasBiometric.value = parsed.state?.hasBiometric ?? false
    } catch {
      localStorage.removeItem('auth')
    }
  }

  hydrate()

  return {
    token, user, loading, hasPin, hasBiometric, isLoggedIn, role, isAdmin,
    loginWarga, loginAdmin, registerPin, loginPin, registerBiometric, loginBiometric, resetPin, fetchMe, logout
  }
})
