<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../../../api/client'
import type { InformasiPublik } from '../../../api/types'

const router  = useRouter()
const items   = ref<InformasiPublik[]>([])
const loading = ref(true)
const error   = ref('')

onMounted(async () => {
  try {
    const res: any = await api.get<any>('/api/v1/informasi')
    items.value = res.data?.data ?? res.data ?? []
  } catch {
    error.value = 'Gagal memuat informasi'
  } finally {
    loading.value = false
  }
})

function badgeClass(k: string) {
  if (k === 'pengumuman') return 'badge badge-error'
  if (k === 'kegiatan')   return 'badge badge-primary'
  return 'badge badge-tertiary'
}

function stripHtml(html: string): string {
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

<template>
  <div class="space-y-4">
    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Informasi & Pengumuman</h2>
      <p class="text-xs mt-1" style="color: var(--clr-text-tertiary);">Kabar dan pengumuman resmi dari Pemerintah Desa</p>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-32 rounded-xl" />
    </div>

    <!-- Empty state -->
    <div v-else-if="items.length === 0" class="text-center py-16" style="color: var(--clr-text-tertiary);">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
      </svg>
      <p class="text-sm font-medium">Belum ada informasi publik</p>
    </div>

    <!-- Items List -->
    <div v-else class="space-y-3">
      <div
        v-for="info in items"
        :key="info.id"
        class="rounded-xl overflow-hidden press cursor-pointer transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
        @click="router.push(`/warga/informasi/${info.slug || info.id}`)"
      >
        <div class="flex flex-col sm:flex-row">
          <img
            v-if="info.cover_image"
            :src="info.cover_image"
            class="w-full sm:w-36 h-36 object-cover shrink-0"
            alt="Cover"
          />
          <div class="p-4 flex-1 min-w-0 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <span :class="badgeClass(info.kategori)">{{ info.kategori }}</span>
                <span class="text-[11px] font-medium" style="color: var(--clr-text-tertiary);">{{ formatDate(info.created_at) }}</span>
              </div>
              <h3 class="font-bold text-[15px] leading-snug line-clamp-2" style="color: var(--clr-text);">{{ info.judul }}</h3>
              <p class="text-[13px] mt-1.5 line-clamp-2" style="color: var(--clr-text-secondary);">{{ stripHtml(info.konten) }}</p>
            </div>
            <div class="mt-3 flex items-center gap-1 text-[12px] font-semibold" style="color: var(--clr-primary);">
              <span>Baca selengkapnya</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
