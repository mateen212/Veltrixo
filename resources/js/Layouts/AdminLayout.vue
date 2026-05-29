<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import {
    ChartBarSquareIcon, TruckIcon, CalendarDaysIcon,
    ShoppingBagIcon, WalletIcon, UserGroupIcon, PresentationChartLineIcon,
    Bars3Icon, XMarkIcon, BellIcon, ArrowRightOnRectangleIcon,
    Cog6ToothIcon, MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()

const page        = usePage()
const sidebarOpen = ref(false)
const user        = computed(() => (page.props.auth as any)?.user)
const initials    = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})

const navGroups = [
    {
        label: 'Overview',
        items: [
            { label: 'Dashboard',  href: 'admin.dashboard',           icon: ChartBarSquareIcon },
            { label: 'Analytics',  href: 'admin.analytics',           icon: PresentationChartLineIcon },
        ],
    },
    {
        label: 'Operations',
        items: [
            { label: 'Deliveries',    href: 'admin.deliveries.index',    icon: TruckIcon },
            { label: 'Subscriptions', href: 'admin.subscriptions.index', icon: CalendarDaysIcon },
            { label: 'Riders',        href: 'admin.riders.index',        icon: UserGroupIcon },
        ],
    },
    {
        label: 'Catalog',
        items: [
            { label: 'Products', href: 'admin.products.index', icon: ShoppingBagIcon },
            { label: 'Wallets',  href: 'admin.wallets.index',  icon: WalletIcon },
        ],
    },
]

function isActive(routeName: string) {
    try { return page.url.startsWith(route(routeName).replace(window.location.origin, '')) } catch { return false }
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-muted">
        <AppToast />

        <!-- Sidebar overlay (mobile) -->
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
            <!-- Brand -->
            <div class="flex h-14 items-center gap-3 px-4 border-b border-canvas-border shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-white font-bold text-sm shrink-0 shadow-md">V</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-white text-sm tracking-wide font-display">Veltrixo</p>
                    <p class="text-[10px] text-white/35 uppercase tracking-[0.12em]">Business Admin</p>
                </div>
                <button class="lg:hidden p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/[0.06] transition-colors"
                    @click="sidebarOpen = false">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto scrollbar-none py-4 px-3 space-y-5">
                <div v-for="group in navGroups" :key="group.label">
                    <p class="px-3 mb-1.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-white/30">
                        {{ group.label }}
                    </p>
                    <nav class="space-y-0.5">
                        <Link v-for="item in group.items" :key="item.label"
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
                </div>
            </div>

            <!-- User footer -->
            <div class="border-t border-canvas-border p-3 shrink-0">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-white/[0.05] transition-colors group cursor-default">
                    <div class="h-8 w-8 rounded-lg bg-brand-600/40 border border-brand-500/30 flex items-center justify-center text-xs font-bold text-brand-300 shrink-0">
                        {{ initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ user?.name }}</p>
                        <p class="text-[10px] text-white/35 truncate">{{ user?.email }}</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="p-1.5 rounded-md text-white/30 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100"
                        title="Sign out">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <!-- Top bar -->
            <header class="flex h-14 items-center justify-between gap-4 border-b border-border bg-white/95 backdrop-blur-sm px-4 lg:px-6 shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors"
                        @click="sidebarOpen = true">
                        <Bars3Icon class="h-5 w-5" />
                    </button>
                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-1.5 text-sm">
                        <span class="hidden sm:inline text-ink-faint font-medium">Admin</span>
                        <span class="hidden sm:inline text-border-strong">/</span>
                        <h1 class="font-semibold text-ink">{{ title }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <slot name="header-actions" />
                    <button class="rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors">
                        <MagnifyingGlassIcon class="h-4.5 w-4.5" />
                    </button>
                    <button class="relative rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors">
                        <BellIcon class="h-4.5 w-4.5" />
                        <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-red-500 ring-2 ring-white" />
                    </button>
                    <div class="h-7 w-7 rounded-lg bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700 ml-1 cursor-pointer hover:bg-brand-200 transition-colors">
                        {{ initials }}
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
