<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

defineProps<{ canLogin?: boolean; canRegister?: boolean }>()

const heroVisible = ref(false)
const year = new Date().getFullYear()

onMounted(() => {
    // Trigger hero animations on mount
    setTimeout(() => { heroVisible.value = true }, 100)

    // Scroll reveal using IntersectionObserver
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view')
                    observer.unobserve(entry.target)
                }
            })
        },
        { threshold: 0.1, rootMargin: '0px 0px -60px 0px' }
    )
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el))
})

const features = [
    {
        icon: '🚚',
        title: 'Smart Dispatch',
        desc: 'Auto-assign deliveries to riders by zone and route. Real-time tracking updates customers instantly.',
        accent: 'from-blue-500/10 to-indigo-500/5',
        border: 'border-blue-200/60',
    },
    {
        icon: '🔄',
        title: 'Flexible Subscriptions',
        desc: 'Daily, weekly, or custom schedules. Customers control pause, skip, and cancel with one tap.',
        accent: 'from-violet-500/10 to-purple-500/5',
        border: 'border-violet-200/60',
    },
    {
        icon: '💳',
        title: 'Integrated Wallet',
        desc: 'Built-in digital wallet with auto-deduction on delivery, recharge flows, and full history.',
        accent: 'from-emerald-500/10 to-teal-500/5',
        border: 'border-emerald-200/60',
    },
    {
        icon: '🏢',
        title: 'Multi-Tenant',
        desc: 'Each business gets an isolated workspace with branded portal and independent data.',
        accent: 'from-amber-500/10 to-orange-500/5',
        border: 'border-amber-200/60',
    },
    {
        icon: '📊',
        title: 'Live Analytics',
        desc: 'KPIs for deliveries, revenue, active subscriptions, and missed deliveries in real time.',
        accent: 'from-pink-500/10 to-rose-500/5',
        border: 'border-pink-200/60',
    },
    {
        icon: '🔔',
        title: 'Smart Notifications',
        desc: 'Push and in-app notifications for every delivery event, renewal, and wallet activity.',
        accent: 'from-cyan-500/10 to-sky-500/5',
        border: 'border-cyan-200/60',
    },
]

const plans = [
    {
        name: 'Starter',
        price: '$29',
        period: '/month',
        featured: false,
        desc: 'Perfect for small dairies and local stores.',
        features: ['Up to 200 deliveries/mo', '2 riders', '1 admin seat', 'Email support'],
    },
    {
        name: 'Growth',
        price: '$79',
        period: '/month',
        featured: true,
        desc: 'For growing businesses scaling their operations.',
        features: ['Up to 2,000 deliveries/mo', '10 riders', '5 admin seats', 'Wallet & invoicing', 'Priority support'],
    },
    {
        name: 'Enterprise',
        price: '$199',
        period: '/month',
        featured: false,
        desc: 'Unlimited scale with dedicated success management.',
        features: ['Unlimited deliveries', 'Unlimited riders', 'Custom integrations', 'SLA + dedicated CSM'],
    },
]

const stats = [
    { value: '10k+', label: 'Deliveries / Day' },
    { value: '500+', label: 'Active Tenants' },
    { value: '99.9%', label: 'Uptime SLA' },
    { value: '4.9 ★', label: 'Customer Rating' },
]

const steps = [
    { step: '01', title: 'Create your tenant', desc: 'Sign up, name your business, upload a logo, and configure delivery zones in minutes.' },
    { step: '02', title: 'Add products & plans', desc: 'Define your catalogue, set recurring subscription plans, and assign pricing tiers.' },
    { step: '03', title: 'Go live', desc: 'Share your portal link. Customers subscribe, riders get dispatched, deliveries run automatically.' },
]

const navOpen = ref(false)
</script>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.reveal.in-view {
    opacity: 1;
    transform: translateY(0);
}
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
.reveal-delay-3 { transition-delay: 0.3s; }
.reveal-delay-4 { transition-delay: 0.4s; }
.reveal-delay-5 { transition-delay: 0.5s; }

