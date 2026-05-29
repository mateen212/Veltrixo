<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Account Suspended" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="text-center max-w-sm">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 ring-1 ring-red-200 mx-auto mb-5">
                <svg class="h-7 w-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-ink font-display">Account Suspended</h1>
            <p class="mt-2 text-sm text-ink-muted leading-relaxed">
                This workspace has been suspended. Please contact support or the platform admin.
            </p>
            <Link href="/" class="mt-6 inline-flex btn btn-outline btn-sm">Go Home</Link>
        </div>
    </div>
</template>
