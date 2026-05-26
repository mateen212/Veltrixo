<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'

interface Owner {
    id: number
    name: string
    email: string
    phone: string | null
}

interface Tenant {
    id: number
    name: string
    email: string
    phone: string | null
    latitude: number | null
    longitude: number | null
    delivery_radius_km: number
    verification_status: string
    address: Record<string, string> | null
    meta: Record<string, any> | null
    created_at: string
    owner: Owner | null
}

interface Paginated<T> {
    data: T[]
    current_page: number
    last_page: number
    total: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    pending: Paginated<Tenant>
    recentlyActioned: Tenant[]
    stats: { pending: number; approved: number; rejected: number }
}>()

function statusClass(status: string) {
    const map: Record<string, string> = {
        pending_verification: 'bg-amber-100 text-amber-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
        suspended: 'bg-slate-100 text-slate-600',
    }
    return map[status] ?? 'bg-slate-100 text-slate-600'
}

function statusLabel(status: string) {
    const map: Record<string, string> = {
        pending_verification: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
        suspended: 'Suspended',
    }
    return map[status] ?? status
}
</script>

<template>
    <Head title="Business Verifications — Super Admin" />

    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Business Verifications</h1>
                <p class="text-slate-500 text-sm mt-0.5">Review and approve new business registrations</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs font-medium text-amber-600 uppercase tracking-wide">Pending Review</p>
                <p class="text-3xl font-bold text-amber-700 mt-1">{{ stats.pending }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Approved</p>
                <p class="text-3xl font-bold text-green-700 mt-1">{{ stats.approved }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-xs font-medium text-red-600 uppercase tracking-wide">Rejected</p>
                <p class="text-3xl font-bold text-red-700 mt-1">{{ stats.rejected }}</p>
            </div>
        </div>

        <!-- Pending table -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-700">Pending Verifications</h2>
            </div>

            <div v-if="pending.data.length === 0" class="py-16 text-center text-slate-400">
                <svg class="mx-auto h-12 w-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                No pending verifications
            </div>

            <table v-else class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Business</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Owner</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Location</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Plan Requested</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Submitted</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="tenant in pending.data" :key="tenant.id" class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ tenant.name }}</p>
                            <p class="text-slate-400 text-xs">{{ tenant.email }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-slate-700">{{ tenant.owner?.name ?? '—' }}</p>
                            <p class="text-slate-400 text-xs">{{ tenant.owner?.phone ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-500">
                            <span v-if="tenant.latitude">{{ tenant.address?.city ?? '—' }} · {{ tenant.delivery_radius_km }} km radius</span>
                            <span v-else class="text-slate-300">No GPS</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="bg-indigo-50 text-indigo-700 text-xs rounded px-2 py-0.5">
                                {{ tenant.meta?.selected_plan ?? 'starter' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs whitespace-nowrap">
                            {{ new Date(tenant.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-5 py-3">
                            <Link
                                :href="route('super-admin.verifications.show', tenant.id)"
                                class="text-indigo-600 hover:underline text-xs font-medium"
                            >
                                Review →
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="pending.last_page > 1" class="px-5 py-3 border-t border-slate-100 flex gap-1 justify-end">
                <template v-for="link in pending.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-1 text-sm rounded border"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                        v-html="link.label"
                    />
                    <span v-else class="px-3 py-1 text-sm text-slate-300" v-html="link.label" />
                </template>
            </div>
        </div>

        <!-- Recently actioned -->
        <div v-if="recentlyActioned.length" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-700">Recently Actioned</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Business</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Status</th>
                        <th class="px-5 py-3 text-left font-medium text-slate-500">Actioned</th>
                        <th class="px-5 py-3" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="tenant in recentlyActioned" :key="tenant.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ tenant.name }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs rounded px-2 py-0.5 font-medium" :class="statusClass(tenant.verification_status)">
                                {{ statusLabel(tenant.verification_status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs">
                            {{ tenant.verified_at ? new Date(tenant.verified_at).toLocaleDateString() : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <Link
                                :href="route('super-admin.verifications.show', tenant.id)"
                                class="text-indigo-600 hover:underline text-xs"
                            >
                                View
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
