<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/authStore'
import { api } from '../../../api/client'

const router  = useRouter()
const auth    = useAuthStore()
const anggota = ref(auth.user?.keluarga ?? [])
const loading = ref(false)
const error   = ref('')

onMounted(async () => {
  if (anggota.value.length > 0) return
  loading.value = true
  try {
    const res: any = await api.get<any>('/api/v1/dashboard')
    anggota.value = res.data?.anggotaKeluarga ?? res.anggotaKeluarga ?? []
  } catch {
    error.value = 'Gagal memuat data keluarga'
  } finally {
    loading.value = false
  }
})

function initials(name: string): string {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase() || '?'
}
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/warga/profil" class="hover:text-[var(--clr-primary)] transition-colors">Profil</router-link>
    </div>

    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Anggota Keluarga</h2>
      <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">No. KK: {{ auth.user?.no_kk ?? '—' }}</p>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-20 rounded-xl" />
    </div>

    <!-- Empty state -->
    <div v-else-if="anggota.length === 0" class="text-center py-16 text-sm font-medium" style="color: var(--clr-text-tertiary);">
      Belum ada anggota keluarga terdaftar
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <div
        v-for="a in anggota"
        :key="a.nik"
        class="rounded-xl p-4 flex items-center gap-3.5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div
          class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-white text-sm shrink-0"
          style="background: linear-gradient(135deg, var(--clr-primary), var(--clr-secondary));"
        >{{ initials(a.nama_lengkap) }}</div>
        <div class="min-w-0 flex-1">
          <p class="font-bold text-[15px] truncate" style="color: var(--clr-text);">{{ a.nama_lengkap }}</p>
          <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">NIK: {{ a.nik }}</p>
        </div>
        <div
          v-if="a.status_keluarga || (a as any).hub_keluarga"
          class="badge badge-primary"
        >{{ (a as any).hub_keluarga ?? a.status_keluarga }}</div>
      </div>
    </div>
  </div>
</template>
