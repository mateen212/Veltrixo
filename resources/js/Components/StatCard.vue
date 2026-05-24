<template>
  <div class="bg-white rounded-xl border p-5">
    <p class="text-sm text-gray-500 mb-1">{{ title }}</p>
    <p class="text-2xl font-bold text-gray-800">{{ value }}</p>
    <p v-if="sub" class="text-xs text-gray-400 mt-1">{{ sub }}</p>
  </div>
</template>

<script setup lang="ts">
defineProps<{ title: string; value: string | number; sub?: string }>()
</script>
