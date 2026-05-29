<script setup lang="ts">
defineProps<{
    lines?: number
    type?: 'text' | 'card' | 'table' | 'avatar' | 'kpi'
    rows?: number
}>()
</script>

<template>
    <!-- KPI Skeleton -->
    <div v-if="type === 'kpi'" class="card p-5 flex items-start gap-4">
        <div class="skeleton h-11 w-11 rounded-xl shrink-0" />
        <div class="flex-1 space-y-2 pt-1">
            <div class="skeleton h-3 w-24 rounded" />
            <div class="skeleton h-7 w-16 rounded" />
        </div>
    </div>

    <!-- Table Skeleton -->
    <div v-else-if="type === 'table'" class="space-y-px">
        <div v-for="i in (rows ?? 5)" :key="i"
            class="flex items-center gap-4 px-4 py-3.5 bg-white border-b border-border-muted last:border-0">
            <div class="skeleton h-4 w-8 rounded" />
            <div class="skeleton h-4 flex-1 rounded" />
            <div class="skeleton h-4 w-20 rounded" />
            <div class="skeleton h-5 w-16 rounded-full" />
            <div class="skeleton h-4 w-14 rounded" />
        </div>
    </div>

    <!-- Card Skeleton -->
    <div v-else-if="type === 'card'" class="card p-5 space-y-3">
        <div class="skeleton h-4 w-3/5 rounded" />
        <div class="skeleton h-3 w-4/5 rounded" />
        <div class="skeleton h-3 w-2/5 rounded" />
        <div class="skeleton h-8 w-24 rounded-lg mt-2" />
    </div>

    <!-- Avatar + text Skeleton -->
    <div v-else-if="type === 'avatar'" class="flex items-center gap-3">
        <div class="skeleton h-9 w-9 rounded-full shrink-0" />
        <div class="flex-1 space-y-2">
            <div class="skeleton h-3.5 w-2/3 rounded" />
            <div class="skeleton h-3 w-1/2 rounded" />
        </div>
    </div>

    <!-- Text Lines Skeleton (default) -->
    <div v-else class="space-y-2">
        <div v-for="i in (lines ?? 3)" :key="i"
            class="skeleton h-4 rounded"
            :style="{ width: i === (lines ?? 3) ? '60%' : '100%' }" />
    </div>
</template>
