<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
defineProps<{ status?: string }>()
const form = useForm({})
const submit = () => form.post(route('verification.send'))
const verificationLinkSent = computed(() => (typeof status !== 'undefined'))
</script>

<template>
    <Head title="Email Verification — Veltrixo" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="w-full max-w-md text-center">
            <div class="card p-8 space-y-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 ring-1 ring-brand-200 mx-auto">
                    <svg class="h-7 w-7 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-ink font-display">Verify your email</h1>
                <p class="text-sm text-ink-muted max-w-xs mx-auto leading-relaxed">
                    We sent a verification link to your email. Please check your inbox.
                </p>
                <div v-if="status === 'verification-link-sent'"
                    class="rounded-xl bg-emerald-50 ring-1 ring-emerald-200 px-4 py-3 text-sm text-emerald-700">
                    A new verification link has been sent.
                </div>
                <form @submit.prevent="submit">
                    <button type="submit" :disabled="form.processing" class="w-full btn btn-primary btn-md">
                        {{ form.processing ? 'Sending…' : 'Resend Verification Email' }}
                    </button>
                </form>
                <Link :href="route('logout')" method="post" as="button" class="text-sm text-ink-muted hover:text-ink transition-colors">
                    Sign out
                </Link>
            </div>
        </div>
    </div>
</template>
