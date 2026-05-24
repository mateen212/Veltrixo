<script setup lang="ts">
defineProps<{ title?: string; size?: 'sm' | 'md' | 'lg' | 'xl' }>()
const emit = defineEmits<{ close: [] }>()
</script>
<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="emit('close')" />
            <div :class="[
                    'relative bg-white rounded-2xl shadow-2xl w-full flex flex-col max-h-[90vh]',
                    size === 'sm' ? 'max-w-sm'
                    : size === 'lg' ? 'max-w-2xl'
                    : size === 'xl' ? 'max-w-4xl'
                    : 'max-w-lg',
                ]">
                <div v-if="title" class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
                    <button @click="emit('close')" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-5">
                    <slot />
                </div>
                <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
