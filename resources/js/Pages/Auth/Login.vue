<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{ canResetPassword?: boolean; status?: string }>();

const form = useForm({ email: '', password: '', remember: false });

const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Sign In — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">Welcome back.<br/>Your deliveries<br/>are waiting.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">Manage recurring deliveries, riders, and customers — all from one place.</p>
            </div>
            <p class="text-sm text-indigo-300">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-12 lg:px-20 bg-white">
            <!-- Mobile logo -->
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-lg">V</div>
                <span class="text-xl font-bold tracking-tight text-gray-900">Veltrixo</span>
            </Link>

            <div class="w-full max-w-sm mx-auto">
                <h2 class="text-2xl font-bold text-gray-900">Sign in to your account</h2>
                <p class="mt-1 text-sm text-gray-500">Don't have an account? <Link :href="route('register')" class="font-medium text-indigo-600 hover:text-indigo-500">Register free</Link></p>

                <div v-if="status" class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ status }}</div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-indigo-600 hover:text-indigo-500">Forgot password?</Link>
                        </div>
                        <input v-model="form.password" type="password" required autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" v-model="form.remember" type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                        <label for="remember" class="text-sm text-gray-600">Remember me</label>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Signing in…' : 'Sign In' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
