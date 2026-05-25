<script setup lang="ts">
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import {
    BuildingOfficeIcon,
    CheckCircleIcon,
    ClockIcon,
    CurrencyDollarIcon,
    TruckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{
    stats: {
        total_tenants: number
        active_tenants: number
        trial_tenants: number
        monthly_revenue: string
        deliveries_today: number
    }
    recentTenants: Array<{
        id: number
        name: string
        slug: string
        status: string
        owner_name: string
        owner_email: string
        trial_ends: string
        created_at: string
    }>
}>()

const statCards = [
    { label: 'Total Tenants',     value: () => props.stats.total_tenants,    icon: BuildingOfficeIcon, color: 'indigo' },
    { label: 'Active Tenants',    value: () => props.stats.active_tenants,   icon: CheckCircleIcon,    color: 'green' },
    { label: 'In Trial',          value: () => props.stats.trial_tenants,    icon: ClockIcon,          color: 'amber' },
    { label: 'Monthly Revenue',   value: () => props.stats.monthly_revenue,  icon: CurrencyDollarIcon, color: 'blue' },
    { label: 'Deliveries Today',  value: () => props.stats.deliveries_today, icon: TruckIcon,          color: 'purple' },
]

const statusColors: Record<string, string> = {
    active:    'bg-green-100 text-green-700',
    suspended: 'bg-red-100 text-red-700',
    trial:     'bg-amber-100 text-amber-700',
    inactive:  'bg-gray-100 text-gray-600',
}

const iconColors: Record<string, string> = {
    indigo: 'bg-indigo-100 text-indigo-600',
    green:  'bg-green-100 text-green-600',
    amber:  'bg-amber-100 text-amber-600',
    blue:   'bg-blue-100 text-blue-600',
    purple: 'bg-purple-100 text-purple-600',
}
</script>

<template>
    <SuperAdminLayout title="Platform Dashboard">
        <!-- KPI cards -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5 mb-8">
            <div v-for="card in statCards" :key="card.label"
                class="rounded-xl bg-white shadow-sm border border-gray-100 p-5 flex items-start gap-4">
                <div :class="['flex h-10 w-10 items-center justify-center rounded-lg shrink-0', iconColors[card.color]]">
                    <component :is="card.icon" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ card.value() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ card.label }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Tenants table -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Recent Tenants</h2>
                <Link :href="route('super-admin.tenants.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    View all →
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Business</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trial Ends</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="tenant in recentTenants" :key="tenant.id"
                            class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ tenant.name }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                <div>{{ tenant.owner_name }}</div>
                                <div class="text-xs text-gray-400">{{ tenant.owner_email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusColors[tenant.status] ?? 'bg-gray-100 text-gray-600']">
                                    {{ tenant.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ tenant.trial_ends }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ tenant.created_at }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('super-admin.tenants.show', tenant.id)"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!recentTenants.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No tenants yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </SuperAdminLayout>
</template>
