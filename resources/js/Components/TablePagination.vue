<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
    meta?: {
        current_page: number
        last_page: number
        per_page: number
        total: number
        from: number | null
        to: number | null
    } | null
    links?: Array<{ url: string | null; label: string; active: boolean } | null> | null
}>()

const safeLinks = computed(() => {
    const raw = props.links ?? []
    if (Array.isArray(raw)) {
        return raw.filter((l): l is { url: string | null; label: string; active: boolean } => l !== null)
    }
    if (raw && typeof raw === 'object') {
        return Object.values(raw)
            .filter(Boolean)
            .map((l: any) => ({
                url: l?.url ?? null,
                label: String(l?.label ?? ''),
                active: Boolean(l?.active ?? false),
            }))
    }
    return []
})
</script>
<template>
    <div v-if="meta && meta.total > 0"
        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3.5 border-t border-border-muted bg-surface-muted/40">
        <p class="text-xs text-ink-muted order-2 sm:order-1">
            <template v-if="meta.from && meta.to">
                Showing <span class="font-semibold text-ink-secondary">{{ meta.from }}</span>–<span class="font-semibold text-ink-secondary">{{ meta.to }}</span>
                of <span class="font-semibold text-ink-secondary">{{ meta.total }}</span>
            </template>
            <template v-else>
                <span class="font-semibold text-ink-secondary">{{ meta.total }}</span> results
            </template>
        </p>
        <div class="flex items-center gap-1 order-1 sm:order-2">
            <template v-for="link in safeLinks" :key="link.label">
                <component
                    :is="link.url ? Link : 'span'"
                    :href="link.url ?? undefined"
                    :class="[
                        'inline-flex h-7 min-w-7 items-center justify-center rounded-md px-2 text-xs font-medium transition-all duration-100',
                        link.active
                            ? 'bg-brand-600 text-white shadow-sm'
                            : link.url
                                ? 'text-ink-secondary hover:bg-surface-subtle hover:text-ink'
                                : 'text-ink-faint cursor-not-allowed',
                    ]"
                    v-html="link.label"
                    preserve-scroll
                />
            </template>
        </div>
    </div>
</template>
