<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { ShieldCheckIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

defineProps<{
    verifications: any[]
}>()

const statusMap: Record<string, any> = {
    pending: 'warning', approved: 'success', rejected: 'danger',
}
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

        <div v-if="verifications.length" class="grid gap-4">
            <div v-for="v in verifications" :key="v.id"
                class="card p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-11 w-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <ShieldCheckIcon class="h-5 w-5 text-amber-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-ink">{{ v.business_name ?? v.tenant?.name }}</p>
                    <p class="text-xs text-ink-muted mt-0.5">Submitted {{ v.created_at }}</p>
                </div>
                <AppBadge :variant="statusMap[v.status] ?? 'neutral'" dot size="xs">{{ v.status }}</AppBadge>
                <Link :href="route('super-admin.verifications.show', v.id)"
                    class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-surface-subtle transition-colors text-ink-muted hover:text-ink shrink-0">
                    <ArrowRightIcon class="h-4 w-4" />
                </Link>
            </div>
        </div>
        <div v-else class="card py-16 px-6">
            <EmptyState icon="check" title="All clear!" description="No pending verification requests." />
        </div>
    </SuperAdminLayout>
</template>
