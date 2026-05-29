<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { CalendarDaysIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

defineProps<{
    subscriptions: any[]
}>()

const statusMap: Record<string, any> = {
    active: 'success', paused: 'warning', cancelled: 'danger', pending: 'neutral',
}
</script>

<template>
    <Head title="My Subscriptions" />
    <CustomerLayout title="Subscriptions">
        <div class="page-header mb-6">
            <div>
                <h1 class="page-title">My Subscriptions</h1>
                <p class="page-subtitle">Your active delivery plans</p>
            </div>
        </div>

        <div v-if="subscriptions.length" class="grid gap-4">
            <div v-for="sub in subscriptions" :key="sub.id"
                class="card p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-11 w-11 rounded-xl bg-brand-50 flex items-center justify-center shrink-0">
                    <CalendarDaysIcon class="h-5 w-5 text-brand-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-ink">{{ sub.plan?.name ?? 'Subscription #' + sub.id }}</p>
                    <p class="text-xs text-ink-muted mt-0.5">Since {{ sub.start_date }} · {{ sub.frequency ?? 'Daily' }}</p>
                </div>
                <AppBadge :variant="statusMap[sub.status] ?? 'neutral'" dot size="xs">{{ sub.status }}</AppBadge>
                <Link :href="route('customer.subscriptions.show', sub.id)"
                    class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-surface-subtle transition-colors text-ink-muted hover:text-ink shrink-0">
                    <ArrowRightIcon class="h-4 w-4" />
                </Link>
            </div>
        </div>
        <div v-else class="card py-16 px-6">
            <EmptyState icon="calendar" title="No subscriptions yet" description="Subscribe to a delivery plan to get started." />
        </div>
    </CustomerLayout>
</template>
