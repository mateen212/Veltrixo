<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import KpiCard from '@/Components/KpiCard.vue'
import StatCard from '@/Components/StatCard.vue'
import AnimatedCounter from '@/Components/AnimatedCounter.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { TruckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    metrics: Record<string, any>
    from: string
    to: string
}>()

const from = ref(props.from)
const to   = ref(props.to)

function formatCurrency(v: number) {
    return 'Rs ' + new Intl.NumberFormat('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(v)
}

function reload() {
    router.get(route('admin.dashboard'), { from: from.value, to: to.value }, { preserveState: true })
}

const statusMap: Record<string, any> = {
    delivered: 'success', scheduled: 'neutral', pending: 'warning',
    missed: 'danger', in_progress: 'info', assigned: 'info',
}

const riderPerf = () => props.metrics.rider_performance ?? []
</script>

<template>
    <Head title="Dashboard — Admin" />
    <AdminLayout title="Dashboard">
        <!-- Date filter -->
        <div class="flex flex-wrap items-center gap-3 mb-8">
            <div class="flex items-center gap-2">
                <input type="date" v-model="from"
                    class="field-input text-sm py-2 px-3 h-9 min-w-0 w-auto" />
                <span class="text-ink-faint text-sm">to</span>
                <input type="date" v-model="to"
                    class="field-input text-sm py-2 px-3 h-9 min-w-0 w-auto" />
            </div>
            <button @click="reload" class="btn btn-primary btn-sm">Apply</button>
        </div>

        <!-- KPI cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <KpiCard title="Revenue"
                :value="formatCurrency(metrics.revenue?.total ?? 0)"
                icon="currency" color="green" />
            <KpiCard title="Active Subscriptions"
                :value="metrics.active_subscriptions?.active ?? 0"
                icon="calendar" color="blue" />
            <KpiCard title="Delivery Success"
                :value="`${metrics.delivery_success_rate?.rate ?? 0}%`"
                icon="truck" color="indigo" />
            <KpiCard title="Pending Today"
                :value="metrics.pending_deliveries ?? 0"
                icon="clock" color="yellow" />
        </div>

        <!-- Secondary stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
            <StatCard title="Total Deliveries"
                :value="metrics.delivery_success_rate?.total ?? 0"
                :sub="`${metrics.delivery_success_rate?.delivered ?? 0} delivered · ${metrics.delivery_success_rate?.missed ?? 0} missed`"
                :progress="metrics.delivery_success_rate?.rate ?? 0" />
            <StatCard title="New Customers"
                :value="metrics.new_customers ?? 0"
                sub="This period" />
            <StatCard title="Wallet Balance Total"
                :value="formatCurrency(metrics.wallet_balance_total ?? 0)"
                sub="Across all wallets" />
        </div>

        <!-- Rider performance -->
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Rider Performance</h2>
            </div>
            <div v-if="riderPerf().length" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border-muted">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">Rider</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">Delivered</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">Missed</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-muted">
                        <tr v-for="r in riderPerf()" :key="r.id"
                            class="hover:bg-surface-subtle transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-7 w-7 rounded-lg bg-brand-50 flex items-center justify-center text-xs font-semibold text-brand-700">
                                        {{ r.name?.slice(0, 1) }}
                                    </div>
                                    <span class="font-medium text-ink">{{ r.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-ink-secondary">{{ r.total }}</td>
                            <td class="px-6 py-3.5">
                                <span class="text-emerald-600 font-semibold">{{ r.delivered }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="text-red-500 font-semibold">{{ r.missed }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-ink-secondary">
                                {{ Number(r.rating).toFixed(1) }} ⭐
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="py-16 px-6">
                <EmptyState icon="truck" title="No rider data" description="No deliveries recorded for this period." />
            </div>
        </div>
    </AdminLayout>
</template>
