<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
const props = defineProps<{ email: string; token: string }>()
const form  = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' })
const submit = () => form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') })
</script>

<template>
    <Head title="Set New Password — Veltrixo" />
    <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2 mb-6">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-xl font-bold text-ink font-display">Veltrixo</span>
                </Link>
                <h1 class="text-2xl font-bold text-ink font-display">Set new password</h1>
            </div>
            <div class="card p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="field-label">Email address</label>
                        <input v-model="form.email" type="email" required :class="['field-input', form.errors.email && 'field-input-error']" />
                        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="field-label">New password</label>
                        <input v-model="form.password" type="password" required autocomplete="new-password"
                            :class="['field-input', form.errors.password && 'field-input-error']" placeholder="Min. 8 characters" />
                        <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="field-label">Confirm password</label>
                        <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                            :class="['field-input', form.errors.password_confirmation && 'field-input-error']" placeholder="••••••••" />
                        <p v-if="form.errors.password_confirmation" class="field-error">{{ form.errors.password_confirmation }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full btn btn-primary btn-lg">
                        {{ form.processing ? 'Updating…' : 'Reset Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
