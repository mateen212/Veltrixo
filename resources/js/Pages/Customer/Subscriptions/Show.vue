<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { Link } from '@inertiajs/vue3'
import { CalendarDaysIcon, MapPinIcon, CubeIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

interface SubItem { id: number; product_name: string; quantity: number }
interface Subscription {
    id: number
    status: string
    start_date: string
    next_delivery_date?: string
    address: string
    items: SubItem[]
    deliveries_count?: number
}

defineProps<{ subscription: Subscription }>()

const statusVariant: Record<string, 'success' | 'warning' | 'danger' | 'neutral'> = {
    active:    'success',
    paused:    'warning',
    cancelled: 'danger',
    expired:   'neutral',
}
</script>

<template>
    <Head :title="`Subscription #${subscription.id}`" />
    <CustomerLayout :title="`Subscription #${subscription.id}`">
        <!-- Back -->
        <div class="mb-4">
            <Link :href="route('customer.subscriptions.index')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to subscriptions
            </Link>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Main -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Overview -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50">
                                <CalendarDaysIcon class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Subscription #{{ subscription.id }}</p>
                                <p class="text-xs text-gray-400">Started {{ subscription.start_date }}</p>
                            </div>
                        </div>
                        <AppBadge :variant="statusVariant[subscription.status] ?? 'neutral'" dot>
                            {{ subscription.status }}
                        </AppBadge>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Next Delivery</p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ subscription.next_delivery_date ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Deliveries</p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ subscription.deliveries_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <MapPinIcon class="h-4 w-4 text-gray-400" />
                        <h3 class="text-sm font-semibold text-gray-800">Delivery Address</h3>
                    </div>
                    <p class="text-sm text-gray-600">{{ subscription.address }}</p>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                        <CubeIcon class="h-4 w-4 text-gray-400" />
                        <h3 class="text-sm font-semibold text-gray-800">Items</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in subscription.items" :key="item.id"
                            class="flex items-center justify-between px-5 py-3.5">
                            <p class="text-sm text-gray-800">{{ item.product_name }}</p>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">×{{ item.quantity }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar actions -->
            <div class="space-y-3">
                <Link :href="route('customer.deliveries.index')"
                    class="flex items-center justify-between bg-white rounded-xl ring-1 ring-gray-100 shadow-sm px-4 py-3.5 hover:bg-gray-50 transition-colors">
                    <span class="text-sm font-medium text-gray-700">View Deliveries</span>
                    <ArrowRightIcon class="h-4 w-4 text-gray-400" />
                </Link>
            </div>
        </div>
    </CustomerLayout>
</template>
