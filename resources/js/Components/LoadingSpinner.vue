<script setup>
defineProps({
    size: {
        type: String,
        default: 'size-4',
    },
});
</script>

<template>
    <span :class="[size, 'animate-spin rounded-full border-2 border-current border-r-transparent']" />
</template>
