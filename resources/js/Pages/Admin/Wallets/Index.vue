<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/24/outline'

interface Tx {
    id: number; customer_name: string; type: string;
    amount: string; description: string; created_at: string
}
const props = defineProps<{
    transactions: { data: Tx[]; meta: any; links: any[] }
    filters:      { type?: string; search?: string }
    stats:        { totalIn: string; totalOut: string; netFlow: string }
}>()

const search  = ref(props.filters.search ?? '')
const type    = ref(props.filters.type ?? '')
const loading = ref(false)
watch([type], () => apply())
function apply(reset = false) {
    loading.value = true
    router.get(route('admin.wallets.index'), {
        search: search.value || undefined, type: type.value || undefined, page: reset ? 1 : undefined,
    }, { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const typeVariant: Record<string, string> = {
    credit: 'success', debit: 'danger', refund: 'info',
}
const cols = [
    { key: 'id',            label: '#',          sortable: true, width: '60px' },
    { key: 'customer_name', label: 'Customer',   sortable: true },
    { key: 'type',          label: 'Type',       sortable: true, width: '90px' },
    { key: 'amount',        label: 'Amount',     sortable: true, width: '120px' },
    { key: 'description',   label: 'Description', sortable: false },
    { key: 'created_at',    label: 'Date',       sortable: true, width: '140px' },
]
</script>
<template>
    <Head title="Wallet Transactions" />
    <AdminLayout title="Wallet Transactions">
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div v-for="s in [
                { label: 'Total In',  value: stats.totalIn,  bg: 'bg-emerald-50', c: 'text-emerald-700' },
                { label: 'Total Out', value: stats.totalOut, bg: 'bg-red-50',     c: 'text-red-700' },
                { label: 'Net Flow',  value: stats.netFlow,  bg: 'bg-white',      c: 'text-gray-900' },
            ]" :key="s.label" :class="['rounded-xl p-4 ring-1 ring-black/5', s.bg]">
                <p class="text-xs font-medium text-gray-500">{{ s.label }}</p>
                <p :class="['text-2xl font-bold mt-1', s.c]">{{ s.value }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-gray-100">
                <div class="relative flex-1 max-w-xs">
                    <input v-model="search" @keydown.enter="apply(true)" type="text" placeholder="Search customer…"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                </div>
                <select v-model="type" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none">
                    <option value="">All types</option>
                    <option value="credit">Credit</option>
                    <option value="debit">Debit</option>
                    <option value="refund">Refund</option>
                </select>
                <AppButton v-if="type || search" variant="ghost" size="sm" @click="type=''; search=''; apply(true)">
                    <XMarkIcon class="h-4 w-4" />Reset
                </AppButton>
                <AppButton variant="outline" size="sm" @click="apply()"><ArrowPathIcon class="h-4 w-4" /></AppButton>
            </div>
            <DataTable :columns="cols" :rows="transactions.data" :loading="loading" empty-message="No transactions found" empty-icon="💳">
                <template #default="{ row }">
                    <td class="px-4 py-3.5 text-xs font-mono text-gray-400">#{{ row.id }}</td>
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800">{{ row.customer_name }}</td>
                    <td class="px-4 py-3.5"><AppBadge :variant="(typeVariant[row.type] ?? 'neutral') as any" dot>{{ row.type }}</AppBadge></td>
                    <td class="px-4 py-3.5 text-sm font-semibold" :class="row.type === 'credit' ? 'text-emerald-700' : 'text-red-600'">
                        {{ row.type === 'credit' ? '+' : '-' }}{{ row.amount }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-500 truncate max-w-xs">{{ row.description }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-400 whitespace-nowrap">{{ row.created_at }}</td>
                </template>
            </DataTable>
            <TablePagination :meta="transactions.meta" :links="transactions.links" />
        </div>
    </AdminLayout>
</template>
