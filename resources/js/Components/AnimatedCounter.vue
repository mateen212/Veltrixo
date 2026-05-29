<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

const props = defineProps<{
    value: number
    duration?: number
    prefix?: string
    suffix?: string
    decimals?: number
}>()

const displayed = ref(0)

function animateTo(target: number) {
    const start    = displayed.value
    const duration = props.duration ?? 800
    const startAt  = performance.now()

    function step(now: number) {
        const elapsed  = now - startAt
        const progress = Math.min(elapsed / duration, 1)
        // Ease-out cubic
        const eased    = 1 - Math.pow(1 - progress, 3)
        displayed.value = start + (target - start) * eased
        if (progress < 1) requestAnimationFrame(step)
        else displayed.value = target
    }
    requestAnimationFrame(step)
}

onMounted(() => animateTo(props.value))
watch(() => props.value, (v) => animateTo(v))

function format(n: number) {
    return n.toLocaleString('en', {
        minimumFractionDigits: props.decimals ?? 0,
        maximumFractionDigits: props.decimals ?? 0,
    })
}
</script>

<template>
    <span class="tabular-nums">{{ prefix ?? '' }}{{ format(displayed) }}{{ suffix ?? '' }}</span>
</template>