.hero-text {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.hero-text.visible {
    opacity: 1;
    transform: translateY(0);
}
.hero-text-delay-1 { transition-delay: 0.15s; }
.hero-text-delay-2 { transition-delay: 0.3s; }
.hero-text-delay-3 { transition-delay: 0.45s; }
.hero-text-delay-4 { transition-delay: 0.6s; }
</style>

<template>
    <Head title="Veltrixo — Smart Recurring Delivery Platform" />

    <div class="min-h-screen bg-white font-sans text-ink antialiased overflow-x-hidden">

        <!-- ─── Navbar ───────────────────────────────────────────────────── -->
        <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
            style="background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.06);">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 h-16">
                <Link href="/" class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg text-white font-bold text-sm"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-lg font-bold tracking-tight text-ink font-display">Veltrixo</span>
                </Link>

                <nav class="hidden gap-8 text-sm font-medium text-ink-secondary md:flex">
                    <a href="#features"     class="hover:text-brand-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="hover:text-brand-600 transition-colors">How It Works</a>
                    <a href="#pricing"      class="hover:text-brand-600 transition-colors">Pricing</a>
                </nav>

                <div class="flex items-center gap-3">
                    <Link v-if="canLogin"    :href="route('login')"
                        class="hidden sm:inline-flex text-sm font-medium text-ink-secondary hover:text-ink transition-colors">
                        Sign In
                    </Link>
                    <Link v-if="canRegister" :href="route('register')"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-all hover:opacity-90 hover:-translate-y-px active:scale-[0.98] shadow-sm"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                        Get Started
                    </Link>
                    <button class="md:hidden p-2 rounded-lg hover:bg-surface-subtle" @click="navOpen = !navOpen">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path v-if="!navOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Mobile nav -->
            <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 -translate-y-2"
                leave-active-class="transition duration-150" leave-to-class="opacity-0 -translate-y-2">
                <div v-if="navOpen" class="md:hidden border-t border-border-muted px-6 py-4 space-y-3 bg-white">
                    <a href="#features"     class="block text-sm font-medium text-ink-secondary py-1" @click="navOpen=false">Features</a>
                    <a href="#how-it-works" class="block text-sm font-medium text-ink-secondary py-1" @click="navOpen=false">How It Works</a>
                    <a href="#pricing"      class="block text-sm font-medium text-ink-secondary py-1" @click="navOpen=false">Pricing</a>
                    <Link v-if="canLogin"    :href="route('login')"    class="block text-sm font-medium text-ink py-1">Sign In</Link>
                    <Link v-if="canRegister" :href="route('register')" class="block btn btn-primary btn-sm w-full text-center">Get Started</Link>
                </div>
            </Transition>
        </header>

        <!-- ─── Hero ─────────────────────────────────────────────────────── -->
        <section class="relative overflow-hidden pt-32 pb-28">
            <!-- Background gradients -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 h-[700px] w-full max-w-4xl"
                    style="background: radial-gradient(ellipse at center top, rgba(99,102,241,0.08) 0%, transparent 65%);" />
                <div class="absolute top-1/3 left-1/4 h-96 w-96 rounded-full blur-3xl opacity-10"
                    style="background: radial-gradient(circle, #7C3AED, transparent);" />
            </div>

            <div class="relative mx-auto max-w-4xl px-6 text-center">
                <div :class="['hero-text', heroVisible && 'visible']">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-200 mb-8"
                        style="background: rgba(99,102,241,0.06);">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-500 animate-pulse-subtle" />
                        Smart Recurring Delivery Platform
                    </span>
                </div>

                <h1 :class="['text-5xl sm:text-6xl lg:text-7xl font-bold leading-[1.08] tracking-tight font-display hero-text hero-text-delay-1', heroVisible && 'visible']">
                    Deliver smarter,<br/>
                    <span style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        every single day
                    </span>
                </h1>

                <p :class="['mt-7 text-lg text-ink-muted max-w-2xl mx-auto leading-relaxed hero-text hero-text-delay-2', heroVisible && 'visible']">
                    Veltrixo is a multi-tenant SaaS platform built for dairy farms, grocery stores, and subscription businesses
                    to manage recurring deliveries, riders, and customers — all in one intelligent system.
                </p>

                <div :class="['mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center hero-text hero-text-delay-3', heroVisible && 'visible']">
                    <Link :href="route('business.register')"
                        class="rounded-xl px-8 py-3.5 text-base font-semibold text-white transition-all hover:opacity-90 hover:-translate-y-0.5 shadow-lg"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED); box-shadow: 0 8px 24px -4px rgba(99,102,241,0.4);">
                        Register Your Business
                        <span class="ml-1">→</span>
                    </Link>
                    <a href="#features"
                        class="flex items-center gap-2 rounded-xl border border-border bg-white px-8 py-3.5 text-base font-semibold text-ink-secondary shadow-sm hover:border-border-strong hover:text-ink transition-all">
                        See Features
                    </a>
                </div>

                <!-- Stats -->
                <div :class="['mt-20 grid grid-cols-2 sm:grid-cols-4 gap-8 pt-12 border-t border-border-muted hero-text hero-text-delay-4', heroVisible && 'visible']">
                    <div v-for="s in stats" :key="s.value">
                        <p class="text-3xl font-bold text-ink font-display" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            {{ s.value }}
                        </p>
                        <p class="text-sm text-ink-muted mt-1.5">{{ s.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Features ──────────────────────────────────────────────────── -->
        <section id="features" class="py-28" style="background: #FAFAFA;">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-16 reveal">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink font-display tracking-tight">
                        Everything your delivery business needs
                    </h2>
                    <p class="mt-4 text-ink-muted max-w-xl mx-auto">
                        One platform for tenants, customers, riders, and admins.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(f, i) in features" :key="f.title"
                        :class="['reveal', `reveal-delay-${(i % 3) + 1}`]">
                        <div :class="[
                            'h-full rounded-2xl border bg-gradient-to-br p-7 transition-all duration-200',
                            'hover:shadow-md hover:-translate-y-0.5 cursor-default',
                            f.accent, f.border,
                        ]">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm border border-border-muted text-2xl">
                                {{ f.icon }}
                            </div>
                            <h3 class="text-base font-semibold text-ink font-display">{{ f.title }}</h3>
                            <p class="mt-2.5 text-sm text-ink-muted leading-relaxed">{{ f.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── How It Works ──────────────────────────────────────────────── -->
        <section id="how-it-works" class="py-28 bg-white">
            <div class="mx-auto max-w-5xl px-6">
                <div class="text-center mb-16 reveal">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink font-display tracking-tight">
                        Up and running in minutes
                    </h2>
                    <p class="mt-4 text-ink-muted">Three simple steps to launch your delivery operation.</p>
                </div>

                <div class="relative">
                    <!-- Connector line (desktop) -->
                    <div class="absolute top-10 left-[5%] right-[5%] h-px bg-border-muted hidden md:block" />

                    <div class="grid gap-12 md:grid-cols-3">
                        <div v-for="(s, i) in steps" :key="s.step"
                            :class="['relative text-center reveal', `reveal-delay-${i + 1}`]">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 ring-2 ring-brand-200 text-sm font-bold text-brand-600 mb-5 relative z-10 bg-white font-display">
                                {{ s.step }}
                            </div>
                            <h3 class="text-base font-semibold text-ink font-display mb-2">{{ s.title }}</h3>
                            <p class="text-sm text-ink-muted leading-relaxed">{{ s.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Pricing ───────────────────────────────────────────────────── -->
        <section id="pricing" class="py-28" style="background: #FAFAFA;">
            <div class="mx-auto max-w-6xl px-6">
                <div class="text-center mb-16 reveal">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink font-display tracking-tight">Simple, transparent pricing</h2>
                    <p class="mt-4 text-ink-muted">Start free. Scale as you grow. No hidden fees.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div v-for="(p, i) in plans" :key="p.name"
                        :class="[
                            'relative rounded-2xl p-8 transition-all duration-200 reveal',
                            `reveal-delay-${i + 1}`,
                            p.featured
                                ? 'bg-canvas text-white shadow-xl shadow-canvas/30 scale-105 z-10'
                                : 'bg-white border border-border hover:shadow-md hover:-translate-y-0.5',
                        ]">
                        <div v-if="p.featured"
                            class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full px-3 py-1 text-xs font-bold text-white"
                            style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                            Most Popular
                        </div>

                        <h3 :class="['text-lg font-bold font-display', p.featured ? 'text-white' : 'text-ink']">{{ p.name }}</h3>
                        <p :class="['text-sm mt-1', p.featured ? 'text-white/60' : 'text-ink-muted']">{{ p.desc }}</p>

                        <div class="mt-5 flex items-end gap-1">
                            <span :class="['text-4xl font-bold font-display', p.featured ? 'text-white' : 'text-ink']">{{ p.price }}</span>
                            <span :class="['text-sm mb-1.5', p.featured ? 'text-white/50' : 'text-ink-muted']">{{ p.period }}</span>
                        </div>

                        <ul class="mt-6 space-y-2.5">
                            <li v-for="feat in p.features" :key="feat" class="flex items-center gap-2.5 text-sm">
                                <svg :class="['h-4 w-4 shrink-0', p.featured ? 'text-brand-400' : 'text-brand-500']"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span :class="p.featured ? 'text-white/80' : 'text-ink-secondary'">{{ feat }}</span>
                            </li>
                        </ul>

                        <Link v-if="canRegister" :href="route('register')"
                            :class="[
                                'mt-8 block text-center rounded-xl py-3 text-sm font-semibold transition-all',
                                p.featured
                                    ? 'bg-white text-brand-700 hover:bg-brand-50'
                                    : 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm',
                            ]">
                            Get started
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── CTA ───────────────────────────────────────────────────────── -->
        <section class="py-28 bg-white">
            <div class="mx-auto max-w-3xl px-6 text-center reveal">
                <h2 class="text-3xl sm:text-4xl font-bold text-ink font-display tracking-tight">
                    Ready to modernize your delivery operations?
                </h2>
                <p class="mt-4 text-ink-muted max-w-xl mx-auto leading-relaxed">
                    Join hundreds of businesses using Veltrixo to run smarter, faster delivery operations.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link v-if="canRegister" :href="route('register')"
                        class="rounded-xl px-8 py-3.5 text-base font-semibold text-white transition-all hover:opacity-90 hover:-translate-y-0.5 shadow-lg"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED); box-shadow: 0 8px 24px -4px rgba(99,102,241,0.35);">
                        Start Free Trial — No Card Required
                    </Link>
                </div>
            </div>
        </section>

        <!-- ─── Footer ────────────────────────────────────────────────────── -->
        <footer style="background: #0C0C1D;" class="text-white/50">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg text-white font-bold text-sm"
                            style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                        <span class="text-base font-bold text-white font-display">Veltrixo</span>
                    </div>
                    <p class="text-sm">© {{ year }} Veltrixo. Built for modern delivery businesses.</p>
                    <div class="flex gap-5 text-sm">
                        <Link v-if="canLogin"    :href="route('login')"    class="hover:text-white transition-colors">Sign In</Link>
                        <Link v-if="canRegister" :href="route('register')" class="hover:text-white transition-colors">Register</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
