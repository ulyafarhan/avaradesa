<script setup lang="ts">
import { reactive, computed } from 'vue'

const props = defineProps<{ schema?: any[] }>()
const emit = defineEmits<{ update: [] }>()

const form = reactive<Record<string, any>>({})

const values = computed(() => ({ ...form }))

function setValue(field: string, value: any) {
  form[field] = value
  emit('update')
}

defineExpose({ values })
</script>

<template>
  <div class="space-y-4">
    <div v-for="field in schema" :key="field.field" class="space-y-1.5">
      <label class="text-sm font-semibold" style="color: var(--clr-text);">
        {{ field.label }}
        <span v-if="field.required" class="text-red-500 ml-0.5">*</span>
      </label>

      <input
        v-if="field.type === 'text' || field.type === 'number'"
        :type="field.type"
        :placeholder="'Masukkan ' + field.label.toLowerCase()"
        class="w-full px-3 py-2.5 rounded-lg text-sm border"
        :style="{ background: 'var(--clr-bg)', borderColor: 'var(--clr-border)', color: 'var(--clr-text)' }"
        @input="setValue(field.field, ($event.target as HTMLInputElement).value)"
      />

      <textarea
        v-else-if="field.type === 'textarea'"
        :placeholder="'Masukkan ' + field.label.toLowerCase()"
        rows="3"
        class="w-full px-3 py-2.5 rounded-lg text-sm border resize-none"
        :style="{ background: 'var(--clr-bg)', borderColor: 'var(--clr-border)', color: 'var(--clr-text)' }"
        @input="setValue(field.field, ($event.target as HTMLTextAreaElement).value)"
      />

      <select
        v-else-if="field.type === 'select'"
        class="w-full px-3 py-2.5 rounded-lg text-sm border"
        :style="{ background: 'var(--clr-bg)', borderColor: 'var(--clr-border)', color: 'var(--clr-text)' }"
        @change="setValue(field.field, ($event.target as HTMLSelectElement).value)"
      >
        <option value="">Pilih {{ field.label }}</option>
        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
      </select>

      <p v-if="!field.type || field.type === 'text'" class="text-xs font-medium" style="color: var(--clr-text-tertiary);">
        Masukkan {{ field.label.toLowerCase() }}
      </p>
    </div>
  </div>
</template>
