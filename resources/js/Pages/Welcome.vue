<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

const features = [
    { icon: '📦', bg: 'bg-indigo-50', title: 'Subscription Management', description: 'Let customers subscribe to daily, weekly, or custom delivery schedules with pause, skip, and cancel controls.' },
    { icon: '🛵', bg: 'bg-green-50',  title: 'Rider Dispatch',           description: 'Auto-assign deliveries to riders by route and zone. Track real-time location and delivery status.' },
    { icon: '💳', bg: 'bg-yellow-50', title: 'Wallet & Payments',        description: 'Built-in digital wallet with recharge requests, auto-deduction on delivery, and full transaction history.' },
    { icon: '🏢', bg: 'bg-purple-50', title: 'Multi-Tenant',             description: 'Each business gets its own isolated workspace with branded portal, custom plans, and independent data.' },
    { icon: '📊', bg: 'bg-pink-50',   title: 'Admin Analytics',          description: 'Real-time KPIs for deliveries, revenue, active subscriptions, and missed deliveries.' },
    { icon: '🔔', bg: 'bg-orange-50', title: 'Smart Notifications',      description: 'Push and in-app notifications for delivery updates, subscription renewals, and wallet top-ups.' },
];

const steps = [
    { title: 'Create your tenant',   description: 'Sign up, set your business name, upload a logo, and configure your delivery zones in minutes.' },
    { title: 'Add products & plans', description: 'Define your product catalogue, set recurring subscription plans, and assign pricing tiers.' },
    { title: 'Go live',              description: 'Share your customer portal. Customers subscribe, riders get dispatched, and deliveries roll out automatically.' },
];

const plans = [
    { name: 'Starter',    price: '$29',  featured: false, description: 'Perfect for small dairies and local stores.',      features: ['Up to 200 deliveries/mo', '2 riders', '1 admin seat', 'Email support'] },
    { name: 'Growth',     price: '$79',  featured: true,  description: 'For growing businesses with bigger volumes.',       features: ['Up to 2,000 deliveries/mo', '10 riders', '5 admin seats', 'Wallet & invoicing', 'Priority support'] },
    { name: 'Enterprise', price: '$199', featured: false, description: 'Unlimited scale with dedicated support.',           features: ['Unlimited deliveries', 'Unlimited riders', 'Custom integrations', 'SLA & dedicated CSM'] },
];

const year = new Date().getFullYear();
</script>

