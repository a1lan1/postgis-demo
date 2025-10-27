<script setup lang="ts">
import { onMounted, watch, ref, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { icon } from 'leaflet'
import { useClinicStore } from '@/stores/clinic'
import { useDebounceFn } from '@vueuse/core'
import { useMap } from '@/composables/useMap'
import { useRouting } from '@/composables/useRouting'
import type { Clinic } from '@/types'
import RouteInfoPanel from '@/components/clinic/RouteInfoPanel.vue'
import MapControlPanel from '@/components/clinic/MapControlPanel.vue'
import Autocomplete from '@/components/ui/autocomplete/Autocomplete.vue'
import CreateClinicForm from '@/components/clinic/CreateClinicForm.vue'
import { LMap, LTileLayer, LMarker, LPopup, LCircle } from '@vue-leaflet/vue-leaflet'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'

const { mapRef, zoom, defaultCenter } = useMap()

const clinicStore = useClinicStore()
const { getUserLocation, fetchNearbyClinics, autocomplete, resetFilters, addClinic } = clinicStore
const { userLocation, radius, clinics } = storeToRefs(clinicStore)

const { selectedClinic, routeDistance, straightLineDistance, routeDuration, isRouteLoading, buildRoute, clearRoute } = useRouting(mapRef, userLocation)

const newClinicLat = ref<number>(0)
const newClinicLng = ref<number>(0)
const isClinicSelected = ref(false)
const isCreateClinicDialogOpen = ref(false)

const iconUser = icon({
  iconUrl: '/img/marker-icon-red.png',
  iconSize: [25, 41],
  iconAnchor: [12, 40]
})
const iconClinic = icon({
  iconUrl: '/img/marker-icon-blue.png',
  iconSize: [25, 41],
  iconAnchor: [12, 40]
})
const iconDelivery = icon({
  iconUrl: '/img/marker-icon-delivery.png',
  iconSize: [64, 64],
  iconAnchor: [12, 40]
})
const iconFlag = icon({
  iconUrl: '/img/marker-icon-flag.png',
  iconSize: [50, 50],
  iconAnchor: [12, 40]
})

const userMarker = computed(() => selectedClinic.value ? iconDelivery : iconUser)

const showFormDialog = (event: any) => {
  newClinicLat.value = event.latlng.lat
  newClinicLng.value = event.latlng.lng
  isCreateClinicDialogOpen.value = true
}

const handleClinicCreated = (clinic: Clinic) => {
  addClinic(clinic)
  isCreateClinicDialogOpen.value = false
}

const handleFetchClinics = async() => {
  if (!userLocation.value) {
    try {
      await getUserLocation()
    } catch (error) {
      console.error('Failed to get user location:', error)

      return
    }
  }
  fetchNearbyClinics()
}

onMounted(() => {
  if (!userLocation.value) {
    getUserLocation().catch((error: any) => {
      console.warn('Could not automatically get user location:', error)
    })
  }
})

const debouncedFetch = useDebounceFn(() => {
  if (userLocation.value) {
    fetchNearbyClinics()
  }
}, 500)

const handleAutocompleteSearch = async(query: string): Promise<Clinic[]> => {
  if (isClinicSelected.value) {
    isClinicSelected.value = false

    return []
  }

  return autocomplete(query)
}

const handleClinicSelect = (clinic: Clinic) => {
  isClinicSelected.value = true
  buildRoute(clinic)
}

watch(radius, debouncedFetch)

watch(userLocation, () => {
  if (selectedClinic.value) {
    buildRoute(selectedClinic.value)
  }
})

watch(isCreateClinicDialogOpen, (newVal) => {
  if (!newVal) {
    newClinicLat.value = 0
    newClinicLng.value = 0
  }
})
</script>

<template>
  <div style="height: 100vh; width: 100vw">
    <Autocomplete
      :search="handleAutocompleteSearch"
      name="search"
      autocomplete="search"
      placeholder="Search for a clinic"
      class="search-autocomplete"
      @select="handleClinicSelect"
    />

    <MapControlPanel
      :selected-clinic="!!selectedClinic"
      @fetch-clinics="handleFetchClinics"
      @clear-route="clearRoute"
      @reset-filters="resetFilters"
    />

    <RouteInfoPanel
      :selected-clinic="selectedClinic"
      :route-distance="routeDistance"
      :straight-line-distance="straightLineDistance"
      :route-duration="routeDuration"
    />

    <div
      v-if="isRouteLoading"
      class="route-loading"
    >
      Building route...
    </div>

    <LMap
      ref="mapRef"
      v-model:zoom="zoom"
      :center="userLocation || defaultCenter"
      @dblclick="showFormDialog"
    >
      <LTileLayer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        attribution="&copy; OpenStreetMap contributors"
      />

      <LCircle
        v-if="userLocation"
        :lat-lng="userLocation"
        :radius="radius"
        color="blue"
        fill-color="blue"
        :fill-opacity="0.1"
      />

      <LMarker
        v-if="userLocation"
        :lat-lng="userLocation"
        :icon="userMarker"
      >
        <LPopup>You are here</LPopup>
      </LMarker>

      <LMarker
        v-if="newClinicLat && newClinicLng"
        :lat-lng="[newClinicLat, newClinicLng]"
        :icon="iconFlag"
      >
        <LPopup>New Clinic</LPopup>
      </LMarker>

      <LMarker
        v-for="clinic in clinics"
        :key="clinic.id"
        :lat-lng="[clinic.location.coordinates[1], clinic.location.coordinates[0]]"
        :icon="iconClinic"
        @click="buildRoute(clinic)"
      >
        <LPopup>
          <strong>{{ clinic.name }}</strong><br>
          {{ clinic.location.coordinates[1].toFixed(4) }},
          {{ clinic.location.coordinates[0].toFixed(4) }}
        </LPopup>
      </LMarker>
    </LMap>

    <Dialog v-model:open="isCreateClinicDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Add a new clinic</DialogTitle>
        </DialogHeader>
        <CreateClinicForm
          :lat="newClinicLat"
          :lng="newClinicLng"
          @success="handleClinicCreated"
          @cancel="isCreateClinicDialogOpen = false"
        />
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
:deep(div.leaflet-control-attribution) {
  display: none !important;
}

.search-autocomplete {
  position: absolute;
  top: 20px;
  left: 50%;
  right: 50%;
  z-index: 9999 !important;
  width: 450px;
  transform: translateX(-50%);
}

:deep(.leaflet-routing-container) {
  right: 240px;
  background-color: white;
  color: var(--color-black);
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);

  tr {
    cursor: pointer;
  }
}

.route-loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999 !important;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
</style>
