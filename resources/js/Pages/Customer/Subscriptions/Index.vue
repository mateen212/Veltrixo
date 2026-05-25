<template>
  <CustomerLayout>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">My Subscriptions</h1>
      <p class="text-gray-500 mt-1">Manage your delivery subscriptions</p>
    </div>

    <!-- Empty state -->
    <div v-if="!subscriptions.data?.length" class="text-center py-16">
      <CalendarIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <h3 class="text-lg font-medium text-gray-600">No subscriptions yet</h3>
      <p class="text-gray-400 mt-2">Start a subscription to receive regular deliveries.</p>
        <button @click="showCreate = true" class="btn-primary mt-4 inline-block">New Subscription</button>
    </div>

      <AppModal v-if="showCreate" title="New Subscription" size="md" @close="showCreate = false">
        <form @submit.prevent="createSubscription" class="space-y-4">
          <div>
            <label class="text-sm font-medium text-gray-700">Frequency</label>
            <select v-model="form.frequency" class="input-base w-full">
              <option value="weekly">Weekly</option>
              <option value="biweekly">Bi-weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">Delivery Address</label>
            <input v-model="form.address" type="text" class="input-base w-full" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">Notes (optional)</label>
            <input v-model="form.notes" type="text" class="input-base w-full" />
          </div>
        </form>
        <template #footer>
          <button class="btn-outline" @click="showCreate = false">Cancel</button>
          <button class="btn-primary" @click="createSubscription">Create</button>
        </template>
      </AppModal>

    <!-- Subscription cards -->
    <div v-else class="space-y-4">
      <div v-for="sub in subscriptions.data" :key="sub.id" class="card">
        <div class="flex items-start justify-between mb-4">
          <div>
            <StatusBadge :status="sub.status" class="mb-2" />
            <h3 class="font-semibold text-gray-800 capitalize">{{ sub.frequency }} Delivery</h3>
            <p class="text-sm text-gray-500 mt-1">Next: {{ sub.next_delivery_date ?? '—' }}</p>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-gray-800">Rs {{ sub.total_amount }}</p>
            <p class="text-xs text-gray-400">per delivery</p>
          </div>
        </div>

        <!-- Items -->
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
          <div v-for="item in sub.items" :key="item.id" class="flex justify-between text-sm py-1">
            <span class="text-gray-700">{{ item.product?.name }} × {{ item.quantity }}</span>
            <span class="text-gray-600 font-medium">Rs {{ item.subtotal }}</span>
          </div>
        </div>

        <!-- Address -->
        <p class="text-xs text-gray-400 mb-4 flex items-center gap-1">
          <MapPinIcon class="w-3 h-3" />
          {{ sub.address?.full_address ?? 'No address' }}
        </p>

        <!-- Actions -->
        <div class="flex gap-2 flex-wrap">
          <Link :href="route('customer.subscriptions.show', sub.id)" class="btn-outline text-sm">View</Link>

          <button v-if="sub.status === 'active'" @click="pause(sub.id)" class="btn-outline text-sm text-yellow-600 border-yellow-300">
            Pause
          </button>
          <button v-if="sub.status === 'paused'" @click="resume(sub.id)" class="btn-primary text-sm bg-green-600">
            Resume
          </button>
          <button v-if="sub.status === 'active'" @click="skipNext(sub)" class="btn-outline text-sm">
            Skip Next
          </button>
          <button v-if="['active','paused'].includes(sub.status)" @click="cancel(sub.id)" class="text-sm text-red-500 hover:underline">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import { router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { CalendarIcon, MapPinIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import AppModal from '@/Components/AppModal.vue'
import { useForm } from '@inertiajs/vue3'

defineProps<{ subscriptions: { data: any[] } }>()

const showCreate = ref(false)
const form = useForm({ frequency: 'weekly', address: '', notes: '' })

async function createSubscription() {
  form.post(route('customer.subscriptions.store'), {
    onSuccess: () => { showCreate.value = false; router.reload() },
  })
}

async function pause(id: number) {
  const pauseUntil = prompt('Pause until (YYYY-MM-DD), or leave blank:')
  await axios.post(route('customer.subscriptions.pause', id), { pause_until: pauseUntil || undefined })
  router.reload()
}

async function resume(id: number) {
  await axios.post(route('customer.subscriptions.resume', id))
  router.reload()
}

async function cancel(id: number) {
  const reason = prompt('Reason for cancellation:')
  if (!reason) return
  await axios.post(route('customer.subscriptions.cancel', id), { reason })
  router.reload()
}

async function skipNext(sub: any) {
  const date = sub.next_delivery_date
  if (!date || !confirm(`Skip delivery on ${date}?`)) return
  await axios.post(route('customer.subscriptions.skip', sub.id), { date })
  router.reload()
}
</script>
