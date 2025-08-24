<script setup>
import { ref, computed, onMounted, nextTick, toRaw, shallowRef } from 'vue'
import { useWorkflowStore } from './composables/workflowStore.js'
import { useWorkflowSettings } from './composables/useWorkflowSettings.js'
import WorkflowStepper from './components/WorkflowStepper.vue'
import WorkflowForm from './components/WorkflowForm.vue'
// REMOVED: import FinanceButton from '../../components/ui/FinanceButton.vue'
// REMOVED: import FinanceValueCard from '../../components/ui/FinanceValueCard.vue'
// REMOVED: import StatusBadge from '../../components/ui/StatusBadge.vue'

const props = defineProps({
  workflowId: {
    type: String,
    default: null
  },
  mode: {
    type: String,
    default: 'create', // 'create', 'view', 'edit'
    validator: value => ['create', 'view', 'edit'].includes(value)
  },
  templateId: {
    type: String,
    default: 'vendor-onboarding'
  }
})

const emit = defineEmits(['workflow-created', 'workflow-updated', 'workflow-completed'])

const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

// Component state
const isLoading = ref(false)
const currentWorkflow = ref(null)
const currentStepData = ref({})
const showApprovalModal = ref(false)
const approvalComments = ref('')
const validationErrors = ref({})

// UI state
const activeTab = ref('overview') // 'overview', 'progress', 'history', 'approvals'

// Get workflow instance - use shallowRef to avoid deep reactivity
const workflow = shallowRef(null)

// Manual update function to avoid circular dependencies
const updateWorkflow = () => {
  try {
    if (props.workflowId) {
      // Access store data directly to avoid circular dependencies
      const instances = toRaw(workflowStore.workflowInstances) || []
      if (!Array.isArray(instances)) {
        workflow.value = null
        return
      }
      const instance = instances.find(w => w && w.id === props.workflowId)
      workflow.value = instance ? { ...instance } : null // shallow copy to break reactivity
    } else {
      workflow.value = currentWorkflow.value ? { ...currentWorkflow.value } : null
    }
  } catch (error) {
    console.warn('Error accessing workflow:', error)
    workflow.value = null
  }
}

// Simple update without debouncing since we control when it's called

// Get current step
const currentStep = computed(() => {
  try {
    const wf = workflow.value
    if (!wf || !wf.steps || typeof wf.current_step !== 'number') return null
    const step = wf.steps[wf.current_step]
    return step ? toRaw(step) : null
  } catch (error) {
    console.warn('Error accessing current step:', error)
    return null
  }
})

// Get template
const template = computed(() => {
  const templates = workflowStore.builtInTemplates
  if (!Array.isArray(templates)) return null
  return templates.find(t => t && t.id === props.templateId) || null
})

// Check if user can approve current step
const canApprove = computed(() => {
  const step = currentStep.value
  if (!step || !step.approval_required) return false
  if (step.status !== workflowStore.STEP_STATES.REQUIRES_APPROVAL) return false
  
  // In a real app, you'd check user roles against step.approver_roles
  return true
})

// Workflow statistics
const workflowStats = computed(() => {
  try {
    const wf = workflow.value
    if (!wf || !Array.isArray(wf.steps)) {
      return { completed: 0, total: 0, progress: 0 }
    }
    
    const completed = wf.steps.filter(s => 
      s && (s.status === workflowStore.STEP_STATES.COMPLETED || 
            s.status === workflowStore.STEP_STATES.APPROVED)
    ).length
    
    const total = wf.steps.length
    const progress = total > 0 ? Math.round((completed / total) * 100) : 0
    
    return { completed, total, progress }
  } catch (error) {
    console.warn('Error computing workflow stats:', error)
    return { completed: 0, total: 0, progress: 0 }
  }
})

// Initialize workflow
onMounted(async () => {
  // Initial workflow update
  updateWorkflow()
  
  if (props.mode === 'create') {
    await createNewWorkflow()
  } else if (props.workflowId) {
    // Don't set currentWorkflow when we have workflowId - let manual update handle it
    const existingWorkflow = workflowStore.getWorkflowById(props.workflowId)
    if (existingWorkflow) {
      const step = existingWorkflow.steps[existingWorkflow.current_step]
      if (step && step.data) {
        currentStepData.value = { ...step.data }
      }
    }
  }
  
  // Manual updates only when needed - no watchers!
})

