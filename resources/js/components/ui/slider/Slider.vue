<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue: number
        min?: number
        max?: number
        step?: number
    }>(),
    {
        min: 0,
        max: 100,
        step: 1,
    },
)

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void
}>()

const value = computed({
    get: () => props.modelValue,
    set: (newValue: number) => {
        emit('update:modelValue', newValue)
    },
})
</script>

<template>
    <div class="slider-container">
        <input
            v-model.number="value"
            type="range"
            :min="min"
            :max="max"
            :step="step"
            class="slider"
        />
    </div>
</template>

<style scoped>
.slider-container {
    display: flex;
    align-items: center;
    width: 100%;
}

.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 8px;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}
</style>
