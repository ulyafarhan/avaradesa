<script setup>
defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: [String, Number],
    options: { type: Array, default: () => [] },
    required: Boolean,
    error: [String, Array],
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label :for="id" class="block">
        <span class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ label }} <span v-if="required" class="text-red-600">*</span>
        </span>
        <select
            :id="id"
            :value="modelValue"
            :required="required"
            class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2.5 text-base text-slate-800 focus:border-teal-500 focus:ring-teal-500"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">Pilih opsi</option>
            <option v-for="option in options" :key="option" :value="option">{{ option }}</option>
        </select>
        <span v-if="error" class="mt-1 block text-sm text-red-600">{{ Array.isArray(error) ? error[0] : error }}</span>
    </label>
</template>
