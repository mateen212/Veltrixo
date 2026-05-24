<template>
  <span :class="['inline-flex items-center px-2 py-0.5 rounded-full font-medium', classes]">
    {{ label }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ status: string; size?: 'sm' | 'md' }>()

const config: Record<string, { classes: string; label: string }> = {
  active:      { classes: 'bg-green-100 text-green-700',   label: 'Active' },
  paused:      { classes: 'bg-yellow-100 text-yellow-700', label: 'Paused' },
  cancelled:   { classes: 'bg-red-100 text-red-600',       label: 'Cancelled' },
  delivered:   { classes: 'bg-green-100 text-green-700',   label: 'Delivered' },
  assigned:    { classes: 'bg-blue-100 text-blue-700',     label: 'Assigned' },
  scheduled:   { classes: 'bg-gray-100 text-gray-600',     label: 'Scheduled' },
  in_progress: { classes: 'bg-indigo-100 text-indigo-700', label: 'In Progress' },
  missed:      { classes: 'bg-red-100 text-red-600',       label: 'Missed' },
  pending:     { classes: 'bg-yellow-50 text-yellow-600',  label: 'Pending' },
  expired:     { classes: 'bg-gray-100 text-gray-500',     label: 'Expired' },
  paid:        { classes: 'bg-green-100 text-green-700',   label: 'Paid' },
  overdue:     { classes: 'bg-red-100 text-red-600',       label: 'Overdue' },
  draft:       { classes: 'bg-gray-100 text-gray-500',     label: 'Draft' },
}

const label   = computed(() => config[props.status]?.label ?? props.status)
const classes = computed(() => [
  config[props.status]?.classes ?? 'bg-gray-100 text-gray-600',
  props.size === 'sm' ? 'text-xs' : 'text-sm',
])
</script>
