<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { Link } from '@inertiajs/vue3'
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

interface Product {
    id: number; name: string; description: string; price: string
    category: string; unit: string; is_active: boolean
}

const props = defineProps<{ product: Product }>()

function destroy() {
    if (confirm('Delete this product?')) {
        router.delete(route('admin.products.destroy', props.product.id))
    }
}
</script>

<template>
    <Head :title="product.name" />
    <AdminLayout :title="product.name">
        <div class="mb-4">
            <Link :href="route('admin.products.index')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to products
            </Link>
        </div>

        <div class="max-w-xl">
            <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ product.name }}</h2>
                        <p v-if="product.category" class="text-xs text-gray-400 mt-0.5">{{ product.category }}</p>
                    </div>
                    <AppBadge :variant="product.is_active ? 'success' : 'neutral'" dot>
                        {{ product.is_active ? 'Active' : 'Inactive' }}
                    </AppBadge>
                </div>

                <dl class="divide-y divide-gray-50 text-sm">
                    <div class="flex justify-between py-3">
                        <dt class="text-gray-500">Price</dt>
                        <dd class="font-semibold text-gray-800">{{ product.price }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-gray-500">Unit</dt>
                        <dd class="text-gray-700">{{ product.unit }}</dd>
                    </div>
                    <div v-if="product.description" class="py-3">
                        <dt class="text-gray-500 mb-1">Description</dt>
                        <dd class="text-gray-700">{{ product.description }}</dd>
                    </div>
                </dl>

                <div class="flex gap-2 mt-5 pt-4 border-t border-gray-50">
                    <AppButton size="sm" :href="route('admin.products.edit', product.id)">
                        <PencilSquareIcon class="h-4 w-4" />Edit
                    </AppButton>
                    <AppButton size="sm" variant="danger" @click="destroy">
                        <TrashIcon class="h-4 w-4" />Delete
                    </AppButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
