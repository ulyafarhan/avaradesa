<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import AppButton from '../../components/AppButton.vue'
import FormInput from '../../components/FormInput.vue'

const router = useRouter()
const auth = useAuthStore()
const pin = ref('')
const confirmPin = ref('')
const nik = ref('')
const no_kk = ref('')
const step = ref<'register' | 'login' | 'done'>('register')
const error = ref('')

async function register() {
  error.value = ''
  if (pin.value.length < 4) { error.value = 'PIN minimal 4 digit'; return }
  if (pin.value !== confirmPin.value) { error.value = 'PIN tidak cocok'; return }
  try {
    await auth.registerPin(nik.value, no_kk.value, pin.value)
    step.value = 'login'
  } catch (e: any) {
    error.value = e.message ?? 'Gagal daftar PIN'
  }
}

async function login() {
  error.value = ''
  try {
    await auth.loginPin(nik.value, pin.value)
    router.push('/warga/dashboard')
  } catch (e: any) {
    error.value = e.message ?? 'PIN salah'
  }
}
</script>

<template>
  <!-- ponytail: full screen layout with orange-amber themed ambient spin -->
  <div class="min-h-screen flex items-center justify-center bg-[var(--ne-background)] p-4 relative overflow-hidden ne-glow-ambient">
    <div class="w-full max-w-md ne-glass border border-[var(--ne-outline-variant)] rounded-3xl shadow-xl p-8 z-10">
      <div class="text-center mb-8">
        <!-- ponytail: active circle icon with spring scale click feedback -->
        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4 ne-spring-press active:scale-[0.9]">
          <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-[var(--ne-on-background)] tracking-tight">PIN Setup</h1>
        <p class="text-[var(--ne-on-surface-variant)] mt-1.5 text-sm">
          {{ step === 'register' ? 'Buat PIN untuk akses cepat masuk' : 'Masukkan PIN terdaftar Anda' }}
        </p>
      </div>

      <form @submit.prevent="step === 'register' ? register() : login()" class="space-y-5">
        <FormInput v-model="nik" label="NIK" maxlength="16" placeholder="Masukkan NIK" inputmode="numeric" required />
        <FormInput v-if="step === 'register'" v-model="no_kk" label="Nomor KK" maxlength="16" placeholder="Masukkan No. KK" inputmode="numeric" required />
        <FormInput v-model="pin" label="PIN" type="password" maxlength="6" placeholder="Masukkan PIN" inputmode="numeric" required />
        <FormInput v-if="step === 'register'" v-model="confirmPin" label="Konfirmasi PIN" type="password" maxlength="6" placeholder="Ulangi PIN" required />

        <p v-if="error" class="text-[var(--ne-error)] text-sm text-center font-medium bg-[var(--ne-error-container)]/10 p-2.5 rounded-xl">{{ error }}</p>

        <AppButton type="submit" variant="expressive" class="w-full" :loading="auth.loading">
          {{ step === 'register' ? 'Daftarkan PIN' : 'Masuk dengan PIN' }}
        </AppButton>
      </form>
    </div>
  </div>
</template>
