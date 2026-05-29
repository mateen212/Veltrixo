<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps<{ canResetPassword?: boolean; status?: string }>()

const form = useForm({ email: '', password: '', remember: false })
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') })
</script>

<template>
    <Head title="Sign In — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between relative overflow-hidden"
            style="background: linear-gradient(135deg, #1a1a3e 0%, #0f0f2e 50%, #0c0c1d 100%);">
            <!-- Decorative mesh gradient -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/4 -left-24 w-96 h-96 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #4F46E5 0%, transparent 70%);" />
                <div class="absolute bottom-1/3 right-0 w-72 h-72 rounded-full opacity-15"
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
                            Welcome back.<br/>
                            <span style="background: linear-gradient(135deg, #818CF8, #A78BFA); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                Your deliveries
                            </span><br/>are waiting.
                        </h1>
                        <p class="mt-5 text-white/50 text-base leading-relaxed">
                            Manage recurring deliveries, riders, and customers — all from one place.
                        </p>

                        <!-- Feature highlights -->
                        <div class="mt-10 space-y-4">
                            <div v-for="f in [
                                { icon: '🚀', text: 'Auto-dispatch rider assignments' },
                                { icon: '💳', text: 'Built-in wallet & billing system' },
                                { icon: '📊', text: 'Real-time analytics dashboard' },
                            ]" :key="f.text"
                                class="flex items-center gap-3">
                                <span class="text-lg leading-none">{{ f.icon }}</span>
                                <span class="text-sm text-white/60">{{ f.text }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-white/25 relative z-10">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
            </div>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-10 lg:px-16 bg-white">
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden w-fit">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold text-lg"
                    style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                <span class="text-xl font-bold tracking-tight text-ink font-display">Veltrixo</span>
            </Link>

            <div class="w-full max-w-sm mx-auto">
                <h2 class="text-2xl font-bold text-ink font-display">Sign in to your account</h2>
                <p class="mt-1.5 text-sm text-ink-muted">
                    Don't have an account?
                    <Link :href="route('register')" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                        Register free
                    </Link>
                </p>

                <div v-if="status" class="mt-4 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 px-4 py-3 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="field-label">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            :class="['field-input', form.errors.email && 'field-input-error']"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="field-label mb-0">Password</label>
                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                                Forgot password?
                            </Link>
                        </div>
                        <input v-model="form.password" type="password" required autocomplete="current-password"
                            :class="['field-input', form.errors.password && 'field-input-error']"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" v-model="form.remember" type="checkbox"
                            class="h-4 w-4 rounded border-border-strong text-brand-600 focus:ring-brand-500/30" />
                        <label for="remember" class="text-sm text-ink-secondary">Remember me</label>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full btn btn-primary btn-lg transition-all">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Signing in…' : 'Sign In' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
