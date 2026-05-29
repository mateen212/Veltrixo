<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import RiderLayout from '@/Layouts/RiderLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import TablePagination from '@/Components/TablePagination.vue'
import { MapPinIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    deliveries: { data: any[]; meta: any; links: any[] }
    filters: { status?: string }
}>()

const status  = ref(props.filters?.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply() {
    loading.value = true
    router.get(route('rider.deliveries.index'), { status: status.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

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
    <Head title="My Deliveries — Rider" />
    <RiderLayout title="Deliveries">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-base font-bold text-ink font-display">All Deliveries</h1>
            <select v-model="status" class="field-input text-sm py-2 w-auto">
                <option value="">All</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="delivered">Delivered</option>
                <option value="missed">Missed</option>
            </select>
        </div>

        <div v-if="deliveries.data.length" class="space-y-3">
            <div v-for="d in deliveries.data" :key="d.id"
                class="card p-4 flex items-start gap-3 active:scale-[0.99] transition-all">
                <div class="h-9 w-9 rounded-xl bg-brand-50 flex items-center justify-center shrink-0 mt-0.5">
                    <MapPinIcon class="h-4 w-4 text-brand-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-ink truncate">{{ d.address ?? 'Address not set' }}</p>
                    <p class="text-xs text-ink-faint mt-0.5">{{ d.delivery_date }} · #{{ d.id }}</p>
                </div>
                <AppBadge :variant="statusMap[d.status] ?? 'neutral'" dot size="xs">
                    {{ statusLabel[d.status] ?? d.status }}
                </AppBadge>
            </div>
        </div>
        <div v-else class="card py-14 px-4">
            <EmptyState icon="truck" title="No deliveries" description="No deliveries match this filter." />
        </div>
        <TablePagination :meta="deliveries.meta" :links="deliveries.links" class="mt-4" />
    </RiderLayout>
</template>
