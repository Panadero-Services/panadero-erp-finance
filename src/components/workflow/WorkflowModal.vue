<script setup>
import { useFinanceStore } from '../../stores/financeStore.js'
import WorkflowStepper from './WorkflowStepper.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowInfo from './WorkflowInfo.vue'

// Store
const store = useFinanceStore()

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  activeWorkflow: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close'])
</script>


<!--
  Workflow Management Modal Component
  @version 1.0.8
  @description Large modal for active workflow management
-->
<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-95" @click="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-7xl h-5/6 mx-4 flex flex-col" @click.stop>
      
      <!-- Modal Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
            <i class="fas fa-cogs text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div>
            <h2 :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white">
              {{ activeWorkflow?.name }}
            </h2>
            <div class="flex items-center space-x-3 mt-1">
              <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-hashtag mr-1"></i>{{ activeWorkflow?.workflowNr }}
              </span>
              <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-2 py-1 font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                {{ activeWorkflow?.status }}
              </span>
            </div>
          </div>
        </div>
        <button @click="$emit('close')" :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Modal Content - 4 Columns -->
      <div class="flex-1 grid grid-cols-4 overflow-hidden w-full h-full">

        <!-- Column 1: Workflow Stepper -->
        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 border-r border-gray-200 dark:border-gray-600 overflow-y-auto">
          <h3 :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-list-ol mr-1 text-indigo-600"></i>
            Workflow Steps
          </h3>
          <WorkflowStepper />
        </div>

        <!-- Column 2: Form (spans 2 columns) -->
        <div class="col-span-2 p-6 overflow-y-auto max-h-full">
          <h3 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" class="font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-edit mr-2 text-green-600"></i>
            Current Step Form
          </h3>
          <WorkflowForm />
        </div>

        <!-- Column 3: Tabbed Info & History -->
        <div class="relative h-full">
          <div class="absolute top-0 left-0 right-0" style="height: 95%;">
            <div class="h-full overflow-y-auto">
              <WorkflowInfo :workflowData="activeWorkflow" />
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0" style="height: 5%;">
            <!-- Empty space (invisible) -->
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
        <div class="flex justify-between items-center">
          <div class="flex space-x-3">
            <button :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <i class="fas fa-pause mr-2"></i>
              Pause
            </button>
            <button :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
              <i class="fas fa-stop mr-2"></i>
              Cancel
            </button>
          </div>
          <div class="flex space-x-3">
            <button :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <i class="fas fa-arrow-left mr-2"></i>
              Previous
            </button>
            <button :style="{ fontSize: `${store.fontSizes.base}px` }" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <i class="fas fa-arrow-right mr-2"></i>
              Next Step
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
