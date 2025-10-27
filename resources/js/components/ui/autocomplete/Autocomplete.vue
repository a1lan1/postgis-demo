<script setup lang="ts">
import { ref, watch, useAttrs } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import type { Clinic } from "@/types"
import Input from '@/components/ui/input/Input.vue'


const props = withDefaults(
  defineProps<{
    search: (query: string) => Promise<Clinic[]>
    delay?: number
    minLength?: number
  }>(),
  {
    delay: 300,
    minLength: 2,
  },
)

const emit = defineEmits<{
  (e: 'select', payload: Clinic): void
}>()


const searchTerm = ref('')
const results = ref<Clinic[]>([])
const isLoading = ref(false)
const isDropdownOpen = ref(false)

const searchHandler = useDebounceFn(async (query: string) => {
  if (query.length < props.minLength) {
    results.value = []
    isDropdownOpen.value = false
    return
  }

  isLoading.value = true
  isDropdownOpen.value = true
  try {
    results.value = await props.search(query)
  }
  catch (error) {
    console.error('Autocomplete search error:', error)
    results.value = []
  }
  finally {
    isLoading.value = false
  }
}, props.delay)

watch(searchTerm, (newQuery) => {
  searchHandler(newQuery)
})

function selectItem(item: Clinic) {
  searchTerm.value = item.name
  isDropdownOpen.value = false
  emit('select', item)
}
</script>

<template>
  <div class="relative">
    <Input
      v-model="searchTerm"
      v-bind="useAttrs()"
      @focus="isDropdownOpen = results.length > 0"
      @blur="isDropdownOpen = false"
    />

    <div
      v-if="isDropdownOpen && (isLoading || results.length > 0)"
      class="absolute z-10 mt-1 w-full rounded-md border border-gray-300 bg-white shadow-lg text-black"
    >
      <div v-if="isLoading" class="px-4 py-2">
        Loading...
      </div>
      <ul v-else>
        <li
          v-for="item in results"
          :key="item.id"
          class="cursor-pointer px-4 py-2 hover:bg-gray-100 "
          @mousedown="selectItem(item)"
        >
          {{ item.name }}
        </li>
      </ul>
    </div>
  </div>
</template>
