<script setup lang="ts">
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { PlusIcon, PencilSquareIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    plans: { data: any[], links: any[], meta: any }
    filters: { search?: string }
}>()

const showCreate = ref(false)
const editItem = ref(null as any)

const form = useForm({
    name: '', slug: '', description: '', price_monthly: 0, price_yearly: 0,
    max_customers: 0, max_riders: 0, max_products: 0, max_orders_per_month: 0,
    features: [], is_active: true, is_public: true, sort_order: 0,
})
const featuresInput = ref('')

function openCreate() {
    editItem.value = null
    form.reset()
    featuresInput.value = ''
    showCreate.value = true
}

function openEdit(plan: any) {
    editItem.value = plan
    form.fill({
        name: plan.name, slug: plan.slug, description: plan.description ?? '',
        price_monthly: Number(plan.price_monthly || 0), price_yearly: Number(plan.price_yearly || 0),
        max_customers: plan.max_customers || 0, max_riders: plan.max_riders || 0,
        max_products: plan.max_products || 0, max_orders_per_month: plan.max_orders_per_month || 0,
        features: plan.features || [], is_active: plan.is_active, is_public: plan.is_public, sort_order: plan.sort_order || 0,
    })
    featuresInput.value = (plan.features || []).join(', ')
    showCreate.value = true
}

function submit() {
    // convert comma-separated features string into array
    form.features = (featuresInput.value || '').split(',').map(f => f.trim()).filter(Boolean)

    if (editItem.value) {
        form.patch(route('super-admin.plans.update', editItem.value.id))
    } else {
        form.post(route('super-admin.plans.store'))
    }
}

function remove(plan: any) {
    if (!confirm('Delete this plan?')) return
    router.delete(route('super-admin.plans.destroy', plan.id))
}
</script>

<template>
    <SuperAdminLayout title="Plans">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <input type="text" placeholder="Search plans…" class="pl-9 pr-3 py-2 rounded-lg border" />
                </div>
            </div>
            <button @click="openCreate" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white">
                <PlusIcon class="h-4 w-4" /> New Plan
            </button>
        </div>

        <div class="rounded-xl bg-white shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Monthly</th>
                            <th class="px-6 py-3 text-left">Yearly</th>
                            <th class="px-6 py-3 text-left">Public</th>
                            <th class="px-6 py-3 text-left">Active</th>
                            <th class="px-6 py-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="plan in plans.data" :key="plan.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3">{{ plan.name }}</td>
                            <td class="px-6 py-3">Rs {{ plan.price_monthly }}</td>
                            <td class="px-6 py-3">Rs {{ plan.price_yearly }}</td>
                            <td class="px-6 py-3">{{ plan.is_public ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-3">{{ plan.is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="openEdit(plan)" class="mr-2 text-indigo-600"><PencilSquareIcon class="h-4 w-4" /></button>
                                <button @click="remove(plan)" class="text-red-600"><TrashIcon class="h-4 w-4" /></button>
                            </td>
                        </tr>
                        <tr v-if="!plans.data.length">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">No plans yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
                <form @submit.prevent="submit" class="w-full max-w-2xl bg-white rounded-2xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold">{{ editItem ? 'Edit Plan' : 'Create Plan' }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold">Name</label>
                            <input v-model="form.name" required class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Slug</label>
                            <input v-model="form.slug" required class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Monthly (PKR)</label>
                            <input v-model.number="form.price_monthly" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Yearly (PKR)</label>
                            <input v-model.number="form.price_yearly" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-semibold">Description</label>
                            <textarea v-model="form.description" class="w-full rounded border px-3 py-2" rows="3"></textarea>
                        </div>

                        <div>
                            <label class="text-xs font-semibold">Max customers</label>
                            <input v-model.number="form.max_customers" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Max riders</label>
                            <input v-model.number="form.max_riders" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Max products</label>
                            <input v-model.number="form.max_products" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Max orders / month</label>
                            <input v-model.number="form.max_orders_per_month" type="number" class="w-full rounded border px-3 py-2" />
                        </div>

                        <div class="col-span-2">
                            <label class="text-xs font-semibold">Features (comma separated)</label>
                            <input v-model="featuresInput" placeholder="e.g. analytics,api-access" class="w-full rounded border px-3 py-2" />
                        </div>

                        <div>
                            <label class="text-xs font-semibold">Sort order</label>
                            <input v-model.number="form.sort_order" type="number" class="w-full rounded border px-3 py-2" />
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-xs">
                                <input type="checkbox" v-model="form.is_active" class="h-4 w-4" />
                                <span>Active</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs">
                                <input type="checkbox" v-model="form.is_public" class="h-4 w-4" />
                                <span>Public</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showCreate = false" class="px-4 py-2">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Save</button>
                    </div>
                </form>
            </div>
        </Transition>
    </SuperAdminLayout>
</template>
