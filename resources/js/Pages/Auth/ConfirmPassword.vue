<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });
const submit = () => form.post(route('password.confirm'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Confirm Password — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">Security<br/>checkpoint.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">This area requires your password to continue.</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900">Confirm your password</h2>
                <p class="mt-1 text-sm text-gray-500">Please confirm your password before continuing to this secure area.</p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input v-model="form.password" type="password" required autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Confirming…' : 'Confirm Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
