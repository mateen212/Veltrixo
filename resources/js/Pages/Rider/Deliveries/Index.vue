<template>
  <RiderLayout>
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-white">Today's Deliveries</h2>
      <input type="date" v-model="selectedDate" @change="reload" class="text-sm bg-slate-700 text-white rounded-lg px-3 py-1.5 border border-slate-600" />
    </div>

    <!-- Summary bar -->
    <div class="grid grid-cols-3 gap-3 mb-5">
      <div class="bg-slate-700 rounded-xl p-3 text-center">
        <div class="text-xl font-bold text-white">{{ deliveries.data?.length ?? 0 }}</div>
        <div class="text-xs text-slate-400">Total</div>
      </div>
      <div class="bg-green-900/50 rounded-xl p-3 text-center">
        <div class="text-xl font-bold text-green-400">{{ delivered }}</div>
        <div class="text-xs text-slate-400">Done</div>
      </div>
      <div class="bg-yellow-900/50 rounded-xl p-3 text-center">
        <div class="text-xl font-bold text-yellow-400">{{ pending }}</div>
        <div class="text-xs text-slate-400">Pending</div>
      </div>
    </div>

    <!-- Delivery cards -->
    <div class="space-y-3">
      <div v-for="d in deliveries.data" :key="d.id" class="bg-white rounded-2xl overflow-hidden shadow">
        <!-- Status indicator -->
        <div :class="['h-1.5', statusColor(d.status)]" />

        <div class="p-4">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="font-semibold text-gray-800">{{ d.user?.name }}</p>
              <p class="text-xs text-gray-400">{{ d.user?.phone }}</p>
            </div>
            <StatusBadge :status="d.status" size="sm" />
          </div>

          <p class="text-sm text-gray-600 mb-3 flex items-start gap-1">
            <MapPinIcon class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" />
            {{ d.address?.full_address }}
          </p>

          <!-- Items summary -->
          <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-600 mb-3">
            {{ d.items?.map((i: any) => `${i.product_name} ×${i.quantity}`).join(', ') }}
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <button v-if="d.status === 'assigned'" @click="startDelivery(d.id)" class="flex-1 bg-blue-600 text-white text-sm font-medium py-2 rounded-lg">
              Start
            </button>
            <button v-if="d.status === 'in_progress'" @click="openComplete(d)" class="flex-1 bg-green-600 text-white text-sm font-medium py-2 rounded-lg">
              Complete
            </button>
            <a :href="`tel:${d.user?.phone}`" v-if="d.user?.phone" class="px-4 py-2 border rounded-lg text-sm text-gray-600">
              📞 Call
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Complete delivery modal -->
    <Modal v-if="completing" @close="completing = null" title="Complete Delivery">
      <form @submit.prevent="submitComplete" class="space-y-4">
        <div>
          <label class="label">OTP (from customer)</label>
          <input v-model="completeForm.otp" type="text" maxlength="6" class="input-base w-full text-center text-2xl tracking-widest" placeholder="______" />
        </div>
        <div>
          <label class="label">Notes (optional)</label>
          <textarea v-model="completeForm.rider_notes" rows="2" class="input-base w-full" />
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" @click="completing = null" class="btn-outline">Cancel</button>
          <button type="submit" class="btn-primary bg-green-600">Mark Delivered</button>
        </div>
      </form>
    </Modal>
  </RiderLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import RiderLayout from '@/Layouts/RiderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import Modal from '@/Components/Modal.vue'
import { MapPinIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{ deliveries: { data: any[] }; date: string }>()

const selectedDate = ref(props.date)
const completing = ref<any>(null)
const completeForm = ref({ otp: '', rider_notes: '' })

const delivered = computed(() => props.deliveries.data?.filter(d => d.status === 'delivered').length ?? 0)
const pending = computed(() => props.deliveries.data?.filter(d => ['assigned', 'in_progress'].includes(d.status)).length ?? 0)

function statusColor(status: string): string {
  return { delivered: 'bg-green-500', in_progress: 'bg-blue-500', assigned: 'bg-yellow-400', missed: 'bg-red-500' }[status] ?? 'bg-gray-200'
}

function reload() {
  router.get(route('rider.deliveries.index'), { date: selectedDate.value }, { preserveState: true })
}

async function startDelivery(id: number) {
  await axios.post(route('rider.deliveries.start', id))
  reload()
}

function openComplete(d: any) {
  completing.value = d
  completeForm.value = { otp: '', rider_notes: '' }
}

async function submitComplete() {
  await axios.post(route('rider.deliveries.complete', completing.value.id), completeForm.value)
  completing.value = null
  reload()
}
</script>
