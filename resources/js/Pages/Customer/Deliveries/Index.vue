<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'

const props = defineProps<{
    deliveries: { data: any[]; meta: any; links: any[] }
    filters: { status?: string }
}>()

const status  = ref(props.filters?.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply() {
    loading.value = true
    router.get(route('customer.deliveries.index'), { status: status.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const statusMap: Record<string, any> = {
    delivered: 'success', scheduled: 'neutral', pending: 'warning',
    missed: 'danger', in_progress: 'info', assigned: 'info',
}

const cols = [
    { key: 'id',            label: '#',       sortable: true, width: '60px' },
    { key: 'delivery_date', label: 'Date',    sortable: true, width: '130px' },
    { key: 'items_count',   label: 'Items',   sortable: false, width: '80px' },
    { key: 'status',        label: 'Status',  sortable: false, width: '110px' },
    { key: 'actions',       label: '',        sortable: false, width: '60px' },
]
</script>

<template>
    <Head title="My Deliveries" />
    <CustomerLayout title="Deliveries">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">My Deliveries</h1>
                <p class="page-subtitle">Track your delivery history</p>
            </div>
            <select v-model="status" class="field-input text-sm py-2 w-auto">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="delivered">Delivered</option>
                <option value="missed">Missed</option>
            </select>
        </div>
        <div class="card overflow-hidden">
            <DataTable :columns="cols" :rows="deliveries.data" :loading="loading"
                empty-title="No deliveries" empty-description="Your deliveries will appear here.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink">{{ row.delivery_date }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.items_count ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="statusMap[row.status] ?? 'neutral'" dot size="xs">
                            {{ row.status?.replace('_', ' ') }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5">
                        <Link :href="route('customer.deliveries.show', row.id)"
                            class="text-xs font-medium text-brand-600 hover:text-brand-700">View</Link>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="deliveries.meta" :links="deliveries.links" />
        </div>
    </CustomerLayout>
</template>
