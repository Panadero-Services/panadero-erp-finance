<!--
  Workflow Database Submit Component
  @version 1.1.0
  @description Handles database submit step types in workflows - step type specific
-->
<script setup>
import { ref, computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  step: {
    type: Object,
    required: true
  },
  stepData: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['update-step-data', 'step-completed'])

// Composables
const settings = useWorkflowSettings()

// State
const isSubmitting = ref(false)
const submissionStatus = ref(props.stepData.submissionStatus || 'pending')
const submissionDetails = ref(props.stepData.submissionDetails || {})

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)
const _body = computed(() => `${settings.fontSizesComputed.value.body}px`)
const _bodySmall = computed(() => `${settings.fontSizesComputed.value.bodySmall}px`)

// Computed
const canSubmit = computed(() => {
  return submissionStatus.value === 'pending' && !isSubmitting.value
})

const statusConfig = computed(() => {
  switch (submissionStatus.value) {
    case 'pending':
      return { 
        icon: 'fas fa-clock', 
        color: 'text-yellow-600', 
        bgColor: 'bg-yellow-100 dark:bg-yellow-900/30',
        text: 'Pending Submission'
      }
    case 'submitting':
      return { 
        icon: 'fas fa-spinner fa-spin', 
        color: 'text-blue-600', 
        bgColor: 'bg-blue-100 dark:bg-blue-900/30',
        text: 'Submitting...'
      }
    case 'success':
      return { 
        icon: 'fas fa-check-circle', 
        color: 'text-green-600', 
        bgColor: 'bg-green-100 dark:bg-green-900/30',
        text: 'Successfully Submitted'
      }
    case 'error':
      return { 
        icon: 'fas fa-exclamation-triangle', 
        color: 'text-red-600', 
        bgColor: 'bg-red-100 dark:bg-red-900/30',
        text: 'Submission Failed'
      }
    default:
      return { 
        icon: 'fas fa-question-circle', 
        color: 'text-gray-600', 
        bgColor: 'bg-gray-100 dark:bg-gray-700',
        text: 'Unknown Status'
      }
  }
})

// Methods
async function submitToDatabase() {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  submissionStatus.value = 'submitting'
  
  try {
    // Simulate database submission
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Mock successful submission
    submissionStatus.value = 'success'
    submissionDetails.value = {
      submittedAt: new Date().toISOString(),
      recordId: `DB_${Date.now()}`,
      table: props.step.databaseTable || 'workflow_data',
      status: 'active'
    }
    
    // Emit completion
    emit('step-completed', {
      stepId: props.step.id,
      submissionDetails: submissionDetails.value,
      completedAt: new Date().toISOString()
    })
    
    // Update step data
    emit('update-step-data', props.step.id, {
      submissionStatus: submissionStatus.value,
      submissionDetails: submissionDetails.value,
      completed: true,
      completedAt: new Date().toISOString()
    })
    
  } catch (error) {
    console.error('Database submission failed:', error)
    submissionStatus.value = 'error'
    submissionDetails.value = {
      error: error.message,
      failedAt: new Date().toISOString()
    }
    
    // Update step data with error
    emit('update-step-data', props.step.id, {
      submissionStatus: submissionStatus.value,
      submissionDetails: submissionDetails.value
    })
  } finally {
    isSubmitting.value = false
  }
}

function resetSubmission() {
  submissionStatus.value = 'pending'
  submissionDetails.value = {}
  emit('update-step-data', props.step.id, {
    submissionStatus: submissionStatus.value,
    submissionDetails: submissionDetails.value
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Database Submit Information -->
    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h4 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white mb-3">
        <i class="fas fa-database text-indigo-600 mr-2"></i>
        Database Submission Required
      </h4>
      
      <div class="space-y-3">
        <div v-if="props.step.description" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
        
        <div v-if="props.step.databaseTable" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">
            <i class="fas fa-table mr-1"></i>
            Target Table: <span class="font-mono text-indigo-600">{{ props.step.databaseTable }}</span>
          </p>
        </div>
        
        <div v-if="props.step.databaseOperation" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">
            <i class="fas fa-cogs mr-1"></i>
            Operation: <span class="font-mono text-indigo-600">{{ props.step.databaseOperation }}</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Submission Status -->
    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
        Submission Status
      </h5>
      
      <div :class="['p-3 rounded-lg border-2', statusConfig.bgColor, 'border-current']">
        <div class="flex items-center space-x-3">
          <i :class="[statusConfig.icon, statusConfig.color, 'text-xl']"></i>
          <div class="flex-1">
            <h6 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white">
              {{ statusConfig.text }}
            </h6>
            <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
              {{ props.step.databaseTable || 'workflow_data' }} table
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Submission Details -->
    <div v-if="submissionDetails && Object.keys(submissionDetails).length > 0" 
         class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i class="fas fa-file-alt mr-2 text-green-600"></i>
        Submission Details
      </h5>
      
      <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
        <dl class="space-y-2">
          <div v-if="submissionDetails.recordId" class="flex justify-between">
            <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Record ID:</dt>
            <dd :style="{ fontSize: _bodySmall }" class="font-mono text-gray-900 dark:text-white">{{ submissionDetails.recordId }}</dd>
          </div>
          <div v-if="submissionDetails.submittedAt" class="flex justify-between">
            <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Submitted:</dt>
            <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ new Date(submissionDetails.submittedAt).toLocaleString() }}</dd>
          </div>
          <div v-if="submissionDetails.table" class="flex justify-between">
            <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Table:</dt>
            <dd :style="{ fontSize: _bodySmall }" class="font-mono text-gray-900 dark:text-white">{{ submissionDetails.table }}</dd>
          </div>
          <div v-if="submissionDetails.status" class="flex justify-between">
            <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Status:</dt>
            <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ submissionDetails.status }}</dd>
          </div>
          <div v-if="submissionDetails.error" class="flex justify-between">
            <dt :style="{ fontSize: _bodySmall }" class="text-red-500">Error:</dt>
            <dd :style="{ fontSize: _bodySmall }" class="text-red-600 dark:text-red-400">{{ submissionDetails.error }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
      <button
        v-if="submissionStatus === 'success' || submissionStatus === 'error'"
        @click="resetSubmission"
        class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        :style="{ fontSize: _bodySmall }"
      >
        <i class="fas fa-redo mr-2"></i>
        Reset
      </button>
      
      <button
        @click="submitToDatabase"
        :disabled="!canSubmit"
        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-database mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit to Database' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
