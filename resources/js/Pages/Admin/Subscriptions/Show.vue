<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import AppButton from '@/Components/AppButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { CalendarDaysIcon, UserIcon, CubeIcon } from '@heroicons/vue/24/outline'

interface SubItem { id: number; product_name: string; quantity: number }
interface Delivery { id: number; delivery_date: string; status: string }
interface Subscription {
    id: number
    customer_name: string
    plan_name: string
    status: string
    start_date: string
    next_delivery_date?: string
    address: string
    items: SubItem[]
    deliveries: Delivery[]
}

defineProps<{ subscription: Subscription }>()

const statusVariant: Record<string, 'success' | 'warning' | 'danger' | 'neutral'> = {
    active: 'success', paused: 'warning', cancelled: 'danger', expired: 'neutral',
}

function doAction(action: 'cancel' | 'pause' | 'resume', id: number) {
    router.post(route(`admin.subscriptions.${action}`, id), {}, { preserveScroll: true })
}
</script>

<template>
    <Head :title="`Subscription #${subscription.id}`" />
    <AdminLayout :title="`Subscription #${subscription.id}`">
        <div class="mb-4">
            <Link :href="route('admin.subscriptions.index')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </Link>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                <!-- Overview -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50">
                                <CalendarDaysIcon class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Subscription #{{ subscription.id }}</p>
                                <p class="text-xs text-gray-400">{{ subscription.plan_name }}</p>
                            </div>
                        </div>
                        <AppBadge :variant="statusVariant[subscription.status] ?? 'neutral'" dot>{{ subscription.status }}</AppBadge>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50 text-sm">
                        <div><p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Customer</p><p class="mt-1 font-medium text-gray-800">{{ subscription.customer_name }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Started</p><p class="mt-1 text-gray-700">{{ subscription.start_date }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Next Delivery</p><p class="mt-1 text-gray-700">{{ subscription.next_delivery_date ?? '—' }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Address</p><p class="mt-1 text-gray-700 truncate">{{ subscription.address }}</p></div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                        <CubeIcon class="h-4 w-4 text-gray-400" />
                        <h3 class="text-sm font-semibold text-gray-800">Items</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in subscription.items" :key="item.id"
                            class="flex items-center justify-between px-5 py-3.5">
                            <p class="text-sm text-gray-800">{{ item.product_name }}</p>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">×{{ item.quantity }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent deliveries -->
                <div v-if="subscription.deliveries?.length" class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-800">Deliveries</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="d in subscription.deliveries" :key="d.id"
                            class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="text-gray-700">#{{ d.id }} — {{ d.delivery_date }}</span>
                            <AppBadge :variant="({ delivered: 'success', missed: 'danger', pending: 'warning' } as any)[d.status] ?? 'neutral'" dot>{{ d.status }}</AppBadge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4 space-y-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Actions</h3>
                    <AppButton v-if="subscription.status === 'active'" variant="secondary" class="w-full justify-center" size="sm" @click="doAction('pause', subscription.id)">Pause</AppButton>
                    <AppButton v-if="subscription.status === 'paused'" class="w-full justify-center" size="sm" @click="doAction('resume', subscription.id)">Resume</AppButton>
                    <AppButton v-if="subscription.status !== 'cancelled'" variant="danger" class="w-full justify-center" size="sm" @click="doAction('cancel', subscription.id)">Cancel</AppButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
