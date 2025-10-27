<script setup lang="ts">
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useClinicStore } from '@/stores/clinic'

const props = defineProps<{
  lat: number,
  lng: number,
}>()

const emit = defineEmits<{
  (e: 'success', clinic: any): void,
  (e: 'cancel'): void,
}>()

const clinicStore = useClinicStore()
const { processing, errors } = storeToRefs(clinicStore)

const name = ref('')

const submit = async() => {
  try {
    const clinic = await clinicStore.createClinic({
      name: name.value,
      location: {
        type: 'Point',
        coordinates: [props.lng, props.lat]
      }
    })
    emit('success', clinic)
  } catch (error) {
    console.error('Error creating clinic:', error)
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div class="grid gap-4 py-4">
      <div class="grid grid-cols-4 items-center gap-4">
        <Label
          for="name"
          class="text-right"
        >
          Name
        </Label>
        <Input
          id="name"
          v-model="name"
          class="col-span-3"
          :class="{ 'border-red-500': errors.name }"
        />
        <p
          v-if="errors.name"
          class="col-span-4 text-right text-red-500 text-sm"
        >
          {{ errors.name[0] }}
        </p>
      </div>

      <div class="grid grid-cols-4 items-center gap-4">
        <Label
          for="lat"
          class="text-right"
        >
          Latitude
        </Label>
        <Input
          id="lat"
          :model-value="props.lat"
          class="col-span-3"
          disabled
        />
      </div>

      <div class="grid grid-cols-4 items-center gap-4">
        <Label
          for="lng"
          class="text-right"
        >
          Longitude
        </Label>
        <Input
          id="lng"
          :model-value="props.lng"
          class="col-span-3"
          disabled
        />
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <Button
        type="button"
        variant="outline"
        @click="emit('cancel')"
      >
        Cancel
      </Button>
      <Button
        type="submit"
        :disabled="processing"
      >
        Create
      </Button>
    </div>
  </form>
</template>
