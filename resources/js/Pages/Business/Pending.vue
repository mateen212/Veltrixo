<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Verification Pending" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="w-full max-w-md text-center">
            <div class="card p-10 space-y-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 ring-1 ring-amber-200 mx-auto">
                    <svg class="h-7 w-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-ink font-display">Verification Pending</h1>
                <p class="text-sm text-ink-muted leading-relaxed max-w-xs mx-auto">
                    Your business registration is under review. We'll notify you once it's approved — usually within 24 hours.
                </p>
                <div class="pt-2">
                    <Link :href="route('logout')" method="post" as="button" class="btn btn-outline btn-sm">
                        Sign Out
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
