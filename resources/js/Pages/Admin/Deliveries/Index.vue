<template>
  <AdminLayout title="Deliveries">
    <template #header-actions>
      <input type="date" v-model="selectedDate" @change="reload" class="input-base text-sm" />
      <button @click="generate" :disabled="generating" class="btn-primary text-sm">
        {{ generating ? 'Generating...' : '+ Generate' }}
      </button>
    </template>

    <!-- Stats bar -->
    <div class="grid grid-cols-4 gap-3 mb-6">
      <div v-for="s in statusStats" :key="s.label" class="bg-white rounded-lg border p-4 text-center">
        <div :class="['text-2xl font-bold', s.color]">{{ s.value }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ s.label }}</div>
      </div>
    </div>

    <!-- Bulk assign bar -->
    <div v-if="selected.length" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4 flex items-center gap-4">
      <span class="text-sm text-indigo-700 font-medium">{{ selected.length }} selected</span>
      <select v-model="bulkRiderId" class="input-base text-sm flex-1 max-w-xs">
        <option value="">Select rider...</option>
        <option v-for="r in riders" :key="r.id" :value="r.id">{{ r.user?.name ?? r.id }}</option>
      </select>
      <button @click="bulkAssign" :disabled="!bulkRiderId" class="btn-primary text-sm">Assign</button>
      <button @click="selected = []" class="btn-outline text-sm">Clear</button>
    </div>

    <!-- Delivery table -->
    <div class="card overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="px-4 py-3 text-left">
              <input type="checkbox" @change="toggleAll" :checked="selected.length === deliveries.data?.length" />
            </th>
            <th class="px-4 py-3 text-left">Customer</th>
            <th class="px-4 py-3 text-left">Address</th>
            <th class="px-4 py-3 text-left">Rider</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Amount</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="d in deliveries.data" :key="d.id" class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <input type="checkbox" :value="d.id" v-model="selected" />
            </td>
            <td class="px-4 py-3">
              <div class="font-medium text-gray-800">{{ d.user?.name }}</div>
              <div class="text-xs text-gray-400">{{ d.user?.phone }}</div>
            </td>
            <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">{{ d.address?.full_address }}</td>
            <td class="px-4 py-3">
              <span v-if="d.rider" class="text-gray-700">{{ d.rider.user?.name }}</span>
              <span v-else class="text-gray-400 italic">Unassigned</span>
            </td>
            <td class="px-4 py-3">
              <StatusBadge :status="d.status" />
            </td>
            <td class="px-4 py-3 font-medium">₹{{ d.total_amount }}</td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <button v-if="!d.rider" @click="openAssignModal(d)" class="text-xs btn-outline">Assign</button>
                <button v-if="d.status !== 'missed' && d.status !== 'delivered'" @click="markMissed(d)" class="text-xs text-red-500 hover:underline">Missed</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Assign Modal -->
    <Modal v-if="assignTarget" @close="assignTarget = null" title="Assign Rider">
      <div class="space-y-4">
        <p class="text-sm text-gray-600">Assign a rider to delivery #{{ assignTarget.id }}</p>
        <select v-model="assignRiderId" class="input-base w-full">
          <option value="">Select rider...</option>
          <option v-for="r in riders" :key="r.id" :value="r.id">{{ r.user?.name }}</option>
        </select>
        <div class="flex gap-3 justify-end">
          <button @click="assignTarget = null" class="btn-outline">Cancel</button>
          <button @click="assignRider" :disabled="!assignRiderId" class="btn-primary">Assign</button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps<{
  deliveries: { data: any[]; meta?: any }
  riders: any[]
  date: string
  stats: { total: number; delivered: number; pending: number; missed: number }
}>()

const selectedDate = ref(props.date)
const selected = ref<number[]>([])
const bulkRiderId = ref('')
const assignTarget = ref<any>(null)
const assignRiderId = ref('')
const generating = ref(false)

const statusStats = computed(() => [
  { label: 'Total', value: props.stats.total, color: 'text-gray-800' },
  { label: 'Delivered', value: props.stats.delivered, color: 'text-green-600' },
  { label: 'Pending', value: props.stats.pending, color: 'text-yellow-500' },
  { label: 'Missed', value: props.stats.missed, color: 'text-red-500' },
])

function reload() {
  router.get(route('admin.deliveries.index'), { date: selectedDate.value }, { preserveState: true })
}

function toggleAll(e: Event) {
  const checked = (e.target as HTMLInputElement).checked
  selected.value = checked ? props.deliveries.data.map(d => d.id) : []
}

function openAssignModal(d: any) {
  assignTarget.value = d
  assignRiderId.value = ''
}

async function assignRider() {
  await axios.post(route('admin.deliveries.assign', assignTarget.value.id), { rider_id: assignRiderId.value })
  assignTarget.value = null
  reload()
}

async function bulkAssign() {
  await axios.post(route('admin.deliveries.bulk-assign'), { delivery_ids: selected.value, rider_id: bulkRiderId.value })
  selected.value = []
  bulkRiderId.value = ''
  reload()
}

async function markMissed(d: any) {
  const reason = prompt('Reason for missing:')
  if (!reason) return
  await axios.post(route('admin.deliveries.missed', d.id), { reason })
  reload()
}

async function generate() {
  generating.value = true
  try {
    const { data } = await axios.post(route('admin.deliveries.generate'), { date: selectedDate.value })
    alert(data.message)
    reload()
  } finally {
    generating.value = false
  }
}
</script>
