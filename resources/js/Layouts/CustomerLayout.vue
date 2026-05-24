<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile header -->
    <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between lg:hidden">
      <span class="font-semibold text-gray-800">DeliverySaaS</span>
      <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-600">
        <Bars3Icon class="w-5 h-5" />
      </button>
    </header>

    <!-- Sidebar overlay (mobile) -->
    <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" />

    <!-- Sidebar -->
    <aside :class="['fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col transform transition-transform lg:translate-x-0', sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']">
      <div class="flex items-center gap-3 px-6 py-5 border-b">
        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
          <span class="text-white font-bold text-sm">D</span>
        </div>
        <span class="font-semibold text-gray-800">My Deliveries</span>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <NavLink :href="route('customer.dashboard')" :active="$page.url === '/customer/dashboard'">
          <HomeIcon class="w-5 h-5" /> Dashboard
        </NavLink>
        <NavLink :href="route('customer.subscriptions.index')" :active="$page.url.startsWith('/customer/subscriptions')">
          <CalendarIcon class="w-5 h-5" /> My Subscriptions
        </NavLink>
        <NavLink :href="route('customer.deliveries.index')" :active="$page.url.startsWith('/customer/deliveries')">
          <TruckIcon class="w-5 h-5" /> Deliveries
        </NavLink>
        <NavLink :href="route('customer.wallet')" :active="$page.url.startsWith('/customer/wallet')">
          <WalletIcon class="w-5 h-5" /> Wallet
        </NavLink>
      </nav>

      <div class="border-t px-4 py-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
            {{ userInitials }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">{{ $page.props.auth.user.name }}</p>
          </div>
          <Link :href="route('logout')" method="post" as="button" class="text-gray-400">
            <ArrowRightOnRectangleIcon class="w-5 h-5" />
          </Link>
        </div>
      </div>
    </aside>

    <main class="lg:ml-64 min-h-screen">
      <div class="p-4 lg:p-6">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue'
import {
  HomeIcon, CalendarIcon, TruckIcon, WalletIcon,
  ArrowRightOnRectangleIcon, Bars3Icon,
} from '@heroicons/vue/24/outline'

const sidebarOpen = ref(false)
const page = usePage()

const userInitials = computed(() => {
  const name: string = (page.props.auth as any)?.user?.name ?? ''
  return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
})
</script>
