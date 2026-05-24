<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{ token: string; email: string }>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <Head title="Reset Password — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">Set a new<br/>strong password.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">Choose a password you haven't used before, at least 8 characters.</p>
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
                <h2 class="text-2xl font-bold text-gray-900">Reset your password</h2>
                <p class="mt-1 text-sm text-gray-500">Enter your new password below.</p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                        <input v-model="form.password" type="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="Min. 8 characters" />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
                        <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Resetting…' : 'Reset Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
