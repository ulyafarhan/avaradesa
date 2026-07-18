<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { SuratPengajuan } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'
import AppButton from '../../../components/AppButton.vue'

interface SingleWrap<T> { data: T }

const route = useRoute()
const router = useRouter()
const item = ref<SuratPengajuan | null>(null)
const loading = ref(true)
const activeAction = ref<'approve' | 'reject' | null>(null)
const catatan = ref('')
const error = ref('')

onMounted(async () => {
  try {
    const res = await api.get<SingleWrap<SuratPengajuan>>(endpoints.surat.detail(route.params.id as string))
    item.value = res.data ?? (res as any)
  } catch {
    error.value = 'Gagal memuat detail pengajuan'
    item.value = null
  } finally {
    loading.value = false
  }
})

async function approve() {
  activeAction.value = 'approve'
  try {
    await api.post(endpoints.surat.approve(route.params.id as string))
    router.push('/admin/surat')
  } finally {
    activeAction.value = null
  }
}

async function reject() {
  if (!catatan.value.trim()) return
  activeAction.value = 'reject'
  try {
    await api.post(endpoints.surat.reject(route.params.id as string), { catatan_penolakan: catatan.value })
    router.push('/admin/surat')
  } finally {
    activeAction.value = null
  }
}

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })
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
      <router-link to="/admin/surat" class="hover:text-[var(--clr-primary)] transition-colors">Pengajuan Surat</router-link>
    </div>

    <!-- Error alert -->
    <div
      v-if="error"
      class="rounded-xl p-3 text-sm font-medium"
      style="background: var(--clr-error-bg); color: var(--clr-error-text);"
    >{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-48 rounded-xl" />
      <div class="skeleton h-32 rounded-xl" />
    </div>

    <div v-else-if="item" class="space-y-4">
      <!-- Main Card -->
      <div
        class="rounded-xl p-5 sm:p-6"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-start justify-between gap-3 mb-5 pb-3 border-b" style="border-color: var(--clr-border-light);">
          <div>
            <h2 class="text-xl font-bold" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? 'Surat Keterangan' }}</h2>
            <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">No. Reg: {{ item.nomor_registrasi }}</p>
          </div>
          <StatusBadge :status="item.status" size="lg" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div class="p-3.5 rounded-lg" style="background: var(--clr-surface-dim);">
            <p class="text-xs font-medium" style="color: var(--clr-text-tertiary);">NIK Pemohon</p>
            <p class="font-bold font-mono mt-0.5 text-base" style="color: var(--clr-text);">{{ item.nik_pemohon }}</p>
          </div>

          <div v-if="item.pemohon" class="p-3.5 rounded-lg" style="background: var(--clr-surface-dim);">
            <p class="text-xs font-medium" style="color: var(--clr-text-tertiary);">Nama Pemohon</p>
            <p class="font-bold mt-0.5 text-base" style="color: var(--clr-text);">{{ item.pemohon.nama_lengkap }}</p>
          </div>

          <div class="p-3.5 rounded-lg" style="background: var(--clr-surface-dim);">
            <p class="text-xs font-medium" style="color: var(--clr-text-tertiary);">Kategori Surat</p>
            <p class="font-bold mt-0.5 text-base" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? '-' }}</p>
          </div>

          <div class="p-3.5 rounded-lg" style="background: var(--clr-surface-dim);">
            <p class="text-xs font-medium" style="color: var(--clr-text-tertiary);">Tanggal Pengajuan</p>
            <p class="font-bold mt-0.5 text-base" style="color: var(--clr-text);">{{ formatDate(item.created_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Data Isian Card -->
      <div
        v-if="item.data_isian"
        class="rounded-xl p-5 sm:p-6"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-base mb-3" style="color: var(--clr-text);">Data Isian Permohonan</h3>
        <div class="space-y-2 text-sm">
          <div
            v-for="(val, key) in item.data_isian"
            :key="key"
            class="flex justify-between py-2 border-b last:border-0"
            style="border-color: var(--clr-border-light);"
          >
            <span class="capitalize" style="color: var(--clr-text-secondary);">{{ String(key).replace(/_/g, ' ') }}</span>
            <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-text);">{{ val }}</span>
          </div>
        </div>
      </div>

      <!-- Admin Action Card -->
      <div
        v-if="item.status === 'Pending' || item.status === 'Diproses'"
        class="rounded-xl p-5 sm:p-6 space-y-4"
        style="background: var(--clr-primary-bg); border: 1.5px solid var(--clr-primary-glow);"
      >
        <h3 class="font-bold text-base" style="color: var(--clr-primary-text);">Persetujuan Administrator</h3>
        <div class="space-y-3">
          <div>
            <label class="input-label block" style="margin-bottom: 6px;">Catatan Penolakan (Opsional jika disetujui)</label>
            <textarea
              v-model="catatan"
              rows="3"
              class="input-field"
              placeholder="Catatan penolakan jika permohonan ditolak..."
            />
          </div>

          <div class="flex gap-3">
            <AppButton
              @click="approve"
              :loading="activeAction === 'approve'"
              :disabled="!!activeAction"
              variant="success"
              class="flex-1"
            >Setujui & Terbitkan Surat</AppButton>

            <AppButton
              @click="reject"
              :loading="activeAction === 'reject'"
              :disabled="!catatan.trim() || !!activeAction"
              variant="danger"
              class="flex-1"
            >Tolak Pengajuan</AppButton>
          </div>
        </div>
      </div>

      <!-- Tracking Timeline -->
      <div
        class="rounded-xl p-5 sm:p-6"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-base mb-4" style="color: var(--clr-text);">Riwayat Tracking</h3>
        <div v-if="!item.tracking?.length" class="text-center py-6 text-xs" style="color: var(--clr-text-tertiary);">
          Belum ada riwayat pelacakan
        </div>
        <div v-else class="space-y-4 relative pl-3">
          <div
            v-for="(t, i) in item.tracking"
            :key="i"
            class="flex items-start gap-3 relative"
          >
            <div
              v-if="i < item.tracking.length - 1"
              class="absolute left-[7px] top-[18px] bottom-[-16px] w-[2px]"
              style="background: var(--clr-border);"
            />
            <div
              class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 z-10 mt-0.5"
              :style="{ background: i === item.tracking.length - 1 ? 'var(--clr-primary)' : 'var(--clr-border)' }"
            >
              <div class="w-1.5 h-1.5 rounded-full bg-white" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="font-bold text-[14px]" style="color: var(--clr-text);">{{ t.status_baru }}</p>
              <p v-if="t.keterangan_update" class="text-[12px] mt-0.5" style="color: var(--clr-text-secondary);">
                {{ t.keterangan_update }} <template v-if="t.diupdate_oleh">— {{ t.diupdate_oleh }}</template>
              </p>
              <p class="text-[11px] font-medium mt-1" style="color: var(--clr-text-tertiary);">{{ formatDate(t.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-16 text-sm" style="color: var(--clr-text-tertiary);">
      Pengajuan tidak ditemukan
    </div>
  </div>
</template>
