<script setup lang="ts">
import type { Clinic } from '@/types'

withDefaults(
  defineProps<{
    selectedClinic: Clinic | null,
    routeDistance: number | null,
    straightLineDistance: number | null,
    routeDuration: number | null
  }>(),
  {
    selectedClinic: null,
    routeDistance: null,
    straightLineDistance: null,
    routeDuration: null
  }
)
</script>

<template>
  <div
    v-if="selectedClinic && (routeDistance || straightLineDistance)"
    class="route-info"
  >
    <h3>Route to {{ selectedClinic.name }}</h3>
    <p v-if="routeDistance">
      Distance by road: {{ routeDistance.toFixed(2) }} km
    </p>
    <p v-if="straightLineDistance">
      Distance by straight line: {{ straightLineDistance.toFixed(2) }} km
    </p>
    <p v-if="routeDuration">
      Duration: {{ Math.round(routeDuration) }} min
    </p>
  </div>
</template>

<style scoped>
.route-info {
  position: absolute;
  bottom: 20px;
  left: 20px;
  z-index: 9999 !important;
  background-color: white;
  color: var(--color-black);
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 300px;
}
</style>
