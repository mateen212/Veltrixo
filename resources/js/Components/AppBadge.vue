<script setup lang="ts">
defineProps<{
    variant?: 'success' | 'warning' | 'danger' | 'info' | 'neutral' | 'purple'
    dot?: boolean
    size?: 'sm' | 'md'
}>()
</script>
<template>
    <span :class="[
        'inline-flex items-center gap-1.5 font-medium rounded-full',
        size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-xs',
        variant === 'success' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
        : variant === 'warning' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200'
        : variant === 'danger'  ? 'bg-red-50 text-red-700 ring-1 ring-red-200'
        : variant === 'info'    ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200'
        : variant === 'purple'  ? 'bg-purple-50 text-purple-700 ring-1 ring-purple-200'
        : 'bg-gray-100 text-gray-600 ring-1 ring-gray-200',
    ]">
        <span v-if="dot" class="h-1.5 w-1.5 rounded-full"
            :class="variant === 'success' ? 'bg-emerald-500'
                : variant === 'warning' ? 'bg-amber-500'
                : variant === 'danger'  ? 'bg-red-500'
                : variant === 'info'    ? 'bg-blue-500'
                : variant === 'purple'  ? 'bg-purple-500'
                : 'bg-gray-400'"
        ></span>
        <slot />
    </span>
</template>
