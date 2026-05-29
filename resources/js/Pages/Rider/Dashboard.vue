<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import RiderLayout from '@/Layouts/RiderLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { TruckIcon, CheckCircleIcon, ClockIcon, MapPinIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

defineProps<{
    todayDeliveries:  number
    completedToday:   number
    pendingToday:     number
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
            <div class="rounded-2xl p-4 text-center text-white shadow-lg"
                style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                <TruckIcon class="h-5 w-5 mx-auto mb-1 opacity-80" />
                <p class="text-2xl font-bold font-display">{{ todayDeliveries }}</p>
                <p class="text-xs opacity-60 mt-0.5">Today</p>
            </div>
            <div class="card p-4 text-center">
                <CheckCircleIcon class="h-5 w-5 mx-auto mb-1 text-emerald-500" />
                <p class="text-2xl font-bold text-ink font-display">{{ completedToday }}</p>
                <p class="text-xs text-ink-muted mt-0.5">Done</p>
            </div>
            <div class="card p-4 text-center">
                <ClockIcon class="h-5 w-5 mx-auto mb-1 text-amber-500" />
                <p class="text-2xl font-bold text-ink font-display">{{ pendingToday }}</p>
                <p class="text-xs text-ink-muted mt-0.5">Remaining</p>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="card p-4 mb-5">
            <div class="flex items-center justify-between text-xs font-medium text-ink-secondary mb-2">
                <span>Today's progress</span>
                <span class="text-brand-600">{{ todayDeliveries > 0 ? Math.round(completedToday / todayDeliveries * 100) : 0 }}%</span>
            </div>
            <div class="h-2 rounded-full bg-surface-subtle overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                    style="background: linear-gradient(90deg, #4F46E5, #7C3AED);"
                    :style="{ width: todayDeliveries > 0 ? (completedToday / todayDeliveries * 100) + '%' : '0%' }" />
            </div>
        </div>

        <!-- Delivery list -->
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3.5 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Assigned Deliveries</h2>
                <Link :href="route('rider.deliveries.index')"
                    class="flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                    All <ArrowRightIcon class="h-3 w-3" />
                </Link>
            </div>

            <div v-if="recentDeliveries.length" class="divide-y divide-border-muted">
                <div v-for="d in recentDeliveries" :key="d.id"
                    class="flex items-start gap-3 px-4 py-3.5 hover:bg-surface-subtle transition-colors">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-xl bg-brand-50 shrink-0">
                        <MapPinIcon class="h-4 w-4 text-brand-600" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ d.address }}</p>
                        <p class="text-xs text-ink-faint">{{ d.delivery_date }}</p>
                    </div>
                    <AppBadge :variant="statusMap[d.status] ?? 'neutral'" dot size="xs">
                        {{ statusLabel[d.status] ?? d.status }}
                    </AppBadge>
                </div>
            </div>
            <div v-else class="py-12 px-4">
                <EmptyState icon="truck" title="No deliveries today" description="No deliveries have been assigned to you yet." />
            </div>
        </div>
    </RiderLayout>
</template>
