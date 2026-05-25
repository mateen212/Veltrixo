<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { MagnifyingGlassIcon, PlusIcon, TruckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    riders: {
        data: Array<{
            id: number; name: string; email: string; phone: string;
            vehicle_type: string; vehicle_number: string | null;
            status: string; is_available: boolean;
            deliveries_today: number; created_at: string;
        }>
        links: any[]
    }
    filters: { search?: string; status?: string }
}>()

const search     = ref(props.filters.search ?? '')
const showCreate = ref(false)

const form = useForm({
    name:           '',
    email:          '',
    phone:          '',
    password:       '',
    password_confirmation: '',
    vehicle_type:   'bike',
    vehicle_number: '',
})

function applySearch() {
    router.get(route('admin.riders.index'), { search: search.value }, { preserveState: true })
}

function submitCreate() {
    form.post(route('admin.riders.store'), {
        onSuccess: () => { showCreate.value = false; form.reset() },
    })
}

const statusColors: Record<string, string> = {
    active:    'bg-green-100 text-green-700',
    inactive:  'bg-gray-100 text-gray-600',
    suspended: 'bg-red-100 text-red-700',
}

const vehicleLabels: Record<string, string> = {
    bike: '🏍️ Bike', bicycle: '🚲 Bicycle',
    van: '🚐 Van', car: '🚗 Car', walk: '🚶 Walk',
}
</script>

<template>
    <AdminLayout title="Riders">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <form @submit.prevent="applySearch" class="flex items-center gap-2 flex-1 max-w-sm">
                <div class="relative flex-1">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <input v-model="search" type="text" placeholder="Search riders…"
                        class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                </div>
                <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">Search</button>
            </form>
            <button @click="showCreate = true"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                <PlusIcon class="h-4 w-4" /> Add Rider
            </button>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rider</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Available</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Today</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="rider in riders.data" :key="rider.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ rider.name }}</div>
                                <div class="text-xs text-gray-400">{{ rider.email }}</div>
                                <div class="text-xs text-gray-400">{{ rider.phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div>{{ vehicleLabels[rider.vehicle_type] ?? rider.vehicle_type }}</div>
                                <div v-if="rider.vehicle_number" class="text-xs text-gray-400">{{ rider.vehicle_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusColors[rider.status] ?? 'bg-gray-100 text-gray-600']">
                                    {{ rider.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', rider.is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                    {{ rider.is_available ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <TruckIcon class="h-4 w-4 text-gray-400" />
                                    <span class="font-semibold text-gray-800">{{ rider.deliveries_today }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ rider.created_at }}</td>
                        </tr>
                        <tr v-if="!riders.data.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No riders found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Rider Modal -->
        <Transition name="fade">
            <div v-if="showCreate"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showCreate = false">
                <form @submit.prevent="submitCreate"
                    class="w-full max-w-lg rounded-2xl bg-white shadow-2xl p-6 space-y-4">
                    <h2 class="text-lg font-bold text-gray-900">Add New Rider</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
                            <input v-model="form.name" type="text" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                            <input v-model="form.email" type="email" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                            <input v-model="form.phone" type="text" placeholder="+92300…" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Vehicle Type</label>
                            <select v-model="form.vehicle_type"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="bike">Bike</option>
                                <option value="bicycle">Bicycle</option>
                                <option value="van">Van</option>
                                <option value="car">Car</option>
                                <option value="walk">Walk</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Vehicle Number</label>
                            <input v-model="form.vehicle_number" type="text" placeholder="LEA-12-3456"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                            <input v-model="form.password" type="password" required minlength="8"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCreate = false"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                            {{ form.processing ? 'Creating…' : 'Add Rider' }}
                        </button>
                    </div>
                </form>
            </div>
        </Transition>
    </AdminLayout>
</template>
