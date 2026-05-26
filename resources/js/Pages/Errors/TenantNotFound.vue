<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps<{ subdomain: string }>()
</script>

<template>
    <Head title="Business Not Found — Veltrixo" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-red-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-red-100">
                <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-3">Business Not Found</h1>

            <p class="text-slate-500 mb-6">
                The business portal
                <strong class="text-slate-700">{{ subdomain }}.veltrixo.com</strong>
                does not exist or has been removed.
            </p>

            <a
                href="/"
                class="inline-block bg-slate-800 text-white px-8 py-2.5 rounded-lg font-medium hover:bg-slate-700 transition"
            >
                Go to Veltrixo
            </a>
        </div>
    </div>
</template>
