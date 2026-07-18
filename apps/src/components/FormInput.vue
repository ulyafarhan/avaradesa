<script setup lang="ts">
defineProps<{
  modelValue?: string | number
  label?:      string
  type?:       string
  placeholder?: string
  required?:   boolean
  maxlength?:  string
  inputmode?:  'none' | 'text' | 'decimal' | 'numeric' | 'tel' | 'search' | 'email' | 'url'
  hint?:       string
  error?:      string
  class?:      string
  min?:        string | number
  max?:        string | number
}>()

defineEmits<{ 'update:modelValue': [value: any] }>()
</script>

<template>
  <div :class="class">
    <!-- Label -->
    <label
      v-if="label"
      class="input-label block"
      :style="{ marginBottom: '6px' }"
    >{{ label }}<span v-if="required" style="color: var(--clr-error); margin-left: 2px;">*</span></label>

    <!-- Input -->
    <input
      :type="type ?? 'text'"
      :value="modelValue"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      :placeholder="placeholder"
      :required="required"
      :maxlength="maxlength"
      :inputmode="inputmode"
      :min="min"
      :max="max"
      class="input-field"
      :style="error ? 'border-color: var(--clr-error); box-shadow: 0 0 0 3px var(--clr-error-bg);' : ''"
    />

    <!-- Hint -->
    <p v-if="hint && !error" class="t-caption" style="color: var(--clr-text-tertiary); margin-top: 5px;">{{ hint }}</p>
    <!-- Error -->
    <p v-if="error" class="t-caption" style="color: var(--clr-error); margin-top: 5px;">{{ error }}</p>
  </div>
</template>
