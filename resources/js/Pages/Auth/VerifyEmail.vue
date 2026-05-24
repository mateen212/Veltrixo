<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{ status?: string }>();
const verified = computed(() => props.status === 'verification-link-sent');

const form = useForm({});
const submit = () => form.post(route('verification.send'));
</script>

<template>
    <Head title="Verify Email — Veltrixo" />

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-indigo-600 p-12 text-white">
            <Link href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold text-xl">V</div>
                <span class="text-xl font-bold tracking-tight">Veltrixo</span>
            </Link>
            <div>
                <h1 class="text-4xl font-extrabold leading-snug">One last step<br/>to get started.</h1>
                <p class="mt-4 text-indigo-200 text-base max-w-sm">Verify your email address to activate your Veltrixo account.</p>
            </div>
            <p class="text-sm text-indigo-300">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-12 lg:px-20 bg-white">
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-lg">V</div>
                <span class="text-xl font-bold tracking-tight text-gray-900">Veltrixo</span>
            </Link>

            <div class="w-full max-w-sm mx-auto text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900">Check your inbox</h2>
                <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                    Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent you.
                </p>

                <div v-if="verified" class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                    A new verification link has been sent to your email.
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-4">
                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-60 transition-all">
                        {{ form.processing ? 'Sending…' : 'Resend Verification Email' }}
                    </button>
                </form>

                <Link :href="route('logout')" method="post" as="button"
                    class="mt-4 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Sign out
                </Link>
            </div>
        </div>
    </div>
</template>
