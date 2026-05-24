<script setup lang="ts">
defineProps<{
    variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline'
    size?: 'xs' | 'sm' | 'md' | 'lg'
    loading?: boolean
    disabled?: boolean
    type?: 'button' | 'submit' | 'reset'
    icon?: boolean
}>()
</script>

<template>
    <button
        :type="type ?? 'button'"
        :disabled="disabled || loading"
        :class="[
            'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed',
            // Variants
            variant === 'secondary' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-300'
            : variant === 'danger'  ? 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500'
            : variant === 'ghost'   ? 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-300'
            : variant === 'outline' ? 'bg-white text-gray-700 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 focus:ring-indigo-400'
            : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
            // Sizes
            icon && size === 'xs' ? 'p-1'
            : icon && size === 'sm' ? 'p-1.5'
            : icon ? 'p-2'
            : size === 'xs' ? 'px-2.5 py-1.5 text-xs'
            : size === 'sm' ? 'px-3 py-2 text-sm'
            : size === 'lg' ? 'px-6 py-3.5 text-base'
            : 'px-4 py-2.5 text-sm',
        ]"
    >
        <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <slot />
    </button>
</template>
