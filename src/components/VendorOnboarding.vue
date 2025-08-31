<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useFinanceStore } from '../stores/financeStore.js'
import { WorkflowManager, useWorkflowStore } from 'panadero-workflow'
import FinanceValueCard from './ui/FinanceValueCard.vue'
import FinanceButton from './ui/FinanceButton.vue'
import StatusBadge from './ui/StatusBadge.vue'
import { useScaling } from '../../../shared/composables/useScaling.js'

const workflowStore = useWorkflowStore()
const financeStore = useFinanceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()

// Component state
const activeWorkflows = ref([])
const completedWorkflows = ref([])
const selectedWorkflowId = ref(null)
const showWorkflowManager = ref(false)
const isLoading = ref(false)

// Get workflow statistics
const workflowStats = computed(() => {
  const active = workflowStore.getWorkflowsByStatus(workflowStore.WORKFLOW_STATES.ACTIVE).filter(w => w.template_id === 'vendor-onboarding')
  const pending = workflowStore.getWorkflowsByStatus(workflowStore.WORKFLOW_STATES.PENDING).filter(w => w.template_id === 'vendor-onboarding')
  const completed = workflowStore.getWorkflowsByStatus(workflowStore.WORKFLOW_STATES.COMPLETED).filter(w => w.template_id === 'vendor-onboarding')
  const total = workflowStore.workflowInstances.filter(w => w.template_id === 'vendor-onboarding').length
  
  return {
    active: active.length,
    pending: pending.length,
    completed: completed.length,
    total: total
  }
})

// Get recent workflows
const recentWorkflows = computed(() => {
  return workflowStore.workflowInstances
    .filter(w => w.template_id === 'vendor-onboarding')
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 10)
})

// Load data on mount
onMounted(() => {
  loadWorkflows()
})

// Load workflows
const loadWorkflows = () => {
  // Filter vendor onboarding workflows
  activeWorkflows.value = workflowStore.workflowInstances.filter(w => 
    w.template_id === 'vendor-onboarding' && 
    [workflowStore.WORKFLOW_STATES.ACTIVE, workflowStore.WORKFLOW_STATES.PENDING].includes(w.status)
  )
  
  completedWorkflows.value = workflowStore.workflowInstances.filter(w => 
    w.template_id === 'vendor-onboarding' && 
    w.status === workflowStore.WORKFLOW_STATES.COMPLETED
  )
}

// Start new vendor onboarding
const startNewOnboarding = () => {
  selectedWorkflowId.value = null
  showWorkflowManager.value = true
}

// View existing workflow
const viewWorkflow = (workflowId) => {
  selectedWorkflowId.value = workflowId
  showWorkflowManager.value = true
}

// Handle workflow events
const handleWorkflowCreated = (workflow) => {
  console.log('Vendor onboarding workflow created:', workflow)
  loadWorkflows()
}

const handleWorkflowUpdated = (workflow) => {
  console.log('Vendor onboarding workflow updated:', workflow)
  loadWorkflows()
}

const handleWorkflowCompleted = (workflow) => {
  console.log('Vendor onboarding workflow completed:', workflow)
  loadWorkflows()
  
  // In a real app, you might:
  // - Create vendor record in ERP system
  // - Send completion notifications
  // - Update vendor database
  // - Generate reports
  alert(`Vendor onboarding completed successfully!\n\nVendor: ${workflow.data.company_name || 'Unknown'}\nCompleted: ${new Date(workflow.completed_at).toLocaleString()}`)
}

