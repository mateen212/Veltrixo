<template>
    <div class="card p-5 animate-fade-up">
        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">{{ title }}</p>
        <p class="text-2xl font-bold text-ink mt-1 tabular-nums">{{ value }}</p>
        <p v-if="sub" class="text-xs text-ink-faint mt-1.5">{{ sub }}</p>
        <div v-if="progress !== undefined" class="mt-3">
            <div class="h-1 w-full rounded-full bg-border-muted overflow-hidden">
                <div class="h-full rounded-full bg-brand-500 transition-all duration-700"
                    :style="{ width: Math.min(100, progress) + '%' }" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{ title: string; value: string | number; sub?: string; progress?: number }>()
</script>