// Create new workflow
const createNewWorkflow = async () => {
  try {
    isLoading.value = true
    
    const instance = workflowStore.createWorkflowInstance(props.templateId, {
      created_by: 'current_user' // In real app, get from auth
    })
    
    // Start the workflow first
    const startedWorkflow = workflowStore.startWorkflow(instance.id)
    
    // Set local state only for local workflows (no workflowId prop)
    if (!props.workflowId) {
      await nextTick()
      currentWorkflow.value = startedWorkflow
    }
    
    // Manually update workflow display
    await nextTick()
    updateWorkflow()
    
    emit('workflow-created', startedWorkflow)
    
  } catch (error) {
    console.error('Failed to create workflow:', error)
  } finally {
    isLoading.value = false
  }
}

// Complete current step
const completeCurrentStep = async () => {
  const wf = workflow.value
  const step = currentStep.value
  if (!wf || !step) return
  
  try {
    isLoading.value = true
    validationErrors.value = {}
    
    const updatedWorkflow = workflowStore.completeStep(
      wf.id,
      wf.current_step,
      currentStepData.value,
      'current_user' // In real app, get from auth
    )
    
    // Only update if we're managing the workflow locally
    if (!props.workflowId) {
      await nextTick()
      currentWorkflow.value = updatedWorkflow
    }
    
    // Reset step data for next step
    await nextTick()
    if (updatedWorkflow.status === workflowStore.WORKFLOW_STATES.ACTIVE) {
      currentStepData.value = {}
    }
    
    // Check if workflow is completed
    await nextTick()
    if (updatedWorkflow.status === workflowStore.WORKFLOW_STATES.COMPLETED) {
      emit('workflow-completed', updatedWorkflow)
    } else {
      emit('workflow-updated', updatedWorkflow)
    }
    
  } catch (error) {
    console.error('Failed to complete step:', error)
    // Parse validation errors if they exist
    if (error.message.includes('Validation failed:')) {
      const errorMessage = error.message.replace('Validation failed: ', '')
      validationErrors.value = { general: errorMessage }
    }
  } finally {
    isLoading.value = false
  }
}

// Approve current step
const approveCurrentStep = async () => {
  const wf = workflow.value
  const step = currentStep.value
  if (!wf || !step) return
  
  try {
    isLoading.value = true
    
    const updatedWorkflow = workflowStore.approveStep(
      wf.id,
      wf.current_step,
      'current_user', // In real app, get from auth
      approvalComments.value
    )
    
    // Only update if we're managing the workflow locally
    if (!props.workflowId) {
      await nextTick()
      currentWorkflow.value = updatedWorkflow
    }
    
    showApprovalModal.value = false
    approvalComments.value = ''
    
    // Reset step data for next step
    await nextTick()
    if (updatedWorkflow.status === workflowStore.WORKFLOW_STATES.ACTIVE) {
      currentStepData.value = {}
    }
    
    // Check if workflow is completed
    await nextTick()
    if (updatedWorkflow.status === workflowStore.WORKFLOW_STATES.COMPLETED) {
      emit('workflow-completed', updatedWorkflow)
    } else {
      emit('workflow-updated', updatedWorkflow)
    }
    
  } catch (error) {
    console.error('Failed to approve step:', error)
  } finally {
    isLoading.value = false
  }
}

// Reject current step
const rejectCurrentStep = async () => {
  const wf = workflow.value
  const step = currentStep.value
  if (!wf || !step) return
  
  try {
    isLoading.value = true
    
    const updatedWorkflow = workflowStore.rejectStep(
      wf.id,
      wf.current_step,
      'current_user', // In real app, get from auth
      approvalComments.value || 'No reason provided'
    )
    
    // Only update if we're managing the workflow locally
    if (!props.workflowId) {
      await nextTick()
      currentWorkflow.value = updatedWorkflow
    }
    
    showApprovalModal.value = false
    approvalComments.value = ''
    
    await nextTick()
    emit('workflow-updated', updatedWorkflow)
    
  } catch (error) {
    console.error('Failed to reject step:', error)
  } finally {
    isLoading.value = false
  }
}

