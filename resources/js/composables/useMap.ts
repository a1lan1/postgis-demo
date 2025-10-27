import { ref } from 'vue'
import type { LatLngExpression } from 'leaflet'

export function useMap() {
  const mapRef = ref<any>(null)
  const zoom = ref<number>(12)
  const defaultCenter: LatLngExpression = [-8.38, -46.45]

  return {
    mapRef,
    zoom,
    defaultCenter
  }
}
