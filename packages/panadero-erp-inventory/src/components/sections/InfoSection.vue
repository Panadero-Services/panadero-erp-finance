<script setup>
import { ref, computed } from 'vue'
import { useInventoryStore } from '../../stores/inventoryStore.js'
import { useScaling } from 'panadero-shared-styling'
import { useInventoryInfoBoxes } from '../../composables/useInventoryInfoBoxes.js'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()
const { infoBoxes } = useInventoryInfoBoxes()

const selectedInfoBox = ref('inventory-overview')

const currentInfoBox = computed(() => {
  return infoBoxes.value.find(box => box.id === selectedInfoBox.value) || infoBoxes.value[0]
})
</script>

<template>
  <div class="info-section">
    <!-- Header -->
    <div class="mb-8">
      <h2 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        ERP Inventory Module
      </h2>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        Comprehensive inventory management with stock tracking, warehousing, and supply chain management
      </p>
    </div>

    <!-- Info Box Selector -->
    <div class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <button
          v-for="box in infoBoxes"
          :key="box.id"
          @click="selectedInfoBox = box.id"
          :class="[
            'p-4 rounded-lg border-2 transition-all duration-200 text-left',
            selectedInfoBox === box.id
              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
              : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'
          ]"
        >
          <div class="flex items-center mb-2">
            <i :class="[box.icon, `text-${box.color}-500`]" :style="scalingStyles.iconSize" class="mr-3"></i>
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">{{ box.title }}</h3>
          </div>
          <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ box.description }}</p>
        </button>
      </div>
    </div>

    <!-- Selected Info Box Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center mb-4">
        <i :class="[currentInfoBox.icon, `text-${currentInfoBox.color}-500`]" :style="scalingStyles.iconSize" class="mr-3"></i>
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">
          {{ currentInfoBox.title }}
        </h3>
      </div>
      
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400 mb-6">
        {{ currentInfoBox.description }}
      </p>

      <!-- Stats -->
      <div v-if="currentInfoBox.stats" class="mb-6">
        <h4 :style="scalingStyles.subtitleFontSize" class="font-medium text-gray-900 dark:text-white mb-3">
          Current Statistics
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="stat in currentInfoBox.stats"
            :key="stat.label"
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3"
          >
            <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ stat.label }}</p>
            <p :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">{{ stat.value }}</p>
          </div>
        </div>
      </div>

      <!-- Features -->
      <div v-if="currentInfoBox.features">
        <h4 :style="scalingStyles.subtitleFontSize" class="font-medium text-gray-900 dark:text-white mb-3">
          Key Features
        </h4>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
          <li
            v-for="feature in currentInfoBox.features"
            :key="feature"
            :style="scalingStyles.smallFontSize"
            class="flex items-center text-gray-600 dark:text-gray-400"
          >
            <i class="fas fa-check text-green-500 mr-2"></i>
            {{ feature }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.info-section {
  padding: 0;
}
</style>
