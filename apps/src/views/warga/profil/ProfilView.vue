<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '../../../stores/authStore'
import { useRouter } from 'vue-router'
import { hapticSuccess, hapticError } from '../../../api/native'
import AppButton from '../../../components/AppButton.vue'

const auth   = useAuthStore()
const router = useRouter()

const showPinModal = ref(false)
const showBioModal = ref(false)

const pinForm = ref({ pin: '', confirm: '' })
const pinError = ref('')
const pinSuccess = ref('')
const pinSubmitting = ref(false)

const bioSubmitting = ref(false)
const bioError = ref('')
const bioSuccess = ref('')

const initials = computed(() => {
  const name = auth.user?.nama_lengkap ?? ''
  return name.split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase() || '?'
})

const infoRows = computed(() => [
  { label: 'Nama lengkap', value: auth.user?.nama_lengkap ?? '—' },
  { label: 'NIK',          value: auth.user?.nik ?? '—' },
  { label: 'No. KK',       value: auth.user?.no_kk ?? '—' },
  { label: 'Status PIN',   value: auth.hasPin ? 'Aktif (PIN 6-Digit)' : 'Belum Diaktifkan' },
  { label: 'Sidik Jari',   value: auth.hasBiometric ? 'Aktif (Terhubung)' : 'Belum Diaktifkan' },
])

async function handleSetPin() {
  pinError.value = ''
  pinSuccess.value = ''
  if (pinForm.value.pin.length !== 6) {
    pinError.value = 'PIN harus 6 digit angka'
    return
  }
  if (pinForm.value.pin !== pinForm.value.confirm) {
    pinError.value = 'Konfirmasi PIN tidak cocok'
    return
  }

  pinSubmitting.value = true
  try {
    await auth.registerPin(auth.user?.nik ?? '', auth.user?.no_kk ?? '', pinForm.value.pin)
    hapticSuccess()
    pinSuccess.value = 'PIN 6-Digit berhasil disimpan! Anda sekarang dapat login menggunakan NIK + PIN.'
    setTimeout(() => {
      showPinModal.value = false
      pinForm.value = { pin: '', confirm: '' }
    }, 1500)
  } catch (e: any) {
    hapticError()
    pinError.value = e.response?.data?.message ?? e.message ?? 'Gagal mendaftarkan PIN'
  } finally {
    pinSubmitting.value = false
  }
}

async function handleActivateBiometric() {
  bioError.value = ''
  bioSuccess.value = ''
  bioSubmitting.value = true
  try {
    // Generasi kunci biometrik unik untuk perangkat ini
    const key = 'bio_' + auth.user?.nik + '_' + Math.random().toString(36).substring(2, 12)
    await auth.registerBiometric(key)
    hapticSuccess()
    bioSuccess.value = 'Sensor Sidik Jari berhasil dihubungkan dengan perangkat ini!'
    setTimeout(() => {
      showBioModal.value = false
    }, 1500)
  } catch (e: any) {
    hapticError()
    bioError.value = e.response?.data?.message ?? e.message ?? 'Gagal mengaktifkan sidik jari'
  } finally {
    bioSubmitting.value = false
  }
}

function logout() {
  auth.logout()
  router.push('/auth/login-warga')
}
</script>

