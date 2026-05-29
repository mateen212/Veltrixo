<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import KpiCard from '@/Components/KpiCard.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { BuildingOfficeIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

defineProps<{
    totalTenants:          number
    activeTenants:         number
    totalRevenue:          number | string
    pendingVerifications:  number
    recentTenants:         Array<{ id: number; name: string; status: string; created_at: string; plan?: string }>
}>()

const statusMap: Record<string, any> = {
    active: 'success', inactive: 'neutral', suspended: 'danger', pending: 'warning',
}
</script>

<template>
    <Head title="Super Admin Dashboard" />
    <SuperAdminLayout title="Dashboard">
        <!-- KPI cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <KpiCard title="Total Tenants"   :value="totalTenants"         icon="building" color="blue" />
            <KpiCard title="Active Tenants"  :value="activeTenants"        icon="check"    color="green" />
            <KpiCard title="Revenue (MRR)"   :value="totalRevenue"         icon="currency" color="indigo" />
            <KpiCard title="Pending Verify"  :value="pendingVerifications" icon="clock"    color="yellow" />
        </div>

        <!-- Recent tenants -->
        <div class="card overflow-hidden mb-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Recent Tenants</h2>
                <Link :href="route('super-admin.tenants.index')"
                    class="flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:text-violet-700 transition-colors">
                    View all <ArrowRightIcon class="h-3 w-3" />
                </Link>
            </div>

            <div v-if="recentTenants.length" class="divide-y divide-border-muted">
                <div v-for="t in recentTenants" :key="t.id"
                    class="flex items-center justify-between px-6 py-3.5 hover:bg-surface-subtle transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                            <BuildingOfficeIcon class="h-4 w-4 text-violet-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-ink">{{ t.name }}</p>
                            <p class="text-xs text-ink-faint">{{ t.created_at }} · {{ t.plan ?? '—' }}</p>
                        </div>
                    </div>
                    <AppBadge :variant="statusMap[t.status] ?? 'neutral'" dot size="xs">{{ t.status }}</AppBadge>
                </div>
            </div>
            <div v-else class="py-14 px-6">
                <EmptyState icon="building" title="No tenants yet" description="Tenants will appear here once they register." />
            </div>
        </div>

        <!-- Pending verifications CTA -->
        <div v-if="pendingVerifications > 0"
            class="rounded-2xl p-5 flex items-center justify-between"
            style="background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(251,191,36,0.05)); border: 1px solid rgba(245,158,11,0.2);">
            <div>
                <p class="text-sm font-semibold text-ink">
                    {{ pendingVerifications }} pending verification{{ pendingVerifications !== 1 ? 's' : '' }} awaiting review
                </p>
                <p class="text-xs text-ink-muted mt-0.5">Businesses waiting for admin approval</p>
            </div>
            <Link :href="route('super-admin.verifications.index')"
                class="btn btn-sm rounded-xl font-semibold text-amber-700"
                style="background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3);">
                Review Now
            </Link>
        </div>
    </SuperAdminLayout>
</template>
