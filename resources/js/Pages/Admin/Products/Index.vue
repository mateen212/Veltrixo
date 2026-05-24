<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { ArrowPathIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'

interface Product {
    id: number; name: string; description: string; price: string;
    category: string; is_active: boolean; unit: string
}
const props = defineProps<{
    products: { data: Product[]; meta: any; links: any[] }
    filters:  { search?: string; active?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const active  = ref(props.filters?.active ?? '')
const loading = ref(false)

watch([active], () => apply())
function apply(reset = false) {
    loading.value = true
    router.get(route('admin.products.index'), {
        search: search.value || undefined, active: active.value || undefined, page: reset ? 1 : undefined,
    }, { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const cols = [
    { key: 'id',          label: '#',         sortable: true,  width: '60px' },
    { key: 'name',        label: 'Product',   sortable: true },
    { key: 'category',    label: 'Category',  sortable: true,  width: '130px' },
    { key: 'price',       label: 'Price',     sortable: true,  width: '100px' },
    { key: 'unit',        label: 'Unit',      sortable: false, width: '80px' },
    { key: 'is_active',   label: 'Status',    sortable: false, width: '90px' },
]
</script>
<template>
    <Head title="Products" />
    <AdminLayout title="Products">
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-gray-100">
                <div class="relative flex-1 max-w-xs">
                    <input v-model="search" @keydown.enter="apply(true)" type="text" placeholder="Search products…"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                </div>
                <select v-model="active" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <AppButton v-if="search || active" variant="ghost" size="sm" @click="search=''; active=''; apply(true)">
                    <XMarkIcon class="h-4 w-4" />Reset
                </AppButton>
                <div class="sm:ml-auto">
                    <AppButton size="sm" :href="route('admin.products.create')">
                        <PlusIcon class="h-4 w-4" />New Product
                    </AppButton>
                </div>
            </div>
            <DataTable :columns="cols" :rows="products.data" :loading="loading" empty-message="No products yet" empty-icon="📦">
                <template #default="{ row }">
                    <td class="px-4 py-3.5 text-xs font-mono text-gray-400">#{{ row.id }}</td>
                    <td class="px-4 py-3.5">
                        <p class="text-sm font-medium text-gray-800">{{ row.name }}</p>
                        <p v-if="row.description" class="text-xs text-gray-400 truncate max-w-xs">{{ row.description }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ row.category }}</td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ row.price }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-500">{{ row.unit }}</td>
                    <td class="px-4 py-3.5">
                        <AppBadge :variant="row.is_active ? 'success' : 'neutral'" dot>
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </AppBadge>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="products.meta" :links="products.links" />
        </div>
    </AdminLayout>
</template>
