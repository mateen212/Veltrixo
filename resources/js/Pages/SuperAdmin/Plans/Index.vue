<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppBadge from '@/Components/AppBadge.vue'
import AppModal from '@/Components/AppModal.vue'
import Checkbox from '@/Components/Checkbox.vue'
import DataTable from '@/Components/DataTable.vue'
import TablePagination from '@/Components/TablePagination.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

interface Plan {
    id: number
    name: string
    slug: string
    description?: string
    price_monthly: string
    price_yearly: string
    is_active: boolean
    is_public: boolean
    features: string[]
    max_customers?: number
    max_riders?: number
    max_products?: number
    max_orders_per_month?: number
}

interface PlansData {
    data: Plan[]
    meta: any
    links: any[]
}

const props = defineProps<{
    plans: PlansData
}>()

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref<number | null>(null)
const loading = ref(false)

const form = ref({
    name: '',
    slug: '',
    description: '',
    price_monthly: '',
    price_yearly: '',
    max_customers: '',
    max_riders: '',
    max_products: '',
    max_orders_per_month: '',
    features: [] as string[],
    is_active: true,
    is_public: true,
})

const cols = [
    { key: 'id', label: '#', sortable: false, width: '60px' },
    { key: 'name', label: 'Plan', sortable: false },
    { key: 'price_monthly', label: 'Monthly Price', sortable: false, width: '120px' },
    { key: 'is_public', label: 'Public', sortable: false, width: '80px' },
    { key: 'is_active', label: 'Status', sortable: false, width: '100px' },
]

function openAddModal() {
    resetForm()
    isEditing.value = false
    editingId.value = null
    showModal.value = true
}

function openEditModal(plan: Plan) {
    form.value = {
        name: plan.name,
        slug: plan.slug,
        description: plan.description || '',
        price_monthly: plan.price_monthly,
        price_yearly: plan.price_yearly,
        max_customers: plan.max_customers?.toString() || '',
        max_riders: plan.max_riders?.toString() || '',
        max_products: plan.max_products?.toString() || '',
        max_orders_per_month: plan.max_orders_per_month?.toString() || '',
        features: Array.isArray(plan.features) ? [...plan.features] : [],
        is_active: plan.is_active,
        is_public: plan.is_public,
    }
    isEditing.value = true
    editingId.value = plan.id
    showModal.value = true
}

function resetForm() {
    form.value = {
        name: '',
        slug: '',
        description: '',
        price_monthly: '',
        price_yearly: '',
        max_customers: '',
        max_riders: '',
        max_products: '',
        max_orders_per_month: '',
        features: [],
        is_active: true,
        is_public: true,
    }
}

function submitForm() {
    loading.value = true
    const endpoint = isEditing.value
        ? route('super-admin.plans.update', editingId.value)
        : route('super-admin.plans.store')

    router.post(endpoint, form.value as any, {
        method: isEditing.value ? 'patch' : 'post',
        onSuccess: () => {
            showModal.value = false
            resetForm()
            loading.value = false
        },
        onError: () => {
            loading.value = false
        },
    })
}

function deletePlan(plan: Plan) {
    if (confirm(`Are you sure you want to delete "${plan.name}"? This action cannot be undone.`)) {
        loading.value = true
        router.delete(route('super-admin.plans.destroy', plan.id), {
            onSuccess: () => {
                loading.value = false
            },
        })
    }
}

function addFeature() {
    form.value.features.push('')
}

function removeFeature(index: number) {
    form.value.features.splice(index, 1)
}

function updateFeature(index: number, value: string) {
    form.value.features[index] = value
}
</script>