// Close workflow manager
const closeWorkflowManager = () => {
  showWorkflowManager.value = false
  selectedWorkflowId.value = null
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

// Get workflow progress
const getWorkflowProgress = (workflow) => {
  const completed = workflow.steps.filter(s => 
    s.status === workflowStore.STEP_STATES.COMPLETED || 
    s.status === workflowStore.STEP_STATES.APPROVED
  ).length
  
  const total = workflow.steps.length
  return total > 0 ? Math.round((completed / total) * 100) : 0
}

// Get current step name
const getCurrentStepName = (workflow) => {
  const currentStep = workflow.steps[workflow.current_step]
  return currentStep ? currentStep.name : 'N/A'
}
</script>

<template>
  <div class="vendor-onboarding">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1 
          class="page-title"
          :style="{ fontSize: `${fontSizes.base + 6}px` }"
        >
          Vendor Onboarding
        </h1>
        <p 
          class="page-description"
          :style="{ fontSize: `${fontSizes.base}px` }"
        >
          Streamlined vendor registration and validation process with automated workflows
        </p>
      </div>
      
      <div class="header-actions">
        <FinanceButton
          variant="primary"
          size="normal"
          icon-left="fas fa-plus"
          @click="startNewOnboarding"
        >
          Start New Onboarding
        </FinanceButton>
      </div>
    </div>

    <!-- Workflow Manager Modal -->
    <div v-if="showWorkflowManager" class="workflow-modal-overlay" @click="closeWorkflowManager">
      <div class="workflow-modal" @click.stop>
        <div class="modal-header">
          <h2 :style="{ fontSize: `${fontSizes.base + 4}px` }" class="modal-title">
            {{ selectedWorkflowId ? 'View Vendor Onboarding' : 'New Vendor Onboarding' }}
          </h2>
          <button 
            class="modal-close"
            @click="closeWorkflowManager"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="modal-content">
          <WorkflowManager
            :workflow-id="selectedWorkflowId"
            :mode="selectedWorkflowId ? 'view' : 'create'"
            template-id="vendor-onboarding"
            @workflow-created="handleWorkflowCreated"
            @workflow-updated="handleWorkflowUpdated"
            @workflow-completed="handleWorkflowCompleted"
          />
        </div>
      </div>
    </div>

    <!-- Overview Section -->
    <div v-if="!showWorkflowManager" class="overview-section">
      <!-- Statistics Cards -->
      <div class="stats-grid">
        <FinanceValueCard
          title="Active Onboardings"
          :value="workflowStats.active"
          rows="2-row"
          color="info"
          icon="fas fa-spinner"
        />
        <FinanceValueCard
          title="Pending Approvals"
          :value="workflowStats.pending"
          rows="2-row"
          color="warning"
          icon="fas fa-clock"
        />
        <FinanceValueCard
          title="Completed"
          :value="workflowStats.completed"
          rows="2-row"
          color="positive"
          icon="fas fa-check-circle"
        />
        <FinanceValueCard
          title="Total Processed"
          :value="workflowStats.total"
          rows="2-row"
          color="neutral"
          icon="fas fa-chart-bar"
        />
      </div>

      <!-- Active Workflows -->
      <div class="workflows-section">
        <div class="section-header">
          <h2 
            class="section-title"
            :style="{ fontSize: `${fontSizes.base + 4}px` }"
          >
            Active Onboarding Workflows
          </h2>
          <FinanceButton
            variant="outline-primary"
            size="small"
            icon-left="fas fa-refresh"
            @click="loadWorkflows"
          >
            Refresh
          </FinanceButton>
        </div>

        <!-- Active Workflows Table -->
        <div v-if="activeWorkflows.length > 0" class="workflows-table">
          <div class="table-header">
            <div class="header-cell">Vendor</div>
            <div class="header-cell">Status</div>
            <div class="header-cell">Current Step</div>
            <div class="header-cell">Progress</div>
            <div class="header-cell">Created</div>
            <div class="header-cell">Actions</div>
          </div>
          
          <div 
            v-for="workflow in activeWorkflows" 
            :key="workflow.id"
            class="table-row"
            :style="scalingStyles.tableRowHeight"
          >
            <div class="table-cell">
              <div class="vendor-info">
                <span class="vendor-name">
                  {{ workflow.data.company_name || 'Unnamed Vendor' }}
                </span>
                <span 
                  class="vendor-id"
                  :style="{ fontSize: `${fontSizes.base - 3}px` }"
                >
                  ID: {{ workflow.id.split('-').pop() }}
                </span>
              </div>
            </div>
            
            <div class="table-cell">
              <StatusBadge 
                :status="workflow.status"
                :color="getStatusColor(workflow.status)"
                :label="formatStatus(workflow.status)"
              />
            </div>
            
            <div class="table-cell">
              <span :style="{ fontSize: `${fontSizes.base - 1}px` }">
                {{ getCurrentStepName(workflow) }}
              </span>
            </div>
            
            <div class="table-cell">
              <div class="progress-container">
                <div class="progress-bar">
                  <div 
                    class="progress-fill"
                    :style="{ width: `${getWorkflowProgress(workflow)}%` }"
                  ></div>
                </div>
                <span 
                  class="progress-text"
                  :style="{ fontSize: `${fontSizes.base - 2}px` }"
                >
                  {{ getWorkflowProgress(workflow) }}%
                </span>
              </div>
            </div>
            
            <div class="table-cell">
              <span :style="{ fontSize: `${fontSizes.base - 2}px` }">
                {{ new Date(workflow.created_at).toLocaleDateString() }}
              </span>
            </div>
            
            <div class="table-cell">
              <FinanceButton
                variant="outline-primary"
                size="small"
                icon-left="fas fa-eye"
                @click="viewWorkflow(workflow.id)"
              >
                View
              </FinanceButton>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="text-gray-600 dark:text-gray-400">
            No Active Onboarding Workflows
          </h3>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-500 dark:text-gray-500">
            Start a new vendor onboarding process to get started.
          </p>
          <FinanceButton
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
            class="mt-4"
            @click="startNewOnboarding"
          >
            Start New Onboarding
          </FinanceButton>
        </div>
      </div>

      <!-- Recent Workflows -->
      <div v-if="recentWorkflows.length > 0" class="recent-workflows-section">
        <h2 
          class="section-title"
          :style="{ fontSize: `${fontSizes.base + 4}px` }"
        >
          Recent Workflows
        </h2>

        <div class="recent-workflows-grid">
          <div 
            v-for="workflow in recentWorkflows.slice(0, 6)" 
            :key="workflow.id"
            class="workflow-card"
            @click="viewWorkflow(workflow.id)"
          >
            <div class="card-header">
              <h3 
                class="card-title"
                :style="{ fontSize: `${fontSizes.base}px` }"
              >
                {{ workflow.data.company_name || 'Unnamed Vendor' }}
              </h3>
              <StatusBadge 
                :status="workflow.status"
                :color="getStatusColor(workflow.status)"
                :label="formatStatus(workflow.status)"
              />
            </div>
            
            <div class="card-content">
              <p 
                class="card-step"
                :style="{ fontSize: `${fontSizes.base - 2}px` }"
              >
                Current Step: {{ getCurrentStepName(workflow) }}
              </p>
              <div class="card-progress">
                <div class="progress-bar">
                  <div 
                    class="progress-fill"
                    :style="{ width: `${getWorkflowProgress(workflow)}%` }"
                  ></div>
                </div>
                <span 
                  class="progress-text"
                  :style="{ fontSize: `${fontSizes.base - 3}px` }"
                >
                  {{ getWorkflowProgress(workflow) }}%
                </span>
              </div>
            </div>
            
            <div class="card-footer">
              <span 
                class="card-date"
                :style="{ fontSize: `${fontSizes.base - 3}px` }"
              >
                {{ new Date(workflow.created_at).toLocaleDateString() }}
              </span>
              <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Layout */
.vendor-onboarding {
  @apply w-full max-w-7xl mx-auto p-6;
}

/* Page Header */
.page-header {
  @apply flex items-start justify-between mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

.header-content {
  @apply flex-1;
}

.page-title {
  @apply font-bold text-gray-900 dark:text-white mb-2;
}

.page-description {
  @apply text-gray-600 dark:text-gray-400;
}

.header-actions {
  @apply flex-shrink-0 ml-6;
}

/* Modal */
.workflow-modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4;
}

.workflow-modal {
  @apply bg-white dark:bg-gray-800 rounded-lg w-full max-w-6xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-gray-700;
}

.modal-header {
  @apply flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700;
}

.modal-title {
  @apply font-bold text-gray-900 dark:text-white;
}

.modal-close {
  @apply p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors;
}

.modal-content {
  @apply p-6 overflow-y-auto max-h-[calc(90vh-80px)];
}

/* Overview Section */
.overview-section {
  @apply space-y-8;
}

/* Stats Grid */
.stats-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4;
}

