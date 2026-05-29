<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import {
    HomeIcon, TruckIcon, CalendarDaysIcon, WalletIcon,
    Bars3Icon, XMarkIcon, BellIcon, ArrowRightOnRectangleIcon,
    ShoppingBagIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()
const page        = usePage()
const sidebarOpen = ref(false)
const user        = computed(() => (page.props.auth as any)?.user)
const initials    = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})

const navItems = [
    { label: 'Overview',      href: 'customer.dashboard',           icon: HomeIcon },
    { label: 'Subscriptions', href: 'customer.subscriptions.index',  icon: CalendarDaysIcon },
    { label: 'Deliveries',    href: 'customer.deliveries.index',    icon: TruckIcon },
    { label: 'Wallet',        href: 'customer.wallet',              icon: WalletIcon },
]

function isActive(routeName: string) {
    try { return page.url.startsWith(route(routeName).replace(window.location.origin, '')) } catch { return false }
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-muted">
        <AppToast />

        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150" leave-to-class="opacity-0">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-canvas/60 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false" />
        </Transition>

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex w-[var(--sidebar-width)] flex-col',
            'bg-canvas text-white border-r border-canvas-border',
            'transform transition-transform duration-250 ease-smooth lg:static lg:translate-x-0',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <div class="flex h-14 items-center gap-3 px-4 border-b border-canvas-border shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-white font-bold text-sm shrink-0 shadow-md">V</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-white text-sm font-display">Veltrixo</p>
                    <p class="text-[10px] text-white/35 uppercase tracking-[0.12em]">My Account</p>
                </div>
                <button class="lg:hidden p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/[0.06] transition-colors"
                    @click="sidebarOpen = false">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto scrollbar-none py-4 px-3 space-y-0.5">
                <Link v-for="item in navItems" :key="item.label"
                    :href="route(item.href)"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150',
                        isActive(item.href)
                            ? 'bg-white/[0.1] text-white'
                            : 'text-white/55 hover:bg-white/[0.06] hover:text-white/90',
                    ]"
                    @click="sidebarOpen = false">
                    <component :is="item.icon"
                        :class="['h-4 w-4 shrink-0 transition-colors', isActive(item.href) ? 'text-brand-400' : 'text-white/35']" />
                    {{ item.label }}
                    <span v-if="isActive(item.href)"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400 shadow-[0_0_0_3px_rgba(129,140,248,0.25)]" />
                </Link>
            </nav>

            <div class="border-t border-canvas-border p-3 shrink-0">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-white/[0.05] transition-colors group cursor-default">
                    <div class="h-8 w-8 rounded-lg bg-brand-600/40 border border-brand-500/30 flex items-center justify-center text-xs font-bold text-brand-300 shrink-0">
                        {{ initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ user?.name }}</p>
                        <p class="text-[10px] text-white/35 truncate">Customer</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="p-1.5 rounded-md text-white/30 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <header class="flex h-14 items-center justify-between gap-4 border-b border-border bg-white/95 backdrop-blur-sm px-4 lg:px-6 shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors"
                        @click="sidebarOpen = true">
                        <Bars3Icon class="h-5 w-5" />
                    </button>
                    <div class="flex items-center gap-1.5 text-sm">
                        <span class="hidden sm:inline text-ink-faint font-medium">Portal</span>
                        <span class="hidden sm:inline text-border-strong">/</span>
                        <h1 class="font-semibold text-ink">{{ title }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button class="relative rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors">
                        <BellIcon class="h-4.5 w-4.5" />
                    </button>
                    <div class="h-7 w-7 rounded-lg bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700 ml-1">
                        {{ initials }}
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4 lg:p-6"><slot /></main>
        </div>
    </div>
</template>
