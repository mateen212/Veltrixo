<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import {
    HomeIcon, TruckIcon,
    Bars3Icon, XMarkIcon, ArrowRightOnRectangleIcon, BellIcon,
    SignalIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()

const page        = usePage()
const sidebarOpen = ref(false)
const isOnline    = ref(true)

const user = computed(() => (page.props.auth as any)?.user)
const initials = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})

const navItems = [
    { label: 'Home',          href: 'rider.dashboard',          icon: HomeIcon },
    { label: 'Deliveries',    href: 'rider.deliveries.index',   icon: TruckIcon },
]

function isActive(routeName: string) {
    try { return page.url.startsWith(route(routeName).replace(window.location.origin, '')) } catch { return false }
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-[#F5F6FA]">
        <AppToast />

        <!-- Overlay -->
        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" />
        </Transition>

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex w-[240px] flex-col bg-[#1E1E2D] text-white',
            'transform transition-transform duration-200 ease-in-out lg:static lg:translate-x-0',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <!-- Logo -->
            <div class="flex h-16 items-center gap-3 px-5 border-b border-white/[0.06] shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500 text-white font-bold text-sm shrink-0 shadow">V</div>
                <div class="min-w-0">
                    <p class="font-semibold text-white text-sm tracking-wide">Veltrixo</p>
                    <p class="text-[11px] text-white/40 uppercase tracking-widest">Rider</p>
                </div>
                <button class="ml-auto lg:hidden p-1 rounded-md text-white/40 hover:text-white" @click="sidebarOpen = false">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>

            <!-- Online toggle -->
            <div class="mx-3 mt-4 rounded-xl bg-white/[0.05] border border-white/[0.08] p-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <SignalIcon :class="['h-4 w-4', isOnline ? 'text-emerald-400' : 'text-white/30']" />
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

            <p class="mt-5 mb-1 px-5 text-[10px] font-semibold uppercase tracking-[0.12em] text-white/30">Navigation</p>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-3 space-y-0.5">
                <Link v-for="item in navItems" :key="item.label" :href="route(item.href)"
                    :class="['flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all border',
                        isActive(item.href) ? 'bg-indigo-500/20 text-white border-indigo-500/30' : 'text-white/60 hover:bg-white/[0.06] hover:text-white border-transparent']"
                    @click="sidebarOpen = false">
                    <component :is="item.icon" :class="['h-4 w-4 shrink-0', isActive(item.href) ? 'text-indigo-400' : 'text-white/40']" />
                    {{ item.label }}
                    <span v-if="isActive(item.href)" class="ml-auto h-1.5 w-1.5 rounded-full bg-indigo-400" />
                </Link>
            </nav>

            <!-- Footer -->
            <div class="border-t border-white/[0.06] p-3 shrink-0">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-white/[0.06] transition-colors">
                    <div class="h-8 w-8 rounded-full bg-indigo-500/30 border border-indigo-500/40 flex items-center justify-center text-xs font-bold text-indigo-300 shrink-0">{{ initials }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/40 truncate">Rider</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="p-1.5 rounded-md text-white/30 hover:text-red-400 transition-colors" title="Sign out">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <!-- Top bar -->
            <header class="flex h-14 items-center justify-between gap-4 border-b border-gray-200/60 bg-white/90 backdrop-blur-sm px-4 shrink-0">
                <div class="flex items-center gap-3">
                    <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = true">
                        <Bars3Icon class="h-5 w-5" />
                    </button>
                    <div class="flex items-center gap-1.5 text-sm">
                        <span class="text-gray-400 hidden sm:inline">Rider</span>
                        <span class="text-gray-300 hidden sm:inline">/</span>
                        <h1 class="font-semibold text-gray-900">{{ title }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                        :class="isOnline ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                        <span :class="['h-1.5 w-1.5 rounded-full', isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400']" />
                        {{ isOnline ? 'Online' : 'Offline' }}
                    </div>
                    <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">
                        <BellIcon class="h-5 w-5" />
                    </button>
                    <div class="h-7 w-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 ml-1">{{ initials }}</div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 pb-20 lg:pb-4">
                <slot />
            </main>

            <!-- Bottom nav (mobile only) -->
            <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-100 flex items-stretch">
                <Link v-for="item in navItems" :key="item.label" :href="route(item.href)"
                    :class="['flex flex-1 flex-col items-center justify-center py-2.5 gap-1 text-xs font-medium transition-colors',
                        isActive(item.href) ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-700']">
                    <component :is="item.icon" :class="['h-5 w-5', isActive(item.href) ? 'text-indigo-600' : '']" />
                    {{ item.label }}
                </Link>
            </nav>
        </div>
    </div>
</template>