/* Workflows Section */
.workflows-section {
  @apply bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6;
}

.section-header {
  @apply flex items-center justify-between mb-6;
}

.section-title {
  @apply font-bold text-gray-900 dark:text-white;
}

/* Workflows Table */
.workflows-table {
  @apply overflow-x-auto;
}

.table-header {
  @apply grid grid-cols-6 gap-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-t-lg font-medium text-gray-700 dark:text-gray-300;
  grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1fr;
}

.header-cell {
  @apply text-left;
}

.table-row {
  @apply grid grid-cols-6 gap-4 p-3 border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors;
  grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1fr;
}

.table-cell {
  @apply flex items-center;
}

/* Vendor Info */
.vendor-info {
  @apply flex flex-col;
}

.vendor-name {
  @apply font-medium text-gray-900 dark:text-white;
}

.vendor-id {
  @apply text-gray-500 dark:text-gray-400;
}

/* Progress */
.progress-container {
  @apply flex items-center space-x-2 w-full;
}

.progress-bar {
  @apply flex-1 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden;
}

.progress-fill {
  @apply h-full bg-blue-500 transition-all duration-300;
}

.progress-text {
  @apply text-gray-600 dark:text-gray-400 whitespace-nowrap;
}

/* Empty State */
.empty-state {
  @apply flex flex-col items-center justify-center py-12 text-center;
}

/* Recent Workflows */
.recent-workflows-section {
  @apply bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6;
}

.recent-workflows-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6;
}

/* Workflow Card */
.workflow-card {
  @apply p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer transition-all duration-200 hover:shadow-md;
}

.card-header {
  @apply flex items-start justify-between mb-3;
}

.card-title {
  @apply font-medium text-gray-900 dark:text-white flex-1 mr-2;
}

.card-content {
  @apply space-y-2 mb-3;
}

.card-step {
  @apply text-gray-600 dark:text-gray-400;
}

.card-progress {
  @apply flex items-center space-x-2;
}

.card-footer {
  @apply flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-600;
}

.card-date {
  @apply text-gray-500 dark:text-gray-400;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-header {
    @apply flex-col space-y-4;
  }
  
  .header-actions {
    @apply ml-0;
  }
  
  .stats-grid {
    @apply grid-cols-1;
  }
  
  .table-header,
  .table-row {
    @apply grid-cols-2 gap-2;
    grid-template-columns: 1fr 1fr;
  }
  
  .table-cell:nth-child(n+3) {
    @apply hidden;
  }
  
  .recent-workflows-grid {
    @apply grid-cols-1;
  }
  
  .workflow-modal {
    @apply max-w-full m-2;
  }
}

@media (max-width: 640px) {
  .card-header {
    @apply flex-col space-y-2;
  }
  
  .card-title {
    @apply mr-0;
  }
}
</style>
