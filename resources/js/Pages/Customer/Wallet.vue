<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppBadge from '@/Components/AppBadge.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { WalletIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/outline'

defineProps<{
    wallet:       any
    transactions: any[]
}>()

function txType(type: string) {
    return type === 'credit' ? 'success' : 'danger'
}
</script>

<template>
    <Head title="My Wallet" />
    <CustomerLayout title="Wallet">
        <!-- Balance card -->
        <div class="rounded-2xl p-6 mb-6 text-white"
            style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); box-shadow: 0 8px 32px -4px rgba(79,70,229,0.35);">
            <div class="flex items-center gap-3 mb-5">
                <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                    <WalletIcon class="h-5 w-5 text-white/80" />
                </div>
                <div>
                    <p class="text-xs text-white/60 uppercase tracking-wider">Available Balance</p>
                    <p class="text-3xl font-bold font-display mt-0.5">{{ wallet?.balance }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white/10 px-4 py-3">
                    <p class="text-xs text-white/50">Total Recharged</p>
                    <p class="text-base font-semibold mt-0.5">{{ wallet?.total_credit ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3">
                    <p class="text-xs text-white/50">Total Spent</p>
                    <p class="text-base font-semibold mt-0.5">{{ wallet?.total_debit ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-border-muted">
                <h2 class="text-sm font-semibold text-ink">Transaction History</h2>
            </div>
            <div v-if="transactions.length" class="divide-y divide-border-muted">
                <div v-for="tx in transactions" :key="tx.id"
                    class="flex items-center gap-3 px-6 py-3.5 hover:bg-surface-subtle transition-colors">
                    <div :class="['h-9 w-9 rounded-xl flex items-center justify-center shrink-0',
                        tx.type === 'credit' ? 'bg-emerald-50' : 'bg-red-50']">
                        <component :is="tx.type === 'credit' ? ArrowUpCircleIcon : ArrowDownCircleIcon"
                            :class="['h-5 w-5', tx.type === 'credit' ? 'text-emerald-600' : 'text-red-500']" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink">{{ tx.description ?? tx.type }}</p>
                        <p class="text-xs text-ink-faint">{{ tx.created_at }}</p>
                    </div>
                    <span :class="['text-sm font-semibold', tx.type === 'credit' ? 'text-emerald-600' : 'text-red-500']">
                        {{ tx.type === 'credit' ? '+' : '-' }}{{ tx.amount }}
                    </span>
                </div>
            </div>
            <div v-else class="py-14 px-6">
                <EmptyState icon="wallet" title="No transactions yet" description="Your transaction history will appear here." />
            </div>
        </div>
    </CustomerLayout>
</template>
