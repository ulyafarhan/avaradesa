<script setup lang="ts">
import { onMounted } from 'vue'
import { useAppStore } from './stores/appStore'

const app = useAppStore()
onMounted(() => app.initTheme())
</script>

<template>
  <router-view />
</template>
