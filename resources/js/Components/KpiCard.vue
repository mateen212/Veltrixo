<template>
  <div class="bg-white rounded-xl border p-5 flex items-center gap-4">
    <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', iconBg]">
      <component :is="iconComponent" class="w-6 h-6 text-white" />
    </div>
    <div>
      <p class="text-sm text-gray-500">{{ title }}</p>
      <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ value }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  CurrencyRupeeIcon, CalendarIcon, TruckIcon, ClockIcon
} from '@heroicons/vue/24/solid'

const props = defineProps<{ title: string; value: string | number; icon: string; color: string }>()

const colorMap: Record<string, string> = {
  green: 'bg-green-500', blue: 'bg-blue-500', indigo: 'bg-indigo-500',
  yellow: 'bg-yellow-500', red: 'bg-red-500', purple: 'bg-purple-500',
}

const iconMap: Record<string, any> = {
  currency: CurrencyRupeeIcon, calendar: CalendarIcon,
  truck: TruckIcon, clock: ClockIcon,
}

const iconBg = computed(() => colorMap[props.color] ?? 'bg-gray-500')
const iconComponent = computed(() => iconMap[props.icon] ?? ClockIcon)
</script>
