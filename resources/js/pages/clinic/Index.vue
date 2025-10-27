<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import ClinicsMap from '@/components/clinic/ClinicsMap.vue'
</script>

<template>
  <Head title="Clinics" />

  <main class="flex w-full">
    <ClinicsMap />
  </main>
</template>
