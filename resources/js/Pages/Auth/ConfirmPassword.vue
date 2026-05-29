<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
const form = useForm({ password: '' })
const submit = () => form.post(route('password.confirm'), { onFinish: () => form.reset() })
</script>

<template>
    <Head title="Confirm Password" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2 mb-6">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-xl font-bold text-ink font-display">Veltrixo</span>
                </Link>
                <h1 class="text-2xl font-bold text-ink font-display">Confirm your password</h1>
                <p class="mt-2 text-sm text-ink-muted max-w-xs mx-auto">
                    This area is secure. Please confirm your password before continuing.
                </p>
            </div>
            <div class="card p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="field-label">Password</label>
                        <input v-model="form.password" type="password" required autocomplete="current-password"
                            :class="['field-input', form.errors.password && 'field-input-error']"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full btn btn-primary btn-lg">
                        {{ form.processing ? 'Confirming…' : 'Confirm Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
