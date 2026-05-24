import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

interface WalletData {
  id: number
  balance: number
  total_credited: number
  total_debited: number
  status: string
  low_balance_threshold: number
  is_below_threshold: boolean
}

interface Transaction {
  id: number
  uuid: string
  type: 'credit' | 'debit'
  category: string
  amount: number
  balance_after: number
  description: string
  status: string
  created_at: string
}

export const useWalletStore = defineStore('wallet', () => {
  const wallet = ref<WalletData | null>(null)
  const transactions = ref<Transaction[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchWallet() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(route('customer.wallet'))
      wallet.value = data.wallet
      transactions.value = data.transactions?.data ?? []
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Failed to load wallet'
    } finally {
      loading.value = false
    }
  }

  async function submitRecharge(payload: {
    amount: number
    payment_method: string
    payment_reference?: string
    notes?: string
  }) {
    const formData = new FormData()
    Object.entries(payload).forEach(([k, v]) => v && formData.append(k, String(v)))
    const { data } = await axios.post(route('customer.wallet.recharge'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data
  }

  return { wallet, transactions, loading, error, fetchWallet, submitRecharge }
})
