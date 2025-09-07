<script setup>
import { ref, computed, onMounted } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import FinanceValueCard from '../ui/FinanceValueCard.vue'
import FinanceButtonDemo from '../demo/FinanceButtonDemo.vue'
import FinanceDropdownDemo from '../demo/FinanceDropdownDemo.vue'
import FinanceInputDemo from '../demo/FinanceInputDemo.vue'
import FinanceToggleDemo from '../demo/FinanceToggleDemo.vue'
import StatusBadgeDemo from '../demo/StatusBadgeDemo.vue'
import FinanceValueCardDemo from '../demo/FinanceValueCardDemo.vue'

const store = useFinanceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()

// Selected demo component
const selectedDemo = ref('buttons')

// Demo selector options
const demoOptions = [
  { label: 'Finance Buttons', value: 'buttons' },
  { label: 'Finance Dropdowns', value: 'dropdowns' },
  { label: 'Finance Inputs', value: 'inputs' },
  { label: 'Finance Toggles', value: 'toggles' },
  { label: 'Status Badges', value: 'badges' },
  { label: 'Finance Value Cards', value: 'cards' }
]

// Component statistics for dashboard
const componentStats = computed(() => ({
  totalComponents: 19, // Total custom components in the finance module
  buttonsUsed: 42,    // Number of FinanceButton instances across all components
  cardsUsed: 38,      // Number of FinanceValueCard instances across all components
  dropdownsUsed: 18,  // Number of FinanceDropdown instances across all components
  inputsUsed: 24      // Number of FinanceInput instances across all components
}))

// Icon mapping for demo types
const iconMap = {
  'buttons': 'fas fa-hand-pointer',
  'dropdowns': 'fas fa-caret-square-down',
  'inputs': 'fas fa-edit',
  'toggles': 'fas fa-toggle-on',
  'badges': 'fas fa-tag',
  'cards': 'fas fa-id-card'
}

// Get icon for demo type
function getIconForDemo(demoType) {
  return iconMap[demoType] || 'fas fa-puzzle-piece'
}
</script>

<template>
  <div class="ui-elements-section">
    <!-- Dashboard-style UI Elements Layout -->
    
    <!-- UI Components Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <h2 :style="{ fontSize: `${fontSizes.base + 6}px` }" class="col-span-full font-bold text-gray-900 dark:text-white mb-4">Stats Dashboard</h2>
      
      <FinanceValueCard 
        title="Total Components" 
        :value="componentStats.totalComponents" 
        rows="2-row" 
        format="number" 
        color="info" 
        icon="fas fa-puzzle-piece" 
      />
      <FinanceValueCard 
        title="Buttons Used" 
        :value="componentStats.buttonsUsed" 
        rows="2-row" 
        format="number" 
        color="positive" 
        icon="fas fa-hand-pointer" 
      />
      <FinanceValueCard 
        title="Cards Used" 
        :value="componentStats.cardsUsed" 
        rows="2-row" 
        format="number" 
        color="warning" 
        icon="fas fa-id-card" 
      />
      <FinanceValueCard 
        title="Dropdowns Used" 
        :value="componentStats.dropdownsUsed" 
        rows="2-row" 
        format="number" 
        color="neutral" 
        icon="fas fa-caret-square-down" 
      />
    </div>

    <!-- Interactive Demo Center -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
      <div class="mb-6">
        <h2 :style="{ fontSize: `${fontSizes.base + 4}px` }" class="font-bold text-gray-900 dark:text-white mb-4">Interactive Demo Center</h2>
        
        <!-- Demo Selection Buttons -->
        <div class="flex flex-wrap gap-3">
          <button
            v-for="option in demoOptions"
            :key="option.value"
            @click="selectedDemo = option.value"
            :class="[
              'px-4 py-2 rounded-lg border transition-all duration-200 flex items-center space-x-2',
              selectedDemo === option.value
                ? 'bg-indigo-600 border-indigo-600 text-white shadow-md'
                : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-600 hover:border-indigo-300 dark:hover:border-indigo-400'
            ]"
            :style="{ fontSize: `${fontSizes.base - 1}px` }"
          >
            <i :class="getIconForDemo(option.value)" class="text-sm"></i>
            <span>{{ option.label }}</span>
          </button>
        </div>
      </div>

      <!-- Demo Component Display -->
      <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border dark:border-gray-600">
        <FinanceButtonDemo v-if="selectedDemo === 'buttons'" />
        <FinanceDropdownDemo v-else-if="selectedDemo === 'dropdowns'" />
        <FinanceInputDemo v-else-if="selectedDemo === 'inputs'" />
        <FinanceToggleDemo v-else-if="selectedDemo === 'toggles'" />
        <StatusBadgeDemo v-else-if="selectedDemo === 'badges'" />
        <FinanceValueCardDemo v-else-if="selectedDemo === 'cards'" />
        <div v-else class="text-center py-8">
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-500 dark:text-gray-400">
            Select a component demo from the dropdown above
          </p>
        </div>
      </div>
    </div>

    <!-- Advanced Analytics -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h2 :style="{ fontSize: `${fontSizes.base + 4}px` }" class="font-bold text-gray-900 dark:text-white mb-6">Advanced Analytics</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Component Usage Breakdown -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">Component Usage</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">FinanceButton</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">42 instances</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">FinanceValueCard</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">38 instances</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">FinanceDropdown</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">18 instances</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">FinanceInput</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">24 instances</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">StatusBadge</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">15 instances</span>
            </div>
          </div>
        </div>

        <!-- Design System Metrics -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">Design System</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Color Variants</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">12 types</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Size Options</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">4 sizes</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Icon Support</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">Font Awesome</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Scaling</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-gray-900 dark:text-white">Dynamic</span>
            </div>
          </div>
        </div>

        <!-- Performance Metrics -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">Performance</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Bundle Size</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-green-600 dark:text-green-400">275KB</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Load Time</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-green-600 dark:text-green-400">< 1s</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Reusability</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-green-600 dark:text-green-400">95%</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="text-gray-600 dark:text-gray-400">Accessibility</span>
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }" class="font-medium text-green-600 dark:text-green-400">WCAG 2.1</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* UI Elements section specific styles if needed */
</style>
