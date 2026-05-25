<template>
  <AdminLayout title="Dashboard">
    <!-- Date range filter -->
    <div class="flex gap-3 mb-6">
      <input type="date" v-model="from" class="input-base" />
      <input type="date" v-model="to" class="input-base" />
      <button @click="reload" class="btn-primary">Refresh</button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <KpiCard title="Revenue" :value="formatCurrency(metrics.revenue?.total ?? 0)" icon="currency" color="green" />
      <KpiCard title="Active Subscriptions" :value="metrics.active_subscriptions?.active ?? 0" icon="calendar" color="blue" />
      <KpiCard title="Delivery Success" :value="`${metrics.delivery_success_rate?.rate ?? 0}%`" icon="truck" color="indigo" />
      <KpiCard title="Pending Today" :value="metrics.pending_deliveries ?? 0" icon="clock" color="yellow" />
    </div>

    <!-- Secondary metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
      <StatCard title="Total Deliveries" :value="metrics.delivery_success_rate?.total ?? 0" :sub="`${metrics.delivery_success_rate?.delivered ?? 0} delivered, ${metrics.delivery_success_rate?.missed ?? 0} missed`" />
      <StatCard title="New Customers" :value="metrics.new_customers ?? 0" sub="This period" />
      <StatCard title="Wallet Balance (Total)" :value="formatCurrency(metrics.wallet_balance_total ?? 0)" sub="Across all wallets" />
    </div>

    <!-- Rider performance table -->
    <div class="card">
      <h2 class="card-title mb-4">Rider Performance</h2>
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="thead">
            <tr>
              <th>Rider</th>
              <th>Total</th>
              <th>Delivered</th>
              <th>Missed</th>
              <th>Rating</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in metrics.rider_performance" :key="r.id" class="border-b last:border-0">
              <td class="py-3 px-4">{{ r.name }}</td>
              <td class="py-3 px-4">{{ r.total }}</td>
              <td class="py-3 px-4 text-green-600 font-medium">{{ r.delivered }}</td>
              <td class="py-3 px-4 text-red-500">{{ r.missed }}</td>
              <td class="py-3 px-4">{{ Number(r.rating).toFixed(1) }} ⭐</td>
            </tr>
            <tr v-if="!metrics.rider_performance?.length">
              <td colspan="5" class="py-6 text-center text-gray-400">No data for this period</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import KpiCard from '@/Components/KpiCard.vue'
import StatCard from '@/Components/StatCard.vue'

const props = defineProps<{
  metrics: Record<string, any>
  from: string
  to: string
}>()

const from = ref(props.from)
const to   = ref(props.to)

function formatCurrency(value: number) {
  return 'Rs ' + new Intl.NumberFormat('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value)
}

function reload() {
  router.get(route('admin.dashboard'), { from: from.value, to: to.value }, { preserveState: true })
}
</script>
