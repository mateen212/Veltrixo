<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    tenants: { data: any[]; meta: any; links: any[] }
    filters: { search?: string; status?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const status  = ref(props.filters?.status ?? '')
const loading = ref(false)

watch([status], () => apply())
function apply() {
    loading.value = true
    router.get(route('super-admin.tenants.index'), { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const statusMap: Record<string, any> = {
    active: 'success', inactive: 'neutral', suspended: 'danger', pending: 'warning',
}

const cols = [
    { key: 'id',         label: '#',        sortable: true,  width: '60px' },
    { key: 'name',       label: 'Tenant',   sortable: true },
    { key: 'plan',       label: 'Plan',     sortable: false, width: '120px' },
    { key: 'status',     label: 'Status',   sortable: false, width: '100px' },
    { key: 'created_at', label: 'Joined',   sortable: true,  width: '120px' },
]
</script>

<template>
    <Head title="Tenants — Super Admin" />
    <SuperAdminLayout title="Tenants">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Tenants</h1>
                <p class="page-subtitle">All businesses on the platform</p>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply" type="text" placeholder="Search tenants…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
                <select v-model="status" class="field-input text-sm py-2 w-auto">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <DataTable :columns="cols" :rows="tenants.data" :loading="loading"
                empty-title="No tenants yet" empty-description="Tenants will appear once they register.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-lg bg-violet-50 flex items-center justify-center text-xs font-bold text-violet-700 shrink-0">
                                {{ row.name?.slice(0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-ink">{{ row.name }}</p>
                                <p v-if="row.domain" class="text-xs text-ink-faint">{{ row.domain }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.plan?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="statusMap[row.status] ?? 'neutral'" dot size="xs">{{ row.status }}</AppBadge>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.created_at }}</td>
                </template>
            </DataTable>
            <TablePagination :meta="tenants.meta" :links="tenants.links" />
        </div>
    </SuperAdminLayout>
</template>
