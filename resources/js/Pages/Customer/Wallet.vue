<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import AppModal from '@/Components/AppModal.vue'
import TablePagination from '@/Components/TablePagination.vue'
import {
    BanknotesIcon, ArrowUpCircleIcon, ArrowDownCircleIcon, PlusCircleIcon,
} from '@heroicons/vue/24/outline'

interface Wallet {
    balance: number
    total_credited: number
    total_debited: number
    status: string
    is_below_threshold: boolean
}
interface Transaction {
    id: number; type: 'credit' | 'debit'; amount: number
    description: string; created_at: string
}

const props = defineProps<{
    wallet?: Wallet | null
    transactions?: { data: Transaction[]; meta?: any; links?: any[] } | null
    recharges?: any[]
}>()

const fmt = (n: number | undefined | null) => (+(n ?? 0)).toFixed(2)

const showRecharge = ref(false)
const form = useForm({ amount: 500, payment_method: 'upi', payment_reference: '' })

function submitRecharge() {
    form.post(route('customer.wallet.recharge'), {
        onSuccess: () => { showRecharge.value = false },
    })
}
</script>

<template>
    <Head title="My Wallet" />
    <CustomerLayout title="My Wallet">
        <!-- Balance Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700 p-6 text-white shadow-xl mb-5">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/5" />
            <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/5" />
            <div class="relative">
                <div class="flex items-center gap-2 mb-1">
                    <BanknotesIcon class="h-4 w-4 text-indigo-200" />
                    <p class="text-sm text-indigo-200 font-medium">Available Balance</p>
                </div>
                <p class="text-4xl font-bold tracking-tight">₹{{ fmt(wallet?.balance) }}</p>
                <div v-if="wallet?.is_below_threshold" class="mt-3 inline-flex items-center gap-1.5 bg-yellow-400/20 rounded-lg px-3 py-1.5 text-xs text-yellow-200 font-medium">
                    ⚠ Low balance — recharge to avoid interruption
                </div>
                <div class="flex gap-6 mt-4 pt-4 border-t border-white/10 text-sm text-indigo-200">
                    <div class="flex items-center gap-1.5">
                        <ArrowUpCircleIcon class="h-4 w-4 text-emerald-300" />
                        <span>Credited: ₹{{ fmt(wallet?.total_credited) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <ArrowDownCircleIcon class="h-4 w-4 text-red-300" />
                        <span>Debited: ₹{{ fmt(wallet?.total_debited) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recharge button -->
        <div class="mb-5">
            <AppButton @click="showRecharge = true">
                <PlusCircleIcon class="h-4 w-4" />Recharge Wallet
            </AppButton>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Transaction History</h2>
            </div>
            <div v-if="!transactions?.data?.length" class="py-12 text-center">
                <BanknotesIcon class="h-8 w-8 text-gray-300 mx-auto mb-2" />
                <p class="text-sm text-gray-400">No transactions yet</p>
            </div>
            <div v-else class="divide-y divide-gray-50">
                <div v-for="tx in transactions!.data" :key="tx.id"
                    class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ tx.description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ new Date(tx.created_at).toLocaleString() }}</p>
                    </div>
                    <span :class="['text-sm font-semibold', tx.type === 'credit' ? 'text-emerald-600' : 'text-red-500']">
                        {{ tx.type === 'credit' ? '+' : '-' }}₹{{ tx.amount }}
                    </span>
                </div>
            </div>
            <TablePagination v-if="transactions" :meta="transactions.meta" :links="transactions.links" />
        </div>

        <!-- Recharge Modal -->
        <AppModal v-if="showRecharge" title="Recharge Wallet" @close="showRecharge = false">
            <form @submit.prevent="submitRecharge" class="space-y-4">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Amount (₹)</label>
                    <input v-model.number="form.amount" type="number" min="1"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" required />
                    <p v-if="form.errors.amount" class="text-xs text-red-600">{{ form.errors.amount }}</p>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Payment Method</label>
                    <select v-model="form.payment_method"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40">
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Reference / UTR <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input v-model="form.payment_reference" type="text"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/40" />
                </div>
            </form>
            <template #footer>
                <AppButton variant="outline" @click="showRecharge = false">Cancel</AppButton>
                <AppButton :loading="form.processing" @click="submitRecharge">Submit Request</AppButton>
            </template>
        </AppModal>
    </CustomerLayout>
</template>