<template>
    <Head title="Veltrixo — Smart Recurring Delivery Platform" />

    <div class="min-h-screen bg-white font-sans text-gray-900 antialiased">

        <!-- Navbar -->
        <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-lg">V</div>
                    <span class="text-xl font-bold tracking-tight text-gray-900">Veltrixo</span>
                </div>
                <nav class="hidden gap-8 text-sm font-medium text-gray-600 md:flex">
                    <a href="#features"    class="hover:text-indigo-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="hover:text-indigo-600 transition-colors">How It Works</a>
                    <a href="#pricing"     class="hover:text-indigo-600 transition-colors">Pricing</a>
                </nav>
                <div class="flex items-center gap-3">
                    <Link v-if="canLogin"    :href="route('login')"    class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Sign In</Link>
                    <Link v-if="canRegister" :href="route('register')" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Get Started</Link>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden pt-32 pb-24">
            <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 h-[600px] w-[900px] rounded-full bg-indigo-100 opacity-50 blur-3xl"></div>
            <div class="relative mx-auto max-w-5xl px-6 text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 ring-1 ring-indigo-200 mb-6">
                    🚀 Recurring Delivery, Simplified
                </span>
                <h1 class="text-5xl font-extrabold leading-tight tracking-tight text-gray-900 md:text-6xl lg:text-7xl">
                    Deliver Smarter,<br/>
                    <span class="text-indigo-600">Every Single Day</span>
                </h1>
                <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    Veltrixo is a multi-tenant SaaS platform built for dairy farms, grocery stores, and subscription box businesses
                    to manage recurring deliveries, riders, and customers — all in one place.
                </p>
                <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link v-if="canRegister" :href="route('register')"
                        class="rounded-xl bg-indigo-600 px-8 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-all hover:-translate-y-0.5">
                        Start Free Trial
                    </Link>
                    <a href="#how-it-works"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-8 py-3.5 text-base font-semibold text-gray-700 shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        See How It Works
                    </a>
                </div>
                <!-- Stats strip -->
                <div class="mt-16 flex flex-wrap justify-center gap-x-12 gap-y-6 border-t border-gray-100 pt-10">
                    <div><p class="text-3xl font-bold text-indigo-600">10k+</p><p class="text-sm text-gray-500 mt-1">Deliveries / Day</p></div>
                    <div><p class="text-3xl font-bold text-indigo-600">500+</p><p class="text-sm text-gray-500 mt-1">Active Tenants</p></div>
                    <div><p class="text-3xl font-bold text-indigo-600">99.9%</p><p class="text-sm text-gray-500 mt-1">Uptime SLA</p></div>
                    <div><p class="text-3xl font-bold text-indigo-600">4.9 ★</p><p class="text-sm text-gray-500 mt-1">Customer Rating</p></div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="bg-gray-50 py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Everything you need to run your delivery business</h2>
                    <p class="mt-4 text-gray-500 max-w-xl mx-auto">One platform for tenants, customers, riders, and admins.</p>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="f in features" :key="f.title"
                        class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-100 hover:shadow-md hover:-translate-y-1 transition-all">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl text-2xl" :class="f.bg">{{ f.icon }}</div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ f.title }}</h3>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ f.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-24 bg-white">
            <div class="mx-auto max-w-5xl px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Up and running in minutes</h2>
                    <p class="mt-4 text-gray-500">Three simple steps to launch your delivery operation.</p>
                </div>
                <div class="grid gap-12 md:grid-cols-3">
                    <div v-for="(step, i) in steps" :key="i" class="text-center">
                        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white text-xl font-bold shadow-lg ring-4 ring-indigo-100">
                            {{ i + 1 }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ step.title }}</h3>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="bg-gray-50 py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Simple, transparent pricing</h2>
                    <p class="mt-4 text-gray-500">No hidden fees. Cancel anytime.</p>
                </div>
                <div class="grid gap-8 md:grid-cols-3 max-w-5xl mx-auto">
                    <div v-for="plan in plans" :key="plan.name"
                        class="rounded-2xl p-8 ring-1 transition-all"
                        :class="plan.featured ? 'bg-indigo-600 text-white ring-indigo-600 shadow-2xl scale-105' : 'bg-white text-gray-900 ring-gray-100 shadow-sm hover:shadow-md'">
                        <p class="text-sm font-semibold uppercase tracking-widest" :class="plan.featured ? 'text-indigo-200' : 'text-indigo-600'">{{ plan.name }}</p>
                        <p class="mt-4 text-4xl font-extrabold">{{ plan.price }}<span class="text-lg font-medium">/mo</span></p>
                        <p class="mt-2 text-sm" :class="plan.featured ? 'text-indigo-200' : 'text-gray-500'">{{ plan.description }}</p>
                        <ul class="mt-8 space-y-3">
                            <li v-for="feat in plan.features" :key="feat" class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 shrink-0" :class="plan.featured ? 'text-indigo-200' : 'text-indigo-500'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ feat }}
                            </li>
                        </ul>
                        <Link v-if="canRegister" :href="route('register')"
                            class="mt-8 block rounded-xl py-3 text-center text-sm font-semibold transition-all"
                            :class="plan.featured ? 'bg-white text-indigo-600 hover:bg-indigo-50' : 'bg-indigo-600 text-white hover:bg-indigo-700'">
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-24 bg-indigo-600">
            <div class="mx-auto max-w-3xl px-6 text-center">
                <h2 class="text-3xl font-bold text-white md:text-4xl">Ready to scale your delivery business?</h2>
                <p class="mt-4 text-indigo-200">Join hundreds of businesses already using Veltrixo to delight their customers.</p>
                <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link v-if="canRegister" :href="route('register')"
                        class="rounded-xl bg-white px-8 py-3.5 text-base font-semibold text-indigo-600 shadow-lg hover:bg-indigo-50 transition-all">
                        Start Free Trial
                    </Link>
                    <Link v-if="canLogin" :href="route('login')"
                        class="rounded-xl border border-indigo-400 px-8 py-3.5 text-base font-semibold text-white hover:bg-indigo-700 transition-all">
                        Sign In
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 py-12">
            <div class="mx-auto max-w-7xl px-6 flex flex-col items-center gap-4 md:flex-row md:justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold text-sm">V</div>
                    <span class="text-base font-semibold text-white">Veltrixo</span>
                </div>
                <p class="text-sm text-gray-500">© {{ year }} Veltrixo. All rights reserved.</p>
                <div class="flex gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Contact</a>
                </div>
            </div>
        </footer>

    </div>
</template>