<template>
  <div class="space-y-4 max-w-xl mx-auto">

    <!-- Profile hero card -->
    <div class="rounded-2xl overflow-hidden relative" style="min-height: 120px;">
      <div class="hero-gradient-green absolute inset-0" />
      <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white opacity-[0.05]" />
      <div class="relative z-10 p-5 flex items-center gap-4">
        <div
          class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold text-white shrink-0"
          style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);"
        >{{ initials }}</div>
        <div class="min-w-0">
          <p class="text-white/60 text-[11px] font-medium mb-0.5">Profil warga</p>
          <h2 class="text-white text-[17px] font-bold truncate">{{ auth.user?.nama_lengkap ?? 'Warga' }}</h2>
          <p class="text-white/50 text-[11px] font-medium mt-1 font-mono">NIK: {{ auth.user?.nik ?? '—' }}</p>
        </div>
      </div>
    </div>

    <!-- Info rows -->
    <div class="rounded-xl overflow-hidden" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
      <div class="px-4 py-3 border-b" style="border-color: var(--clr-border-light);">
        <p class="text-[12px] font-semibold" style="color: var(--clr-text-tertiary);">Informasi akun & keamanan</p>
      </div>
      <div>
        <div
          v-for="row in infoRows"
          :key="row.label"
          class="flex items-center justify-between px-4 py-3 border-b last:border-0"
          style="border-color: var(--clr-border-light);"
        >
          <span class="text-[14px]" style="color: var(--clr-text-secondary);">{{ row.label }}</span>
          <span
            class="text-[14px] font-semibold capitalize ml-4 text-right truncate"
            style="color: var(--clr-text); max-width: 55%;"
          >{{ row.value }}</span>
        </div>
      </div>
    </div>

    <!-- Security Settings (Aktivasi PIN & Sidik Jari) -->
    <div class="rounded-xl overflow-hidden" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
      <div class="px-4 py-3 border-b" style="border-color: var(--clr-border-light);">
        <p class="text-[12px] font-semibold" style="color: var(--clr-text-tertiary);">Pengaturan Autentikasi Cepat</p>
      </div>

      <!-- Set PIN 6-Digit Button -->
      <button
        @click="showPinModal = true"
        class="w-full row-item text-left border-b press transition-colors"
        style="border-color: var(--clr-border-light);"
      >
        <div class="icon-box-sm" style="background: var(--clr-primary-bg);">
          <svg class="w-4.5 h-4.5" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[14px] font-semibold" style="color: var(--clr-text);">
            {{ auth.hasPin ? 'Ubah PIN 6-Digit' : 'Aktifkan PIN 6-Digit' }}
          </p>
          <p class="text-[12px]" style="color: var(--clr-text-tertiary);">Login cepat tanpa ngetik Nomor KK</p>
        </div>
        <span class="badge" :class="auth.hasPin ? 'badge-primary' : ''" style="border: 1px solid var(--clr-border);">
          {{ auth.hasPin ? 'Aktif' : 'Setel' }}
        </span>
      </button>

      <!-- Set Biometric Button -->
      <button
        @click="showBioModal = true"
        class="w-full row-item text-left press transition-colors"
      >
        <div class="icon-box-sm" style="background: var(--clr-tertiary-bg);">
          <svg class="w-4.5 h-4.5" style="color: var(--clr-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11c0 2.473.345 4.866.99 7.132" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[14px] font-semibold" style="color: var(--clr-text);">
            {{ auth.hasBiometric ? 'Sidik Jari Terhubung' : 'Aktifkan Login Sidik Jari' }}
          </p>
          <p class="text-[12px]" style="color: var(--clr-text-tertiary);">1-Tap Login via TouchID / FaceID HP</p>
        </div>
        <span class="badge" :class="auth.hasBiometric ? 'badge-tertiary' : ''" style="border: 1px solid var(--clr-border);">
          {{ auth.hasBiometric ? 'Aktif' : 'Hubungkan' }}
        </span>
      </button>
    </div>

    <!-- Menus -->
    <div class="rounded-xl overflow-hidden" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
      <div class="px-4 py-3 border-b" style="border-color: var(--clr-border-light);">
        <p class="text-[12px] font-semibold" style="color: var(--clr-text-tertiary);">Layanan lainnya</p>
      </div>
      <button
        @click="router.push('/warga/keluarga')"
        class="w-full row-item text-left border-b press"
        style="border-color: var(--clr-border-light);"
      >
        <div class="icon-box-sm" style="background: var(--clr-primary-bg);">
          <svg class="w-4.5 h-4.5" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[14px] font-semibold" style="color: var(--clr-text);">Data Keluarga</p>
          <p class="text-[12px]" style="color: var(--clr-text-tertiary);">Anggota Kartu Keluarga</p>
        </div>
      </button>

      <button
        @click="router.push('/warga/statistik')"
        class="w-full row-item text-left press"
      >
        <div class="icon-box-sm" style="background: var(--clr-warning-bg);">
          <svg class="w-4.5 h-4.5" style="color: var(--clr-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[14px] font-semibold" style="color: var(--clr-text);">Statistik Desa</p>
          <p class="text-[12px]" style="color: var(--clr-text-tertiary);">Demografi kependudukan</p>
        </div>
      </button>
    </div>

    <!-- Logout -->
    <button
      class="w-full row-item rounded-xl press"
      style="background: var(--clr-error-bg); border: 1px solid var(--clr-border-light);"
      @click="logout"
    >
      <div class="icon-box-sm" style="background: rgba(220,38,38,0.1);">
        <svg class="w-4.5 h-4.5" style="color: var(--clr-error);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
      </div>
      <span class="text-[14px] font-semibold" style="color: var(--clr-error);">Keluar dari sesi</span>
    </button>

    <!-- Modal Set PIN -->
    <div
      v-if="showPinModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
      <div class="rounded-2xl p-6 w-full max-w-sm" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
        <h3 class="text-lg font-bold mb-1" style="color: var(--clr-text);">Buat PIN 6-Digit</h3>
        <p class="text-xs mb-4" style="color: var(--clr-text-tertiary);">Gunakan PIN 6-digit untuk login cepat tanpa mengetik Nomor KK lagi.</p>

        <div v-if="pinError" class="rounded-xl p-3 text-xs font-medium mb-3" style="background: var(--clr-error-bg); color: var(--clr-error-text);">
          {{ pinError }}
        </div>
        <div v-if="pinSuccess" class="rounded-xl p-3 text-xs font-medium mb-3" style="background: var(--clr-tertiary-bg); color: var(--clr-tertiary-text);">
          {{ pinSuccess }}
        </div>

        <div class="space-y-3">
          <div>
            <label class="input-label block mb-1">PIN 6-Digit Baru</label>
            <input
              v-model="pinForm.pin"
              type="password"
              maxlength="6"
              inputmode="numeric"
              placeholder="● ● ● ● ● ●"
              class="input-field text-center font-mono text-lg tracking-widest"
            />
          </div>

          <div>
            <label class="input-label block mb-1">Konfirmasi PIN Baru</label>
            <input
              v-model="pinForm.confirm"
              type="password"
              maxlength="6"
              inputmode="numeric"
              placeholder="● ● ● ● ● ●"
              class="input-field text-center font-mono text-lg tracking-widest"
            />
          </div>

          <div class="flex gap-2 pt-2">
            <AppButton @click="showPinModal = false" variant="ghost" class="flex-1">Batal</AppButton>
            <AppButton @click="handleSetPin" :loading="pinSubmitting" variant="primary" class="flex-1">Simpan PIN</AppButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Set Biometric -->
    <div
      v-if="showBioModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
      <div class="rounded-2xl p-6 w-full max-w-sm text-center" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
        <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: var(--clr-tertiary-bg);">
          <svg class="w-8 h-8" style="color: var(--clr-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11c0 2.473.345 4.866.99 7.132" />
          </svg>
        </div>
        <h3 class="text-lg font-bold mb-1" style="color: var(--clr-text);">Hubungkan Sidik Jari</h3>
        <p class="text-xs mb-5" style="color: var(--clr-text-tertiary);">Aktifkan TouchID / FaceID untuk login instan 1-Tap tanpa mengetik PIN.</p>

        <div v-if="bioError" class="rounded-xl p-3 text-xs font-medium mb-3 text-left" style="background: var(--clr-error-bg); color: var(--clr-error-text);">
          {{ bioError }}
        </div>
        <div v-if="bioSuccess" class="rounded-xl p-3 text-xs font-medium mb-3 text-left" style="background: var(--clr-tertiary-bg); color: var(--clr-tertiary-text);">
          {{ bioSuccess }}
        </div>

        <div class="flex gap-2">
          <AppButton @click="showBioModal = false" variant="ghost" class="flex-1">Nanti</AppButton>
          <AppButton @click="handleActivateBiometric" :loading="bioSubmitting" variant="tertiary" class="flex-1">Aktifkan Jari</AppButton>
        </div>
      </div>
    </div>

  </div>
</template>
