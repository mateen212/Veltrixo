<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { TruckIcon, CheckCircleIcon, XCircleIcon, ClockIcon, ChartBarIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    daily: {
        total: number
        delivered: number
        missed: number
        pending: number
        revenue: string
        active_subscriptions: number
    }
    weekly: Array<{
        date: string
        total: number
        delivered: number
        missed: number
        revenue: string
    }>
    riderPerformance: Array<{
        rider_id: number
        rider_name: string
        total: number
        delivered: number
        missed: number
        completion_rate: number
    }>
}>()

const deliveryRate = () => {
    const total = props.daily.total
    return total > 0 ? Math.round((props.daily.delivered / total) * 100) : 0
}
</script>

<template>
    <AdminLayout title="Analytics">
        <!-- Today's KPIs -->
        <div class="mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Today's Summary</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6">
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <TruckIcon class="h-4 w-4 text-indigo-500" />
                        <span class="text-xs text-gray-500">Total</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ daily.total }}</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <CheckCircleIcon class="h-4 w-4 text-green-500" />
                        <span class="text-xs text-gray-500">Delivered</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ daily.delivered }}</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <XCircleIcon class="h-4 w-4 text-red-500" />
                        <span class="text-xs text-gray-500">Missed</span>
                    </div>
                    <p class="text-2xl font-bold text-red-600">{{ daily.missed }}</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <ClockIcon class="h-4 w-4 text-amber-500" />
                        <span class="text-xs text-gray-500">Pending</span>
                    </div>
                    <p class="text-2xl font-bold text-amber-600">{{ daily.pending }}</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <ChartBarIcon class="h-4 w-4 text-blue-500" />
                        <span class="text-xs text-gray-500">Revenue</span>
                    </div>
                    <p class="text-lg font-bold text-blue-700">{{ daily.revenue }}</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-gray-500">Completion</span>
                    </div>
                    <p class="text-2xl font-bold text-indigo-600">{{ deliveryRate() }}%</p>
                </div>
            </div>
        </div>

        <!-- Weekly breakdown -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Last 7 Days</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Delivered</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Missed</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="row in weekly" :key="row.date" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ row.date }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ row.total }}</td>
                            <td class="px-6 py-3 text-green-600 font-medium">{{ row.delivered }}</td>
                            <td class="px-6 py-3 text-red-600">{{ row.missed }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ row.revenue }}</td>
                        </tr>
                        <tr v-if="!weekly.length">
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">No data available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rider performance -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Rider Performance (Last 7 Days)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rider</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Delivered</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Missed</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="rider in riderPerformance" :key="rider.rider_id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ rider.rider_name }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ rider.total }}</td>
                            <td class="px-6 py-3 text-green-600 font-medium">{{ rider.delivered }}</td>
                            <td class="px-6 py-3 text-red-600">{{ rider.missed }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100 max-w-[80px]">
                                        <div class="h-1.5 rounded-full bg-indigo-500 transition-all"
                                            :style="{ width: rider.completion_rate + '%' }" />
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">{{ rider.completion_rate }}%</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!riderPerformance.length">
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">No rider data available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
