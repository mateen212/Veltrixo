import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

interface Delivery {
  id: number
  uuid: string
  delivery_date: string
  scheduled_time: string | null
  status: string
  total_amount: number
  is_paid: boolean
  otp_verified: boolean
  delivered_at: string | null
  user: { id: number; name: string; phone: string } | null
  rider: { id: number; user: { name: string } } | null
  address: { full_address: string } | null
  items: DeliveryItem[]
}

interface DeliveryItem {
  id: number
  product_name: string
  quantity: number
  unit_price: number
  subtotal: number
  is_delivered: boolean
}

export const useDeliveryStore = defineStore('delivery', () => {
  const deliveries = ref<Delivery[]>([])
  const selectedDate = ref(new Date().toISOString().split('T')[0])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchDeliveries(date?: string) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(route('rider.deliveries.index'), {
        params: { date: date ?? selectedDate.value },
      })
      deliveries.value = data.data ?? data
    } catch (e: any) {
      error.value = 'Failed to load deliveries'
    } finally {
      loading.value = false
    }
  }

  async function startDelivery(id: number) {
    const { data } = await axios.post(route('rider.deliveries.start', id))
    const idx = deliveries.value.findIndex(d => d.id === id)
    if (idx !== -1) deliveries.value[idx] = data.data
    return data.data
  }

  async function completeDelivery(id: number, payload: {
    otp?: string
    latitude?: number
    longitude?: number
    rider_notes?: string
  }) {
    const { data } = await axios.post(route('rider.deliveries.complete', id), payload)
    const idx = deliveries.value.findIndex(d => d.id === id)
    if (idx !== -1) deliveries.value[idx] = data.data
    return data.data
  }

  async function updateLocation(latitude: number, longitude: number) {
    await axios.post(route('rider.location'), { latitude, longitude })
  }

  return { deliveries, selectedDate, loading, error, fetchDeliveries, startDelivery, completeDelivery, updateLocation }
})
