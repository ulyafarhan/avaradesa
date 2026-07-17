<script setup>
import { computed } from 'vue';

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
    let rawValue = event.target.value;
    
    if (props.type === 'currency') {
        rawValue = rawValue.replace(/\D/g, '');
        if (rawValue) {
            event.target.value = parseInt(rawValue, 10).toLocaleString('id-ID');
        } else {
            event.target.value = '';
        }
        emit('update:modelValue', rawValue);
    } else {
        emit('update:modelValue', rawValue);
    }
};

const displayValue = computed(() => {
    if (props.type === 'currency') {
        if (!props.modelValue && props.modelValue !== 0) return '';
        const numeric = props.modelValue.toString().replace(/\D/g, '');
        return numeric ? parseInt(numeric, 10).toLocaleString('id-ID') : '';
    }
    return props.modelValue;
});
</script>

<template>
    <label :for="id" class="block">
        <span class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ label }} <span v-if="required" class="text-red-600">*</span>
        </span>
        <input
            :id="id"
            :type="type === 'currency' ? 'text' : type"
            :value="displayValue"
            :placeholder="placeholder"
            :required="required"
            class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2.5 text-base text-slate-800 placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500"
            @input="handleInput"
        >
        <span v-if="error" class="mt-1 block text-sm text-red-600">{{ Array.isArray(error) ? error[0] : error }}</span>
    </label>
</template>
