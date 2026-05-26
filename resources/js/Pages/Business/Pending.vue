<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Registration Under Review — Veltrixo" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md text-center">
            <!-- Icon -->
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-10 w-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-3">Registration Under Review</h1>

            <p class="text-slate-600 mb-4">
                Thank you for registering! Our team will review your application and you will receive an email
                within <strong>24–48 hours</strong> once your business is approved.
            </p>

            <p class="text-slate-500 text-sm mb-8">
                In the meantime, feel free to <a href="/contact" class="text-indigo-600 hover:underline">contact support</a>
                if you have any questions.
            </p>

            <a
                :href="route('home')"
                class="inline-block bg-indigo-600 text-white px-8 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition"
            >
                Back to Home
            </a>
        </div>
    </div>
</template>
