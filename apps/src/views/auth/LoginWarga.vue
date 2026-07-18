<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { hapticSuccess, hapticError } from '../../api/native'
import AppButton from '../../components/AppButton.vue'

const router = useRouter()
const auth   = useAuthStore()

// Modes: 'pin' | 'biometric' | 'kk' | 'reset'
const mode = ref<'pin' | 'biometric' | 'kk' | 'reset'>('kk')

const nik = ref('')
const no_kk = ref('')
const pin = ref('')
const resetForm = ref({ nik: '', no_kk: '', pin: '', confirm: '' })

const error = ref('')
const successMsg = ref('')
const submitting = ref(false)

onMounted(() => {
  const lastNik = localStorage.getItem('last_nik')
  if (lastNik) {
    nik.value = lastNik
    resetForm.value.nik = lastNik
    mode.value = 'pin'
  }
})

async function submitKK() {
  error.value = ''
  submitting.value = true
  try {
    await auth.loginWarga(nik.value, no_kk.value)
    hapticSuccess()
    router.push('/warga/dashboard')
  } catch (e: any) {
    hapticError()
    error.value = e.response?.data?.message ?? e.message ?? 'NIK atau No. KK tidak sesuai'
  } finally {
    submitting.value = false
  }
}

async function submitPIN() {
  error.value = ''
  submitting.value = true
  try {
    await auth.loginPin(nik.value, pin.value)
    hapticSuccess()
    router.push('/warga/dashboard')
  } catch (e: any) {
    hapticError()
    error.value = e.response?.data?.message ?? e.message ?? 'PIN salah atau belum didaftarkan'
  } finally {
    submitting.value = false
  }
}

async function submitBiometric() {
  error.value = ''
  submitting.value = true
  try {
    const bioKey = localStorage.getItem(`bio_key_${nik.value}`) ?? 'mock_bio_key'
    await auth.loginBiometric(nik.value, bioKey)
    hapticSuccess()
    router.push('/warga/dashboard')
  } catch (e: any) {
    hapticError()
    error.value = e.response?.data?.message ?? e.message ?? 'Sidik jari tidak dikenali'
  } finally {
    submitting.value = false
  }
}

