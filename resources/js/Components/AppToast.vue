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
            setTimeout(() => { toasts.value = toasts.value.filter(t => t.id !== id) }, 4000)
        }
    })
}, { deep: true })

function dismiss(id: number) { toasts.value = toasts.value.filter(t => t.id !== id) }

const colors: Record<string, string> = {
    success: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
    error:   'bg-red-50 text-red-800 ring-red-200',
    warning: 'bg-amber-50 text-amber-800 ring-amber-200',
    info:    'bg-blue-50 text-blue-800 ring-blue-200',
}
const icons: Record<string, string> = {
    success: '✓', error: '✕', warning: '⚠', info: 'i',
}
</script>
<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
            <Transition
                v-for="t in toasts" :key="t.id"
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 translate-x-4"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0 translate-x-4"
            >
                <div :class="['pointer-events-auto flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg ring-1', colors[t.type]]">
                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-current/10 text-xs font-bold">{{ icons[t.type] }}</span>
                    <p class="flex-1 text-sm font-medium leading-5">{{ t.message }}</p>
                    <button @click="dismiss(t.id)" class="text-current/50 hover:text-current/80 text-lg leading-none">&times;</button>
                </div>
            </Transition>
        </div>
    </Teleport>
</template>
