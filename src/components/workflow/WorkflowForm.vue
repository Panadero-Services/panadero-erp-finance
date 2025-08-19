<!--
  Workflow Form Component
  @version 1.0.8
  @description Dynamic form for current workflow step
-->


<template>
  <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
    <div class="space-y-4">
      <div>
        <label :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
          Document Title
        </label>
        <input 
          type="text" 
          :style="{ fontSize: `${store.fontSizes.base}px` }"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
          placeholder="Enter document title...">
      </div>
      
      <div>
        <label :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
          Amount
        </label>
        <input 
          type="number" 
          :style="{ fontSize: `${store.fontSizes.base}px` }"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
          placeholder="0.00">
      </div>
      
      <div>
        <label :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
          Description
        </label>
        <textarea 
          rows="3" 
          :style="{ fontSize: `${store.fontSizes.base}px` }"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
          placeholder="Enter description..."></textarea>
      </div>
      
      <div>
        <label :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
          Category
        </label>
        <select :style="{ fontSize: `${store.fontSizes.base}px` }" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <option>General</option>
          <option>Operations</option>
          <option>Finance</option>
        </select>
      </div>
      
      <div>
        <label :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
          Priority
        </label>
        <div class="flex space-x-4">
          <label class="flex items-center">
            <input type="radio" name="priority" value="low" class="mr-2">
            <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-700 dark:text-gray-300">Low</span>
          </label>
          <label class="flex items-center">
            <input type="radio" name="priority" value="medium" class="mr-2" checked>
            <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-700 dark:text-gray-300">Medium</span>
          </label>
          <label class="flex items-center">
            <input type="radio" name="priority" value="high" class="mr-2">
            <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-700 dark:text-gray-300">High</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useFinanceStore } from '../../stores/financeStore.js'

// Store
const store = useFinanceStore()

// Props for dynamic form configuration
const props = defineProps({
  workflowStep: {
    type: Object,
    default: () => ({})
  },
  formData: {
    type: Object,
    default: () => ({})
  }
})

// Emits for form updates
const emit = defineEmits(['update:formData'])

// In a real implementation, this would generate forms based on JSON schema
// from the workflow step configuration
</script>