<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps<{ tenantName: string }>()
</script>

<template>
    <Head title="Account Suspended — Veltrixo" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-amber-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-10 w-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-3">Account Suspended</h1>

            <p class="text-slate-500 mb-6">
                <strong class="text-slate-700">{{ tenantName }}</strong> has been temporarily suspended.
                Please contact support to resolve this issue.
            </p>

            <a
                href="mailto:support@veltrixo.com"
                class="inline-block bg-amber-500 text-white px-8 py-2.5 rounded-lg font-medium hover:bg-amber-600 transition"
            >
                Contact Support
            </a>
        </div>
    </div>
</template>
