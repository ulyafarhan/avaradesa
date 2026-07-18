<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'

const router     = useRouter()
const auth       = useAuthStore()
const username   = ref('')
const password   = ref('')
const showPass   = ref(false)
const error      = ref('')
const submitting = ref(false)

async function submit() {
  error.value      = ''
  submitting.value = true
  try {
    await auth.loginAdmin(username.value, password.value)
    router.push('/admin/dashboard')
  } catch (e: any) {
    error.value = e.response?.data?.message ?? e.message ?? 'Username atau password salah'
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
    <div class="app-ambient" />

    <div class="flex-1 flex flex-col z-10 relative">

      <!-- Hero section (violet/secondary) -->
      <div class="hero-gradient-violet px-6 pt-16 pb-10 relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.05]" />
        <div class="relative z-10 max-w-screen-sm mx-auto">
          <div
            class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5"
            style="background: rgba(255,255,255,0.15); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);"
          >
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04
                       A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622
                       0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <h1 class="text-white text-2xl font-bold">Panel Administrator</h1>
          <p class="text-white/60 text-sm mt-2">AvaraDesa — akses terbatas</p>
        </div>
      </div>

      <!-- Form card -->
      <div
        class="flex-1 px-4 -mt-5 relative z-10 max-w-screen-sm mx-auto w-full"
        style="padding-bottom: 40px;"
      >
        <div
          class="rounded-2xl p-6"
          style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);
                 box-shadow: 0 4px 24px rgba(0,0,0,0.07);"
        >
          <p class="text-[15px] font-semibold mb-5" style="color: var(--clr-text);">
            Masuk ke panel administrasi
          </p>

          <form @submit.prevent="submit" class="space-y-4" novalidate>
            <!-- Username -->
            <div>
              <label for="username" class="input-label">Username</label>
              <input
                id="username"
                name="username"
                type="text"
                required
                autocomplete="username"
                v-model="username"
                class="input-field"
                placeholder="Username administrator"
              />
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="input-label">Kata sandi</label>
              <div class="relative">
                <input
                  id="password"
                  name="password"
                  :type="showPass ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  v-model="password"
                  class="input-field"
                  placeholder="••••••••"
                  style="padding-right: 48px;"
                />
                <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg press"
                  style="color: var(--clr-text-tertiary);"
                  @click="showPass = !showPass"
                  :aria-label="showPass ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                >
                  <!-- Eye / Eye-off icon -->
                  <svg v-if="!showPass" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                             a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
                             l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                             m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0
                             01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Error -->
            <div
              v-if="error"
              class="rounded-xl p-3 text-sm font-medium"
              style="background: var(--clr-error-bg); color: var(--clr-error-text);"
            >
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ error }}
              </div>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="submitting"
              class="btn btn-full mt-2"
              style="background: var(--clr-secondary); color: #fff; box-shadow: 0 4px 14px var(--clr-secondary-glow);"
            >
              <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              {{ submitting ? 'Memverifikasi...' : 'Masuk' }}
            </button>
          </form>
        </div>

        <!-- Back to warga -->
        <div class="text-center mt-5">
          <span class="text-sm" style="color: var(--clr-text-tertiary);">Bukan administrator? </span>
          <router-link
            to="/auth/login-warga"
            class="text-sm font-semibold"
            style="color: var(--clr-secondary);"
          >Masuk sebagai warga</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