async function submitResetPIN() {
  error.value = ''
  successMsg.value = ''
  if (resetForm.value.pin.length !== 6) {
    error.value = 'PIN harus 6 digit angka'
    return
  }
  if (resetForm.value.pin !== resetForm.value.confirm) {
    error.value = 'Konfirmasi PIN tidak cocok'
    return
  }

  submitting.value = true
  try {
    await auth.resetPin(resetForm.value.nik, resetForm.value.no_kk, resetForm.value.pin)
    hapticSuccess()
    successMsg.value = 'PIN berhasil di-reset! Silakan login dengan PIN baru.'
    setTimeout(() => {
      nik.value = resetForm.value.nik
      mode.value = 'pin'
      successMsg.value = ''
    }, 1500)
  } catch (e: any) {
    hapticError()
    error.value = e.response?.data?.message ?? e.message ?? 'Verifikasi data kependudukan gagal'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div
    class="min-h-screen flex flex-col relative overflow-hidden"
    style="background: var(--clr-bg);"
  >
    <!-- Ambient bg -->
    <div class="app-ambient" />

    <!-- Scrollable content -->
    <div class="flex-1 flex flex-col z-10 relative">

      <!-- Hero header -->
      <div class="hero-gradient-blue px-6 pt-14 pb-9 relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white opacity-[0.06]" />
        <div class="relative z-10 max-w-screen-sm mx-auto">
          <div
            class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
            style="background: rgba(255,255,255,0.15); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);"
          >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h1 class="text-white text-2xl font-bold leading-tight">Selamat datang<br>di AvaraDesa</h1>
          <p class="text-white/60 text-xs mt-1">Layanan mandiri warga desa digital</p>
        </div>
      </div>

      <!-- Form Card -->
      <div class="flex-1 px-4 -mt-4 relative z-10 max-w-screen-sm mx-auto w-full pb-10">
        <div
          class="rounded-2xl p-5 sm:p-6"
          style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); box-shadow: 0 4px 24px rgba(0,0,0,0.07);"
        >
          <!-- Mode Tabs -->
          <div class="flex rounded-xl p-1 mb-5" style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);">
            <button
              @click="mode = 'pin'; error = ''"
              class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
              :style="mode === 'pin' ? 'background: var(--clr-surface); color: var(--clr-primary); box-shadow: 0 2px 6px rgba(0,0,0,0.1);' : 'color: var(--clr-text-tertiary);'"
            >
              PIN 6-Digit
            </button>
            <button
              @click="mode = 'biometric'; error = ''"
              class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
              :style="mode === 'biometric' ? 'background: var(--clr-surface); color: var(--clr-tertiary); box-shadow: 0 2px 6px rgba(0,0,0,0.1);' : 'color: var(--clr-text-tertiary);'"
            >
              Sidik Jari
            </button>
            <button
              @click="mode = 'kk'; error = ''"
              class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
              :style="mode === 'kk' ? 'background: var(--clr-surface); color: var(--clr-primary); box-shadow: 0 2px 6px rgba(0,0,0,0.1);' : 'color: var(--clr-text-tertiary);'"
            >
              NIK & No. KK
            </button>
          </div>

          <!-- Error Alert -->
          <div
            v-if="error"
            class="rounded-xl p-3 text-xs font-medium mb-4"
            style="background: var(--clr-error-bg); color: var(--clr-error-text);"
          >
            {{ error }}
          </div>

          <!-- Success Alert -->
          <div
            v-if="successMsg"
            class="rounded-xl p-3 text-xs font-medium mb-4"
            style="background: var(--clr-tertiary-bg); color: var(--clr-tertiary-text);"
          >
            {{ successMsg }}
          </div>

          <!-- Mode 1: PIN 6-Digit (Login Cepat) -->
          <form v-if="mode === 'pin'" @submit.prevent="submitPIN" class="space-y-4">
            <div>
              <label class="input-label block mb-1">NIK (Nomor Induk Kependudukan)</label>
              <input
                v-model="nik"
                type="tel"
                inputmode="numeric"
                maxlength="16"
                required
                class="input-field font-mono"
                placeholder="Masukkan 16 digit NIK"
              />
            </div>

            <div>
              <div class="flex justify-between items-center mb-1">
                <label class="input-label">PIN 6-Digit</label>
                <button
                  type="button"
                  @click="mode = 'reset'; error = ''"
                  class="text-[11px] font-semibold"
                  style="color: var(--clr-primary);"
                >Lupa PIN?</button>
              </div>
              <input
                v-model="pin"
                type="password"
                inputmode="numeric"
                maxlength="6"
                required
                class="input-field font-mono text-center text-xl tracking-widest"
                placeholder="● ● ● ● ● ●"
              />
            </div>

            <AppButton type="submit" :loading="submitting" variant="primary" class="w-full">
              Masuk dengan PIN
            </AppButton>
          </form>

          <!-- Mode 2: Biometric Sidik Jari (1-Tap) -->
          <div v-else-if="mode === 'biometric'" class="text-center py-4 space-y-4">
            <div
              @click="submitBiometric"
              class="w-20 h-20 rounded-full mx-auto flex items-center justify-center press cursor-pointer transition-transform hover:scale-105"
              style="background: var(--clr-tertiary-bg); border: 2px solid var(--clr-tertiary);"
            >
              <svg class="w-10 h-10" style="color: var(--clr-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11c0 2.473.345 4.866.99 7.132" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-bold" style="color: var(--clr-text);">Tempel Sidik Jari Anda</p>
              <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Sentuh tombol di atas atau sensor HP Anda</p>
            </div>
            <div class="max-w-xs mx-auto">
              <input
                v-model="nik"
                type="tel"
                inputmode="numeric"
                maxlength="16"
                class="input-field text-center font-mono text-xs"
                placeholder="NIK: 16 digit"
              />
            </div>
            <AppButton @click="submitBiometric" :loading="submitting" variant="tertiary" class="w-full">
              Masuk via Sidik Jari
            </AppButton>
          </div>

          <!-- Mode 3: NIK & No. KK (First Time / Regular) -->
          <form v-else-if="mode === 'kk'" @submit.prevent="submitKK" class="space-y-4">
            <div>
              <label class="input-label block mb-1">NIK (Nomor Induk Kependudukan)</label>
              <input
                v-model="nik"
                type="tel"
                inputmode="numeric"
                maxlength="16"
                required
                class="input-field font-mono"
                placeholder="Masukkan 16 digit NIK"
              />
            </div>

            <div>
              <label class="input-label block mb-1">Nomor Kartu Keluarga (No. KK)</label>
              <input
                v-model="no_kk"
                type="tel"
                inputmode="numeric"
                maxlength="16"
                required
                class="input-field font-mono"
                placeholder="Masukkan 16 digit No. KK"
              />
            </div>

            <AppButton type="submit" :loading="submitting" variant="primary" class="w-full">
              Masuk dengan NIK & KK
            </AppButton>
          </form>

          <!-- Mode 4: Reset PIN (Lupa PIN) -->
          <form v-else-if="mode === 'reset'" @submit.prevent="submitResetPIN" class="space-y-3">
            <h3 class="text-sm font-bold mb-1" style="color: var(--clr-text);">Reset PIN 6-Digit</h3>
            <p class="text-xs text-secondary mb-3">Verifikasi ulang NIK dan No. KK Anda untuk membuat PIN baru.</p>

            <div>
              <label class="input-label block mb-1">NIK</label>
              <input v-model="resetForm.nik" type="tel" maxlength="16" required class="input-field font-mono" />
            </div>

            <div>
              <label class="input-label block mb-1">No. KK</label>
              <input v-model="resetForm.no_kk" type="tel" maxlength="16" required class="input-field font-mono" />
            </div>

            <div>
              <label class="input-label block mb-1">PIN Baru (6 Digit)</label>
              <input v-model="resetForm.pin" type="password" maxlength="6" inputmode="numeric" required class="input-field font-mono text-center tracking-widest" />
            </div>

            <div>
              <label class="input-label block mb-1">Konfirmasi PIN Baru</label>
              <input v-model="resetForm.confirm" type="password" maxlength="6" inputmode="numeric" required class="input-field font-mono text-center tracking-widest" />
            </div>

            <div class="flex gap-2 pt-2">
              <AppButton type="button" @click="mode = 'pin'; error = ''" variant="ghost" class="flex-1">Batal</AppButton>
              <AppButton type="submit" :loading="submitting" variant="primary" class="flex-1">Simpan PIN</AppButton>
            </div>
          </form>
        </div>

        <!-- Link Admin -->
        <div class="text-center mt-4">
          <span class="text-xs" style="color: var(--clr-text-tertiary);">Bukan warga? </span>
          <router-link to="/auth/login-admin" class="text-xs font-semibold" style="color: var(--clr-primary);">
            Masuk sebagai administrator
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
