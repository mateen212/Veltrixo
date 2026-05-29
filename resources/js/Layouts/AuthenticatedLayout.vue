<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppToast from '@/Components/AppToast.vue'
import { Bars3Icon, XMarkIcon, ArrowRightOnRectangleIcon, BellIcon } from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()
const page        = usePage()
const sidebarOpen = ref(false)
const user        = computed(() => (page.props.auth as any)?.user)
const initials    = computed(() => {
    const name: string = user.value?.name ?? ''
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-muted">
        <AppToast />
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <header class="flex h-14 items-center justify-between gap-4 border-b border-border bg-white/95 backdrop-blur-sm px-4 lg:px-6 shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <Link href="/" class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg text-white font-bold text-xs"
                            style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                        <span class="font-bold text-ink font-display text-sm">Veltrixo</span>
                    </Link>
                    <span class="text-border-strong">/</span>
                    <h1 class="font-semibold text-ink text-sm">{{ title }}</h1>
                </div>
                <div class="flex items-center gap-1">
                    <button class="rounded-lg p-2 text-ink-muted hover:bg-surface-subtle transition-colors">
                        <BellIcon class="h-4.5 w-4.5" />
                    </button>
                    <div class="h-7 w-7 rounded-lg bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700 ml-1">{{ initials }}</div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="rounded-lg p-2 text-ink-muted hover:text-red-500 transition-colors ml-1">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                    </Link>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4 lg:p-6"><slot /></main>
        </div>
    </div>
</template>
