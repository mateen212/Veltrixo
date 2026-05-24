<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import RiderLayout from '@/Layouts/RiderLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import AppButton from '@/Components/AppButton.vue'
import { MapPinIcon, CheckCircleIcon, PlayCircleIcon } from '@heroicons/vue/24/outline'

interface Delivery {
    id: number; address: string; status: string
    delivery_date: string; items_count: number
    customer_name: string; notes?: string
}

const props = defineProps<{
    deliveries: Delivery[]
    date: string
}>()

const statusVariant: Record<string, string> = {
    delivered: 'success', in_progress: 'info', assigned: 'neutral',
    scheduled: 'neutral', pending: 'warning', missed: 'danger',
}

function start(id: number) {
    router.patch(route('rider.deliveries.start', id), {}, { preserveScroll: true })
}
function complete(id: number) {
    router.patch(route('rider.deliveries.complete', id), {}, { preserveScroll: true })
}
</script>
<template>
    <Head title="My Deliveries" />
    <RiderLayout title="My Deliveries">
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm font-medium text-gray-600">{{ date }}</p>
            <AppBadge variant="info">{{ deliveries.length }} deliveries</AppBadge>
        </div>

        <div v-if="deliveries.length" class="space-y-3">
            <div v-for="d in deliveries" :key="d.id"
                class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 shrink-0">
                                <MapPinIcon class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ d.address }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ d.customer_name }} · {{ d.items_count }} item{{ d.items_count !== 1 ? 's' : '' }}</p>
                                <p v-if="d.notes" class="text-xs text-amber-600 mt-1">⚠ {{ d.notes }}</p>
                            </div>
                        </div>
                        <AppBadge :variant="(statusVariant[d.status] ?? 'neutral') as any" dot>
                            {{ d.status.replace('_', ' ') }}
                        </AppBadge>
                    </div>
                </div>
                <!-- Action bar -->
                <div v-if="d.status === 'assigned' || d.status === 'scheduled'"
                    class="border-t border-gray-50 px-4 py-3 bg-gray-50/50">
                    <AppButton size="sm" class="w-full justify-center" @click="start(d.id)">
                        <PlayCircleIcon class="h-4 w-4" />Start Delivery
                    </AppButton>
                </div>
                <div v-else-if="d.status === 'in_progress'"
                    class="border-t border-gray-50 px-4 py-3 bg-emerald-50/50">
                    <AppButton size="sm" class="w-full justify-center" variant="secondary" @click="complete(d.id)">
                        <CheckCircleIcon class="h-4 w-4 text-emerald-600" />Mark as Delivered
                    </AppButton>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-16 text-center">
            <CheckCircleIcon class="h-12 w-12 text-gray-200 mb-3" />
            <p class="text-base font-medium text-gray-500">All done for today!</p>
            <p class="text-sm text-gray-400 mt-1">No deliveries scheduled</p>
        </div>
    </RiderLayout>
</template>