// Handle step click in stepper
const handleStepClick = ({ step, index }) => {
  const wf = workflow.value
  if (!wf || typeof wf.current_step !== 'number') return
  
  if (props.mode === 'view' || (index <= wf.current_step)) {
    // Allow viewing completed steps
    currentStepData.value = step.data ? { ...step.data } : {}
  }
}

// Handle file upload
const handleFileUpload = ({ field, file }) => {
  // In a real app, you'd upload to a file storage service
  console.log(`File uploaded for field ${field}:`, file.name)
  
  // For demo purposes, just update the form data
  currentStepData.value[field] = file.name
}

// Format status for display
const formatStatus = (status) => {
  return status.replace('_', ' ').split(' ').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

// Get status color
const getStatusColor = (status) => {
  const colorMap = {
    'draft': 'neutral',
    'active': 'info',
    'pending': 'warning',
    'approved': 'positive',
    'rejected': 'negative',
    'completed': 'positive',
    'cancelled': 'neutral',
    'failed': 'negative'
  }
  return colorMap[status] || 'neutral'
}

// Dynamic font sizes (same as other components)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _h2 = computed(() => { return `${settings.fontSizesComputed.value.h2}px`; });
const _h3 = computed(() => { return `${settings.fontSizesComputed.value.h3}px`; });
const _body = computed(() => { return `${settings.fontSizesComputed.value.body}px`; });
const _bodySmall = computed(() => { return `${settings.fontSizesComputed.value.bodySmall}px`; });
</script>

<template>
  <div class="workflow-manager">
    <!-- Loading State -->
    <div v-if="isLoading && !workflow" class="loading-container">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
              <p :style="{ fontSize: _body }" class="mt-4 text-gray-600 dark:text-gray-400">
        Setting up workflow...
      </p>
    </div>

    <!-- Workflow Content -->
    <div v-else-if="workflow" class="workflow-content">
      <!-- Header -->
      <div class="workflow-header">
        <div class="header-info">
                      <h1 
              class="workflow-title"
              :style="{ fontSize: _h2 }"
            >
            {{ workflow.name }}
          </h1>
                      <p 
              class="workflow-description"
              :style="{ fontSize: _body }"
            >
            {{ workflow.description }}
          </p>
        </div>
        
        <div class="header-status">
          <div 
            class="px-3 py-1 rounded-full text-sm font-medium"
            :class="getStatusColor(workflow.status)"
          >
            {{ formatStatus(workflow.status) }}
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="workflow-stats">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Progress</div>
              <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ workflowStats.progress }}</div>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Completed Steps</div>
              <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ workflowStats.completed }}/{{ workflowStats.total }}</div>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Created</div>
              <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ new Date(workflow.created_at).toLocaleDateString() }}</div>
            </div>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <i class="fas fa-calendar text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
          </div>
        </div>
        <div v-if="workflow.completed_at" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Completed</div>
              <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ new Date(workflow.completed_at).toLocaleDateString() }}</div>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <i class="fas fa-flag-checkered text-green-600 dark:text-green-400 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Workflow Stepper -->
      <div class="workflow-stepper-container">
        <WorkflowStepper
          :steps="workflow.steps"
          :current-step="workflow.current_step"
          orientation="horizontal"
          :show-estimated-time="true"
          @step-click="handleStepClick"
        />
      </div>

      <!-- Current Step Content -->
      <div v-if="currentStep && workflow.status === workflowStore.WORKFLOW_STATES.ACTIVE" class="current-step-container">
        <div class="step-header">
                      <h2 
              class="step-title"
              :style="{ fontSize: _h3 }"
            >
            {{ currentStep.name }}
          </h2>
                      <p 
              class="step-description"
              :style="{ fontSize: _body }"
            >
            {{ currentStep.description }}
          </p>
        </div>

        <!-- Step Form -->
        <WorkflowForm
          v-if="currentStep.form_schema"
          v-model="currentStepData"
          :schema="currentStep.form_schema"
          :readonly="props.mode === 'view' || currentStep.status === workflowStore.STEP_STATES.REQUIRES_APPROVAL"
          :errors="validationErrors"
          @submit="completeCurrentStep"
          @file-upload="handleFileUpload"
        />

        <!-- Step Actions -->
        <div v-if="props.mode !== 'view'" class="step-actions">
          <!-- Regular completion -->
          <button
            v-if="currentStep.status === workflowStore.STEP_STATES.ACTIVE && !currentStep.approval_required"
            :disabled="isLoading"
            @click="completeCurrentStep"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-arrow-right mr-2"></i>
            Complete Step
          </button>

          <!-- Submit for approval -->
          <button
            v-if="currentStep.status === workflowStore.STEP_STATES.ACTIVE && currentStep.approval_required"
            :disabled="isLoading"
            @click="completeCurrentStep"
            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-paper-plane mr-2"></i>
            Submit for Approval
          </button>

          <!-- Approval actions -->
          <div v-if="currentStep.status === workflowStore.STEP_STATES.REQUIRES_APPROVAL && canApprove" class="approval-actions">
            <button
              :disabled="isLoading"
              @click="showApprovalModal = true"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <i class="fas fa-check mr-2"></i>
              Approve
            </button>
            <button
              :disabled="isLoading"
              @click="showApprovalModal = true"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <i class="fas fa-times mr-2"></i>
              Reject
            </button>
          </div>

          <!-- Pending approval message -->
          <div v-if="currentStep.status === workflowStore.STEP_STATES.REQUIRES_APPROVAL && !canApprove" class="pending-approval">
            <div class="pending-message">
              <i class="fas fa-clock text-yellow-500 mr-2"></i>
                              <span :style="{ fontSize: _body }">
                Waiting for approval from: {{ currentStep.approver_roles?.join(', ') || 'Authorized personnel' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Workflow -->
      <div v-if="workflow.status === workflowStore.WORKFLOW_STATES.COMPLETED" class="completed-workflow">
        <div class="completion-message">
          <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                      <h2 
              class="completion-title"
              :style="{ fontSize: _h3 }"
            >
            Workflow Completed Successfully!
          </h2>
                      <p 
              class="completion-description"
              :style="{ fontSize: _body }"
            >
            All steps have been completed and the {{ workflow.name.toLowerCase() }} process is now finished.
          </p>
                      <p 
              class="completion-date"
              :style="{ fontSize: _bodySmall }"
            >
            Completed on {{ new Date(workflow.completed_at).toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Workflow History -->
      <div class="workflow-history">
                  <h3 
            class="history-title"
            :style="{ fontSize: _value }"
          >
          Workflow History
        </h3>
        <div class="history-timeline">
          <div 
            v-for="(entry, index) in workflow.history" 
            :key="index"
            class="history-entry"
          >
            <div class="history-timestamp">
                              <span :style="{ fontSize: _caption }">
                {{ new Date(entry.timestamp).toLocaleString() }}
              </span>
            </div>
            <div class="history-content">
                              <span 
                  class="history-action"
                  :style="{ fontSize: _bodySmall }"
                >
                {{ entry.details }}
              </span>
                              <span 
                  class="history-user"
                  :style="{ fontSize: _caption }"
                >
                by {{ entry.user }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="error-container">
      <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
              <h2 :style="{ fontSize: _value }" class="text-red-600 dark:text-red-400">
        Workflow Not Found
      </h2>
              <p :style="{ fontSize: _body }" class="text-gray-600 dark:text-gray-400">
        The requested workflow could not be loaded.
      </p>
    </div>

    <!-- Approval Modal -->
    <div v-if="showApprovalModal" class="approval-modal-overlay" @click="showApprovalModal = false">
      <div class="approval-modal" @click.stop>
                  <h3 :style="{ fontSize: _value }" class="modal-title">
          Step Approval
        </h3>
                  <p :style="{ fontSize: _body }" class="modal-description">
          Please provide comments for this approval decision:
        </p>
        
        <textarea
          v-model="approvalComments"
          placeholder="Enter your comments..."
          class="approval-textarea"
          :style="{
            fontSize: _body
          }"
          rows="4"
        ></textarea>
        
        <div class="modal-actions">
          <button
            :disabled="isLoading"
            @click="approveCurrentStep"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Approve
          </button>
          <button
            :disabled="isLoading"
            @click="rejectCurrentStep"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Reject
          </button>
          <button
            @click="showApprovalModal = false"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Layout */
.workflow-manager {
  @apply w-full max-w-7xl mx-auto p-6;
}

.loading-container,
.error-container {
  @apply flex flex-col items-center justify-center py-12 text-center;
}

.workflow-content {
  @apply space-y-8;
}

/* Header */
.workflow-header {
  @apply flex items-start justify-between mb-6 p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

.header-info {
  @apply flex-1;
}

.workflow-title {
  @apply font-bold text-gray-900 dark:text-white mb-2;
}

.workflow-description {
  @apply text-gray-600 dark:text-gray-400;
}

.header-status {
  @apply flex-shrink-0 ml-6;
}

/* Stats */
.workflow-stats {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4;
}

/* Stepper */
.workflow-stepper-container {
  @apply p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

/* Current Step */
.current-step-container {
  @apply p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

.step-header {
  @apply mb-6;
}

.step-title {
  @apply font-bold text-gray-900 dark:text-white mb-2;
}

.step-description {
  @apply text-gray-600 dark:text-gray-400;
}

/* Step Actions */
.step-actions {
  @apply mt-6 pt-6 border-t border-gray-200 dark:border-gray-700;
}

.approval-actions {
  @apply flex items-center space-x-3;
}

.pending-approval {
  @apply p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800;
}

.pending-message {
  @apply flex items-center text-yellow-700 dark:text-yellow-300;
}

/* Completed Workflow */
.completed-workflow {
  @apply p-8 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-center;
}

.completion-message {
  @apply space-y-3;
}

.completion-title {
  @apply font-bold text-green-700 dark:text-green-300;
}

.completion-description {
  @apply text-green-600 dark:text-green-400;
}

.completion-date {
  @apply text-green-500 dark:text-green-500;
}

/* History */
.workflow-history {
  @apply p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

.history-title {
  @apply font-bold text-gray-900 dark:text-white mb-4;
}

.history-timeline {
  @apply space-y-4;
}

.history-entry {
  @apply flex items-start space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.history-timestamp {
  @apply flex-shrink-0 text-gray-500 dark:text-gray-400;
}

.history-content {
  @apply flex-1 space-y-1;
}

.history-action {
  @apply block text-gray-900 dark:text-white;
}

.history-user {
  @apply block text-gray-500 dark:text-gray-400;
}

/* Approval Modal */
.approval-modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.approval-modal {
  @apply bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700;
}

.modal-title {
  @apply font-bold text-gray-900 dark:text-white mb-2;
}

.modal-description {
  @apply text-gray-600 dark:text-gray-400 mb-4;
}

.approval-textarea {
  @apply w-full font-medium rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed mb-4;
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
  resize: vertical;
}

.approval-textarea::placeholder {
  color: rgba(99, 102, 241, 0.6) !important;
}

.approval-textarea:focus {
  @apply ring-indigo-500;
  border-color: #4f46e5 !important;
}

.dark .approval-textarea {
  border-color: #60a5fa !important;
  color: #60a5fa !important;
}

.dark .approval-textarea::placeholder {
  color: rgba(96, 165, 250, 0.6) !important;
}

.modal-actions {
  @apply flex items-center justify-end space-x-3;
}

/* Responsive Design */
@media (max-width: 768px) {
  .workflow-header {
    @apply flex-col space-y-4;
  }
  
  .header-status {
    @apply ml-0;
  }
  
  .workflow-stats {
    @apply grid-cols-1;
  }
  
  .approval-actions {
    @apply flex-col space-y-3 space-x-0;
  }
}
</style>
