<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <Head title="Create Account — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">Start delivering<br/>smarter today.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">Set up your business in minutes. No credit card required.</p>
                <ul class="mt-8 space-y-3 text-sm text-indigo-100">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-indigo-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Recurring delivery subscriptions
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-indigo-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Rider dispatch & live tracking
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-indigo-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Built-in wallet & invoicing
                    </li>
                </ul>
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
                <h2 class="text-2xl font-bold text-gray-900">Create your account</h2>
                <p class="mt-1 text-sm text-gray-500">Already have an account? <Link :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</Link></p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                        <input v-model="form.name" type="text" required autocomplete="name"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="John Doe" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                        <input v-model="form.email" type="email" required autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="you@example.com" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input v-model="form.password" type="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="Min. 8 characters" />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                        <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 outline-none transition"
                            placeholder="••••••••" />
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Creating account…' : 'Create Account' }}
                    </button>

                    <p class="text-center text-xs text-gray-400">
                        By registering you agree to our
                        <a href="#" class="text-indigo-600 hover:underline">Terms of Service</a>
                        and
                        <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
