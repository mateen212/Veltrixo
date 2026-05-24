<script setup lang="ts">
import { ref, computed, watch, type Ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import AppModal from '@/Components/AppModal.vue'
import AppInput from '@/Components/AppInput.vue'
import {
    XMarkIcon, FunnelIcon, ArrowPathIcon, ArrowDownTrayIcon,
    UserPlusIcon, CheckCircleIcon,
} from '@heroicons/vue/24/outline'

interface Rider   { id: number; name: string }
interface Delivery {
    id: number
    subscription_id: number
    delivery_date: string
    status: string
    address: string
    items_count: number
    rider?: Rider
    tenant_name?: string
}
interface PaginatedMeta {
    current_page: number; last_page: number; per_page: number; total: number; from: number; to: number
}

const props = defineProps<{
    deliveries: { data: Delivery[]; meta: PaginatedMeta; links: any[] }
    riders:     Rider[]
    filters:    { status?: string; date?: string; rider_id?: string; search?: string }
    stats:      { total: number; pending: number; assigned: number; completed: number; missed: number }
}>()

// ── Filters ─────────────────────────────────────────────────
const search   = ref(props.filters.search ?? '')
const status   = ref(props.filters.status ?? '')
const date     = ref(props.filters.date ?? '')
const riderId  = ref(props.filters.rider_id ?? '')
const loading  = ref(false)

watch([status, date, riderId], () => applyFilters())

function applyFilters(reset = false) {
    loading.value = true
    router.get(route('admin.deliveries.index'), {
        search:   search.value || undefined,
        status:   status.value || undefined,
        date:     date.value   || undefined,
        rider_id: riderId.value || undefined,
        page:     reset ? 1 : undefined,
    }, {
        preserveState: true, replace: true,
        onFinish: () => { loading.value = false },
    })
}
function resetFilters() { search.value = ''; status.value = ''; date.value = ''; riderId.value = ''; applyFilters(true) }

// ── Bulk assign ──────────────────────────────────────────────
const tableRef     = ref<{ selected: Ref<number[]>; clearSelection: () => void } | null>(null)
const selected     = ref<number[]>([])
const showAssign   = ref(false)
const assignRider  = ref('')
const assigning    = ref(false)

function handleSelect(ids: number[]) { selected.value = ids }

function bulkAssign() {
    assigning.value = true
    router.post(route('admin.deliveries.bulkAssign'), {
        delivery_ids: selected.value,
        rider_id:     assignRider.value,
    }, {
        onSuccess: () => { showAssign.value = false; assignRider.value = ''; tableRef.value?.clearSelection() },
        onFinish:  () => { assigning.value = false },
    })
}

// ── Badge helper ─────────────────────────────────────────────
const badgeMap: Record<string, any> = {
    delivered:   { variant: 'success',  label: 'Delivered' },
    assigned:    { variant: 'info',     label: 'Assigned' },
    in_progress: { variant: 'info',     label: 'In Progress' },
    scheduled:   { variant: 'neutral',  label: 'Scheduled' },
    pending:     { variant: 'warning',  label: 'Pending' },
    missed:      { variant: 'danger',   label: 'Missed' },
    cancelled:   { variant: 'danger',   label: 'Cancelled' },
}
function badge(s: string) { return badgeMap[s] ?? { variant: 'neutral', label: s } }

const columns = [
    { key: 'id',            label: '#',         sortable: true,  width: '60px' },
    { key: 'delivery_date', label: 'Date',       sortable: true,  width: '110px' },
    { key: 'address',       label: 'Address',    sortable: false },
    { key: 'status',        label: 'Status',     sortable: true,  width: '120px' },
    { key: 'rider',         label: 'Rider',      sortable: false, width: '140px' },
    { key: 'items_count',   label: 'Items',      sortable: true,  width: '60px' },
    { key: 'actions',       label: '',           sortable: false, width: '40px' },
]
</script>

<template>
    <Head title="Deliveries" />
    <AdminLayout title="Deliveries">
        <template #header-actions>
            <AppButton variant="outline" size="sm" @click="applyFilters()">
                <ArrowPathIcon class="h-4 w-4" />
            </AppButton>
        </template>

        <!-- Stats strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div v-for="(item, key) in [
                { label: 'Total',     value: stats.total,     color: 'text-gray-900', bg: 'bg-white' },
                { label: 'Pending',   value: stats.pending,   color: 'text-amber-700', bg: 'bg-amber-50' },
                { label: 'Assigned',  value: stats.assigned,  color: 'text-blue-700',  bg: 'bg-blue-50' },
                { label: 'Completed', value: stats.completed, color: 'text-emerald-700', bg: 'bg-emerald-50' },
            ]" :key="key"
                :class="['rounded-xl p-4 ring-1 ring-black/5', item.bg]">
                <p class="text-xs font-medium text-gray-500">{{ item.label }}</p>
                <p :class="['text-2xl font-bold mt-1', item.color]">{{ item.value.toLocaleString() }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 border-b border-gray-100">
                <!-- Search -->
                <div class="relative flex-1 max-w-xs">
                    <input v-model="search" @keydown.enter="applyFilters(true)"
                        type="text" placeholder="Search address or ID…"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400" />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                </div>
                <!-- Filters -->
                <div class="flex items-center gap-2 flex-wrap">
                    <select v-model="status" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_progress">In Progress</option>
                        <option value="delivered">Delivered</option>
                        <option value="missed">Missed</option>
                    </select>
                    <input v-model="date" type="date" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" />
                    <select v-model="riderId" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40">
                        <option value="">All riders</option>
                        <option v-for="r in riders" :key="r.id" :value="r.id">{{ r.name }}</option>
                    </select>
                    <AppButton v-if="status || date || riderId || search" variant="ghost" size="sm" @click="resetFilters">
                        <XMarkIcon class="h-4 w-4" />Reset
                    </AppButton>
                </div>
            </div>

            <!-- Bulk action bar -->
            <Transition
                enter-active-class="transition-all duration-150" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-100" leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-if="selected.length" class="flex items-center gap-3 px-4 py-2.5 bg-indigo-50 border-b border-indigo-100">
                    <span class="text-sm font-medium text-indigo-700">{{ selected.length }} selected</span>
                    <AppButton size="sm" @click="showAssign = true">
                        <UserPlusIcon class="h-4 w-4" /> Assign Rider
                    </AppButton>
                    <AppButton size="sm" variant="secondary" @click="tableRef?.clearSelection()">Clear</AppButton>
                </div>
            </Transition>

            <!-- Table -->
            <DataTable ref="tableRef" :columns="columns" :rows="deliveries.data" :loading="loading" selectable @select="handleSelect">
                <template #default="{ row }">
                    <td class="px-4 py-3.5 text-xs font-mono text-gray-400">#{{ row.id }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-700 whitespace-nowrap">{{ row.delivery_date }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 max-w-xs truncate">{{ row.address }}</td>
                    <td class="px-4 py-3.5">
                        <AppBadge :variant="badge(row.status).variant" dot>{{ badge(row.status).label }}</AppBadge>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">
                        <span v-if="row.rider" class="flex items-center gap-1.5">
                            <div class="h-5 w-5 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-semibold text-indigo-700 shrink-0">
                                {{ row.rider.name.charAt(0) }}
                            </div>
                            {{ row.rider.name }}
                        </span>
                        <span v-else class="text-gray-300 text-xs">Unassigned</span>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-500 text-center">{{ row.items_count }}</td>
                    <td class="px-4 py-3.5 text-right pr-3">
                        <button class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01"/></svg>
                        </button>
                    </td>
                </template>
            </DataTable>

            <!-- Pagination -->
            <TablePagination :meta="deliveries.meta" :links="deliveries.links" />
        </div>

        <!-- Assign Modal -->
        <AppModal v-if="showAssign" title="Assign Rider" size="sm" @close="showAssign = false">
            <div class="space-y-4">
                <p class="text-sm text-gray-500">Assigning <span class="font-semibold text-gray-800">{{ selected.length }}</span> deliveries.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Rider</label>
                    <select v-model="assignRider" class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40">
                        <option value="" disabled>Choose a rider…</option>
                        <option v-for="r in riders" :key="r.id" :value="r.id">{{ r.name }}</option>
                    </select>
                </div>
            </div>
            <template #footer>
                <div class="flex gap-2 justify-end">
                    <AppButton variant="outline" @click="showAssign = false">Cancel</AppButton>
                    <AppButton :loading="assigning" :disabled="!assignRider" @click="bulkAssign">
                        <CheckCircleIcon class="h-4 w-4" /> Confirm
                    </AppButton>
                </div>
            </template>
        </AppModal>
    </AdminLayout>
</template>
