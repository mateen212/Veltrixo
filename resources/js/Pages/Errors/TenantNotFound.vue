<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Tenant Not Found" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="text-center max-w-sm">
            <p class="text-7xl font-bold text-border-strong font-display">404</p>
            <h1 class="mt-4 text-xl font-bold text-ink font-display">Tenant not found</h1>
            <p class="mt-2 text-sm text-ink-muted">The workspace you are looking for does not exist or has been removed.</p>
            <Link href="/" class="mt-6 inline-flex btn btn-primary btn-sm">Go Home</Link>
        </div>
    </div>
</template>
