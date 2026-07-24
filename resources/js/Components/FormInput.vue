<script setup>
const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: [String, Number],
    type: { type: String, default: 'text' },
    placeholder: String,
    required: Boolean,
    error: [String, Array],
});

const emit = defineEmits(['update:modelValue']);

const handleInput = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <label :for="id" class="block">
        <span class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ label }} <span v-if="required" class="text-red-600">*</span>
        </span>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2.5 text-base text-slate-800 placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500"
            @input="handleInput"
        >
        <span v-if="error" class="mt-1 block text-sm text-red-600">{{ Array.isArray(error) ? error[0] : error }}</span>
    </label>
</template>
