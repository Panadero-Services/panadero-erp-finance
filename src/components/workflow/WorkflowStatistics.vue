<!--
  Workflow Statistics Component
  @version 1.0.8
  @description Reusable workflow statistics cards with dynamic scaling
-->
<script setup>
import { ref } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'

const store = useFinanceStore()

// Props
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalWorkflows: 12,
      activeWorkflows: 5,
      completedWorkflows: 7,
      templatesAvailable: 8,
      averageCompletionTime: '2.3 days',
      pendingApprovals: 3
    })
  }
})
</script>

<template>
  <!-- Statistics Cards -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Total Workflows
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white">
            {{ stats.totalWorkflows }}
          </div>
        </div>
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
          <i class="fas fa-sitemap text-blue-600 dark:text-blue-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Active Workflows
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-green-600 dark:text-green-400">
            {{ stats.activeWorkflows }}
          </div>
        </div>
        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <i class="fas fa-play-circle text-green-600 dark:text-green-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Pending Approvals
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-orange-600 dark:text-orange-400">
            {{ stats.pendingApprovals }}
          </div>
        </div>
        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
          <i class="fas fa-clock text-orange-600 dark:text-orange-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Completed Workflows
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-purple-600 dark:text-purple-400">
            {{ stats.completedWorkflows }}
          </div>
        </div>
        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
          <i class="fas fa-check-circle text-purple-600 dark:text-purple-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Templates Available
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-indigo-600 dark:text-indigo-400">
            {{ stats.templatesAvailable }}
          </div>
        </div>
        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
          <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
            Avg. Completion Time
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white">
            {{ stats.averageCompletionTime }}
          </div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <i class="fas fa-stopwatch text-gray-600 dark:text-gray-400" :style="store.scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>
  </div>
</template>
