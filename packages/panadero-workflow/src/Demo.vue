<!--
  Workflow Demo Component
  @version 1.0.0
  @date 13-Aug-2025
  @description Demo section for  wrapper for Workflow feature -->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from './composables/workflowStore.js'
import { useWorkflowSettings } from './composables/useWorkflowSettings.js'
import WorkflowManager from './Manager.vue'
import WorkflowStepper from './components/WorkflowStepper.vue'
import WorkflowForm from './components/WorkflowForm.vue'
// REMOVED: import FinanceButton from '../../components/ui/FinanceButton.vue'
// REMOVED: import FinanceValueCard from '../../components/ui/FinanceValueCard.vue'
// REMOVED: import FinanceDropdown from '../../components/ui/FinanceDropdown.vue'

const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

// Demo state
const selectedDemo = ref('overview')
const demoWorkflow = ref(null)
const demoStepData = ref({})
const showLiveDemo = ref(false)

// Demo options
const demoOptions = [
  { label: 'Overview', value: 'overview' },
  { label: 'Workflow Engine', value: 'engine' },
  { label: 'Stepper Component', value: 'stepper' },
  { label: 'Form Component', value: 'form' },
  { label: 'Live Demo', value: 'live' }
]

// Sample workflow data for stepper demo
const sampleSteps = [
  {
    id: 'step1',
    name: 'Information Collection',
    description: 'Gather basic vendor information and documents',
    status: 'completed',
    estimated_duration: '30 minutes',
    approval_required: false,
    started_at: '2025-08-17T09:00:00Z',
    completed_at: '2025-08-17T09:25:00Z'
  },
  {
    id: 'step2',
    name: 'Due Diligence',
    description: 'Verify credentials and conduct background checks',
    status: 'completed',
    estimated_duration: '2-3 business days',
    approval_required: true,
    approver_roles: ['procurement_manager', 'finance_manager'],
    started_at: '2025-08-17T09:25:00Z',
    completed_at: '2025-08-17T11:30:00Z'
  },
  {
    id: 'step3',
    name: 'Contract Negotiation',
    description: 'Negotiate terms and execute binding agreement',
    status: 'active',
    estimated_duration: '1-2 weeks',
    approval_required: true,
    approver_roles: ['legal_counsel', 'procurement_director'],
    started_at: '2025-08-17T11:30:00Z'
  },
  {
    id: 'step4',
    name: 'System Integration',
    description: 'Integrate vendor into systems and provide access',
    status: 'pending',
    estimated_duration: '3-5 business days',
    approval_required: false
  },
  {
    id: 'step5',
    name: 'Relationship Management',
    description: 'Establish communication and performance monitoring',
    status: 'pending',
    estimated_duration: '1-2 business days',
    approval_required: false
  }
]

// Sample form schema for form demo
const sampleFormSchema = {
  sections: [
    {
      title: 'Basic Information',
      description: 'Enter the vendor\'s basic company information',
      fields: [
        { name: 'company_name', type: 'text', required: true, label: 'Company Name', placeholder: 'Enter company name' },
        { name: 'business_type', type: 'select', required: true, label: 'Business Type', options: ['Corporation', 'LLC', 'Partnership', 'Sole Proprietorship'] },
        { name: 'industry', type: 'select', required: true, label: 'Industry', options: ['Manufacturing', 'Services', 'Technology', 'Healthcare', 'Other'] },
        { name: 'website', type: 'url', required: false, label: 'Website URL', placeholder: 'https://example.com' }
      ]
    },
    {
      title: 'Contact Information',
      description: 'Primary contact details for the vendor',
      fields: [
        { name: 'contact_name', type: 'text', required: true, label: 'Primary Contact Name', icon: 'fas fa-user' },
        { name: 'contact_email', type: 'email', required: true, label: 'Email Address', icon: 'fas fa-envelope' },
        { name: 'contact_phone', type: 'tel', required: true, label: 'Phone Number', icon: 'fas fa-phone' },
        { name: 'address', type: 'textarea', required: true, label: 'Business Address', rows: 3 }
      ]
    },
    {
      title: 'Preferences',
      description: 'Communication and service preferences',
      fields: [
        { name: 'preferred_contact', type: 'select', required: true, label: 'Preferred Contact Method', options: ['Email', 'Phone', 'Both'] },
        { name: 'services', type: 'multi-select', required: false, label: 'Services Offered', options: ['Consulting', 'Manufacturing', 'Support', 'Training'] },
        { name: 'newsletter', type: 'checkbox', required: false, label: 'Subscribe to newsletter updates' },
        { name: 'terms_accepted', type: 'checkbox', required: true, label: 'I accept the terms and conditions' }
      ]
    }
  ]
}

