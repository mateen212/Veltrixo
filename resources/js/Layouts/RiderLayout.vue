<template>
  <div class="min-h-screen bg-slate-900">
    <!-- Top bar -->
    <header class="bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
          <TruckIcon class="w-5 h-5 text-white" />
        </div>
        <span class="font-semibold">Rider App</span>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-slate-300">{{ $page.props.auth.user.name }}</span>
        <Link :href="route('logout')" method="post" as="button" class="text-slate-400">
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
        </Link>
      </div>
    </header>

    <!-- Main content -->
    <main class="max-w-lg mx-auto p-4">
      <slot />
    </main>

    <!-- Bottom nav -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t flex">
      <Link :href="route('rider.dashboard')" class="flex-1 flex flex-col items-center py-3 text-xs gap-1"
        :class="$page.url === '/rider/dashboard' ? 'text-orange-600' : 'text-gray-500'">
        <HomeIcon class="w-6 h-6" />
        Home
      </Link>
      <Link :href="route('rider.deliveries.index')" class="flex-1 flex flex-col items-center py-3 text-xs gap-1"
        :class="$page.url.startsWith('/rider/deliveries') ? 'text-orange-600' : 'text-gray-500'">
        <TruckIcon class="w-6 h-6" />
        Deliveries
      </Link>
    </nav>
    <div class="h-16" /><!-- bottom nav spacer -->
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { HomeIcon, TruckIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'
</script>
