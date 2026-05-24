<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/24/outline'

interface Sub {
    id: number; customer_name: string; plan_name: string;
    status: string; start_date: string; next_delivery_date: string; items_count: number
}
const props = defineProps<{
    subscriptions: { data: Sub[]; meta: any; links: any[] }
    filters: { status?: string; search?: string }
    stats: { active: number; paused: number; cancelled: number; total: number }
}>()

const search  = ref(props.filters.search ?? '')
const status  = ref(props.filters.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply(reset = false) {
    loading.value = true
    router.get(route('admin.subscriptions.index'), {
        search: search.value || undefined, status: status.value || undefined, page: reset ? 1 : undefined,
    }, { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const badgeMap: Record<string, string> = {
    active: 'success', paused: 'warning', cancelled: 'danger', expired: 'neutral',
}
const cols = [
    { key: 'id',                 label: '#',           sortable: true,  width: '60px' },
    { key: 'customer_name',      label: 'Customer',    sortable: true },
    { key: 'plan_name',          label: 'Plan',        sortable: false },
    { key: 'status',             label: 'Status',      sortable: true,  width: '110px' },
    { key: 'start_date',         label: 'Start',       sortable: true,  width: '110px' },
    { key: 'next_delivery_date', label: 'Next Delivery', sortable: true, width: '130px' },
    { key: 'items_count',        label: 'Items',       sortable: false, width: '60px' },
]
</script>
<template>
    <Head title="Subscriptions" />
    <AdminLayout title="Subscriptions">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div v-for="s in [
                { label:'Total',     value: stats.total,     bg: 'bg-white' },
                { label:'Active',    value: stats.active,    bg: 'bg-emerald-50' },
                { label:'Paused',    value: stats.paused,    bg: 'bg-amber-50' },
                { label:'Cancelled', value: stats.cancelled, bg: 'bg-red-50' },
            ]" :key="s.label" :class="['rounded-xl p-4 ring-1 ring-black/5', s.bg]">
                <p class="text-xs font-medium text-gray-500">{{ s.label }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ s.value }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-gray-100">
                <div class="relative flex-1 max-w-xs">
                    <input v-model="search" @keydown.enter="apply(true)" type="text" placeholder="Search customer…"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                </div>
                <select v-model="status" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <AppButton v-if="status || search" variant="ghost" size="sm" @click="status=''; search=''; apply(true)">
                    <XMarkIcon class="h-4 w-4" />Reset
                </AppButton>
                <AppButton variant="outline" size="sm" @click="apply()"><ArrowPathIcon class="h-4 w-4" /></AppButton>
            </div>
            <DataTable :columns="cols" :rows="subscriptions.data" :loading="loading" empty-message="No subscriptions found" empty-icon="📋">
                <template #default="{ row }">
                    <td class="px-4 py-3.5 text-xs font-mono text-gray-400">#{{ row.id }}</td>
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800">{{ row.customer_name }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ row.plan_name }}</td>
                    <td class="px-4 py-3.5"><AppBadge :variant="(badgeMap[row.status] ?? 'neutral') as any" dot>{{ row.status }}</AppBadge></td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 whitespace-nowrap">{{ row.start_date }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 whitespace-nowrap">{{ row.next_delivery_date ?? '—' }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-500 text-center">{{ row.items_count }}</td>
                </template>
            </DataTable>
            <TablePagination :meta="subscriptions.meta" :links="subscriptions.links" />
        </div>
    </AdminLayout>
</template>
