<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    subscriptions: { data: any[]; meta: any; links: any[] }
    filters: { search?: string; status?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const status  = ref(props.filters?.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply() {
    loading.value = true
    router.get(route('admin.subscriptions.index'), { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const statusMap: Record<string, any> = {
    active: 'success', paused: 'warning', cancelled: 'danger', pending: 'neutral',
}

const cols = [
    { key: 'id',         label: '#',        sortable: true, width: '60px' },
    { key: 'customer',   label: 'Customer', sortable: false },
    { key: 'plan',       label: 'Plan',     sortable: false, width: '120px' },
    { key: 'start_date', label: 'Started',  sortable: true,  width: '120px' },
    { key: 'status',     label: 'Status',   sortable: false, width: '100px' },
    { key: 'actions',    label: '',         sortable: false, width: '60px' },
]
</script>

<template>
    <Head title="Subscriptions — Admin" />
    <AdminLayout title="Subscriptions">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Subscriptions</h1>
                <p class="page-subtitle">Manage all customer subscriptions</p>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply" type="text" placeholder="Search subscriptions…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
                <select v-model="status" class="field-input text-sm py-2 w-auto">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <DataTable :columns="cols" :rows="subscriptions.data" :loading="loading"
                empty-title="No subscriptions" empty-description="No subscriptions match your filters.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5 text-sm font-medium text-ink">{{ row.customer?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.plan?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.start_date }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="statusMap[row.status] ?? 'neutral'" dot size="xs">{{ row.status }}</AppBadge>
                    </td>
                    <td class="px-5 py-3.5">
                        <Link :href="route('admin.subscriptions.show', row.id)"
                            class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">View</Link>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="subscriptions.meta" :links="subscriptions.links" />
        </div>
    </AdminLayout>
</template>
