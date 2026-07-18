<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { KategoriSurat } from '../../../api/types'
import DynamicForm from '../../../components/DynamicForm.vue'
import AppButton from '../../../components/AppButton.vue'

const route = useRoute()
const router = useRouter()
const kategories = ref<KategoriSurat[]>([])
const selectedKategori = ref<KategoriSurat | null>(null)
const loading = ref(false)
const loadingKategori = ref(true)
const error = ref('')
const dynamicFormRef = ref<InstanceType<typeof DynamicForm> | null>(null)

onMounted(async () => {
  try {
    const res: any = await api.get(endpoints.surat.kategori)
    const list = res.data ?? res ?? []
    kategories.value = list.filter((k: any) => k.is_active)
    const paramSlug = (route.params.kategori as string) ?? ''
    selectedKategori.value = kategories.value.find((k: any) =>
      paramSlug === k.nama_surat.toLowerCase().replace(/\s+/g, '-')
    ) ?? null
  } finally {
    loadingKategori.value = false
  }
})

async function submit() {
  if (!selectedKategori.value) { error.value = 'Kategori surat tidak ditemukan'; return }
  const dataIsian = dynamicFormRef.value?.values ?? {}
  const required = (selectedKategori.value as any).schema_isian?.filter((f: any) => f.required) ?? []
  const missing = required.find((f: any) => !dataIsian[f.field])
  if (missing) { error.value = `"${missing.label}" wajib diisi`; return }

  loading.value = true; error.value = ''
  try {
    await api.post(endpoints.surat.create!, {
      kategori_surat_id: selectedKategori.value.id,
      data_isian: dataIsian,
      file_syarat: [],
    })
    router.push('/warga/surat/pengajuan')
  } catch (e: any) {
    error.value = e.message ?? 'Gagal mengajukan surat'
  } finally {
    loading.value = false
  }
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
      <router-link to="/warga/surat" class="hover:text-[var(--clr-primary)] transition-colors">Pilih Surat</router-link>
    </div>

    <!-- Loading -->
    <div v-if="loadingKategori" class="space-y-3">
      <div class="skeleton h-8 w-48 rounded-lg" />
      <div class="skeleton h-64 rounded-xl" />
    </div>

    <template v-else-if="selectedKategori">
      <!-- Main Form Card -->
      <div
        class="rounded-xl p-5 sm:p-6"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="mb-5 pb-3 border-b" style="border-color: var(--clr-border-light);">
          <h2 class="text-xl font-bold" style="color: var(--clr-text);">Pengajuan {{ selectedKategori.nama_surat }}</h2>
          <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">Kode: {{ (selectedKategori as any).kode_surat }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <DynamicForm
            v-if="(selectedKategori as any).schema_isian"
            ref="dynamicFormRef"
            :schema="(selectedKategori as any).schema_isian"
            @update="() => error = ''"
          />

          <!-- Requirement Documents Warning Container -->
          <div
            v-if="(selectedKategori as any).syarat_dokumen?.length"
            class="rounded-xl p-4"
            style="background: var(--clr-warning-bg); color: var(--clr-warning-text);"
          >
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <p class="text-sm font-bold">Dokumen Fisik Yang Perlu Disiapkan</p>
            </div>
            <ul class="text-xs space-y-1 pl-6 list-disc font-medium">
              <li v-for="s in (selectedKategori as any).syarat_dokumen" :key="s">{{ s }}</li>
            </ul>
          </div>

          <!-- Error Alert -->
          <div
            v-if="error"
            class="rounded-xl p-3 text-sm font-medium"
            style="background: var(--clr-error-bg); color: var(--clr-error-text);"
          >
            {{ error }}
          </div>

          <AppButton type="submit" variant="primary" class="w-full" :loading="loading">
            Kirim Pengajuan Surat
          </AppButton>
        </form>
      </div>
    </template>

    <div v-else class="text-center py-16 text-sm" style="color: var(--clr-text-tertiary);">
      Kategori surat tidak ditemukan
    </div>
  </div>
</template>
