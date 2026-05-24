<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { TruckIcon, MapPinIcon, CubeIcon, DocumentTextIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'

interface DeliveryItem {
    id: number
    product_name: string
    quantity: number
    unit_price: string
}
interface Delivery {
    id: number
    delivery_date: string
    status: string
    address: string
    items: DeliveryItem[]
    rider_name?: string
    invoice_number?: string
}

defineProps<{ delivery: Delivery }>()

const statusVariant: Record<string, 'success' | 'warning' | 'danger' | 'info' | 'neutral'> = {
    delivered:   'success',
    in_progress: 'info',
    assigned:    'info',
    scheduled:   'neutral',
    pending:     'warning',
    missed:      'danger',
    cancelled:   'danger',
}
</script>

<template>
    <Head :title="`Delivery #${delivery.id}`" />
    <CustomerLayout :title="`Delivery #${delivery.id}`">
        <!-- Back -->
        <div class="mb-4">
            <Link :href="route('customer.deliveries.index')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to deliveries
            </Link>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Main info -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Status card -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50">
                                <TruckIcon class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Delivery #{{ delivery.id }}</p>
                                <p class="text-xs text-gray-400">{{ delivery.delivery_date }}</p>
                            </div>
                        </div>
                        <AppBadge :variant="statusVariant[delivery.status] ?? 'neutral'" dot>
                            {{ delivery.status.replace('_', ' ') }}
                        </AppBadge>
                    </div>
                    <!-- Address -->
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <MapPinIcon class="h-4 w-4 text-gray-400 mt-0.5 shrink-0" />
                        <span>{{ delivery.address }}</span>
                    </div>
                    <div v-if="delivery.rider_name" class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                        <TruckIcon class="h-4 w-4 text-gray-400 shrink-0" />
                        <span>Rider: <span class="font-medium text-gray-700">{{ delivery.rider_name }}</span></span>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                        <CubeIcon class="h-4 w-4 text-gray-400" />
                        <h2 class="text-sm font-semibold text-gray-800">Items</h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in delivery.items" :key="item.id"
                            class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ item.product_name }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ item.quantity }}</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">{{ item.unit_price }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div v-if="delivery.invoice_number" class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <DocumentTextIcon class="h-4 w-4 text-gray-400" />
                        <h3 class="text-sm font-semibold text-gray-800">Invoice</h3>
                    </div>
                    <p class="text-sm text-gray-600">{{ delivery.invoice_number }}</p>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
