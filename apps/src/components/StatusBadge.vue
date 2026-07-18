<script setup lang="ts">
defineProps<{
  status: string
  size?: 'sm' | 'md' | 'lg'
}>()

function badgeClass(status: string) {
  const s = (status ?? '').toLowerCase()
  if (s === 'pending' || s === 'menunggu') return 'badge badge-warning'
  if (s === 'diproses')                    return 'badge badge-primary'
  if (s === 'disetujui' || s === 'selesai') return 'badge badge-tertiary'
  if (s === 'ditolak')                      return 'badge badge-error'
  return 'badge badge-primary'
}

function labelText(status: string) {
  const s = (status ?? '').toLowerCase()
  if (s === 'pending')   return 'Menunggu'
  if (s === 'diproses')  return 'Diproses'
  if (s === 'disetujui') return 'Disetujui'
  if (s === 'selesai')   return 'Selesai'
  if (s === 'ditolak')   return 'Ditolak'
  return status
}
</script>

<template>
  <span :class="[badgeClass(status), size === 'lg' ? 'px-3 py-1 text-xs' : '']">
    {{ labelText(status) }}
  </span>
</template>
