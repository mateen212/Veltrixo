<script setup lang="ts">
defineProps<{
    variant?: 'success' | 'warning' | 'danger' | 'info' | 'neutral' | 'purple' | 'brand'
    dot?: boolean
    size?: 'xs' | 'sm' | 'md'
    pill?: boolean
}>()
</script>
<template>
    <span :class="[
        'inline-flex items-center gap-1.5 font-medium',
        pill !== false ? 'rounded-full' : 'rounded',
        size === 'xs' ? 'px-1.5 py-px text-[10px]'
        : size === 'md' ? 'px-3 py-1 text-xs'
        : 'px-2 py-0.5 text-xs',
        variant === 'success' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
        : variant === 'warning' ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200'
        : variant === 'danger'  ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200'
        : variant === 'info'    ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200'
        : variant === 'purple'  ? 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-200'
        : variant === 'brand'   ? 'bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-200'
        : 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200',
    ]">
        <span v-if="dot" class="h-1.5 w-1.5 rounded-full shrink-0"
            :class="variant === 'success' ? 'bg-emerald-500'
                : variant === 'warning' ? 'bg-amber-500'
                : variant === 'danger'  ? 'bg-red-500'
                : variant === 'info'    ? 'bg-blue-500'
                : variant === 'purple'  ? 'bg-purple-500'
                : variant === 'brand'   ? 'bg-brand-500'
                : 'bg-gray-400'"
        ></span>
        <slot />
    </span>
</template>
