import { ref } from 'vue'

export function useGeolocation() {
  const coords = ref<{ latitude: number; longitude: number } | null>(null)
  const isSupported = 'geolocation' in navigator
  const isLoading = ref(false)
  const error = ref<GeolocationPositionError | null>(null)

  const getCurrentPosition = () => {
    if (!isSupported) {
      console.error('Geolocation is not supported by this browser.')

      return
    }

    isLoading.value = true
    error.value = null

    navigator.geolocation.getCurrentPosition(
      position => {
        coords.value = position.coords
        isLoading.value = false
      },
      err => {
        error.value = err
        isLoading.value = false
      }
    )
  }

  return { coords, isSupported, isLoading, error, getCurrentPosition }
}
