<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppBadge from '@/Components/AppBadge.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    riders: { data: any[]; meta: any; links: any[] }
    filters: { search?: string }
}>()

const search  = ref(props.filters?.search ?? '')
const loading = ref(false)

function apply() {
    loading.value = true
    router.get(route('admin.riders.index'), { search: search.value || undefined },
        { preserveState: true, replace: true, onFinish: () => { loading.value = false } })
}

const cols = [
    { key: 'id',         label: '#',         sortable: true,  width: '60px' },
    { key: 'name',       label: 'Rider',     sortable: true },
    { key: 'phone',      label: 'Phone',     sortable: false, width: '140px' },
    { key: 'is_active',  label: 'Status',    sortable: false, width: '90px' },
    { key: 'deliveries', label: 'Deliveries', sortable: true, width: '110px' },
    { key: 'rating',     label: 'Rating',    sortable: true,  width: '90px' },
]
</script>

<template>
    <Head title="Riders — Admin" />
    <AdminLayout title="Riders">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Riders</h1>
                <p class="page-subtitle">Manage your delivery riders</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-border-muted">
                <div class="relative flex-1 max-w-xs">
                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-ink-faint" />
                    <input v-model="search" @keydown.enter="apply" type="text" placeholder="Search riders…"
                        class="field-input pl-9 py-2 text-sm" />
                </div>
            </div>
            <DataTable :columns="cols" :rows="riders.data" :loading="loading"
                empty-title="No riders found" empty-description="Riders will appear here once they register.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-lg bg-brand-50 flex items-center justify-center text-xs font-bold text-brand-700 shrink-0">
                                {{ row.name?.slice(0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-ink">{{ row.name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.phone ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="row.is_active ? 'success' : 'neutral'" dot size="xs">
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.deliveries_count ?? 0 }}</td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">
                        {{ row.rating ? Number(row.rating).toFixed(1) + ' ⭐' : '—' }}
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="riders.meta" :links="riders.links" />
        </div>
    </AdminLayout>
</template>
