<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { PlusIcon, XMarkIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

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
    { key: 'id',        label: '#',        sortable: true,  width: '60px' },
    { key: 'name',      label: 'Product',  sortable: true },
    { key: 'category',  label: 'Category', sortable: true,  width: '130px' },
    { key: 'price',     label: 'Price',    sortable: true,  width: '100px' },
    { key: 'unit',      label: 'Unit',     sortable: false, width: '80px' },
    { key: 'is_active', label: 'Status',   sortable: false, width: '90px' },
    { key: 'actions',   label: '',         sortable: false, width: '80px' },
]
</script>

<template>
    <Head title="Products — Admin" />
    <AdminLayout title="Products">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Products</h1>
                <p class="page-subtitle">Manage your product catalogue</p>
            </div>
            <AppButton :href="route('admin.products.create')" size="sm">
                <PlusIcon class="h-4 w-4" /> New Product
            </AppButton>
        </div>

        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply(true)" type="text" placeholder="Search products…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
                <select v-model="active" class="field-input text-sm py-2 w-auto">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <AppButton v-if="search || active" variant="ghost" size="sm"
                    @click="search=''; active=''; apply(true)">
                    <XMarkIcon class="h-4 w-4" /> Reset
                </AppButton>
            </div>

            <DataTable :columns="cols" :rows="products.data" :loading="loading"
                empty-title="No products found"
                empty-description="Add your first product to get started.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5">
                        <p class="text-sm font-medium text-ink">{{ row.name }}</p>
                        <p v-if="row.description" class="text-xs text-ink-faint truncate max-w-xs">{{ row.description }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.category }}</td>
                    <td class="px-5 py-3.5 text-sm font-semibold text-ink">{{ row.price }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.unit }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="row.is_active ? 'success' : 'neutral'" dot size="xs">
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <Link :href="route('admin.products.edit', row.id)"
                                class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                                Edit
                            </Link>
                        </div>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="products.meta" :links="products.links" />
        </div>
    </AdminLayout>
</template>
