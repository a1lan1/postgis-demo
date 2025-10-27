<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useClinicStore } from '@/stores/clinic'
import { Button } from '@/components/ui/button'
import { Slider } from '@/components/ui/slider'

const clinicStore = useClinicStore()
const { radius, isGeolocationLoading } = storeToRefs(clinicStore)

const emit = defineEmits(['fetch-clinics', 'clear-route', 'reset-filters'])

withDefaults(
  defineProps<{
    selectedClinic: boolean
  }>(),
  {
    selectedClinic: false
  }
)
</script>

<template>
  <div class="control-buttons-container">
    <Button
      variant="secondary"
      :disabled="isGeolocationLoading"
      class="control-button"
      @click="emit('fetch-clinics')"
    >
      {{ isGeolocationLoading ? 'Getting Location...' : 'Nearby clinics' }}
    </Button>

    <Button
      v-if="selectedClinic"
      variant="outline"
      class="control-button"
      @click="emit('clear-route')"
    >
      Clear route
    </Button>

    <Button
      variant="destructive"
      class="control-button"
      @click="emit('reset-filters')"
    >
      Reset
    </Button>

    <div class="slider-wrapper">
      <Slider
        v-model="radius"
        :min="500"
        :max="50000"
        :step="100"
      />
      <span class="radius-label">Radius: {{ (radius / 1000).toFixed(1) }} km</span>
    </div>
  </div>
</template>

<style scoped>
.control-buttons-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999 !important;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: rgba(255, 255, 255, 0.8);
  padding: 10px;
  border-radius: 8px;
}

.control-button {
  cursor: pointer;
}

.slider-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
  width: 200px;
}

.radius-label {
  font-size: 12px;
  color: #333;
}
</style>
