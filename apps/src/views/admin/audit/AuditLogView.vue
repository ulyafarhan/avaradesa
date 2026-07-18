<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'

interface LogItem {
  id: number
  actor_type: string
  actor_id: string
  action: string
  target_type: string
  target_id: string
  created_at: string
}

const items = ref<LogItem[]>([])
const loading = ref(true)
const error = ref('')

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const res: any = await api.get(endpoints.auditLog.list)
    items.value = res.data ?? []
  } catch {
    error.value = 'Gagal memuat log audit'
  } finally {
    loading.value = false
  }
}

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-4 max-w-6xl mx-auto">
    <!-- Header -->
    <div>
      <h2 class="text-xl font-bold" style="color: var(--clr-text);">Audit Log & Jejak Aktivitas</h2>
      <p class="text-xs mt-0.5" style="color: var(--clr-text-tertiary);">Catatan jejak audit aktivitas sistem secara transparan & akuntabel</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="rounded-xl p-3 text-sm font-medium" style="background: var(--clr-error-bg); color: var(--clr-error-text);">{{ error }}</div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-2">
      <div v-for="i in 5" :key="i" class="skeleton h-14 rounded-xl" />
    </div>

    <!-- Empty -->
    <div v-else-if="items.length === 0" class="text-center py-12 rounded-xl p-5" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light); color: var(--clr-text-tertiary);">
      <p class="font-bold text-sm" style="color: var(--clr-text);">Belum ada catatan audit log</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-2">
      <div
        v-for="l in items"
        :key="l.id"
        class="rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-[11px] shrink-0" style="background: var(--clr-surface-dim); color: var(--clr-primary);">
            {{ l.actor_type === 'admin' ? 'ADM' : 'WRG' }}
          </div>
          <div class="min-w-0">
            <p class="font-bold text-sm truncate" style="color: var(--clr-text);">
              <span class="capitalize" style="color: var(--clr-primary);">{{ l.action }}</span> — {{ l.target_type }} #{{ l.target_id }}
            </p>
            <p class="text-[11px] mt-0.5" style="color: var(--clr-text-tertiary);">Aktor: {{ l.actor_id }} ({{ l.actor_type }})</p>
          </div>
        </div>
        <span class="font-mono text-[11px] shrink-0" style="color: var(--clr-text-tertiary);">{{ formatDate(l.created_at) }}</span>
      </div>
    </div>
  </div>
</template>
