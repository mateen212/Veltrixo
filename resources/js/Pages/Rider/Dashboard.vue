<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import RiderLayout from '@/Layouts/RiderLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { TruckIcon, CheckCircleIcon, ClockIcon, MapPinIcon } from '@heroicons/vue/24/outline'

defineProps<{
    todayDeliveries: number
    completedToday:  number
    pendingToday:    number
    recentDeliveries: Array<{ id: number; address: string; status: string; delivery_date: string }>
}>()

const statusMap: Record<string, any> = {
    delivered: 'success', in_progress: 'info', assigned: 'info',
    scheduled: 'neutral', pending: 'warning', missed: 'danger',
}

const statusLabel: Record<string, string> = {
    delivered: 'Delivered', in_progress: 'In Progress', assigned: 'Assigned',
    scheduled: 'Scheduled', pending: 'Pending', missed: 'Missed',
}
</script>
<template>
    <Head title="Rider Dashboard" />
    <RiderLayout title="Today">
        <!-- KPI cards -->
        <div class="grid grid-cols-3 gap-3 mb-5">
            <div class="rounded-xl bg-indigo-600 text-white p-4 text-center shadow-md shadow-indigo-200">
                <TruckIcon class="h-5 w-5 mx-auto mb-1 opacity-80" />
                <p class="text-xl font-bold">{{ todayDeliveries }}</p>
                <p class="text-xs opacity-70 mt-0.5">Today</p>
            </div>
            <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow-sm p-4 text-center">
                <CheckCircleIcon class="h-5 w-5 mx-auto mb-1 text-emerald-500" />
                <p class="text-xl font-bold text-gray-900">{{ completedToday }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Done</p>
            </div>
            <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow-sm p-4 text-center">
                <ClockIcon class="h-5 w-5 mx-auto mb-1 text-amber-500" />
                <p class="text-xl font-bold text-gray-900">{{ pendingToday }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Pending</p>
            </div>
        </div>

        <!-- Delivery list -->
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3.5 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Today's Deliveries</h2>
            </div>
            <div v-if="recentDeliveries.length" class="divide-y divide-gray-50">
                <div v-for="d in recentDeliveries" :key="d.id"
                    class="flex items-start gap-3 px-4 py-4 hover:bg-gray-50/60 transition-colors active:bg-gray-100">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 shrink-0">
                        <MapPinIcon class="h-4 w-4 text-indigo-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ d.address }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ d.delivery_date }}</p>
                    </div>
                    <AppBadge :variant="statusMap[d.status] ?? 'neutral'" dot>
                        {{ statusLabel[d.status] ?? d.status }}
                    </AppBadge>
                </div>
            </div>
            <div v-else class="py-12 text-center">
                <CheckCircleIcon class="h-8 w-8 text-gray-200 mx-auto mb-2" />
                <p class="text-sm text-gray-400">No deliveries scheduled today</p>
            </div>
        </div>
    </RiderLayout>
</template>
