<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col">
      <div class="flex items-center gap-3 px-6 py-5 border-b">
        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
          <span class="text-white font-bold text-sm">D</span>
        </div>
        <span class="font-semibold text-gray-800 text-lg">DeliverySaaS</span>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <NavLink :href="route('admin.dashboard')" :active="$page.url.startsWith('/admin/dashboard')">
          <ChartBarIcon class="w-5 h-5" /> Dashboard
        </NavLink>
        <NavLink :href="route('admin.deliveries.index')" :active="$page.url.startsWith('/admin/deliveries')">
          <TruckIcon class="w-5 h-5" /> Deliveries
        </NavLink>
        <NavLink :href="route('admin.subscriptions.index')" :active="$page.url.startsWith('/admin/subscriptions')">
          <CalendarIcon class="w-5 h-5" /> Subscriptions
        </NavLink>
        <NavLink :href="route('admin.products.index')" :active="$page.url.startsWith('/admin/products')">
          <ShoppingBagIcon class="w-5 h-5" /> Products
        </NavLink>
        <NavLink :href="route('admin.wallets.index')" :active="$page.url.startsWith('/admin/wallets')">
          <WalletIcon class="w-5 h-5" /> Wallets
        </NavLink>
      </nav>

      <div class="border-t px-4 py-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
            <span class="text-sm font-medium text-gray-600">{{ userInitials }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">{{ $page.props.auth.user.name }}</p>
            <p class="text-xs text-gray-500 truncate">Admin</p>
          </div>
          <Link :href="route('logout')" method="post" as="button" class="text-gray-400 hover:text-gray-600">
            <ArrowRightOnRectangleIcon class="w-5 h-5" />
          </Link>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="ml-64 min-h-screen">
      <!-- Top bar -->
      <header class="sticky top-0 z-40 bg-white border-b px-6 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">{{ title }}</h1>
        <div class="flex items-center gap-3">
          <slot name="header-actions" />
          <NotificationBell />
        </div>
      </header>

      <!-- Page content -->
      <div class="p-6">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue'
import NotificationBell from '@/Components/NotificationBell.vue'
import {
  ChartBarIcon, TruckIcon, CalendarIcon,
  ShoppingBagIcon, WalletIcon, ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

defineProps<{ title?: string }>()

const page = usePage()

const userInitials = computed(() => {
  const name: string = (page.props.auth as any)?.user?.name ?? ''
  return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})
</script>
