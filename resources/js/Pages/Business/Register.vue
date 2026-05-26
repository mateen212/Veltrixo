<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { router, useForm, Head } from '@inertiajs/vue3'

interface Plan {
    id: number
    name: string
    slug: string
    price_monthly: number
    price_yearly: number
    description: string | null
    features: string[]
}

const props = defineProps<{ plans: Plan[] }>()

// ── Wizard state ──────────────────────────────────────────────────────────────
const step = ref(1)
const totalSteps = 3

const form = useForm({
    business_name: '',
    subdomain: '',
    subdomainManuallyEdited: false,
    business_address: '',
    city: '',
    area: '',
    owner_name: '',
    owner_email: '',
    owner_phone: '',
    password: '',
    password_confirmation: '',
    latitude: null as number | null,
    longitude: null as number | null,
    delivery_radius_km: 5,
    selected_plan: props.plans[0]?.slug ?? 'starter',
    logo: null as File | null,
})

// Auto-suggest subdomain from business_name unless user has manually edited it
function toSubdomain(name: string): string {
    return name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .substring(0, 63)
}

watch(() => form.business_name, (name) => {
    if (!form.subdomainManuallyEdited) {
        form.subdomain = toSubdomain(name)
    }
})

// ── Location / Map ────────────────────────────────────────────────────────────
const mapContainer = ref<HTMLDivElement | null>(null)
const locationStatus = ref('')
let map: any = null
let marker: any = null
let radiusCircle: any = null

function initMap(lat = 30.3753, lng = 69.3451) {
    // Leaflet loaded from CDN in the <Head> section; wait for it
    const L = (window as any).L
    if (!L || !mapContainer.value) return

    map = L.map(mapContainer.value).setView([lat, lng], 12)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
    }).addTo(map)

    marker = L.marker([lat, lng], { draggable: true }).addTo(map)
    marker.on('dragend', () => {
        const pos = marker.getLatLng()
        form.latitude = pos.lat
        form.longitude = pos.lng
        updateCircle(pos.lat, pos.lng)
    })

    map.on('click', (e: any) => {
        marker.setLatLng(e.latlng)
        form.latitude = e.latlng.lat
        form.longitude = e.latlng.lng
        updateCircle(e.latlng.lat, e.latlng.lng)
    })

    drawCircle(lat, lng)
}

function drawCircle(lat: number, lng: number) {
    const L = (window as any).L
    if (!L || !map) return
    if (radiusCircle) map.removeLayer(radiusCircle)
    radiusCircle = L.circle([lat, lng], {
        radius: form.delivery_radius_km * 1000,
        color: '#6366f1',
        fillColor: '#6366f1',
        fillOpacity: 0.1,
    }).addTo(map)
}

function updateCircle(lat: number, lng: number) {
    drawCircle(lat, lng)
}

function onRadiusChange() {
    if (form.latitude && form.longitude) {
        drawCircle(form.latitude, form.longitude)
    }
}

function detectLocation() {
    locationStatus.value = 'Detecting…'
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            form.latitude = pos.coords.latitude
            form.longitude = pos.coords.longitude
            locationStatus.value = `Detected: ${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`
            if (map) {
                map.setView([pos.coords.latitude, pos.coords.longitude], 14)
                marker?.setLatLng([pos.coords.latitude, pos.coords.longitude])
                drawCircle(pos.coords.latitude, pos.coords.longitude)
            } else {
                initMap(pos.coords.latitude, pos.coords.longitude)
            }
        },
        () => {
            locationStatus.value = 'Could not detect location. Please pin your location on the map.'
            initMap()
        }
    )
}

