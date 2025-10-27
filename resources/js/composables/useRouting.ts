import { ref } from 'vue'
import L from 'leaflet'
import 'leaflet-routing-machine'
import { useClinicStore } from '@/stores/clinic'
import type { Ref } from 'vue'
import type { Clinic } from '@/types'

export function useRouting(mapRef: Ref<any>, userLocation: Ref<[number, number] | null>) {
  const routingControl = ref<any>(null)
  const selectedClinic = ref<Clinic | null>(null)
  const routeDistance = ref<number | null>(null)
  const straightLineDistance = ref<number | null>(null)
  const routeDuration = ref<number | null>(null)
  const isRouteLoading = ref<boolean>(false)

  const clinicStore = useClinicStore()

  const buildRoute = async(clinic: Clinic) => {
    if (!userLocation.value) {
      console.error('User location is not available to build route')

      return
    }

    isRouteLoading.value = true
    selectedClinic.value = clinic

    try {
      if (routingControl.value && mapRef.value) {
        mapRef.value.leafletObject.removeControl(routingControl.value)
        routingControl.value = null
      }

      const userLatLng = L.latLng(userLocation.value[0], userLocation.value[1])
      const clinicLatLng = L.latLng(
        clinic.location.coordinates[1],
        clinic.location.coordinates[0]
      )

      routingControl.value = L.Routing.control({
        waypoints: [userLatLng, clinicLatLng],
        routeWhileDragging: false,
        showAlternatives: false,
        fitSelectedRoutes: true,
        lineOptions: {
          styles: [{ color: '#6366F1', opacity: 0.8, weight: 6 }],
          extendToWaypoints: false,
          missingRouteTolerance: 0
        }
      }).addTo(mapRef.value.leafletObject)

      routingControl.value.on('routesfound', function(e: any) {
        const routes = e.routes
        const route = routes[0]
        routeDistance.value = route.summary.totalDistance / 1000 // to kilometers
        routeDuration.value = route.summary.totalTime / 60 // to minutes
      })

      clinicStore.getDistance(clinic).then((distance: number | null) => {
        if (distance) {
          straightLineDistance.value = distance
        }
      })

    } catch (error) {
      console.error('Error building route:', error)
    } finally {
      isRouteLoading.value = false
    }
  }

  const clearRoute = () => {
    if (routingControl.value && mapRef.value) {
      mapRef.value.leafletObject.removeControl(routingControl.value)
      routingControl.value = null
      selectedClinic.value = null
      routeDistance.value = null
      routeDuration.value = null
      straightLineDistance.value = null
    }
  }

  return {
    routingControl,
    selectedClinic,
    routeDistance,
    straightLineDistance,
    routeDuration,
    isRouteLoading,
    buildRoute,
    clearRoute
  }
}
