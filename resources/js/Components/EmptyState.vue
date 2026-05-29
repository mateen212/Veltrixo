<script setup lang="ts">
defineProps<{
    title?: string
    description?: string
    icon?: string
    action?: string
    actionHref?: string
    size?: 'sm' | 'md' | 'lg'
}>()

const emit = defineEmits<{ action: [] }>()
</script>

<template>
    <div :class="[
        'flex flex-col items-center text-center py-16 px-6',
        size === 'sm' ? 'py-10' : size === 'lg' ? 'py-24' : 'py-16',
    ]">
        <!-- Icon -->
        <div class="relative mb-5">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-subtle ring-1 ring-border">
                <span v-if="icon" class="text-3xl select-none">{{ icon }}</span>
                <svg v-else class="h-7 w-7 text-ink-faint" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <!-- Decorative dots -->
            <span class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-border-muted ring-2 ring-white" />
            <span class="absolute -bottom-1 -left-1 h-2 w-2 rounded-full bg-brand-100 ring-2 ring-white" />
        </div>

        <h3 class="text-base font-semibold text-ink">{{ title ?? 'Nothing here yet' }}</h3>
        <p class="mt-1.5 text-sm text-ink-muted max-w-xs text-balance">
            {{ description ?? 'Get started by creating your first entry.' }}
        </p>

        <div v-if="action" class="mt-6">
            <a v-if="actionHref" :href="actionHref"
                class="btn btn-primary btn-sm gap-1.5">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ action }}
            </a>
            <button v-else @click="emit('action')"
                class="btn btn-primary btn-sm gap-1.5">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ action }}
            </button>
        </div>

        <slot />
    </div>
</template>