// Workflow statistics for overview
const workflowStats = computed(() => {
  const allWorkflows = workflowStore.workflowInstances
  return {
    total: allWorkflows.length,
    active: allWorkflows.filter(w => w.status === workflowStore.WORKFLOW_STATES.ACTIVE).length,
    pending: allWorkflows.filter(w => w.status === workflowStore.WORKFLOW_STATES.PENDING).length,
    completed: allWorkflows.filter(w => w.status === workflowStore.WORKFLOW_STATES.COMPLETED).length
  }
})

// Initialize demo
onMounted(() => {
  // Create a sample workflow for demo purposes if none exist
  if (workflowStore.workflowInstances.length === 0) {
    createSampleWorkflows()
  }
})

// Create sample workflows for demo
const createSampleWorkflows = () => {
  try {
    // Create a few sample workflows in different states
    const workflow1 = workflowStore.createWorkflowInstance('vendor-onboarding', {
      created_by: 'demo_user',
      company_name: 'Acme Corporation'
    })
    workflowStore.startWorkflow(workflow1.id)
    
    const workflow2 = workflowStore.createWorkflowInstance('vendor-onboarding', {
      created_by: 'demo_user',
      company_name: 'Tech Solutions Inc'
    })
    workflowStore.startWorkflow(workflow2.id)
    
    // Complete first step of workflow2
    workflowStore.completeStep(workflow2.id, 0, {
      company_name: 'Tech Solutions Inc',
      business_type: 'Corporation',
      industry: 'Technology'
    })
    
    const workflow3 = workflowStore.createWorkflowInstance('vendor-onboarding', {
      created_by: 'demo_user',
      company_name: 'Global Manufacturing Ltd'
    })
    // Keep workflow3 in draft state
    
    console.log('Sample workflows created for demo')
  } catch (error) {
    console.warn('Could not create sample workflows:', error)
  }
}

// Start live demo
const startLiveDemo = () => {
  demoWorkflow.value = workflowStore.createWorkflowInstance('vendor-onboarding', {
    created_by: 'demo_user'
  })
  workflowStore.startWorkflow(demoWorkflow.value.id)
  showLiveDemo.value = true
}

// Handle demo workflow events
const handleDemoWorkflowUpdated = (workflow) => {
  // Don't update demoWorkflow.value here to avoid circular updates
  // The WorkflowManager handles the workflow state via workflow-id prop
  console.log('Demo workflow updated:', workflow)
}

const handleDemoWorkflowCompleted = (workflow) => {
  // Don't update demoWorkflow.value here to avoid circular updates
  console.log('Demo workflow completed:', workflow)
  alert('Demo workflow completed successfully!')
}

// Handle form submit in form demo
const handleFormSubmit = (data) => {
  console.log('Form submitted with data:', data)
  alert('Form submitted successfully! Check console for data.')
}

