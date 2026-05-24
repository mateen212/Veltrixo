<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{ status?: string }>();

const form = useForm({ email: '' });
const submit = () => form.post(route('password.email'));
</script>

<template>
    <Head title="Forgot Password — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">Locked out?<br/>We've got you.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">Enter your email and we'll send a secure link to reset your password.</p>
            </div>
            <p class="text-sm text-indigo-300">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-12 lg:px-20 bg-white">
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-lg">V</div>
                <span class="text-xl font-bold tracking-tight text-gray-900">Veltrixo</span>
            </Link>

            <div class="w-full max-w-sm mx-auto">
                <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50">
                    <svg class="h-7 w-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900">Forgot your password?</h2>
                <p class="mt-1 text-sm text-gray-500">No worries. Enter your email below and we'll send you a reset link.</p>

                <div v-if="status" class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ status }}</div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Sending…' : 'Send Reset Link' }}
                    </button>

                    <div class="text-center">
                        <Link :href="route('login')" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            ← Back to Sign In
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
