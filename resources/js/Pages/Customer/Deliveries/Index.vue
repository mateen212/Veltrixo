<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import TablePagination from '@/Components/TablePagination.vue'
import { TruckIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'

interface DeliveryItem { id: number; product_name: string; quantity: number }
interface Delivery {
    id: number
    delivery_date: string
    status: string
    address: string
    items: DeliveryItem[]
}
interface Paginated {
    data: Delivery[]
    meta: { current_page: number; last_page: number; per_page: number; total: number; from: number; to: number }
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{ deliveries: Paginated }>()

const statusVariant: Record<string, 'success' | 'warning' | 'danger' | 'info' | 'neutral'> = {
    delivered:   'success',
    in_progress: 'info',
    assigned:    'info',
    scheduled:   'neutral',
    pending:     'warning',
    missed:      'danger',
    cancelled:   'danger',
}
</script>

<template>
    <Head title="My Deliveries" />
    <CustomerLayout title="My Deliveries">
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-xs text-gray-400">{{ deliveries.meta.total }} total deliveries</p>
            </div>

            <!-- List -->
            <div v-if="deliveries.data.length" class="divide-y divide-gray-50">
                <Link
                    v-for="d in deliveries.data"
                    :key="d.id"
                    :href="route('customer.deliveries.show', d.id)"
                    class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/60 transition-colors group"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 shrink-0">
                        <TruckIcon class="h-5 w-5 text-indigo-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm font-semibold text-gray-800">Delivery #{{ d.id }}</p>
                            <AppBadge :variant="statusVariant[d.status] ?? 'neutral'" dot>
                                {{ d.status.replace('_', ' ') }}
                            </AppBadge>
                        </div>
                        <p class="text-xs text-gray-400 truncate">{{ d.address }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ d.delivery_date }} &middot; {{ d.items.length }} item{{ d.items.length !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <svg class="h-4 w-4 text-gray-300 group-hover:text-gray-500 shrink-0 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>

            <!-- Empty -->
            <div v-else class="flex flex-col items-center justify-center py-16">
                <TruckIcon class="h-10 w-10 text-gray-200 mb-3" />
                <p class="text-sm font-medium text-gray-500">No deliveries yet</p>
                <p class="text-xs text-gray-400 mt-1">Your scheduled deliveries will appear here</p>
            </div>

            <TablePagination :meta="deliveries.meta" :links="deliveries.links" />
        </div>
    </CustomerLayout>
</template>
