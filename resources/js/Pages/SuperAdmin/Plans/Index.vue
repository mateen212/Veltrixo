<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { CheckIcon } from '@heroicons/vue/24/outline'

defineProps<{
    plans: any[]
}>()
</script>

<template>
    <Head title="Plans — Super Admin" />
    <SuperAdminLayout title="Plans">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">Subscription Plans</h1>
                <p class="page-subtitle">Manage platform pricing plans</p>
            </div>
        </div>

        <div v-if="plans.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="plan in plans" :key="plan.id"
                class="card p-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-ink font-display">{{ plan.name }}</h3>
                        <p class="text-xs text-ink-muted mt-0.5">{{ plan.description }}</p>
                    </div>
                    <AppBadge :variant="plan.is_active ? 'success' : 'neutral'" dot size="xs">
                        {{ plan.is_active ? 'Active' : 'Inactive' }}
                    </AppBadge>
                </div>
                <p class="text-3xl font-bold text-ink font-display mb-4">
                    {{ plan.price }}<span class="text-sm font-normal text-ink-muted">/mo</span>
                </p>
                <ul class="space-y-2">
                    <li v-for="feat in (plan.features ?? [])" :key="feat" class="flex items-center gap-2 text-sm text-ink-secondary">
                        <CheckIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                        {{ feat }}
                    </li>
                </ul>
            </div>
        </div>
        <div v-else class="card py-16 px-6">
            <EmptyState icon="currency" title="No plans yet" description="Create subscription plans for tenants." />
        </div>
    </SuperAdminLayout>
</template>
