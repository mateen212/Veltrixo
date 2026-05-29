<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { computed, watch, ref } from 'vue'

interface Flash { success?: string; error?: string; info?: string; warning?: string }

const page   = usePage()
const toasts = ref<Array<{ id: number; type: string; message: string }>>([])
let   next   = 0

const flash = computed(() => (page.props.flash ?? {}) as Flash)

watch(flash, (f) => {
    const types = ['success', 'error', 'info', 'warning'] as const
    types.forEach(type => {
        if (f[type]) {
            const id = ++next
            toasts.value.push({ id, type, message: f[type]! })
            setTimeout(() => { toasts.value = toasts.value.filter(t => t.id !== id) }, 4500)
        }
    })
}, { deep: true })

function dismiss(id: number) { toasts.value = toasts.value.filter(t => t.id !== id) }

const config: Record<string, { ring: string; icon: string; iconBg: string; bar: string }> = {
    success: { ring: 'ring-emerald-200 bg-white text-emerald-900', icon: '✓', iconBg: 'bg-emerald-100 text-emerald-600', bar: 'bg-emerald-500' },
    error:   { ring: 'ring-red-200 bg-white text-red-900',         icon: '✕', iconBg: 'bg-red-100 text-red-600',         bar: 'bg-red-500' },
    warning: { ring: 'ring-amber-200 bg-white text-amber-900',     icon: '!', iconBg: 'bg-amber-100 text-amber-600',     bar: 'bg-amber-500' },
    info:    { ring: 'ring-blue-200 bg-white text-blue-900',       icon: 'i', iconBg: 'bg-blue-100 text-blue-600',       bar: 'bg-blue-500' },
}
</script>
<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none">
            <TransitionGroup
                enter-active-class="transition ease-out duration-250"
                enter-from-class="opacity-0 translate-x-6 scale-95"
                enter-to-class="opacity-100 translate-x-0 scale-100"
                leave-active-class="transition ease-in duration-150 absolute w-full"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 translate-x-6"
            >
                <div v-for="t in toasts" :key="t.id"
                    :class="['pointer-events-auto rounded-xl shadow-lg ring-1 overflow-hidden', config[t.type]?.ring ?? 'ring-gray-200 bg-white']"
                >
                    <div class="flex items-start gap-3 px-4 py-3.5">
                        <span :class="['mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-xs font-bold', config[t.type]?.iconBg]">
                            {{ config[t.type]?.icon }}
                        </span>
                        <p class="flex-1 text-sm font-medium leading-5">{{ t.message }}</p>
                        <button @click="dismiss(t.id)"
                            class="ml-1 text-current/40 hover:text-current/70 transition-colors text-lg leading-none shrink-0">
                            ×
                        </button>
                    </div>
                    <div :class="['h-0.5', config[t.type]?.bar ?? 'bg-gray-200']" />
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