// Handle step click in stepper demo
const handleStepClick = ({ step, index }) => {
  console.log('Step clicked:', step.name, 'at index:', index)
}
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      Workflow System Demo
    </h2>
    
    <!-- Demo Selector -->
    <div class="mb-8">
      <select
        v-model="selectedDemo"
        class="w-64 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
      >
        <option value="">Select Demo</option>
        <option v-for="option in demoOptions" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>

    <!-- Overview Demo -->
    <div v-if="selectedDemo === 'overview'" class="space-y-8">




      <!-- Usage Example -->
      <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Basic Usage:</h4>
        <div class="font-mono text-sm bg-gray-100 dark:bg-gray-800 p-3 rounded text-blue-600 dark:text-blue-400 overflow-x-auto">
          <div>// Create workflow instance</div>
          <div>const workflow = workflowStore.createWorkflowInstance('vendor-onboarding', data)</div>
          <div>workflowStore.startWorkflow(workflow.id)</div>
          <div></div>
          <div>// Complete steps</div>
          <div>workflowStore.completeStep(workflow.id, stepIndex, stepData)</div>
          <div></div>
          <div>// Handle approvals</div>
          <div>workflowStore.approveStep(workflow.id, stepIndex, userId, comments)</div>
        </div>
      </div>
    </div>

    <!-- Engine Demo -->
    <div v-if="selectedDemo === 'engine'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Workflow Engine Architecture
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Engine Components -->
          <div class="space-y-4">
            <h4 class="font-medium text-gray-700 dark:text-gray-300">Core Components:</h4>
            <div class="space-y-3">
              <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800">
                <div class="font-medium text-blue-700 dark:text-blue-300">Workflow Store</div>
                <div class="text-sm text-blue-600 dark:text-blue-400">Centralized state management with Pinia</div>
              </div>
              <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded border border-green-200 dark:border-green-800">
                <div class="font-medium text-green-700 dark:text-green-300">Template Engine</div>
                <div class="text-sm text-green-600 dark:text-green-400">Configurable workflow definitions</div>
              </div>
              <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded border border-purple-200 dark:border-purple-800">
                <div class="font-medium text-purple-700 dark:text-purple-300">Execution Engine</div>
                <div class="text-sm text-purple-600 dark:text-purple-400">Step-by-step process execution</div>
              </div>
              <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded border border-orange-200 dark:border-orange-800">
                <div class="font-medium text-orange-700 dark:text-orange-300">Approval System</div>
                <div class="text-sm text-orange-600 dark:text-orange-400">Role-based approval workflows</div>
              </div>
            </div>
          </div>

          <!-- State Management -->
          <div class="space-y-4">
            <h4 class="font-medium text-gray-700 dark:text-gray-300">Workflow States:</h4>
            <div class="space-y-2">
              <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <span class="text-sm">DRAFT</span>
                <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-xs rounded">Initial</span>
              </div>
              <div class="flex justify-between items-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                <span class="text-sm">ACTIVE</span>
                <span class="px-2 py-1 bg-blue-200 dark:bg-blue-600 text-xs rounded">Running</span>
              </div>
              <div class="flex justify-between items-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                <span class="text-sm">PENDING</span>
                <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-600 text-xs rounded">Approval</span>
              </div>
              <div class="flex justify-between items-center p-2 bg-green-50 dark:bg-green-900/20 rounded">
                <span class="text-sm">COMPLETED</span>
                <span class="px-2 py-1 bg-green-200 dark:bg-green-600 text-xs rounded">Success</span>
              </div>
              <div class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                <span class="text-sm">REJECTED</span>
                <span class="px-2 py-1 bg-red-200 dark:bg-red-600 text-xs rounded">Failed</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Code Example -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Template Definition Example:</h4>
          <div class="font-mono text-xs bg-gray-100 dark:bg-gray-800 p-3 rounded text-gray-700 dark:text-gray-300 overflow-x-auto">
            <pre>{
  id: 'vendor-onboarding',
  name: 'Vendor Onboarding',
  steps: [
    {
      id: 'registration',
      name: 'Vendor Registration',
      type: 'DATA_COLLECTION',
      form_schema: { /* form fields */ },
      validation_rules: { /* validation */ },
      approval_required: false
    },
    {
      id: 'verification',
      name: 'Document Verification',
      type: 'APPROVAL',
      approver_roles: ['manager'],
      approval_required: true
    }
  ]
}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- Stepper Demo -->
    <div v-if="selectedDemo === 'stepper'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Workflow Stepper Component
        </h3>
        
        <!-- Horizontal Stepper -->
        <div class="mb-8">
          <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4">Horizontal Layout:</h4>
          <WorkflowStepper
            :steps="sampleSteps"
            :current-step="2"
            orientation="horizontal"
            :show-estimated-time="true"
            @step-click="handleStepClick"
          />
        </div>

        <!-- Vertical Stepper -->
        <div class="mb-8">
          <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4">Vertical Layout:</h4>
          <div class="max-w-2xl">
            <WorkflowStepper
              :steps="sampleSteps.slice(0, 3)"
              :current-step="1"
              orientation="vertical"
              :show-estimated-time="true"
              @step-click="handleStepClick"
            />
          </div>
        </div>

        <!-- Compact Stepper -->
        <div class="mb-6">
          <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4">Compact Mode:</h4>
          <WorkflowStepper
            :steps="sampleSteps"
            :current-step="2"
            orientation="horizontal"
            :compact="true"
            @step-click="handleStepClick"
          />
        </div>

        <!-- Usage Example -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Usage:</h4>
          <div class="font-mono text-sm bg-gray-100 dark:bg-gray-800 p-3 rounded text-blue-600 dark:text-blue-400 overflow-x-auto">
            &lt;WorkflowStepper<br>
            &nbsp;&nbsp;:steps="workflow.steps"<br>
            &nbsp;&nbsp;:current-step="workflow.current_step"<br>
            &nbsp;&nbsp;orientation="horizontal"<br>
            &nbsp;&nbsp;:show-estimated-time="true"<br>
            &nbsp;&nbsp;@step-click="handleStepClick"<br>
            /&gt;
          </div>
        </div>
      </div>
    </div>

    <!-- Form Demo -->
    <div v-if="selectedDemo === 'form'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Dynamic Workflow Form Component
        </h3>
        
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          The workflow form component automatically generates forms based on schema definitions with validation, 
          file uploads, and various input types.
        </p>

        <!-- Form Demo -->
        <WorkflowForm
          v-model="demoStepData"
          :schema="sampleFormSchema"
          :readonly="false"
          @submit="handleFormSubmit"
        />

        <!-- Current Form Data -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Current Form Data:</h4>
          <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ JSON.stringify(demoStepData, null, 2) }}</pre>
        </div>

        <!-- Schema Example -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Schema Structure:</h4>
          <div class="font-mono text-xs bg-gray-100 dark:bg-gray-800 p-3 rounded text-blue-600 dark:text-blue-400 overflow-x-auto">
            <pre>{
  sections: [
    {
      title: 'Section Title',
      fields: [
        {
          name: 'field_name',
          type: 'text|email|select|checkbox|file',
          required: true,
          label: 'Field Label',
          options: ['Option 1', 'Option 2'] // for select
        }
      ]
    }
  ]
}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- Live Demo -->
    <div v-if="selectedDemo === 'live'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Live Workflow Demo
        </h3>
        
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Experience a complete workflow from start to finish. This demo creates a real vendor onboarding workflow 
          that you can interact with and complete.
        </p>

        <!-- Start Demo Button -->
        <div v-if="!showLiveDemo" class="text-center py-8">
          <button
            @click="startLiveDemo"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-play mr-2"></i>
            Start Live Demo
          </button>
        </div>

        <!-- Live Demo Workflow -->
        <div v-else class="space-y-6">
          <div class="flex items-center justify-between">
            <h4 class="font-medium text-gray-700 dark:text-gray-300">
              Demo Workflow: {{ demoWorkflow?.name }}
            </h4>
            <button
              @click="showLiveDemo = false"
              class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            >
              <i class="fas fa-times mr-2"></i>
              Close Demo
            </button>
          </div>

          <!-- Demo Workflow Manager -->
          <WorkflowManager
            v-if="demoWorkflow"
            :workflow-id="demoWorkflow.id"
            mode="edit"
            template-id="vendor-onboarding"
            @workflow-updated="handleDemoWorkflowUpdated"
            @workflow-completed="handleDemoWorkflowCompleted"
          />
        </div>

        <!-- Demo Tips -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
          <h4 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">💡 Demo Tips:</h4>
          <ul class="text-sm text-blue-600 dark:text-blue-400 space-y-1">
            <li>• Fill out forms with sample data to progress through steps</li>
            <li>• Steps requiring approval will show approval buttons</li>
            <li>• Check the workflow history to see audit trail</li>
            <li>• File uploads are simulated for demo purposes</li>
            <li>• The workflow will complete when all steps are done</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-mono {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

pre {
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style>
