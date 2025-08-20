<!--
  Workflow Stepper Component
  @version 1.0.8
  @description Vertical stepper for workflow steps with tooltips
-->
<script setup>
import { useFinanceStore } from '../../stores/financeStore.js'

// Store
const store = useFinanceStore()
</script>

<template>
  <div class="space-y-4">
    <!-- Step 1: Information Collection - COMPLETED -->
    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
      <div class="flex items-center space-x-3 mb-2">
        <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
          1
        </div>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-medium text-gray-900 dark:text-white">Information Collection</span>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Gather basic vendor information</p>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-green-600 dark:text-green-400 mt-1">
        <i class="fas fa-clock mr-1"></i>
        Completed: 17-8-2025 14:32
      </p>
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
        Gather basic vendor information and documents (~30 minutes)
      </div>
    </div>
          
    <!-- Step 2: Due Diligence - COMPLETED -->
    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
      <div class="flex items-center space-x-3 mb-2">
        <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
          2
        </div>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-medium text-gray-900 dark:text-white">Due Diligence</span>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Verify credentials and background</p>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-green-600 dark:text-green-400 mt-1">
        <i class="fas fa-clock mr-1"></i>
        Completed: 17-8-2025 16:45
      </p>
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
        Verify credentials and conduct background checks (~2-3 business days, procurement_manager)
      </div>
    </div>

    <!-- Step 3: Contract Negotiation - ACTIVE -->
    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors relative group">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-3">
          <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
            3
          </div>
          <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-medium text-gray-900 dark:text-white">Contract Negotiation</span>
        </div>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-2 py-1 font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full">
          ACTIVE
        </span>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Negotiate terms and agreement</p>
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
        Negotiate terms and execute binding agreement (~1-2 weeks, legal_counsel)
      </div>
    </div>

    <!-- Step 4: Final Approval - PENDING -->
    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-colors relative group">
      <div class="flex items-center space-x-3 mb-2">
        <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-xs font-medium">
          4
        </div>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-medium text-gray-400 dark:text-gray-500">Final Approval</span>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-400 dark:text-gray-500">Executive sign-off and contract execution</p>
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
        Executive sign-off and contract execution (~2-3 business days, ceo, cfo)
      </div>
    </div>

    <!-- Step 5: Complete - PENDING -->
    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-colors relative group">
      <div class="flex items-center space-x-3 mb-2">
        <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-xs font-medium">
          5
        </div>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-medium text-gray-400 dark:text-gray-500">Complete</span>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-400 dark:text-gray-500">Vendor onboarding completed</p>
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
        Vendor onboarding completed (Immediate)
      </div>
    </div>
  </div>
</template>