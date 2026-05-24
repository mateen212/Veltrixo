<script setup lang="ts" generic="T extends Record<string, any>">
import { ref, computed } from 'vue'
import AppButton from './AppButton.vue'

const props = defineProps<{
    columns: Array<{ key: string; label: string; sortable?: boolean; width?: string }>
    rows: T[]
    loading?: boolean
    selectable?: boolean
    emptyMessage?: string
    emptyIcon?: string
    stickyHeader?: boolean
}>()

const emit = defineEmits<{
    sort: [key: string, dir: 'asc' | 'desc']
    select: [ids: number[]]
}>()

const selected    = ref<number[]>([])
const sortKey     = ref('')
const sortDir     = ref<'asc' | 'desc'>('asc')

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
        <div v-if="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 backdrop-blur-sm rounded-xl">
            <div class="flex flex-col items-center gap-2">
                <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="text-sm text-gray-500">Loading…</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead :class="stickyHeader ? 'sticky top-0 z-10' : ''">
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th v-if="selectable" class="w-10 px-4 py-3">
                            <input type="checkbox" :checked="allSelected" @change="toggleAll"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
                        </th>
                        <th v-for="col in columns" :key="col.key"
                            :style="col.width ? `width:${col.width}` : ''"
                            :class="[
                                'px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap',
                                col.sortable ? 'cursor-pointer select-none hover:text-gray-800' : '',
                            ]"
                            @click="setSort(col.key)"
                        >
                            <span class="flex items-center gap-1">
                                {{ col.label }}
                                <span v-if="col.sortable" class="ml-0.5">
                                    <svg v-if="sortKey === col.key && sortDir === 'asc'"    class="h-3 w-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/></svg>
                                    <svg v-else-if="sortKey === col.key && sortDir === 'desc'" class="h-3 w-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L15 6.414V12z"/></svg>
                                    <svg v-else class="h-3 w-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/></svg>
                                </span>
                            </span>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50 bg-white">
                    <template v-if="rows.length">
                        <tr v-for="row in rows" :key="row.id"
                            :class="[
                                'group transition-colors',
                                selected.includes(row.id) ? 'bg-indigo-50/50' : 'hover:bg-gray-50/80',
                            ]">
                            <td v-if="selectable" class="px-4 py-3.5">
                                <input type="checkbox" :checked="selected.includes(row.id)"
                                    @change="toggleRow(row.id)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
                            </td>
                            <slot :row="row" />
                        </tr>
                    </template>
                    <tr v-else>
                        <td :colspan="columns.length + (selectable ? 1 : 0)" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <span class="text-3xl">{{ emptyIcon ?? '📭' }}</span>
                                <p class="text-sm font-medium text-gray-500">{{ emptyMessage ?? 'No records found' }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
