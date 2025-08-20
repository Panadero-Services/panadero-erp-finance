<!--
  Workflow Info Component
  @version 1.0.8
  @description Tabbed workflow information and history
-->
<script setup>
import { ref } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'

// Store
const store = useFinanceStore()

// Props
const props = defineProps({
  workflowData: {
    type: Object,
    default: () => ({})
  }
})

// Local state
const activeTab = ref('info')

// Utility function
function getModuleDisplayName(module) {
  if (!module) return 'General'
  
  switch (module) {
    case 'gl': return 'General Ledger'
    case 'ap': return 'Accounts Payable'
    case 'ar': return 'Accounts Receivable'
    case 'cf': return 'Cash Flow'
    case 'procurement': return 'Procurement'
    default: return module.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
  }
}
</script>

<template>
  <div class="bg-gray-50 dark:bg-gray-700/30 flex flex-col overflow-hidden h-full">
    <!-- Tab Headers -->
    <div class="flex border-b border-gray-200 dark:border-gray-600 flex-shrink-0">
      <button 
        @click="activeTab = 'info'"
        :class="[
          'flex-1 px-4 py-3 font-medium transition-colors',
          activeTab === 'info' 
            ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
        ]"
        :style="{ fontSize: `${store.fontSizes.base - 2}px` }">
        <i class="fas fa-info-circle mr-2"></i>
        Workflow Info
      </button>
      <button 
        @click="activeTab = 'history'"
        :class="[
          'flex-1 px-4 py-3 font-medium transition-colors',
          activeTab === 'history' 
            ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
        ]"
        :style="{ fontSize: `${store.fontSizes.base - 2}px` }">
        <i class="fas fa-history mr-2"></i>
        History
      </button>
    </div>

    <!-- Tab Content -->
    <div class="flex-1 overflow-y-auto min-h-0">
      
      <!-- Workflow Info Tab -->
      <div v-if="activeTab === 'info'" class="p-4 h-full overflow-y-auto">
        <div class="space-y-4">
          
          <!-- Workflow Information -->
          <div>
            <h4 :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-info-circle mr-2 text-blue-600"></i>
              Vendor Onboarding Workflow
            </h4>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
              <p class="mb-3"><strong>Purpose:</strong> Complete vendor registration and verification process for new suppliers.</p>
              <p class="mb-3"><strong>Scope:</strong> This workflow covers information collection, due diligence checks, contract negotiation, and final approval.</p>
              <p><strong>Duration:</strong> Typically 2-3 weeks depending on vendor complexity and response times.</p>
            </div>
          </div>

          <!-- Current Step Instructions -->
          <div>
            <h4 :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-tasks mr-2 text-green-600"></i>
              Current Step: Contract Negotiation
            </h4>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
              <p class="mb-2"><strong>Instructions:</strong></p>
              <ul class="list-disc pl-5 space-y-1">
                <li>Review vendor's initial contract proposal</li>
                <li>Negotiate terms including payment schedules, delivery terms, and SLAs</li>
                <li>Ensure compliance with company procurement policies</li>
                <li>Coordinate with Legal team for contract review</li>
                <li>Document all negotiation points and agreements</li>
              </ul>
              <p class="mt-3 text-orange-600"><strong>⚠️ Important:</strong> All contract changes must be approved by Legal before proceeding.</p>
            </div>
          </div>

          <!-- Required Approvals -->
          <div>
            <h4 :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-user-check mr-2 text-purple-600"></i>
              Required Approvals
            </h4>
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
              <div class="grid grid-cols-1 gap-2 text-sm">
                <div class="flex justify-between">
                  <span>Legal Counsel:</span>
                  <span class="text-orange-600 font-medium">Pending</span>
                </div>
                <div class="flex justify-between">
                  <span>Procurement Director:</span>
                  <span class="text-orange-600 font-medium">Pending</span>
                </div>
                <div class="flex justify-between">
                  <span>Finance Manager:</span>
                  <span class="text-green-600 font-medium">Approved</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- History Tab -->
      <div v-if="activeTab === 'history'" class="p-4 h-full overflow-y-auto">
        <div class="space-y-4">
          <h4 :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
            <i class="fas fa-history mr-2 text-indigo-600"></i>
            Workflow History
          </h4>
          
          <!-- History Log -->
          <div class="bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4 font-mono text-sm">
            <div class="space-y-2">
              <div class="text-green-600 dark:text-green-400">
                19-8-2025, 12:28:28 created by demo_user
              </div>
              <div class="text-green-600 dark:text-green-400">
                19-8-2025, 12:28:28 Step 1 completed
              </div>
              <div class="text-green-600 dark:text-green-400">
                19-8-2025, 16:45:22 Step 2 completed
              </div>
              <div class="text-blue-600 dark:text-blue-400">
                19-8-2025, 17:00:00 Step 3 started
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
