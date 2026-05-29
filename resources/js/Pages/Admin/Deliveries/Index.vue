<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    deliveries: { data: any[]; meta: any; links: any[] }
    filters: { search?: string; status?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const status  = ref(props.filters?.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply() {
    loading.value = true
    router.get(route('admin.deliveries.index'), { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const statusMap: Record<string, any> = {
    delivered: 'success', scheduled: 'neutral', pending: 'warning',
    missed: 'danger', in_progress: 'info', assigned: 'info',
}

const cols = [
    { key: 'id',            label: '#',       sortable: true, width: '60px' },
    { key: 'customer',      label: 'Customer', sortable: false },
    { key: 'rider',         label: 'Rider',    sortable: false, width: '140px' },
    { key: 'delivery_date', label: 'Date',     sortable: true,  width: '120px' },
    { key: 'status',        label: 'Status',   sortable: false, width: '110px' },
]
</script>

<template>
    <Head title="Deliveries — Admin" />
    <AdminLayout title="Deliveries">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Deliveries</h1>
                <p class="page-subtitle">Track all delivery orders</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply" type="text" placeholder="Search…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
                <select v-model="status" class="field-input text-sm py-2 w-auto">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="delivered">Delivered</option>
                    <option value="missed">Missed</option>
                </select>
            </div>
            <DataTable :columns="cols" :rows="deliveries.data" :loading="loading"
                empty-title="No deliveries found" empty-description="No deliveries match your filters.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5 text-sm font-medium text-ink">{{ row.customer?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.rider?.name ?? 'Unassigned' }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.delivery_date }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="statusMap[row.status] ?? 'neutral'" dot size="xs">
                            {{ row.status?.replace('_', ' ') }}
                        </AppBadge>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="deliveries.meta" :links="deliveries.links" />
        </div>
    </AdminLayout>
</template>
