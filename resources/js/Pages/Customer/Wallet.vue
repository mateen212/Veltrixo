<template>
  <CustomerLayout>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">My Wallet</h1>
    </div>

    <!-- Balance card -->
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white mb-6">
      <p class="text-indigo-200 text-sm mb-1">Available Balance</p>
      <p class="text-4xl font-bold">₹{{ wallet.balance.toFixed(2) }}</p>
      <div class="flex gap-6 mt-4 text-sm text-indigo-200">
        <span>↑ Credited: ₹{{ wallet.total_credited.toFixed(2) }}</span>
        <span>↓ Debited: ₹{{ wallet.total_debited.toFixed(2) }}</span>
      </div>
      <div v-if="wallet.is_below_threshold" class="mt-3 bg-yellow-400/20 rounded-lg px-3 py-2 text-yellow-200 text-xs">
        ⚠️ Low balance — recharge to avoid service interruption
      </div>
    </div>

    <!-- Recharge button -->
    <button @click="showRecharge = true" class="w-full btn-primary mb-6">+ Recharge Wallet</button>

    <!-- Recharge modal -->
    <Modal v-if="showRecharge" @close="showRecharge = false" title="Recharge Wallet">
      <form @submit.prevent="submitRecharge" class="space-y-4">
        <div>
          <label class="label">Amount (₹)</label>
          <input v-model.number="form.amount" type="number" min="1" class="input-base w-full" required />
        </div>
        <div>
          <label class="label">Payment Method</label>
          <select v-model="form.payment_method" class="input-base w-full" required>
            <option value="upi">UPI</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
          </select>
        </div>
        <div>
          <label class="label">Reference / UTR (optional)</label>
          <input v-model="form.payment_reference" type="text" class="input-base w-full" />
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" @click="showRecharge = false" class="btn-outline">Cancel</button>
          <button type="submit" :disabled="submitting" class="btn-primary">
            {{ submitting ? 'Submitting...' : 'Submit' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Transactions -->
    <div class="card">
      <h2 class="card-title mb-4">Transaction History</h2>
      <div v-if="!transactions.data?.length" class="text-center text-gray-400 py-8">No transactions yet</div>
      <div v-else class="divide-y">
        <div v-for="tx in transactions.data" :key="tx.id" class="flex items-center justify-between py-3">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ tx.description }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ new Date(tx.created_at).toLocaleString() }}</p>
          </div>
          <div :class="['text-base font-semibold', tx.type === 'credit' ? 'text-green-600' : 'text-red-500']">
            {{ tx.type === 'credit' ? '+' : '-' }}₹{{ tx.amount }}
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import Modal from '@/Components/Modal.vue'

defineProps<{
  wallet: { balance: number; total_credited: number; total_debited: number; status: string; is_below_threshold: boolean }
  transactions: { data: any[] }
  recharges: any[]
}>()

const showRecharge = ref(false)
const submitting = ref(false)
const form = ref({ amount: 0, payment_method: 'upi', payment_reference: '' })

async function submitRecharge() {
  submitting.value = true
  try {
    await axios.post(route('customer.wallet.recharge'), form.value)
    showRecharge.value = false
    alert('Recharge request submitted. Admin will approve shortly.')
    router.reload()
  } finally {
    submitting.value = false
  }
}
</script>
