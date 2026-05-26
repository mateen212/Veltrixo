<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import {
    HomeIcon, BuildingOfficeIcon, ChartBarSquareIcon,
    Bars3Icon, XMarkIcon, ArrowRightOnRectangleIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()

const page        = usePage()
const sidebarOpen = ref(false)

const user = computed(() => (page.props.auth as any)?.user)
const initials = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})

const navItems = [
    { label: 'Dashboard',     href: 'super-admin.dashboard',            icon: HomeIcon },
    { label: 'Tenants',       href: 'super-admin.tenants.index',        icon: BuildingOfficeIcon },
    { label: 'Plans',         href: 'super-admin.plans.index',          icon: ChartBarSquareIcon },
    { label: 'Verifications', href: 'super-admin.verifications.index',  icon: ShieldCheckIcon,
      badge: computed(() => (page.props as any).pendingVerificationsCount ?? null) },
]

function isActive(routeName: string) {
    try { return page.url.startsWith(route(routeName).replace(window.location.origin, '')) } catch { return false }
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-[#F5F6FA]">
        <AppToast />

        <!-- Mobile overlay -->
        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150" leave-to-class="opacity-0">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false" />
        </Transition>

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex w-[240px] flex-col bg-[#12122A] text-white',
            'transform transition-transform duration-200 ease-in-out lg:static lg:translate-x-0',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <div class="flex h-16 items-center gap-3 px-5 border-b border-white/[0.06] shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500 text-white font-bold text-sm shrink-0 shadow">V</div>
                <div class="min-w-0">
                    <p class="font-semibold text-white text-sm tracking-wide">Veltrixo</p>
                    <p class="text-[11px] text-violet-400 uppercase tracking-widest">Super Admin</p>
                </div>
                <button class="ml-auto lg:hidden p-1 rounded-md text-white/40 hover:text-white" @click="sidebarOpen = false">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>

            <p class="mt-5 mb-1 px-5 text-[10px] font-semibold uppercase tracking-[0.12em] text-white/30">Platform</p>

            <nav class="flex-1 overflow-y-auto px-3 space-y-0.5">
                <Link v-for="item in navItems" :key="item.label" :href="route(item.href)"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all border',
                        isActive(item.href)
                            ? 'bg-violet-500/20 text-white border-violet-500/30'
                            : 'text-white/60 hover:bg-white/[0.06] hover:text-white border-transparent',
                    ]"
                    @click="sidebarOpen = false">
                    <component :is="item.icon" :class="['h-4 w-4 shrink-0', isActive(item.href) ? 'text-violet-400' : 'text-white/40']" />
                    {{ item.label }}
                    <span
                        v-if="item.badge?.value"
                        class="ml-auto text-[10px] font-bold bg-amber-500 text-white rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                    >{{ item.badge.value }}</span>
                    <span v-else-if="isActive(item.href)" class="ml-auto h-1.5 w-1.5 rounded-full bg-violet-400" />
                </Link>
            </nav>

            <div class="border-t border-white/[0.06] p-3 shrink-0">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-white/[0.06] transition-colors">
                    <div class="h-8 w-8 rounded-full bg-violet-500/30 border border-violet-500/40 flex items-center justify-center text-xs font-bold text-violet-300 shrink-0">
                        {{ initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/40 truncate">{{ user?.email }}</p>
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
            <header class="flex h-14 items-center justify-between gap-4 border-b border-gray-200/60 bg-white/90 backdrop-blur-sm px-4 lg:px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition-colors" @click="sidebarOpen = true">
                        <Bars3Icon class="h-5 w-5" />
                    </button>
                    <div class="flex items-center gap-1.5 text-sm">
                        <span class="text-gray-400 hidden sm:inline">Super Admin</span>
                        <span class="text-gray-300 hidden sm:inline">/</span>
                        <h1 class="font-semibold text-gray-900">{{ title }}</h1>
                    </div>
                </div>
                <slot name="header-actions" />
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
