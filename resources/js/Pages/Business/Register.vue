<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { router, useForm, Head, Link } from '@inertiajs/vue3'

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
    form.latitude !== null && form.longitude !== null &&
    !form.errors.business_name &&
    !form.errors.subdomain &&
    !form.errors.latitude &&
    !form.errors.longitude
)
const step2Valid = computed(() =>
    form.owner_name.trim().length > 0 &&
    form.owner_email.trim().length > 0 &&
    form.password.trim().length >= 8 &&
    form.password === form.password_confirmation &&
    !form.errors.owner_name &&
    !form.errors.owner_email &&
    !form.errors.password &&
    !form.errors.password_confirmation
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

    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between relative overflow-hidden"
            style="background: linear-gradient(135deg, #1a1a3e 0%, #0f0f2e 50%, #0c0c1d 100%);">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/3 -left-24 w-96 h-96 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #4F46E5 0%, transparent 70%);" />
                <div class="absolute bottom-1/4 right-0 w-64 h-64 rounded-full opacity-15"
                    style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%);" />
            </div>

            <div class="relative z-10 p-12 flex flex-col h-full">
                <Link href="/" class="flex items-center gap-2.5 w-fit">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl font-bold text-xl text-white"
                        style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                    <span class="text-xl font-bold text-white font-display tracking-tight">Veltrixo</span>
                </Link>

                <div class="flex-1 flex flex-col justify-center">
                    <div class="max-w-sm">
                        <h1 class="text-4xl font-bold text-white leading-tight font-display">
                            Launch your<br/>
                            <span style="background: linear-gradient(135deg, #818CF8, #A78BFA); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                delivery business.
                            </span>
                        </h1>
                        <p class="mt-5 text-white/50 text-base leading-relaxed">
                            Set up your business in minutes. Get recurring subscriptions, rider dispatch, and built-in payments.
                        </p>

                        <ul class="mt-10 space-y-3.5">
                            <li v-for="item in [
                                'Recurring delivery subscriptions',
                                'Rider dispatch & live tracking',
                                'Built-in wallet & invoicing',
                                'No credit card required',
                            ]" :key="item" class="flex items-center gap-3">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-500/20 shrink-0">
                                    <svg class="h-3 w-3 text-brand-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-white/60">{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <p class="text-sm text-white/25 relative z-10">© {{ new Date().getFullYear() }} Veltrixo. All rights reserved.</p>
            </div>
        </div>

        <!-- Form panel -->
        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-10 lg:px-16 bg-white overflow-y-auto">
            <Link href="/" class="flex items-center gap-2 mb-10 lg:hidden w-fit">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold text-lg"
                    style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">V</div>
                <span class="text-xl font-bold tracking-tight text-ink font-display">Veltrixo</span>
            </Link>

            <div class="w-full  mx-auto">
                <h2 class="text-2xl font-bold text-ink font-display">Register your business</h2>
                <p class="mt-1.5 text-sm text-ink-muted">
                    Already have an account?
                    <Link :href="route('login')" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Sign in</Link>
                </p>

                <!-- Step indicators -->
                <div class="flex items-center justify-center gap-2 mt-6 mb-8">
                    <template v-for="n in totalSteps" :key="n">
                        <div
                            class="h-2 rounded-full transition-all duration-300"
                            :class="[
                                n <= step ? 'bg-brand-500' : 'bg-border-muted',
                                n === step ? 'w-8' : 'w-4',
                            ]"
                        />
                    </template>
                </div>

                <!-- ── Step 1: Business Details + Map ── -->
                <div v-if="step === 1">
                    <h3 class="text-lg font-semibold text-ink mb-5">Business Details & Location</h3>

                    <div class="space-y-5">
                        <div>
                            <label class="field-label">Business Name</label>
                            <input
                                v-model="form.business_name"
                                type="text"
                                placeholder="e.g. Fresh Daily Milk Co."
                                :class="['field-input', form.errors.business_name && 'field-input-error']"
                            />
                            <p v-if="form.errors.business_name" class="field-error">{{ form.errors.business_name }}</p>
                        </div>

                        <!-- Subdomain field with live preview -->
                        <div>
                            <label class="field-label">Your Business Portal URL</label>
                            <div class="flex items-center border border-border-strong rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/30 transition">
                                <input
                                    v-model="form.subdomain"
                                    @input="form.subdomainManuallyEdited = true"
                                    type="text"
                                    placeholder="your-business"
                                    class="flex-1 px-4 py-2.5 outline-none text-ink lowercase bg-white"
                                    maxlength="63"
                                />
                                <span class="px-3 py-2.5 bg-surface-secondary text-ink-muted text-sm border-l border-border-strong select-none">{{ 'veltrixo.test' }}</span>
                            </div>
                            <p v-if="form.subdomain" class="text-brand-600 text-xs mt-1.5">
                                Portal URL: <span class="font-semibold">{{ form.subdomain }}.veltrixo.test</span>
                            </p>
                            <p v-if="form.errors.subdomain" class="field-error">{{ form.errors.subdomain }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="field-label">Address</label>
                                <input v-model="form.business_address" type="text" placeholder="Street address" :class="['field-input', form.errors.business_address && 'field-input-error']" />
                            </div>
                            <div>
                                <label class="field-label">City</label>
                                <input v-model="form.city" type="text" placeholder="e.g. Lahore" :class="['field-input', form.errors.city && 'field-input-error']" />
                            </div>
                        </div>

                        <div>
                            <label class="field-label">Area / Neighbourhood</label>
                            <input v-model="form.area" type="text" placeholder="e.g. DHA Phase 5" :class="['field-input', form.errors.area && 'field-input-error']" />
                        </div>

                        <!-- Map -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="field-label mb-0">Pin Your Business Location</label>
                                <button
                                    type="button"
                                    @click="detectLocation"
                                    class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors flex items-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Auto-detect
                                </button>
                            </div>
                            <div ref="mapContainer" class="w-full h-64 rounded-lg border border-border-strong overflow-hidden" />
                            <p v-if="locationStatus" class="text-xs text-ink-muted mt-1.5">{{ locationStatus }}</p>
                            <p v-if="form.latitude" class="text-xs text-emerald-600 mt-1.5">
                                ✓ Selected: {{ form.latitude?.toFixed(5) }}, {{ form.longitude?.toFixed(5) }}
                            </p>
                            <p v-if="!form.latitude && form.errors.latitude" class="field-error">Please select a location on the map.</p>
                        </div>

                        <!-- Delivery radius -->
                        <div>
                            <label class="field-label">
                                Delivery Radius: <span class="text-brand-600 font-semibold">{{ form.delivery_radius_km }} km</span>
                            </label>
                            <input
                                v-model.number="form.delivery_radius_km"
                                type="range"
                                min="1"
                                max="50"
                                @input="onRadiusChange"
                                class="w-full accent-brand-600"
                            />
                            <div class="flex justify-between text-xs text-ink-muted mt-1">
                                <span>1 km</span><span>50 km</span>
                            </div>
                            <p class="text-xs text-ink-muted mt-1.5">Customers outside this radius cannot subscribe to your service.</p>
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 mt-8">
                        <button
                            type="button"
                            @click="prevStep"
                            v-if="step > 1"
                            class="flex-1 h-10 btn btn-secondary"
                        >
                            ← Back
                        </button>
                        <button
                            type="button"
                            :disabled="!step1Valid || form.processing"
                            @click="nextStep"
                            class="flex-1 h-10 btn btn-primary"
                        >
                            Next →
                        </button>
                    </div>
                </div>

                <!-- ── Step 2: Owner Details ── -->
                <div v-if="step === 2">
                    <h3 class="text-lg font-semibold text-ink mb-5">Owner Information</h3>

                    <div class="space-y-5">
                        <div>
                            <label class="field-label">Full Name</label>
                            <input v-model="form.owner_name" type="text" placeholder="Muhammad Ali" :class="['field-input', form.errors.owner_name && 'field-input-error']" />
                            <p v-if="form.errors.owner_name" class="field-error">{{ form.errors.owner_name }}</p>
                        </div>

                        <div>
                            <label class="field-label">Email Address</label>
                            <input v-model="form.owner_email" type="email" placeholder="ali@freshco.pk" :class="['field-input', form.errors.owner_email && 'field-input-error']" />
                            <p v-if="form.errors.owner_email" class="field-error">{{ form.errors.owner_email }}</p>
                        </div>

                        <div>
                            <label class="field-label">Phone</label>
                            <input v-model="form.owner_phone" type="tel" placeholder="+92 300 0000000" :class="['field-input', form.errors.owner_phone && 'field-input-error']" />
                            <p v-if="form.errors.owner_phone" class="field-error">{{ form.errors.owner_phone }}</p>
                        </div>

                        <div>
                            <label class="field-label">Password</label>
                            <input v-model="form.password" type="password" placeholder="Min. 8 characters (with letters & numbers)" :class="['field-input', form.errors.password && 'field-input-error']" />
                            <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                            <p v-else class="text-xs text-ink-muted mt-1.5">Must be at least 8 characters with letters and numbers</p>
                        </div>

                        <div>
                            <label class="field-label">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" placeholder="Repeat password" :class="['field-input', form.errors.password_confirmation && 'field-input-error']" />
                            <p v-if="form.errors.password_confirmation" class="field-error">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label class="field-label">Business Logo (optional)</label>
                            <div class="flex items-center gap-4">
                                <img v-if="logoPreview" :src="logoPreview" class="h-16 w-16 rounded-full object-cover border border-border-strong" />
                                <input type="file" accept="image/*" @change="onLogoChange" class="text-sm text-ink-muted" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 mt-8">
                        <button type="button" @click="prevStep" class="flex-1 h-10 btn btn-secondary">← Back</button>
                        <button
                            type="button"
                            :disabled="!step2Valid || form.processing"
                            @click="nextStep"
                            class="flex-1 btn btn-primary"
                        >
                            Next →
                        </button>
                    </div>
                </div>

                <!-- ── Step 3: Plan selection ── -->
                <div v-if="step === 3">
                    <h3 class="text-lg font-semibold text-ink mb-5">Choose a Plan</h3>

                    <div class="space-y-3">
                        <label
                            v-for="plan in plans"
                            :key="plan.slug"
                            class="flex items-start gap-4 border rounded-xl p-4 cursor-pointer transition"
                            :class="form.selected_plan === plan.slug ? 'border-brand-500 bg-brand-50/30' : 'border-border-muted hover:border-border-strong'"
                        >
                            <input type="radio" :value="plan.slug" v-model="form.selected_plan" class="mt-1 accent-brand-600" />
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-ink">{{ plan.name }}</span>
                                    <span class="text-brand-700 font-bold">
                                        {{ plan.price_monthly === 0 ? 'Free' : 'PKR ' + plan.price_monthly + '/mo' }}
                                    </span>
                                </div>
                                <p class="text-sm text-ink-muted mt-0.5">{{ plan.description }}</p>
                                <div v-if="plan.features?.length" class="mt-2 flex flex-wrap gap-1.5">
                                    <span
                                        v-for="feat in (typeof plan.features === 'string' ? plan.features.split(',') : plan.features)"
                                        :key="feat"
                                        class="text-xs bg-surface-secondary text-ink-secondary rounded px-2 py-0.5"
                                    >{{ feat.trim() }}</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <p class="text-xs text-ink-muted mt-5 text-center">All plans include a 30-day free trial. No credit card required.</p>

                    <div v-if="form.errors.selected_plan" class="text-red-500 text-sm text-center mt-2">{{ form.errors.selected_plan }}</div>

                    <div class="flex justify-between gap-3 mt-8">
                        <button type="button" @click="prevStep" class="flex-1 btn btn-secondary">← Back</button>
                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="submit"
                            class="flex-1 btn btn-primary"
                        >
                            <span v-if="form.processing">Submitting…</span>
                            <span v-else>Submit Registration</span>
                        </button>
                    </div>
                </div>

            </div>

            <p class="text-center text-sm text-ink-muted mt-6">
                Already have an account?
                <Link :href="route('login')" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Sign in</Link>
            </p>
        </div>
    </div>
</template>
