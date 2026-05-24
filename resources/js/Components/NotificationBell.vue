<template>
  <div class="relative" ref="container">
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700">
      <BellIcon class="w-6 h-6" />
      <span v-if="unreadCount" class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div v-if="open" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-xl border z-50 overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h4 class="font-semibold text-gray-800 text-sm">Notifications</h4>
        <button @click="markAllRead" class="text-xs text-indigo-600 hover:underline">Mark all read</button>
      </div>
      <div class="max-h-80 overflow-y-auto divide-y">
        <div v-for="n in notifications" :key="n.id" :class="['px-4 py-3', n.read_at ? 'bg-white' : 'bg-indigo-50']">
          <p class="text-sm text-gray-700">{{ n.data?.message ?? 'Notification' }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ formatDate(n.created_at) }}</p>
        </div>
        <div v-if="!notifications.length" class="px-4 py-6 text-center text-sm text-gray-400">
          No notifications
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { BellIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const open = ref(false)
const notifications = ref<any[]>([])
const container = ref<HTMLElement | null>(null)

const unreadCount = computed(() => notifications.value.filter(n => !n.read_at).length)

async function fetchNotifications() {
  try {
    const { data } = await axios.get('/api/notifications')
    notifications.value = data.data ?? []
  } catch {}
}

async function markAllRead() {
  await axios.post('/api/notifications/mark-read')
  notifications.value = notifications.value.map(n => ({ ...n, read_at: new Date().toISOString() }))
}

function formatDate(d: string) {
  return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(
    Math.round((new Date(d).getTime() - Date.now()) / 60000), 'minute'
  )
}

function handleClickOutside(e: MouseEvent) {
  if (container.value && !container.value.contains(e.target as Node)) {
    open.value = false
  }
}

onMounted(() => { fetchNotifications(); document.addEventListener('click', handleClickOutside) })
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
