<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' })
const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') })
</script>

<template>
    <Head title="Create Account — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between relative overflow-hidden"
            style="background: linear-gradient(135deg, #1a1a3e 0%, #0f0f2e 50%, #0c0c1d 100%);">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/3 -left-24 w-96 h-96 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #4F46E5 0%, transparent 70%);" />
                <div class="absolute bottom-1/4 right-0 w-64 h-64 rounded-full opacity-15"
                    style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%);" />
            </div>

            <div class="relative z-10 p-12 flex flex-col h-full">
                <Link href="/" class="flex items-center gap-2.5 w-fit">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl font-bold text-xl text-white"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-xl font-bold text-white font-display tracking-tight">Veltrixo</span>
                </Link>

                <div class="flex-1 flex flex-col justify-center">
                    <div class="max-w-sm">
                        <h1 class="text-4xl font-bold text-white leading-tight font-display">
                            Start delivering<br/>
                            <span style="background: linear-gradient(135deg, #818CF8, #A78BFA); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                smarter today.
                            </span>
                        </h1>
                        <p class="mt-5 text-white/50 text-base leading-relaxed">
                            Set up your business in minutes. No credit card required.
                        </p>

                        <ul class="mt-10 space-y-3.5">
                            <li v-for="item in [
                                'Recurring delivery subscriptions',
                                'Rider dispatch & live tracking',
                                'Built-in wallet & invoicing',
                                'Multi-tenant isolated workspaces',
                            ]" :key="item" class="flex items-center gap-3">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-500/20 shrink-0">
                                    <svg class="h-3 w-3 text-brand-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-white/60">{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <p class="text-sm text-white/25 relative z-10">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
            </div>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-10 lg:px-16 bg-white overflow-y-auto">
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden w-fit">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold text-lg"
                    style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                <span class="text-xl font-bold tracking-tight text-ink font-display">Veltrixo</span>
            </Link>

            <div class="w-full max-w-sm mx-auto">
                <h2 class="text-2xl font-bold text-ink font-display">Create your account</h2>
                <p class="mt-1.5 text-sm text-ink-muted">
                    Already have an account?
                    <Link :href="route('login')" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Sign in</Link>
                </p>
                <p class="mt-3 text-xs text-ink-faint border-t border-border-muted pt-3">
                    Are you registering a business? <Link :href="route('business.register')" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Register as a business owner</Link>
                </p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="field-label">Full name</label>
                        <input v-model="form.name" type="text" required autocomplete="name"
                            :class="['field-input', form.errors.name && 'field-input-error']"
                            placeholder="John Doe" />
                        <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="field-label">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            :class="['field-input', form.errors.email && 'field-input-error']"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="field-label">Password</label>
                        <input v-model="form.password" type="password" required autocomplete="new-password"
                            :class="['field-input', form.errors.password && 'field-input-error']"
                            placeholder="Min. 8 characters" />
                        <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="field-label">Confirm password</label>
                        <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                            :class="['field-input', form.errors.password_confirmation && 'field-input-error']"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password_confirmation" class="field-error">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full btn btn-primary btn-lg">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Creating account…' : 'Create Account' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
