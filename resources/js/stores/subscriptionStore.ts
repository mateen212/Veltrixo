import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

interface Subscription {
  id: number
  uuid: string
  frequency: string
  delivery_days: number[]
  preferred_delivery_time: string | null
  starts_at: string
  ends_at: string | null
  next_delivery_date: string | null
  status: string
  total_amount: number
  items: SubscriptionItem[]
  address: Address | null
}

interface SubscriptionItem {
  id: number
  product_id: number
  variant_id: number | null
  quantity: number
  unit_price: number
  subtotal: number
  product: { id: number; name: string; unit: string } | null
}

interface Address {
  id: number
  label: string
  line1: string
  city: string
  full_address: string
}

export const useSubscriptionStore = defineStore('subscription', () => {
  const subscriptions = ref<Subscription[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const activeSubscriptions = computed(() =>
    subscriptions.value.filter(s => s.status === 'active')
  )

  const pausedSubscriptions = computed(() =>
    subscriptions.value.filter(s => s.status === 'paused')
  )

  async function fetchSubscriptions() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(route('customer.subscriptions.index'))
      subscriptions.value = data.data ?? data
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Failed to load subscriptions'
    } finally {
      loading.value = false
    }
  }

  async function pauseSubscription(id: number, pauseUntil?: string) {
    const { data } = await axios.post(route('customer.subscriptions.pause', id), { pause_until: pauseUntil })
    const idx = subscriptions.value.findIndex(s => s.id === id)
    if (idx !== -1) subscriptions.value[idx] = data.data
    return data.data
  }

  async function resumeSubscription(id: number) {
    const { data } = await axios.post(route('customer.subscriptions.resume', id))
    const idx = subscriptions.value.findIndex(s => s.id === id)
    if (idx !== -1) subscriptions.value[idx] = data.data
    return data.data
  }

  async function cancelSubscription(id: number, reason: string) {
    const { data } = await axios.post(route('customer.subscriptions.cancel', id), { reason })
    const idx = subscriptions.value.findIndex(s => s.id === id)
    if (idx !== -1) subscriptions.value[idx] = data.data
    return data.data
  }

  async function skipDate(id: number, date: string, reason = '') {
    const { data } = await axios.post(route('customer.subscriptions.skip', id), { date, reason })
    return data.data
  }

  return {
    subscriptions, loading, error,
    activeSubscriptions, pausedSubscriptions,
    fetchSubscriptions, pauseSubscription, resumeSubscription,
    cancelSubscription, skipDate,
  }
})
