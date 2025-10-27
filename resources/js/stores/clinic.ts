import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'
import clinicRoutes from '@/routes/clinics'
import type { Clinic, ClinicForm } from '@/types'

interface ClinicState {
  userLat: number | null
  userLng: number | null
  radius: number
  clinics: Clinic[]
  isGeolocationLoading: boolean
  geolocationError: string | null
  processing: boolean
  errors: { name?: string[] }
}

const DEFAULT_LAT = -8.38
const DEFAULT_LNG = -46.45
const DEFAULT_RADIUS = 10000

// @ts-expect-error
export const useClinicStore = defineStore('clinic', {
  persist: {
    paths: ['userLat', 'userLng', 'radius']
  },

  state: (): ClinicState => ({
    userLat: DEFAULT_LAT,
    userLng: DEFAULT_LNG,
    radius: DEFAULT_RADIUS,
    clinics: [],
    isGeolocationLoading: false,
    geolocationError: null,
    processing: false,
    errors: {}
  }),

  getters: {
    userLocation: (state): [number, number] | null => {
      if (state.userLat && state.userLng) {
        return [state.userLat, state.userLng]
      }

      return null
    }
  },

  actions: {
    setClinics(clinics: Clinic[]) {
      this.clinics = clinics
    },
    addClinic(clinic: Clinic) {
      this.clinics.push(clinic)
    },
    resetFilters() {
      router.get(clinicRoutes.index().url)
    },
    async autocomplete(query: string): Promise<Clinic[]> {
      const { data } = await this.$axios.get<Clinic[]>(clinicRoutes.autocomplete().url, {
        params: { query }
      })

      return data
    },
    async getDistance(clinic: Clinic): Promise<number | null> {
      if (!this.userLocation) return null

      try {
        const response = await this.$axios.get('/distance', {
          params: {
            lat1: this.userLocation[0],
            lng1: this.userLocation[1],
            lat2: clinic.location.coordinates[1],
            lng2: clinic.location.coordinates[0]
          }
        })

        return response.data.distance ?? null
      } catch (error) {
        console.warn('Could not get distance from backend', error)

        return null
      }
    },
    getUserLocation(): Promise<void> {
      return new Promise((resolve, reject) => {
        if (!('geolocation' in navigator)) {
          this.geolocationError = 'Geolocation is not supported.'
          this.isGeolocationLoading = false

          return reject(new Error(this.geolocationError))
        }

        this.isGeolocationLoading = true
        this.geolocationError = null

        navigator.geolocation.getCurrentPosition(
          position => {
            this.userLat = position.coords.latitude
            this.userLng = position.coords.longitude
            this.isGeolocationLoading = false
            resolve()
          },
          err => {
            this.geolocationError = err.message
            this.isGeolocationLoading = false
            reject(err)
          }
        )
      })
    },
    fetchNearbyClinics() {
      if (!this.userLocation) {
        console.error('User location is not available to fetch nearby clinics.')

        return
      }

      router.get(
        clinicRoutes.index().url,
        {
          lat: this.userLat,
          lng: this.userLng,
          radius: this.radius
        },
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: page => {
            // @ts-expect-error
            this.setClinics(page.props.clinics)
          }
        }
      )
    },
    async createClinic(form: ClinicForm ) {
      this.processing = true
      this.errors = {}
      try {
        const { data } = await this.$axios.post(clinicRoutes.store().url, form)
        this.addClinic(data)

        return data
      } catch (error: any) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors
        }

        throw error
      } finally {
        this.processing = false
      }
    }
  }
})
