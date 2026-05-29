<script setup lang="ts" generic="T extends Record<string, any>">
import { ref, computed } from 'vue'
import SkeletonLoader from './SkeletonLoader.vue'
import EmptyState from './EmptyState.vue'

const props = defineProps<{
    columns: Array<{ key: string; label: string; sortable?: boolean; width?: string }>
    rows: T[]
    loading?: boolean
    selectable?: boolean
    emptyMessage?: string
    emptyIcon?: string
    stickyHeader?: boolean
    emptyAction?: string
    emptyActionHref?: string
}>()

const emit = defineEmits<{
    sort:        [key: string, dir: 'asc' | 'desc']
    select:      [ids: number[]]
    emptyAction: []
}>()

const selected = ref<number[]>([])
const sortKey  = ref('')
const sortDir  = ref<'asc' | 'desc'>('asc')

const allSelected = computed(() =>
    props.rows.length > 0 && props.rows.every(r => selected.value.includes(r.id))
)

function toggleAll() {
    selected.value = allSelected.value ? [] : props.rows.map(r => r.id)
    emit('select', selected.value)
}
function toggleRow(id: number) {
    const idx = selected.value.indexOf(id)
    if (idx === -1) selected.value.push(id)
    else selected.value.splice(idx, 1)
    emit('select', selected.value)
}
function setSort(key: string) {
    if (!props.columns.find(c => c.key === key)?.sortable) return
    if (sortKey.value === key) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    else { sortKey.value = key; sortDir.value = 'asc' }
    emit('sort', key, sortDir.value)
}

defineExpose({ selected, clearSelection: () => { selected.value = [] } })
</script>

<template>
    <div class="relative">
        <!-- Loading overlay -->
        <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0"
            leave-active-class="transition duration-100" leave-to-class="opacity-0">
            <div v-if="loading && rows.length"
                class="absolute inset-0 z-10 bg-white/80 backdrop-blur-[2px] flex items-center justify-center rounded-b-xl">
                <svg class="animate-spin h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
        </Transition>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead :class="stickyHeader ? 'sticky top-0 z-10' : ''">
                    <tr class="border-b border-border-muted bg-surface-muted/60">
                        <th v-if="selectable" class="w-10 px-4 py-3">
                            <input type="checkbox" :checked="allSelected" @change="toggleAll"
                                class="h-4 w-4 rounded border-border-strong text-brand-600 focus:ring-brand-500/30 cursor-pointer" />
                        </th>
                        <th v-for="col in columns" :key="col.key"
                            :style="col.width ? `width:${col.width}` : ''"
                            :class="[
                                'px-4 py-3 text-left text-[11px] font-semibold text-ink-faint uppercase tracking-wider whitespace-nowrap',
                                col.sortable ? 'cursor-pointer select-none hover:text-ink-secondary transition-colors' : '',
                            ]"
                            @click="setSort(col.key)">
                            <span class="inline-flex items-center gap-1.5">
                                {{ col.label }}
                                <span v-if="col.sortable" class="flex flex-col gap-px">
                                    <svg :class="['h-2 w-2 transition-colors', sortKey===col.key&&sortDir==='asc'?'text-brand-500':'text-border-strong']" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l5 5H5l5-5z"/></svg>
                                    <svg :class="['h-2 w-2 transition-colors', sortKey===col.key&&sortDir==='desc'?'text-brand-500':'text-border-strong']" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5-5h10l-5 5z"/></svg>
                                </span>
                            </span>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Skeleton rows while loading with no data -->
                    <template v-if="loading && !rows.length">
                        <tr v-for="i in 5" :key="i" class="border-b border-border-muted last:border-0">
                            <td v-if="selectable" class="px-4 py-3.5"><div class="skeleton h-4 w-4 rounded" /></td>
                            <td v-for="col in columns" :key="col.key" class="px-4 py-3.5">
                                <div class="skeleton h-4 rounded" :style="{ width: col.width ? '70%' : (Math.random() > 0.5 ? '75%' : '55%') }" />
                            </td>
                        </tr>
                    </template>

                    <!-- Data rows -->
                    <template v-else-if="rows.length">
                        <tr v-for="row in rows" :key="row.id"
                            :class="[
                                'border-b border-border-muted last:border-0 transition-colors duration-100',
                                selected.includes(row.id) ? 'bg-brand-50/40' : 'hover:bg-surface-muted/40',
                            ]">
                            <td v-if="selectable" class="px-4 py-3.5">
                                <input type="checkbox" :checked="selected.includes(row.id)" @change="toggleRow(row.id)"
                                    class="h-4 w-4 rounded border-border-strong text-brand-600 focus:ring-brand-500/30 cursor-pointer" />
                            </td>
                            <slot :row="row" />
                        </tr>
                    </template>

                    <!-- Empty state -->
                    <tr v-else>
                        <td :colspan="columns.length + (selectable ? 1 : 0)">
                            <EmptyState
                                :icon="emptyIcon"
                                :title="emptyMessage ?? 'No records found'"
                                description="Try adjusting your filters or create a new entry."
                                :action="emptyAction"
                                :action-href="emptyActionHref"
                                size="sm"
                                @action="emit('emptyAction')"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
