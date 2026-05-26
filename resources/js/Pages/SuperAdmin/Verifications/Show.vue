<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'

interface Tenant {
    id: number
    name: string
    email: string
    phone: string | null
    latitude: number | null
    longitude: number | null
    delivery_radius_km: number
    verification_status: string
    rejection_reason: string | null
    address: Record<string, string> | null
    meta: Record<string, any> | null
    created_at: string
    verified_at: string | null
    owner: {
        id: number
        name: string
        email: string
        phone: string | null
    } | null
    verified_by_admin: {
        id: number
        name: string
    } | null
}

const props = defineProps<{ tenant: Tenant }>()

// ── Map ───────────────────────────────────────────────────────────────────────
const mapContainer = ref<HTMLDivElement | null>(null)

function initMap() {
    const L = (window as any).L
    if (!L || !mapContainer.value) return
    if (!props.tenant.latitude) return

    const lat = props.tenant.latitude
    const lng = props.tenant.longitude!

    const map = L.map(mapContainer.value).setView([lat, lng], 13)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
    }).addTo(map)

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup(props.tenant.name)
        .openPopup()

    L.circle([lat, lng], {
        radius: props.tenant.delivery_radius_km * 1000,
        color: '#6366f1',
        fillColor: '#6366f1',
        fillOpacity: 0.1,
    }).addTo(map)
}

onMounted(() => {
    if (!(window as any).L) {
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)

        const script = document.createElement('script')
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.onload = () => initMap()
        document.head.appendChild(script)
    } else {
        initMap()
    }
})

// ── Approve form ──────────────────────────────────────────────────────────────
const approveForm = useForm({ plan_slug: props.tenant.meta?.selected_plan ?? 'starter' })
function approve() {
    approveForm.patch(route('super-admin.verifications.approve', props.tenant.id))
}

// ── Reject form ───────────────────────────────────────────────────────────────
const rejectForm = useForm({ reason: '' })
const showRejectModal = ref(false)

function reject() {
    rejectForm.patch(route('super-admin.verifications.reject', props.tenant.id), {
        onSuccess: () => {
            showRejectModal.value = false
        },
    })
}

function statusClass(status: string) {
    const map: Record<string, string> = {
        pending_verification: 'bg-amber-100 text-amber-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
    }
    return map[status] ?? 'bg-slate-100 text-slate-600'
}
</script>

<template>
    <Head :title="`Review: ${tenant.name} — Super Admin`" />

    <div class="p-6 space-y-6 max-w-4xl mx-auto">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <Link :href="route('super-admin.verifications.index')" class="hover:text-indigo-600">Verifications</Link>
            <span>/</span>
            <span class="text-slate-800 font-medium">{{ tenant.name }}</span>
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ tenant.name }}</h1>
                <span class="text-xs rounded px-2 py-0.5 font-medium mt-1 inline-block" :class="statusClass(tenant.verification_status)">
                    {{ tenant.verification_status.replace('_', ' ').toUpperCase() }}
                </span>
            </div>

            <!-- Action buttons (only for pending) -->
            <div v-if="tenant.verification_status === 'pending_verification'" class="flex gap-3">
                <button
                    @click="showRejectModal = true"
                    class="border border-red-300 text-red-600 px-4 py-2 rounded-lg hover:bg-red-50 text-sm font-medium transition"
                >
                    Reject
                </button>
                <button
                    @click="approve"
                    :disabled="approveForm.processing"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium transition disabled:opacity-50"
                >
                    <span v-if="approveForm.processing">Approving…</span>
                    <span v-else>Approve</span>
                </button>
            </div>
        </div>

        <!-- Map -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100">
                <h2 class="font-semibold text-slate-700">Business Location & Delivery Radius</h2>
            </div>
            <div v-if="tenant.latitude">
                <div ref="mapContainer" class="w-full h-80" />
                <div class="px-5 py-3 text-sm text-slate-500 flex gap-6">
                    <span>Lat: <strong>{{ tenant.latitude }}</strong></span>
                    <span>Lng: <strong>{{ tenant.longitude }}</strong></span>
                    <span>Radius: <strong>{{ tenant.delivery_radius_km }} km</strong></span>
                </div>
            </div>
            <div v-else class="py-12 text-center text-slate-400">No GPS coordinates provided</div>
        </div>

        <!-- Business + Owner info -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
                <h2 class="font-semibold text-slate-700 mb-3">Business Information</h2>
                <div class="text-sm space-y-1.5">
                    <p><span class="text-slate-400 w-28 inline-block">Name</span><span class="text-slate-800">{{ tenant.name }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Email</span><span class="text-slate-800">{{ tenant.email }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Phone</span><span class="text-slate-800">{{ tenant.phone ?? '—' }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">City</span><span class="text-slate-800">{{ tenant.address?.city ?? '—' }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Area</span><span class="text-slate-800">{{ tenant.address?.area ?? '—' }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Plan Requested</span>
                        <span class="bg-indigo-50 text-indigo-700 text-xs rounded px-2 py-0.5">{{ tenant.meta?.selected_plan ?? 'starter' }}</span>
                    </p>
                    <p><span class="text-slate-400 w-28 inline-block">Submitted</span><span class="text-slate-800">{{ new Date(tenant.created_at).toLocaleString() }}</span></p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
                <h2 class="font-semibold text-slate-700 mb-3">Owner Information</h2>
                <div class="text-sm space-y-1.5">
                    <p><span class="text-slate-400 w-28 inline-block">Name</span><span class="text-slate-800">{{ tenant.owner?.name ?? '—' }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Email</span><span class="text-slate-800">{{ tenant.owner?.email ?? '—' }}</span></p>
                    <p><span class="text-slate-400 w-28 inline-block">Phone</span><span class="text-slate-800">{{ tenant.owner?.phone ?? '—' }}</span></p>
                </div>

                <!-- Approval plan picker -->
                <div v-if="tenant.verification_status === 'pending_verification'" class="pt-3 border-t border-slate-100">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Override Plan on Approval</label>
                    <input
                        v-model="approveForm.plan_slug"
                        type="text"
                        placeholder="starter"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <p v-if="approveForm.errors.plan_slug" class="text-red-500 text-xs mt-1">{{ approveForm.errors.plan_slug }}</p>
                </div>

                <!-- Rejection reason if rejected -->
                <div v-if="tenant.verification_status === 'rejected' && tenant.rejection_reason" class="pt-3 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 mb-1">Rejection Reason</p>
                    <p class="text-sm text-red-600">{{ tenant.rejection_reason }}</p>
                </div>
            </div>
        </div>

        <!-- Reject modal -->
        <div v-if="showRejectModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Reject Business Registration</h3>
                <p class="text-sm text-slate-500 mb-4">Please provide a reason. This will be sent to the business owner.</p>
                <textarea
                    v-model="rejectForm.reason"
                    rows="4"
                    placeholder="e.g. Incomplete information provided. Please resubmit with valid business address and logo."
                    class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"
                />
                <p v-if="rejectForm.errors.reason" class="text-red-500 text-xs mt-1">{{ rejectForm.errors.reason }}</p>
                <div class="flex justify-end gap-3 mt-4">
                    <button @click="showRejectModal = false" class="px-4 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50">Cancel</button>
                    <button
                        @click="reject"
                        :disabled="rejectForm.processing || !rejectForm.reason.trim()"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                    >
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
