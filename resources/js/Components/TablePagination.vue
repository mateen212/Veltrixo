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
    <div v-if="meta" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3 border-t border-gray-100 bg-white">
        <p class="text-sm text-gray-500 order-2 sm:order-1">
            <template v-if="meta.from && meta.to">
                Showing <span class="font-medium text-gray-700">{{ meta.from }}</span>–<span class="font-medium text-gray-700">{{ meta.to }}</span>
                of <span class="font-medium text-gray-700">{{ meta.total }}</span> results
            </template>
            <template v-else>
                <span class="font-medium text-gray-700">{{ meta.total }}</span> results
            </template>
        </p>
        <div class="flex items-center gap-1 order-1 sm:order-2">
            <template v-for="link in safeLinks" :key="link.label">
                <component
                    :is="link.url ? Link : 'span'"
                    :href="link.url ?? undefined"
                    :class="[
                        'inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-medium transition-colors',
                        link.active
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : link.url
                                ? 'text-gray-600 hover:bg-gray-100'
                                : 'text-gray-300 cursor-not-allowed',
                    ]"
                    v-html="link.label"
                    preserve-scroll
                />
            </template>
        </div>
    </div>
</template>
