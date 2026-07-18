<script setup lang="ts">
import { ref, watch } from 'vue'

export interface SchemaField {
  field:    string
  label:    string
  type:     'text' | 'textarea' | 'number' | 'select' | 'date'
  required?: boolean
  options?: string[]
  hint?:    string
}

const props = defineProps<{ schema: SchemaField[] }>()
const emit  = defineEmits<{ (e: 'update', v: Record<string, string>): void }>()
const values = ref<Record<string, string>>({})

watch(values, (v) => emit('update', { ...v }), { deep: true })
function set(field: string, val: string) { values.value[field] = val }
defineExpose({ values })

// Shared input style — rounded-lg (12px), tidak full
const inputStyle = `
  display: block;
  width: 100%;
  font-family: 'Outfit', sans-serif;
  font-size: 15px;
  font-weight: 400;
  color: var(--clr-text);
  background: var(--clr-bg);
  border: 1.5px solid var(--clr-border);
  border-radius: 12px;
  padding: 13px 14px;
  outline: none;
  transition: border-color 0.18s, box-shadow 0.18s;
  -webkit-appearance: none;
  appearance: none;
`
</script>

<template>
  <div class="space-y-4">
    <div v-for="f in schema" :key="f.field">
      <!-- Label -->
      <label
        :for="`field-${f.field}`"
        class="input-label block"
        style="margin-bottom: 6px;"
      >
        {{ f.label }}
        <span v-if="f.required" style="color: var(--clr-error); margin-left: 2px;">*</span>
      </label>

      <!-- Select -->
      <select
        v-if="f.type === 'select'"
        :id="`field-${f.field}`"
        :value="values[f.field] ?? ''"
        @change="set(f.field, ($event.target as HTMLSelectElement).value)"
        :required="f.required"
        :style="inputStyle"
        @focus="($event.target as HTMLElement).style.borderColor = 'var(--clr-primary)'"
        @blur="($event.target as HTMLElement).style.borderColor = 'var(--clr-border)'"
      >
        <option value="" disabled>Pilih {{ f.label.toLowerCase() }}</option>
        <option v-for="o in f.options" :key="o" :value="o">{{ o }}</option>
      </select>

      <!-- Textarea -->
      <textarea
        v-else-if="f.type === 'textarea'"
        :id="`field-${f.field}`"
        :value="values[f.field] ?? ''"
        @input="set(f.field, ($event.target as HTMLTextAreaElement).value)"
        rows="4"
        :placeholder="f.hint ?? `Isi ${f.label.toLowerCase()}`"
        :required="f.required"
        :style="inputStyle + 'resize: vertical; min-height: 100px;'"
        @focus="($event.target as HTMLElement).style.borderColor = 'var(--clr-primary)'"
        @blur="($event.target as HTMLElement).style.borderColor = 'var(--clr-border)'"
      />

      <!-- Date -->
      <input
        v-else-if="f.type === 'date'"
        :id="`field-${f.field}`"
        type="date"
        :value="values[f.field] ?? ''"
        @input="set(f.field, ($event.target as HTMLInputElement).value)"
        :required="f.required"
        :style="inputStyle"
        @focus="($event.target as HTMLElement).style.borderColor = 'var(--clr-primary)'"
        @blur="($event.target as HTMLElement).style.borderColor = 'var(--clr-border)'"
      />

      <!-- Text / Number -->
      <input
        v-else
        :id="`field-${f.field}`"
        :type="f.type === 'number' ? 'number' : 'text'"
        :value="values[f.field] ?? ''"
        @input="set(f.field, ($event.target as HTMLInputElement).value)"
        :placeholder="f.hint ?? `Isi ${f.label.toLowerCase()}`"
        :required="f.required"
        :style="inputStyle"
        @focus="($event.target as HTMLElement).style.borderColor = 'var(--clr-primary)'"
        @blur="($event.target as HTMLElement).style.borderColor = 'var(--clr-border)'"
      />

      <!-- Hint text -->
      <p v-if="f.hint && f.type !== 'textarea'" class="t-caption" style="color: var(--clr-text-tertiary); margin-top: 4px;">{{ f.hint }}</p>
    </div>
  </div>
</template>
