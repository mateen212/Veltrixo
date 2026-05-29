<template>
    <div :class="[
        'card p-5 flex items-start gap-4 group transition-all duration-200 hover:shadow-md hover:-translate-y-0.5',
        'animate-fade-up',
    ]">
        <div :class="['flex h-11 w-11 items-center justify-center rounded-xl shrink-0 transition-transform duration-200 group-hover:scale-110', iconBg]">
            <component :is="iconComponent" class="w-5 h-5 text-white" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider truncate">{{ title }}</p>
            <p :class="['text-2xl font-bold mt-0.5 tabular-nums', valueColor]">{{ value }}</p>
            <p v-if="trend !== undefined" class="mt-1 text-xs font-medium"
                :class="trend >= 0 ? 'text-emerald-600' : 'text-red-500'">
                <span>{{ trend >= 0 ? '↑' : '↓' }} {{ Math.abs(trend) }}%</span>
                <span class="font-normal text-ink-faint ml-1">vs last period</span>
            </p>
        </div>
        <div v-if="badge" class="shrink-0">
            <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold', badgeCls]">
                {{ badge }}
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
    CurrencyRupeeIcon, CalendarIcon, TruckIcon, ClockIcon,
    UserGroupIcon, ChartBarIcon, WalletIcon, CheckCircleIcon,
    ArrowTrendingUpIcon, BoltIcon, StarIcon, BuildingOfficeIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps<{
    title: string
    value: string | number
    icon: string
    color?: string
    trend?: number
    badge?: string
    badgeVariant?: 'success' | 'warning' | 'danger' | 'info'
}>()

const colorMap: Record<string, { bg: string; text: string }> = {
    green:   { bg: 'bg-emerald-500', text: 'text-emerald-600' },
    blue:    { bg: 'bg-blue-500',    text: 'text-blue-600' },
    indigo:  { bg: 'bg-brand-600',   text: 'text-brand-600' },
    yellow:  { bg: 'bg-amber-500',   text: 'text-amber-600' },
    red:     { bg: 'bg-red-500',     text: 'text-red-600' },
    purple:  { bg: 'bg-purple-500',  text: 'text-purple-600' },
    pink:    { bg: 'bg-pink-500',    text: 'text-pink-600' },
    orange:  { bg: 'bg-orange-500',  text: 'text-orange-600' },
}

const iconMap: Record<string, any> = {
    currency: CurrencyRupeeIcon, calendar: CalendarIcon,
    truck: TruckIcon, clock: ClockIcon, users: UserGroupIcon,
    chart: ChartBarIcon, wallet: WalletIcon, check: CheckCircleIcon,
    trend: ArrowTrendingUpIcon, bolt: BoltIcon, star: StarIcon,
    building: BuildingOfficeIcon,
}

const c = computed(() => colorMap[props.color ?? 'indigo'] ?? colorMap.indigo)
const iconBg     = computed(() => c.value.bg)
const valueColor = computed(() => 'text-ink')
const iconComponent = computed(() => iconMap[props.icon] ?? BoltIcon)

const badgeClsMap: Record<string, string> = {
    success: 'bg-emerald-50 text-emerald-700',
    warning: 'bg-amber-50 text-amber-700',
    danger:  'bg-red-50 text-red-700',
    info:    'bg-blue-50 text-blue-700',
}
const badgeCls = computed(() => badgeClsMap[props.badgeVariant ?? 'info'] ?? badgeClsMap.info)
</script>