onMounted(() => {
    // Load Leaflet CSS + JS dynamically if not already loaded
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

// ── Wizard navigation ─────────────────────────────────────────────────────────
const step1Valid = computed(() =>
    form.business_name.trim().length > 0 &&
    form.subdomain.trim().length > 0 &&
    form.latitude !== null && form.longitude !== null
)
const step2Valid = computed(() =>
    form.owner_name.trim().length > 0 &&
    form.owner_email.trim().length > 0 &&
    form.password.trim().length >= 8 &&
    form.password === form.password_confirmation
)

function nextStep() {
    if (step.value < totalSteps) step.value++
}
function prevStep() {
    if (step.value > 1) step.value--
}

// ── Logo preview ──────────────────────────────────────────────────────────────
const logoPreview = ref<string | null>(null)
function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) {
        form.logo = file
        logoPreview.value = URL.createObjectURL(file)
    }
}

// ── Submit ────────────────────────────────────────────────────────────────────
function submit() {
    form.post(route('business.register.store'))
}
</script>

<template>
    <Head title="Register Your Business — Veltrixo" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Register Your Business</h1>
                <p class="text-slate-500 mt-2">Start your 30-day free trial — no credit card required</p>
            </div>

            <!-- Step indicators -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <template v-for="n in totalSteps" :key="n">
                    <div
                        class="h-2 rounded-full transition-all duration-300"
                        :class="[
                            n <= step ? 'bg-indigo-600' : 'bg-slate-200',
                            n === step ? 'w-8' : 'w-4',
                        ]"
                    />
                </template>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8">

                <!-- ── Step 1: Business Details + Map ── -->
                <div v-if="step === 1">
                    <h2 class="text-xl font-semibold text-slate-700 mb-6">Business Details & Location</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Business Name <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.business_name"
                                type="text"
                                placeholder="e.g. Fresh Daily Milk Co."
                                class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.business_name" class="text-red-500 text-sm mt-1">{{ form.errors.business_name }}</p>
                        </div>

                        <!-- Subdomain field with live preview -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Your Business Portal URL <span class="text-red-500">*</span></label>
                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                                <input
                                    v-model="form.subdomain"
                                    @input="form.subdomainManuallyEdited = true"
                                    type="text"
                                    placeholder="your-business"
                                    class="flex-1 px-4 py-2.5 outline-none text-slate-800 lowercase"
                                    maxlength="63"
                                />
                                <span class="px-3 py-2.5 bg-slate-50 text-slate-500 text-sm border-l border-slate-200 select-none">.veltrixo.com</span>
                            </div>
                            <p v-if="form.subdomain" class="text-indigo-600 text-xs mt-1">
                                Your portal: <span class="font-semibold">{{ form.subdomain }}.veltrixo.com</span>
                            </p>
                            <p v-if="form.errors.subdomain" class="text-red-500 text-sm mt-1">{{ form.errors.subdomain }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                                <input v-model="form.city" type="text" placeholder="Lahore" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Area / Neighbourhood</label>
                                <input v-model="form.area" type="text" placeholder="DHA Phase 5" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- Map -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-slate-700">Pin Your Business Location <span class="text-red-500">*</span></label>
                                <button
                                    type="button"
                                    @click="detectLocation"
                                    class="text-xs text-indigo-600 hover:underline flex items-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Auto-detect
                                </button>
                            </div>
                            <div ref="mapContainer" class="w-full h-64 rounded-lg border border-slate-200 overflow-hidden" />
                            <p v-if="locationStatus" class="text-xs text-slate-500 mt-1">{{ locationStatus }}</p>
                            <p v-if="form.latitude" class="text-xs text-green-600 mt-1">
                                Selected: {{ form.latitude?.toFixed(5) }}, {{ form.longitude?.toFixed(5) }}
                            </p>
                            <p v-if="!form.latitude && form.errors.latitude" class="text-red-500 text-sm mt-1">Please select a location on the map.</p>
                        </div>

                        <!-- Delivery radius -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Delivery Radius: <span class="text-indigo-600 font-semibold">{{ form.delivery_radius_km }} km</span>
                            </label>
                            <input
                                v-model.number="form.delivery_radius_km"
                                type="range"
                                min="1"
                                max="50"
                                @input="onRadiusChange"
                                class="w-full accent-indigo-600"
                            />
                            <div class="flex justify-between text-xs text-slate-400 mt-0.5">
                                <span>1 km</span><span>50 km</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Customers outside this radius cannot subscribe to your service.</p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button
                            type="button"
                            :disabled="!step1Valid"
                            @click="nextStep"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
                        >
                            Next →
                        </button>
                    </div>
                </div>

                <!-- ── Step 2: Owner Details ── -->
                <div v-if="step === 2">
                    <h2 class="text-xl font-semibold text-slate-700 mb-6">Owner Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input v-model="form.owner_name" type="text" placeholder="Muhammad Ali" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <p v-if="form.errors.owner_name" class="text-red-500 text-sm mt-1">{{ form.errors.owner_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input v-model="form.owner_email" type="email" placeholder="ali@freshco.pk" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <p v-if="form.errors.owner_email" class="text-red-500 text-sm mt-1">{{ form.errors.owner_email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone <span class="text-red-500">*</span></label>
                            <input v-model="form.owner_phone" type="tel" placeholder="+92 300 0000000" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <p v-if="form.errors.owner_phone" class="text-red-500 text-sm mt-1">{{ form.errors.owner_phone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input v-model="form.password" type="password" placeholder="Min. 8 characters" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <input v-model="form.password_confirmation" type="password" placeholder="Repeat password" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>

                        <!-- Logo -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Business Logo (optional)</label>
                            <div class="flex items-center gap-4">
                                <img v-if="logoPreview" :src="logoPreview" class="h-16 w-16 rounded-full object-cover border border-slate-200" />
                                <input type="file" accept="image/*" @change="onLogoChange" class="text-sm text-slate-600" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="prevStep" class="text-slate-600 px-6 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">← Back</button>
                        <button
                            type="button"
                            :disabled="!step2Valid"
                            @click="nextStep"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
                        >
                            Next →
                        </button>
                    </div>
                </div>

                <!-- ── Step 3: Plan selection ── -->
                <div v-if="step === 3">
                    <h2 class="text-xl font-semibold text-slate-700 mb-6">Choose a Plan</h2>

                    <div class="space-y-3">
                        <label
                            v-for="plan in plans"
                            :key="plan.slug"
                            class="flex items-start gap-4 border rounded-xl p-4 cursor-pointer transition"
                            :class="form.selected_plan === plan.slug ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-slate-300'"
                        >
                            <input type="radio" :value="plan.slug" v-model="form.selected_plan" class="mt-1 accent-indigo-600" />
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-slate-800">{{ plan.name }}</span>
                                    <span class="text-indigo-700 font-bold">
                                        {{ plan.price_monthly === 0 ? 'Free' : 'PKR ' + plan.price_monthly + '/mo' }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 mt-0.5">{{ plan.description }}</p>
                                <div v-if="plan.features?.length" class="mt-2 flex flex-wrap gap-1.5">
                                    <span
                                        v-for="feat in (typeof plan.features === 'string' ? plan.features.split(',') : plan.features)"
                                        :key="feat"
                                        class="text-xs bg-slate-100 text-slate-600 rounded px-2 py-0.5"
                                    >{{ feat.trim() }}</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <p class="text-xs text-slate-400 mt-4 text-center">All plans include a 30-day free trial. No credit card required.</p>

                    <div v-if="form.errors.selected_plan" class="text-red-500 text-sm text-center mt-2">{{ form.errors.selected_plan }}</div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="prevStep" class="text-slate-600 px-6 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">← Back</button>
                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="submit"
                            class="bg-indigo-600 text-white px-8 py-2.5 rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50 transition"
                        >
                            <span v-if="form.processing">Submitting…</span>
                            <span v-else>Submit Registration</span>
                        </button>
                    </div>
                </div>

            </div>

            <p class="text-center text-sm text-slate-500 mt-6">
                Already have an account?
                <a :href="route('login')" class="text-indigo-600 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</template>
