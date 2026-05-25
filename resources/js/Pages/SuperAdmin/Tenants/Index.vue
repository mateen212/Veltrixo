<script setup lang="ts">
import { ref } from 'vue'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import {
    BuildingOfficeIcon, MagnifyingGlassIcon, PlusIcon,
    CheckCircleIcon, NoSymbolIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{
    tenants: {
        data: Array<{
            id: number; name: string; slug: string; status: string;
            email: string; owner_name: string; plan_name: string;
            trial_ends: string; created_at: string;
        }>
        links: any[]
        meta: any
    }
    plans: Array<{ id: number; name: string; slug: string }>
    filters: { search?: string; status?: string }
}>()

const search = ref(props.filters.search ?? '')
const showCreate = ref(false)

const form = useForm({
    business_name: '',
    owner_name:    '',
    owner_email:   '',
    owner_phone:   '',
    plan:          '',
    trial_days:    14,
})

function applySearch() {
    router.get(route('super-admin.tenants.index'), { search: search.value }, { preserveState: true })
}

function submitCreate() {
    form.post(route('super-admin.tenants.store'), {
        onSuccess: () => { showCreate.value = false; form.reset() },
    })
}

const statusColors: Record<string, string> = {
    active:    'bg-green-100 text-green-700',
    suspended: 'bg-red-100 text-red-700',
    trial:     'bg-amber-100 text-amber-700',
    inactive:  'bg-gray-100 text-gray-600',
}
</script>

<template>
    <SuperAdminLayout title="Tenants">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <form @submit.prevent="applySearch" class="flex items-center gap-2 flex-1 max-w-sm">
                <div class="relative flex-1">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <input v-model="search" type="text" placeholder="Search tenants…"
                        class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                </div>
                <button type="submit"
                    class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                    Search
                </button>
            </form>
            <button @click="showCreate = true"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                <PlusIcon class="h-4 w-4" /> New Tenant
            </button>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Business</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trial Ends</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="tenant in tenants.data" :key="tenant.id"
                            class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ tenant.name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ tenant.owner_name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ tenant.plan_name }}</td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusColors[tenant.status] ?? 'bg-gray-100 text-gray-600']">
                                    {{ tenant.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ tenant.trial_ends }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ tenant.created_at }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('super-admin.tenants.show', tenant.id)"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-xs mr-3">View</Link>
                                <Link v-if="tenant.status === 'active'"
                                    :href="route('super-admin.tenants.suspend', tenant.id)"
                                    method="patch" as="button"
                                    class="text-red-500 hover:text-red-700 font-medium text-xs">
                                    Suspend
                                </Link>
                                <Link v-else-if="tenant.status === 'suspended'"
                                    :href="route('super-admin.tenants.reactivate', tenant.id)"
                                    method="patch" as="button"
                                    class="text-green-600 hover:text-green-800 font-medium text-xs">
                                    Reactivate
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!tenants.data.length">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No tenants found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Tenant Modal -->
        <Transition name="fade">
            <div v-if="showCreate"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showCreate = false">
                <form @submit.prevent="submitCreate"
                    class="w-full max-w-lg rounded-2xl bg-white shadow-2xl p-6 space-y-4">
                    <h2 class="text-lg font-bold text-gray-900">Provision New Tenant</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Business Name</label>
                            <input v-model="form.business_name" type="text" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                            <p v-if="form.errors.business_name" class="text-red-500 text-xs mt-1">{{ form.errors.business_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Owner Name</label>
                            <input v-model="form.owner_name" type="text" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Owner Email</label>
                            <input v-model="form.owner_email" type="email" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                            <p v-if="form.errors.owner_email" class="text-red-500 text-xs mt-1">{{ form.errors.owner_email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Owner Phone</label>
                            <input v-model="form.owner_phone" type="text" placeholder="+92300…"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Plan</label>
                            <select v-model="form.plan" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="" disabled>Select a plan</option>
                                <option v-for="plan in plans" :key="plan.id" :value="plan.slug">{{ plan.name }}</option>
                            </select>
                            <p v-if="form.errors.plan" class="text-red-500 text-xs mt-1">{{ form.errors.plan }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Trial Days</label>
                            <input v-model="form.trial_days" type="number" min="0" max="365"
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
                            {{ form.processing ? 'Provisioning…' : 'Provision Tenant' }}
                        </button>
                    </div>
                </form>
            </div>
        </Transition>
    </SuperAdminLayout>
</template>
