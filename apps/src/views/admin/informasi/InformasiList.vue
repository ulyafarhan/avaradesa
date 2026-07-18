<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { Informasi } from '../../../api/types'

interface Paginated<T> { current_page: number; data: T[]; total: number; per_page: number }

const items = ref<Informasi[]>([])
const loading = ref(true)
const error = ref('')
const filter = ref('')

const filters = [
  { label: 'Semua Status', value: '' },
  { label: 'Terbit',       value: '1' },
  { label: 'Draft',        value: '0' },
]

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const params = filter.value ? `?is_published=${filter.value}` : ''
    const res = await api.get<{ data: Paginated<Informasi> }>(endpoints.informasi.list + params)
    items.value = res.data.data ?? []
  } catch {
    error.value = 'Gagal memuat informasi'
  } finally {
    loading.value = false
  }
}

function hapus(id: string) {
  if (!confirm('Hapus informasi ini?')) return
  api.delete(endpoints.informasi.delete(id)).then(() => {
    items.value = items.value.filter(i => i.id !== id)
  })
}

function stripHtml(html: string): string {
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html || ''
  return tmp.textContent || tmp.innerText || ''
}

function badgeClass(k: string) {
  if (k === 'pengumuman') return 'badge badge-error'
  if (k === 'kegiatan')   return 'badge badge-primary'
  return 'badge badge-tertiary'
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">Informasi Desa</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Kelola publikasi pengumuman & berita desa</p>
      </div>
      <router-link to="/admin/informasi/tambah" class="btn btn-primary btn-sm">+ Buat Informasi</router-link>
    </div>

    <!-- Filter chips -->
    <div class="flex gap-2 overflow-x-auto pb-1">
      <button
        v-for="f in filters"
        :key="f.value"
        @click="filter = f.value; fetch()"
        class="badge press cursor-pointer transition-all"
        :class="filter === f.value ? 'badge-primary' : ''"
        :style="filter !== f.value ? 'background: var(--clr-surface); color: var(--clr-text-secondary); border: 1px solid var(--clr-border);' : ''"
      >
        {{ f.label }}
      </button>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading && items.length === 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div v-for="i in 4" :key="i" class="skeleton h-32 rounded-xl" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="items.length === 0"
      class="text-center py-16 rounded-xl p-5"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);"
    >
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
      </svg>
      <p class="text-sm font-bold" style="color: var(--clr-text);">Belum ada informasi publik</p>
      <router-link to="/admin/informasi/tambah" class="btn btn-ghost btn-sm mt-3 inline-flex">Buat Informasi Pertama</router-link>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div
        v-for="info in items"
        :key="info.id"
        class="rounded-xl p-4 flex flex-col justify-between transition-all duration-200"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-2">
            <span :class="badgeClass(info.kategori)">{{ info.kategori }}</span>
            <div class="flex items-center gap-1">
              <router-link
                :to="`/admin/informasi/${info.id}/edit`"
                class="p-1.5 rounded-lg press"
                style="color: var(--clr-primary);"
                title="Edit"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              </router-link>
              <button
                @click="hapus(info.id)"
                class="p-1.5 rounded-lg press"
                style="color: var(--clr-error);"
                title="Hapus"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>

          <h3 class="font-bold text-[15px] leading-snug line-clamp-2" style="color: var(--clr-text);">{{ info.judul }}</h3>
          <p class="text-[12px] mt-1.5 line-clamp-2" style="color: var(--clr-text-secondary);">{{ stripHtml(info.konten) }}</p>
        </div>

        <div class="flex items-center justify-between mt-4 pt-2.5 border-t" style="border-color: var(--clr-border-light);">
          <span class="text-[11px] font-medium" style="color: var(--clr-text-tertiary);">
            {{ new Date(info.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
          </span>
          <span class="badge" :class="info.is_published ? 'badge-tertiary' : 'badge-warning'">
            {{ info.is_published ? 'Terbit' : 'Draft' }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
