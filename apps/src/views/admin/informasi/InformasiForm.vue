<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import AppButton from '../../../components/AppButton.vue'
import FormInput from '../../../components/FormInput.vue'

const route  = useRoute()
const router = useRouter()
const isEdit = !!route.params.id
const loading = ref(isEdit)
const saving  = ref(false)
const error   = ref('')

const form = ref({
  judul: '', konten: '', kategori: 'pengumuman' as 'pengumuman' | 'kegiatan' | 'artikel',
  cover_image: '', is_published: false,
})

onMounted(async () => {
  if (isEdit) {
    try {
      const res: any = await api.get(endpoints.informasi.detail(route.params.id as string))
      const d = res.data ?? {}
      form.value = {
        judul: d.judul ?? '', konten: d.konten ?? d.isi ?? '', kategori: d.kategori as any,
        cover_image: d.cover_image ?? d.gambar ?? '', is_published: d.is_published ?? d.published ?? false,
      }
    } finally {
      loading.value = false
    }
  }
})

async function submit() {
  if (!form.value.judul || !form.value.konten) { error.value = 'Judul dan isi wajib diisi'; return }
  saving.value = true; error.value = ''
  try {
    if (isEdit) {
      await api.put(endpoints.informasi.update(route.params.id as string), form.value)
    } else {
      await api.post(endpoints.informasi.create, form.value)
    }
    router.push('/admin/informasi')
  } catch (e: any) {
    error.value = e.message ?? 'Gagal menyimpan informasi'
    if (e.errors) error.value = Object.values(e.errors as Record<string, string[]>).flat()[0] ?? error.value
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-4 max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/admin/informasi" class="hover:text-[var(--clr-primary)] transition-colors">Informasi Desa</router-link>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="skeleton h-64 rounded-xl" />

    <!-- Form Card -->
    <form
      v-else
      @submit.prevent="submit"
      class="rounded-xl p-5 sm:p-6 space-y-4"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
    >
      <div class="pb-3 border-b" style="border-color: var(--clr-border-light);">
        <h2 class="text-xl font-bold" style="color: var(--clr-text);">{{ isEdit ? 'Edit Informasi Desa' : 'Buat Informasi Baru' }}</h2>
        <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Publikasikan berita, pengumuman, atau artikel kegiatan desa</p>
      </div>

      <FormInput v-model="form.judul" label="Judul Informasi" required placeholder="Masukkan judul pengumuman / berita..." />

      <div>
        <label class="input-label block" style="margin-bottom: 6px;">Kategori <span style="color: var(--clr-error);">*</span></label>
        <select v-model="form.kategori" class="input-field">
          <option value="pengumuman">Pengumuman</option>
          <option value="kegiatan">Kegiatan</option>
          <option value="artikel">Artikel</option>
        </select>
      </div>

      <div>
        <label class="input-label block" style="margin-bottom: 6px;">Isi Konten (HTML / Teks) <span style="color: var(--clr-error);">*</span></label>
        <textarea
          v-model="form.konten"
          rows="8"
          class="input-field"
          style="resize: vertical; min-height: 160px;"
          placeholder="Tuliskan isi pengumuman atau berita secara lengkap..."
          required
        />
      </div>

      <FormInput v-model="form.cover_image" label="URL Cover Gambar (Opsional)" placeholder="https://..." />

      <div class="flex items-center gap-3 pt-2">
        <label class="flex items-center gap-2.5 cursor-pointer press">
          <input
            type="checkbox"
            v-model="form.is_published"
            class="w-4.5 h-4.5 rounded text-blue-600 focus:ring-blue-500"
          />
          <span class="text-sm font-semibold" style="color: var(--clr-text);">Langsung Publikasikan</span>
        </label>
      </div>

      <!-- Error -->
      <div
        v-if="error"
        class="rounded-xl p-3 text-sm font-medium"
        style="background: var(--clr-error-bg); color: var(--clr-error-text);"
      >{{ error }}</div>

      <div class="flex items-center gap-3 pt-2">
        <AppButton type="submit" variant="primary" :loading="saving">
          {{ isEdit ? 'Simpan Perubahan' : 'Publikasikan Informasi' }}
        </AppButton>
        <router-link to="/admin/informasi" class="btn btn-ghost">Batal</router-link>
      </div>
    </form>
  </div>
</template>
