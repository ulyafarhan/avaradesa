<script setup lang="ts">
defineProps<{
  hover?: boolean
  elevated?: boolean
  glass?: boolean
  padding?: 'sm' | 'md' | 'lg' | 'none'
  clickable?: boolean
}>()
</script>

<template>
  <div
    class="rounded-[20px] transition-all"
    :style="{ transitionTimingFunction: 'var(--spring-expressive)', transitionDuration: '0.35s' }"
    :class="[
      padding === 'none' ? '' : padding === 'sm' ? 'p-3' : padding === 'lg' ? 'p-7' : 'p-5',
      glass
        ? 'ne-card-glass'
        : 'ne-card-solid',
      hover && 'cursor-pointer hover:-translate-y-[3px] hover:shadow-[0_16px_40px_-12px_rgba(15,23,42,0.10)] dark:hover:shadow-[0_16px_40px_-12px_rgba(0,0,0,0.4)]',
      clickable && 'ne-spring-press',
      elevated && 'ne-glow-primary',
    ]"
  >
    <slot />
  </div>
</template>
