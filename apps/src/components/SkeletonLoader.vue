<script setup lang="ts">
defineProps<{
  rows?: number
  variant?: 'text' | 'card' | 'list'
  class?: string
}>()
</script>

<template>
  <div class="space-y-3" :class="class">
    <template v-if="variant === 'card'">
      <div v-for="i in (rows ?? 3)" :key="i" class="rounded-xl border overflow-hidden p-4 space-y-3" style="background: var(--clr-surface); border-color: var(--clr-border-light);">
        <div class="h-32 skeleton rounded-lg" />
        <div class="h-4 w-3/4 skeleton rounded-md" />
        <div class="h-3 w-1/2 skeleton rounded-md" />
      </div>
    </template>

    <template v-else-if="variant === 'list'">
      <div v-for="i in (rows ?? 4)" :key="i" class="flex items-center gap-3.5 p-4 rounded-xl border" style="background: var(--clr-surface); border-color: var(--clr-border-light);">
        <div class="w-10 h-10 rounded-xl skeleton shrink-0" />
        <div class="flex-1 space-y-2">
          <div class="h-4 w-2/3 skeleton rounded-md" />
          <div class="h-3 w-1/3 skeleton rounded-md" />
        </div>
      </div>
    </template>

    <template v-else>
      <div v-for="i in (rows ?? 3)" :key="i" class="h-12 rounded-xl skeleton" />
    </template>
  </div>
</template>
