<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppInput from '@/Components/AppInput.vue'
import { Link } from '@inertiajs/vue3'

const form = useForm({
    name:        '',
    description: '',
    price:       '',
    category:    '',
    unit:        'unit',
    is_active:   true,
})

function submit() {
    form.post(route('admin.products.store'))
}
</script>

<template>
    <Head title="New Product" />
    <AdminLayout title="New Product">
        <div class="mb-4">
            <Link :href="route('admin.products.index')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to products
            </Link>
        </div>

        <div class="max-w-xl">
            <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Product details</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <AppInput v-model="form.name" label="Name" :error="form.errors.name" required />
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition" />
                        <p v-if="form.errors.description" class="text-xs text-red-600">{{ form.errors.description }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <AppInput v-model="form.price" label="Price" type="number" :error="form.errors.price" required />
                        <AppInput v-model="form.unit" label="Unit" :error="form.errors.unit" hint="e.g. kg, box, unit" />
                    </div>
                    <AppInput v-model="form.category" label="Category" :error="form.errors.category" />
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm text-gray-700">Active (visible to customers)</span>
                    </label>
                    <div class="flex gap-2 pt-2">
                        <AppButton type="submit" :loading="form.processing">Create Product</AppButton>
                        <AppButton variant="outline" :href="route('admin.products.index')">Cancel</AppButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
