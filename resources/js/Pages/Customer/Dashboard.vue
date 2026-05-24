<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import {
    CalendarDaysIcon, TruckIcon, WalletIcon, ArrowRightIcon,
} from '@heroicons/vue/24/outline'

defineProps<{
    activeSubscriptions: number
    pendingDeliveries:   number
    walletBalance:       number | string
    recentDeliveries:    Array<{ id: number; delivery_date: string; status: string; items_count: number }>
}>()

const statusMap: Record<string, any> = {
    delivered: 'success', scheduled: 'neutral', pending: 'warning',
    missed: 'danger', in_progress: 'info', assigned: 'info',
}
</script>
<template>
    <Head title="My Dashboard" />
    <CustomerLayout title="Overview">
        <!-- KPI cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
            <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 shrink-0">
                    <CalendarDaysIcon class="h-5 w-5 text-indigo-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Plans</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ activeSubscriptions }}</p>
                </div>
            </div>
            <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 shrink-0">
                    <TruckIcon class="h-5 w-5 text-amber-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ pendingDeliveries }}</p>
                </div>
            </div>
            <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 shrink-0">
                    <WalletIcon class="h-5 w-5 text-emerald-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet Balance</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ walletBalance }}</p>
                </div>
            </div>
        </div>

        <!-- Recent deliveries -->
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Recent Deliveries</h2>
                <a :href="route('customer.deliveries.index')" class="flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                    View all <ArrowRightIcon class="h-3 w-3" />
                </a>
            </div>
            <div v-if="recentDeliveries.length" class="divide-y divide-gray-50">
                <div v-for="d in recentDeliveries" :key="d.id"
                    class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <TruckIcon class="h-4 w-4 text-gray-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Delivery #{{ d.id }}</p>
                            <p class="text-xs text-gray-400">{{ d.delivery_date }} · {{ d.items_count }} item{{ d.items_count !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <AppBadge :variant="statusMap[d.status] ?? 'neutral'" dot>{{ d.status.replace('_', ' ') }}</AppBadge>
                </div>
            </div>
            <div v-else class="py-12 text-center">
                <TruckIcon class="h-8 w-8 text-gray-200 mx-auto mb-2" />
                <p class="text-sm text-gray-400">No deliveries yet</p>
            </div>
        </div>
    </CustomerLayout>
</template>
