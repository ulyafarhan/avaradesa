<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { InformasiPublik } from '../../../api/types'

const route   = useRoute()
const router  = useRouter()
const info    = ref<InformasiPublik | null>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res: any = await api.get(endpoints.informasi.detail(route.params.id as string))
    info.value = res.data ?? res
  } catch {
    info.value = null
  } finally {
    loading.value = false
  }
})

function badgeClass(k: string) {
  if (k === 'pengumuman') return 'badge badge-error'
  if (k === 'kegiatan')   return 'badge badge-primary'
  return 'badge badge-tertiary'
}

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb back -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/warga/informasi" class="hover:text-[var(--clr-primary)] transition-colors">Informasi</router-link>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div class="skeleton h-48 rounded-xl" />
      <div class="skeleton h-8 w-3/4 rounded-lg" />
      <div class="skeleton h-32 rounded-xl" />
    </div>

    <!-- Detail Article -->
    <div v-else-if="info" class="space-y-4">
      <img
        v-if="info.cover_image"
        :src="info.cover_image"
        class="w-full aspect-video object-cover rounded-xl border"
        style="border-color: var(--clr-border-light);"
        alt="Cover"
      />

      <article
        class="rounded-xl p-5 sm:p-6"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <!-- Meta -->
        <div class="flex items-center gap-2.5 mb-3">
          <span :class="badgeClass(info.kategori)">{{ info.kategori }}</span>
          <span class="text-xs font-medium" style="color: var(--clr-text-tertiary);">{{ formatDate(info.created_at) }}</span>
        </div>

        <!-- Title -->
        <h1 class="text-xl font-bold leading-snug mb-4" style="color: var(--clr-text);">{{ info.judul }}</h1>

        <div class="divider mb-4" />

        <!-- Content rendered safely with v-html -->
        <div
          class="text-[14px] leading-relaxed space-y-3 article-content"
          style="color: var(--clr-text-secondary);"
          v-html="info.konten"
        />
      </article>
    </div>

    <!-- Not Found -->
    <div v-else class="text-center py-16" style="color: var(--clr-text-tertiary);">
      Informasi tidak ditemukan
    </div>
  </div>
</template>

<style scoped>
.article-content :deep(p) {
  margin-bottom: 0.75rem;
}
.article-content :deep(ul), .article-content :deep(ol) {
  margin-left: 1.25rem;
  margin-bottom: 0.75rem;
}
.article-content :deep(ul) {
  list-style-type: disc;
}
.article-content :deep(ol) {
  list-style-type: decimal;
}
.article-content :deep(strong) {
  color: var(--clr-text);
  font-weight: 600;
}
</style>
