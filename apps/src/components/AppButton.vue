<script setup lang="ts">
defineProps<{
  loading?:  boolean
  disabled?: boolean
  variant?:  'primary' | 'secondary' | 'tertiary' | 'success' | 'danger' | 'ghost' | 'expressive'
  size?:     'sm' | 'md' | 'lg'
  type?:     'button' | 'submit' | 'reset'
}>()
</script>

<template>
  <button
    :type="type ?? 'button'"
    :disabled="loading || disabled"
    class="btn press transition-all"
    :class="[
      variant === 'secondary'  ? 'btn-secondary' :
      variant === 'ghost'      ? 'btn-ghost' :
      variant === 'tertiary'   ? 'btn-primary' :
      variant === 'success'    ? 'btn-primary' :
      variant === 'danger'     ? 'btn-ghost' :
      variant === 'expressive' ? 'btn-primary' :
      'btn-primary',
      size === 'sm' ? 'btn-sm' : size === 'lg' ? 'py-4 px-8 text-[16px]' : '',
    ]"
    :style="variant === 'danger' ? 'background: var(--clr-error-bg); color: var(--clr-error-text);' : (variant === 'success' || variant === 'tertiary') ? 'background: var(--clr-tertiary); color: #fff;' : ''"
  >
    <svg v-if="loading" class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
    <slot />
  </button>
</template>
