<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import {
    HomeIcon, TruckIcon,
    Bars3Icon, XMarkIcon, ArrowRightOnRectangleIcon, SignalIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()
const page        = usePage()
const sidebarOpen = ref(false)
const isOnline    = ref(true)
const user        = computed(() => (page.props.auth as any)?.user)
const initials    = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})

const navItems = [
    { label: 'Home',       href: 'rider.dashboard',        icon: HomeIcon },
    { label: 'Deliveries', href: 'rider.deliveries.index',  icon: TruckIcon },
]

function isActive(routeName: string) {
    try { return page.url.startsWith(route(routeName).replace(window.location.origin, '')) } catch { return false }
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-muted">
        <AppToast />

        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
            leave-to-class="opacity-0">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-canvas/60 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false" />
        </Transition>

        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex w-[var(--sidebar-width)] flex-col',
            'bg-canvas text-white border-r border-canvas-border',
            'transform transition-transform duration-250 ease-smooth lg:static lg:translate-x-0',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <div class="flex h-14 items-center gap-3 px-4 border-b border-canvas-border shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-white font-bold text-sm shrink-0">V</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-white text-sm font-display">Veltrixo</p>
                    <p class="text-[10px] text-emerald-400 uppercase tracking-[0.12em]">Rider App</p>
                </div>
                <button class="lg:hidden p-1.5 rounded-lg text-white/40 hover:text-white transition-colors" @click="sidebarOpen = false">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>

            <!-- Online toggle -->
            <div class="mx-3 mt-4 rounded-xl bg-white/[0.05] border border-white/[0.08] p-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span :class="['status-dot-' + (isOnline ? 'online' : 'offline')]" />
                        <span class="text-sm font-medium text-white/80">Status</span>
                    </div>
                    <button @click="isOnline = !isOnline"
                        :class="['relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none',
                            isOnline ? 'bg-emerald-500' : 'bg-white/20']">
                        <span :class="['h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform',
                            isOnline ? 'translate-x-4' : 'translate-x-0.5']" />
                    </button>
                </div>
                <p class="mt-1.5 text-[11px]" :class="isOnline ? 'text-emerald-400' : 'text-white/30'">
                    {{ isOnline ? 'Online — accepting deliveries' : 'You are offline' }}
                </p>
            </div>

            <nav class="flex-1 overflow-y-auto scrollbar-none py-4 px-3 space-y-0.5">
                <Link v-for="item in navItems" :key="item.label"
                    :href="route(item.href)"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150',
                        isActive(item.href) ? 'bg-white/[0.1] text-white' : 'text-white/55 hover:bg-white/[0.06] hover:text-white/90',
                    ]"
                    @click="sidebarOpen = false">
                    <component :is="item.icon" :class="['h-4 w-4 shrink-0', isActive(item.href) ? 'text-brand-400' : 'text-white/35']" />
                    {{ item.label }}
                    <span v-if="isActive(item.href)" class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400" />
                </Link>
            </nav>

            <div class="border-t border-canvas-border p-3 shrink-0">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-white/[0.05] group cursor-default transition-colors">
                    <div class="h-8 w-8 rounded-lg bg-emerald-600/30 border border-emerald-500/30 flex items-center justify-center text-xs font-bold text-emerald-300 shrink-0">{{ initials }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ user?.name }}</p>
                        <p class="text-[10px] text-white/35 truncate">Rider</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="p-1.5 rounded-md text-white/30 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <header class="flex h-14 items-center justify-between gap-4 border-b border-border bg-white/95 backdrop-blur-sm px-4 shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors" @click="sidebarOpen = true">
                        <Bars3Icon class="h-5 w-5" />
                    </button>
                    <h1 class="font-semibold text-ink text-sm">{{ title }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span :class="['status-dot-' + (isOnline ? 'online' : 'offline')]" />
                    <span class="text-xs font-medium" :class="isOnline ? 'text-emerald-600' : 'text-ink-faint'">
                        {{ isOnline ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4"><slot /></main>
        </div>
    </div>
</template>
