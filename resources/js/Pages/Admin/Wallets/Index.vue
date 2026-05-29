<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    wallets: { data: any[]; meta: any; links: any[] }
    filters: { search?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const loading = ref(false)

function apply() {
    loading.value = true
    router.get(route('admin.wallets.index'), { search: search.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const cols = [
    { key: 'id',       label: '#',        sortable: true,  width: '60px' },
    { key: 'customer', label: 'Customer', sortable: false },
    { key: 'balance',  label: 'Balance',  sortable: true,  width: '120px' },
    { key: 'status',   label: 'Status',   sortable: false, width: '90px' },
]
</script>

<template>
    <Head title="Wallets — Admin" />
    <AdminLayout title="Wallets">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Wallets</h1>
                <p class="page-subtitle">Customer wallet balances</p>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply" type="text" placeholder="Search by customer…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
            </div>
            <DataTable :columns="cols" :rows="wallets.data" :loading="loading"
                empty-title="No wallets found" empty-description="Wallets are created when customers register.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5 text-sm font-medium text-ink">{{ row.customer?.name ?? row.user?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm font-semibold text-emerald-600">{{ row.balance }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="row.is_active ?? row.status === 'active' ? 'success' : 'neutral'" dot size="xs">
                            {{ (row.is_active ?? row.status === 'active') ? 'Active' : 'Inactive' }}
                        </AppBadge>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="wallets.meta" :links="wallets.links" />
        </div>
    </AdminLayout>
</template>