<template>
    <Head title="Plans — Super Admin" />
    <SuperAdminLayout title="Plans">
        <div class="page-header mb-6 flex items-center justify-between">
            <div>
                <h1 class="page-title">Subscription Plans</h1>
                <p class="page-subtitle">Manage platform pricing plans</p>
            </div>
            <AppButton @click="openAddModal" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Plan
            </AppButton>
        </div>

        <div class="card overflow-hidden">
            <DataTable :columns="cols" :rows="plans.data" :loading="loading"
                empty-title="No plans yet" empty-description="Create your first subscription plan.">
                <template #default="{ row }">
                    <td class="px-5 py-3.5 text-xs font-mono text-ink-faint">#{{ row.id }}</td>
                    <td class="px-5 py-3.5">
                        <div>
                            <p class="text-sm font-medium text-ink">{{ row.name }}</p>
                            <p v-if="row.description" class="text-xs text-ink-faint">{{ row.description }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-ink-secondary">${{ row.price_monthly }}/mo</td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="row.is_public ? 'success' : 'neutral'" dot size="xs">
                            {{ row.is_public ? 'Public' : 'Private' }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5">
                        <AppBadge :variant="row.is_active ? 'success' : 'neutral'" dot size="xs">
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </AppBadge>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <AppButton @click="openEditModal(row)" variant="ghost" icon size="sm">
                                <PencilIcon class="h-4 w-4" />
                            </AppButton>
                            <AppButton @click="deletePlan(row)" variant="ghost" icon size="sm">
                                <TrashIcon class="h-4 w-4 text-red-500" />
                            </AppButton>
                        </div>
                    </td>
                </template>
            </DataTable>
            <TablePagination :meta="plans.meta" :links="plans.links" />
        </div>

        <!-- Create/Edit Modal -->
        <AppModal v-if="showModal" :title="`${isEditing ? 'Edit' : 'Add'} Plan`" size="lg" @close="showModal = false">
            <form @submit.prevent="submitForm" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Plan Name *</label>
                        <input v-model="form.name" type="text" placeholder="e.g., Professional" required class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Slug *</label>
                        <input v-model="form.slug" type="text" placeholder="e.g., professional" required class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Description</label>
                    <textarea v-model="form.description" placeholder="Plan description..." class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" rows="2"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Monthly Price ($) *</label>
                        <input v-model="form.price_monthly" type="number" step="0.01" placeholder="0.00" required class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Yearly Price ($) *</label>
                        <input v-model="form.price_yearly" type="number" step="0.01" placeholder="0.00" required class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Max Customers</label>
                        <input v-model="form.max_customers" type="number" placeholder="Unlimited" class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Max Riders</label>
                        <input v-model="form.max_riders" type="number" placeholder="Unlimited" class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Max Products</label>
                        <input v-model="form.max_products" type="number" placeholder="Unlimited" class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Max Orders/Month</label>
                        <input v-model="form.max_orders_per_month" type="number" placeholder="Unlimited" class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-3">Features</label>
                    <div class="space-y-2">
                        <div v-for="(feature, index) in form.features" :key="index" class="flex gap-2">
                            <input :value="feature" @input="updateFeature(index, ($event.target as HTMLInputElement).value)" type="text" placeholder="Feature name" class="flex-1 px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40" />
                            <AppButton @click="removeFeature(index)" variant="danger" icon size="sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </AppButton>
                        </div>
                        <AppButton @click="addFeature" type="button" variant="outline" size="sm" class="w-full">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Feature
                        </AppButton>
                    </div>
                </div>

                <div class="space-y-3 border-t border-border-muted pt-4">
                    <div class="flex items-center gap-3">
                        <Checkbox v-model:checked="form.is_active" id="is_active" />
                        <label for="is_active" class="text-sm text-ink cursor-pointer">Active</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model:checked="form.is_public" id="is_public" />
                        <label for="is_public" class="text-sm text-ink cursor-pointer">Public (show to new businesses)</label>
                    </div>
                </div>
            </form>

            <template #footer>
                <div class="flex items-center justify-end gap-3">
                    <AppButton @click="showModal = false" variant="secondary">Cancel</AppButton>
                    <AppButton @click="submitForm" :loading="loading">{{ isEditing ? 'Update' : 'Create' }} Plan</AppButton>
                </div>
            </template>
        </AppModal>
    </SuperAdminLayout>
</template>
