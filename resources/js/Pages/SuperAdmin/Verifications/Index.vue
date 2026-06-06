<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import StatCard from '@/Components/StatCard.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'

interface Tenant {
    id: number
    name: string
    email: string
    owner?: {
        name: string
    }
    verification_status?: string
    verified_at?: string
    created_at: string
}

interface PendingResponse {
    data: Tenant[]
    meta: any
    links: any[]
}

defineProps<{
    pending: PendingResponse
    recentlyActioned: Tenant[]
    stats: {
        pending: number
        approved: number
        rejected: number
    }
}>()

const statusMap: Record<string, any> = {
    pending_verification: 'warning', approved: 'success', rejected: 'danger',
}

const cols = [
    { key: 'id', label: '#', sortable: false, width: '60px' },
    { key: 'name', label: 'Business', sortable: false },
    { key: 'owner_name', label: 'Owner', sortable: false },
    { key: 'status', label: 'Status', sortable: false, width: '120px' },
    { key: 'created_at', label: 'Submitted', sortable: false, width: '120px' },
]
</script>

<template>
    <Head title="Verifications — Super Admin" />
    <SuperAdminLayout title="Verifications">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Business Verifications</h1>
                <p class="page-subtitle">Review pending tenant verification requests</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <StatCard title="Pending" :value="stats.pending" />
            <StatCard title="Approved" :value="stats.approved" />
            <StatCard title="Rejected" :value="stats.rejected" />
        </div>

        <!-- Pending Verifications Table -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Pending Review</h2>
            </div>
            <DataTable :columns="cols" :rows="pending.data"
                empty-title="All verified!" empty-description="No pending verification requests.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5">
                        <div>
                            <p class="text-sm font-medium text-ink">{{ row.name }}</p>
                            <p class="text-xs text-ink-faint">{{ row.email }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.owner?.name ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="statusMap[row.verification_status] ?? 'neutral'" dot size="xs">
                            {{ row.verification_status?.replace('_', ' ') }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">{{ row.created_at }}</td>
                    <td class="px-5 py-3.5">
                        <Link :href="route('super-admin.verifications.show', row.id)"
                            class="inline-flex h-8 px-3 items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700 transition-colors text-sm font-medium">
                            Review
                        </Link>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="pending.meta" :links="pending.links" />
        </div>

        <!-- Recently Actioned -->
        <div v-if="recentlyActioned.length" class="card mt-6">
            <div class="p-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Recently Actioned</h2>
            </div>
            <div class="divide-y divide-border-muted">
                <div v-for="tenant in recentlyActioned" :key="tenant.id" class="p-4 flex items-center justify-between hover:bg-surface-subtle transition-colors">
                    <div>
                        <p class="text-sm font-medium text-ink">{{ tenant.name }}</p>
                        <p class="text-xs text-ink-faint">{{ tenant.owner?.name ?? '—' }} • {{ tenant.verified_at }}</p>
                    </div>
                    <AppBadge :variant="statusMap[tenant.verification_status] ?? 'neutral'" dot size="xs">
                        {{ tenant.verification_status?.replace('_', ' ') }}
                    </AppBadge>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
