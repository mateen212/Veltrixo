<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
defineProps<{ status?: string }>()
const form = useForm({ email: '' })
const submit = () => form.post(route('password.email'))
</script>

<template>
    <Head title="Reset Password — Veltrixo" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2 mb-6">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-xl font-bold text-ink font-display">Veltrixo</span>
                </Link>
                <h1 class="text-2xl font-bold text-ink font-display">Reset your password</h1>
                <p class="mt-2 text-sm text-ink-muted max-w-sm mx-auto">
                    Enter your email and we'll send a password reset link.
                </p>
            </div>

            <div class="card p-8">
                <div v-if="status" class="mb-5 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 px-4 py-3 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="field-label">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            :class="['field-input', form.errors.email && 'field-input-error']"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full btn btn-primary btn-lg">
                        {{ form.processing ? 'Sending…' : 'Send Reset Link' }}
                    </button>
                    <div class="text-center">
                        <Link :href="route('login')" class="text-sm text-ink-muted hover:text-ink transition-colors">
                            ← Back to sign in
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
