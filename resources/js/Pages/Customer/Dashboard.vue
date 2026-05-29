<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import KpiCard from '@/Components/KpiCard.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { TruckIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

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
            <KpiCard title="Active Plans"      :value="activeSubscriptions" icon="calendar" color="blue" />
            <KpiCard title="Pending Deliveries" :value="pendingDeliveries"   icon="truck"    color="yellow" />
            <KpiCard title="Wallet Balance"    :value="walletBalance"       icon="wallet"   color="green" />
        </div>

        <!-- Recent deliveries -->
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Recent Deliveries</h2>
                <Link :href="route('customer.deliveries.index')"
                    class="flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                    View all <ArrowRightIcon class="h-3 w-3" />
                </Link>
            </div>

            <div v-if="recentDeliveries.length" class="divide-y divide-border-muted">
                <div v-for="d in recentDeliveries" :key="d.id"
                    class="flex items-center justify-between px-6 py-3.5 hover:bg-surface-subtle transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-surface-subtle flex items-center justify-center shrink-0">
                            <TruckIcon class="h-4 w-4 text-ink-muted" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-ink">Delivery #{{ d.id }}</p>
                            <p class="text-xs text-ink-faint">{{ d.delivery_date }} · {{ d.items_count }} item{{ d.items_count !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <AppBadge :variant="statusMap[d.status] ?? 'neutral'" dot size="xs">
                        {{ d.status.replace('_', ' ') }}
                    </AppBadge>
                </div>
            </div>
            <div v-else class="py-14 px-6">
                <EmptyState icon="truck" title="No deliveries yet" description="Your deliveries will appear here once you have an active subscription." />
            </div>
        </div>
    </CustomerLayout>
</template>
