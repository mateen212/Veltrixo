<script setup lang="ts">
defineProps<{ padded?: boolean; divided?: boolean }>()
</script>
<template>
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <slot name="header" />
        <div :class="padded !== false ? 'p-6' : ''">
            <slot />
        </div>
        <slot name="footer" />
    </div>
</template>
