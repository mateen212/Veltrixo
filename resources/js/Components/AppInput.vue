<script setup lang="ts">
defineProps<{
    modelValue?: string | number
    label?: string
    error?: string
    hint?: string
    type?: string
    placeholder?: string
    disabled?: boolean
    required?: boolean
}>()
defineEmits<{ 'update:modelValue': [v: string] }>()
</script>
<template>
    <div class="flex flex-col gap-1">
        <label v-if="label" class="text-sm font-medium text-gray-700">
            {{ label }}<span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        <input
            :type="type ?? 'text'"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            :class="[
                'w-full rounded-lg border px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder-gray-400',
                'focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition',
                error ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white',
                disabled ? 'opacity-60 cursor-not-allowed bg-gray-50' : '',
            ]"
        />
        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="text-xs text-gray-400">{{ hint }}</p>
    </div>
</template>
