<!--
  Workflow Info Component
  @version 1.0.9
  @description Workflow information display (simplified, no tabs or history)
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
const settings = useWorkflowSettings()

// Props
const props = defineProps({
  workflowData: {
    type: Object,
    default: () => ({})
  }
})

// Computed properties
const currentStep = computed(() => {
  if (props.workflowData?.steps && props.workflowData.steps.length > 0) {
    // For now, return the first step - this would be dynamic based on workflow state
    return props.workflowData.steps[0]
  }
  return null
})

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
    <!-- Direct Content - No Tabs -->
    <div class="flex-1 overflow-y-auto min-h-0">
      <div class="p-4 h-full overflow-y-auto">
        <div class="space-y-4">
          
          <!-- Workflow Information -->
          <div>
            <h4 :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-info-circle mr-2 text-blue-600"></i>
              {{ workflowData?.name || 'Workflow Information' }}
            </h4>
            <div :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
              <p v-if="workflowData?.description" class="mb-3"><strong>Description:</strong> {{ workflowData.description }}</p>
              <p v-if="workflowData?.module" class="mb-3"><strong>Module:</strong> {{ getModuleDisplayName(workflowData.module) }}</p>
              <p v-if="workflowData?.category" class="mb-3"><strong>Category:</strong> {{ workflowData.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
              <p v-if="workflowData?.estimated_duration"><strong>Duration:</strong> {{ workflowData.estimated_duration }}</p>
              <p v-if="workflowData?.complexity" class="mb-3"><strong>Complexity:</strong> {{ workflowData.complexity.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
              
              <!-- Data not available indicator -->
              <div v-if="!workflowData?.description && !workflowData?.module" class="text-center py-4">
                <i class="fas fa-info-circle text-gray-400 text-xl mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  Workflow information not available from config file
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                  Using store-based workflow template
                </p>
              </div>
            </div>
          </div>

          <!-- Current Step Instructions -->
          <div v-if="currentStep">
            <h4 :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-tasks mr-2 text-green-600"></i>
              Current Step: {{ currentStep.name }}
            </h4>
            <div :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
              <p v-if="currentStep.description" class="mb-3"><strong>Description:</strong> {{ currentStep.description }}</p>
              
              <div v-if="currentStep.instructions && currentStep.instructions.length > 0">
                <p class="mb-2"><strong>Instructions:</strong></p>
                <ul class="list-disc pl-5 space-y-1">
                  <li v-for="instruction in currentStep.instructions" :key="instruction">{{ instruction }}</li>
                </ul>
              </div>
              
              <div v-if="currentStep.notes && currentStep.notes.length > 0" class="mt-3">
                <div v-for="note in currentStep.notes" :key="note.type" :class="[
                  'p-2 rounded',
                  note.type === 'warning' ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300' : 
                  note.type === 'info' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300' :
                  'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                ]">
                  <strong>{{ note.type === 'warning' ? '⚠️ Warning:' : note.type === 'info' ? 'ℹ️ Info:' : 'Note:' }}</strong> {{ note.message }}
                </div>
              </div>
              
              <!-- Step data not available indicator -->
              <div v-if="!currentStep.description && !currentStep.instructions" class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-gray-400 text-xl mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  Step instructions not available from config file
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                  Check workflow configuration for step details
                </p>
              </div>
            </div>
          </div>

          <!-- No Current Step Available -->
          <div v-else-if="workflowData?.steps">
            <h4 :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-tasks mr-2 text-gray-600"></i>
              Current Step
            </h4>
            <div :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
              <i class="fas fa-question-circle text-gray-400 text-xl mb-2"></i>
              <p class="text-gray-500 dark:text-gray-400 text-sm">
                No current step available
              </p>
              <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                Workflow may not be started or step index is invalid
              </p>
            </div>
          </div>

          <!-- Required Approvals -->
          <div v-if="currentStep?.approvalRequired || props.workflowData?.approval_levels > 0">
            <h4 :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="font-semibold text-gray-900 dark:text-white mb-3">
              <i class="fas fa-user-check mr-2 text-purple-600"></i>
              Required Approvals
            </h4>
            <div :style="{ fontSize: `${settings.fontSizesComputed.caption}px` }" class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
              
              <!-- Dynamic approvals from current step -->
              <div v-if="currentStep?.approvals && currentStep.approvals.length > 0" class="grid grid-cols-1 gap-2">
                <div v-for="approval in currentStep.approvals" :key="approval.role" class="flex justify-between">
                  <span>{{ approval.role }}:</span>
                  <span :class="[
                    'font-medium',
                    approval.status === 'approved' ? 'text-green-600' :
                    approval.status === 'rejected' ? 'text-red-600' :
                    'text-orange-600'
                  ]">
                    {{ approval.status === 'approved' ? 'Approved' : 
                        approval.status === 'rejected' ? 'Rejected' : 'Pending' }}
                  </span>
                </div>
              </div>
              
              <!-- Workflow-level approval info -->
              <div v-else-if="props.workflowData?.approval_levels > 0">
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                  This workflow requires <strong>{{ props.workflowData.approval_levels }}</strong> approval level(s).
                </p>
                <div v-if="props.workflowData?.notifications?.approval_required" class="text-sm text-blue-600 dark:text-blue-400">
                  <i class="fas fa-bell mr-1"></i>
                  Notifications will be sent to: {{ props.workflowData.notifications.approval_required.join(', ') }}
                </div>
              </div>
              
              <!-- Approval data not available indicator -->
              <div v-else class="text-center py-4">
                <i class="fas fa-user-clock text-gray-400 text-xl mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  Approval information not available from config file
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                  Check workflow configuration for approval details
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
