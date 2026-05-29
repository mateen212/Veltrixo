<script setup lang="ts">
defineProps<{
    variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline' | 'brand'
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
    loading?: boolean
    disabled?: boolean
    type?: 'button' | 'submit' | 'reset'
    icon?: boolean
    href?: string
    full?: boolean
}>()
</script>

<template>
    <component
        :is="href ? 'a' : 'button'"
        :href="href"
        :type="href ? undefined : (type ?? 'button')"
        :disabled="!href && (disabled || loading)"
        :class="[
            // Base
            'inline-flex items-center justify-center gap-2 font-semibold rounded-lg',
            'transition-all duration-150 select-none',
            'focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-1',
            'disabled:opacity-50 disabled:cursor-not-allowed',
            full ? 'w-full' : '',

            // Variants
            variant === 'secondary' ? 'bg-surface-subtle text-ink-secondary border border-border hover:bg-border-muted hover:border-border-strong'
            : variant === 'danger'  ? 'bg-red-600 text-white hover:bg-red-700 shadow-sm hover:shadow-md active:scale-[0.98]'
            : variant === 'ghost'   ? 'bg-transparent text-ink-muted hover:bg-surface-subtle hover:text-ink-secondary'
            : variant === 'outline' ? 'bg-white text-ink-secondary border border-border hover:border-border-strong hover:bg-surface-subtle'
            : variant === 'brand'   ? 'bg-brand-gradient text-white shadow-md hover:shadow-lg hover:opacity-90 active:scale-[0.98]'
            : 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm hover:shadow-md active:scale-[0.98]',

            // Sizes
            icon && size === 'xs' ? 'p-1.5 rounded'
            : icon && size === 'sm' ? 'p-2 rounded-lg'
            : icon ? 'p-2.5 rounded-lg'
            : size === 'xs'  ? 'px-2.5 py-1.5 text-xs'
            : size === 'sm'  ? 'px-3 py-2 text-xs'
            : size === 'lg'  ? 'px-5 py-2.5 text-sm'
            : size === 'xl'  ? 'px-7 py-3.5 text-base'
            : 'px-4 py-2 text-sm',
        ]"
    >
        <svg v-if="loading" class="animate-spin h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <slot />
    </component>
</template>
