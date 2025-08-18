<script setup>
import { ref, computed } from 'vue'
import { useWorkflowStore } from '../../stores/workflowStore'
import { useFinanceStore } from '../../stores/financeStore'
import FinanceButton from '../ui/FinanceButton.vue'
import FinanceInput from '../ui/FinanceInput.vue'
import FinanceDropdown from '../ui/FinanceDropdown.vue'
import FinanceValueCard from '../ui/FinanceValueCard.vue'

const props = defineProps({
  mode: {
    type: String,
    default: 'create', // 'create', 'view', 'edit'
    validator: value => ['create', 'view', 'edit'].includes(value)
  }
})

const emit = defineEmits(['template-created', 'template-updated'])

const workflowStore = useWorkflowStore()
const financeStore = useFinanceStore()

// Form state
const isLoading = ref(false)
const validationErrors = ref({})

// Template form data based on the live vendor-onboarding example
const templateData = ref({
  // Template metadata
  template_id: '',
  template_name: '',
  template_description: '',
  category: 'vendor_management',
  estimated_duration: '3-5 days',
  
  // Steps configuration (based on existing vendor-onboarding)
  steps: [
    {
      id: 'discovery',
      name: 'Vendor Discovery & Registration',
      description: 'Initial vendor information collection and evaluation',
      order: 1,
      required: true,
      approval_required: false,
      automated_action: null,
      estimated_duration: '1-2 hours',
      approver_roles: [],
      form_fields: [
        { name: 'company_name', type: 'text', required: true, label: 'Company Name' },
        { name: 'business_type', type: 'select', required: true, label: 'Business Type', options: ['Corporation', 'LLC', 'Partnership', 'Sole Proprietorship'] },
        { name: 'industry', type: 'select', required: true, label: 'Industry', options: ['Manufacturing', 'Services', 'Technology', 'Healthcare', 'Other'] },
        { name: 'website', type: 'url', required: false, label: 'Website' },
        { name: 'primary_contact_name', type: 'text', required: true, label: 'Primary Contact Name' },
        { name: 'primary_contact_email', type: 'email', required: true, label: 'Primary Contact Email' },
        { name: 'primary_contact_phone', type: 'tel', required: true, label: 'Primary Contact Phone' },
        { name: 'business_address', type: 'textarea', required: true, label: 'Business Address' }
      ]
    },
    {
      id: 'due_diligence',
      name: 'Due Diligence & Verification',
      description: 'Comprehensive background checks and document verification',
      order: 2,
      required: true,
      approval_required: true,
      approver_roles: ['vendor_manager', 'finance_manager'],
      estimated_duration: '2-3 days',
      form_fields: [
        { name: 'tax_id', type: 'text', required: true, label: 'Tax ID / EIN' },
        { name: 'business_license', type: 'file', required: true, label: 'Business License' },
        { name: 'insurance_certificate', type: 'file', required: true, label: 'Insurance Certificate' },
        { name: 'financial_statements', type: 'file', required: false, label: 'Financial Statements' },
        { name: 'references', type: 'textarea', required: true, label: 'Business References' }
      ]
    },
    {
      id: 'contract_negotiation',
      name: 'Contract Negotiation & Agreement',
      description: 'Terms negotiation and contract finalization',
      order: 3,
      required: true,
      approval_required: true,
      approver_roles: ['legal_team', 'senior_management'],
      estimated_duration: '1-2 days',
      form_fields: [
        { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 60', 'Net 90', 'COD', 'Prepaid'] },
        { name: 'delivery_terms', type: 'select', required: true, label: 'Delivery Terms', options: ['FOB', 'CIF', 'DDP', 'EXW'] },
        { name: 'contract_value', type: 'number', required: true, label: 'Annual Contract Value' },
        { name: 'contract_duration', type: 'select', required: true, label: 'Contract Duration', options: ['1 Year', '2 Years', '3 Years', '5 Years'] },
        { name: 'special_terms', type: 'textarea', required: false, label: 'Special Terms & Conditions' }
      ]
    },
    {
      id: 'system_integration',
      name: 'System Integration & Setup',
      description: 'Technical integration and account setup',
      order: 4,
      required: true,
      approval_required: false,
      estimated_duration: '1 day',
      form_fields: [
        { name: 'vendor_code', type: 'text', required: true, label: 'Vendor Code' },
        { name: 'payment_method', type: 'select', required: true, label: 'Payment Method', options: ['ACH', 'Wire Transfer', 'Check', 'Credit Card'] },
        { name: 'banking_details', type: 'textarea', required: true, label: 'Banking Details' },
        { name: 'system_access_required', type: 'checkbox', required: false, label: 'System Access Required' },
        { name: 'portal_training_completed', type: 'checkbox', required: false, label: 'Portal Training Completed' }
      ]
    },
    {
      id: 'final_approval',
      name: 'Final Approval & Activation',
      description: 'Final review and vendor activation',
      order: 5,
      required: true,
      approval_required: true,
      approver_roles: ['vendor_manager', 'finance_director'],
      estimated_duration: '1 day',
      form_fields: [
        { name: 'compliance_verified', type: 'checkbox', required: true, label: 'Compliance Verified' },
        { name: 'background_check_passed', type: 'checkbox', required: true, label: 'Background Check Passed' },
        { name: 'contract_signed', type: 'checkbox', required: true, label: 'Contract Signed' },
        { name: 'system_setup_complete', type: 'checkbox', required: true, label: 'System Setup Complete' },
        { name: 'activation_notes', type: 'textarea', required: false, label: 'Activation Notes' }
      ]
    }
  ]
})

// Computed properties for validation and statistics
const templateStats = computed(() => {
  const steps = templateData.value.steps
  return {
    totalSteps: steps.length,
    approvalSteps: steps.filter(s => s.approval_required).length,
    estimatedDays: templateData.value.estimated_duration,
    totalFields: steps.reduce((sum, step) => sum + step.form_fields.length, 0)
  }
})

const isValidTemplate = computed(() => {
  const data = templateData.value
  return data.template_name && 
         data.template_description && 
         data.template_id && 
         data.steps.length > 0
})

// Available categories for workflow templates
const templateCategories = [
  { label: 'Vendor Management', value: 'vendor_management' },
  { label: 'Employee Management', value: 'employee_management' },
  { label: 'Financial Processes', value: 'financial_processes' },
  { label: 'Compliance & Audit', value: 'compliance_audit' },
  { label: 'Project Management', value: 'project_management' },
  { label: 'Document Management', value: 'document_management' }
]

// Duration options
const durationOptions = [
  { label: '1-2 days', value: '1-2 days' },
  { label: '3-5 days', value: '3-5 days' },
  { label: '1 week', value: '1 week' },
  { label: '2 weeks', value: '2 weeks' },
  { label: '1 month', value: '1 month' }
]

// Field type options for step configuration
const fieldTypes = [
  { label: 'Text Input', value: 'text' },
  { label: 'Email', value: 'email' },
  { label: 'Phone', value: 'tel' },
  { label: 'Number', value: 'number' },
  { label: 'URL', value: 'url' },
  { label: 'Text Area', value: 'textarea' },
  { label: 'Select Dropdown', value: 'select' },
  { label: 'Checkbox', value: 'checkbox' },
  { label: 'File Upload', value: 'file' },
  { label: 'Date', value: 'date' }
]

// Form methods
const updateTemplateField = (field, value) => {
  templateData.value[field] = value
}

const addStep = () => {
  const newStep = {
    id: `step_${Date.now()}`,
    name: 'New Step',
    description: 'Step description',
    order: templateData.value.steps.length + 1,
    required: true,
    approval_required: false,
    estimated_duration: '1 day',
    form_fields: []
  }
  templateData.value.steps.push(newStep)
}

const removeStep = (stepIndex) => {
  if (templateData.value.steps.length > 1) {
    templateData.value.steps.splice(stepIndex, 1)
    // Reorder remaining steps
    templateData.value.steps.forEach((step, index) => {
      step.order = index + 1
    })
  }
}

const addFieldToStep = (stepIndex) => {
  const newField = {
    name: `field_${Date.now()}`,
    type: 'text',
    required: false,
    label: 'New Field'
  }
  templateData.value.steps[stepIndex].form_fields.push(newField)
}

const removeFieldFromStep = (stepIndex, fieldIndex) => {
  templateData.value.steps[stepIndex].form_fields.splice(fieldIndex, 1)
}

const saveTemplate = async () => {
  try {
    isLoading.value = true
    validationErrors.value = {}
    
    // Validate template
    if (!isValidTemplate.value) {
      validationErrors.value.general = 'Please fill in all required template fields'
      return
    }
    
    // In a real implementation, you would save to backend/store
    console.log('Saving workflow template:', templateData.value)
    
    // Emit success
    emit('template-created', templateData.value)
    
    // Reset form for new template
    if (props.mode === 'create') {
      resetTemplate()
    }
    
  } catch (error) {
    console.error('Failed to save template:', error)
    validationErrors.value.general = 'Failed to save template. Please try again.'
  } finally {
    isLoading.value = false
  }
}

const resetTemplate = () => {
  templateData.value.template_id = ''
  templateData.value.template_name = ''
  templateData.value.template_description = ''
  // Keep the step structure as example
}

const loadExampleTemplate = () => {
  templateData.value.template_id = 'vendor-onboarding-v2'
  templateData.value.template_name = 'Vendor Onboarding Process'
  templateData.value.template_description = 'Comprehensive vendor onboarding workflow with due diligence, contract negotiation, and system integration'
}
</script>

<template>
  <div class="workflow-template">
    <!-- Header -->
    <div class="template-header">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 
            class="font-bold text-gray-900 dark:text-white"
            :style="{ fontSize: `${financeStore.fontSizes.base + 6}px` }"
          >
            <i class="fas fa-layer-group mr-3 text-indigo-600 dark:text-indigo-400"></i>
            Workflow Template Builder
          </h1>
          <p 
            class="text-gray-600 dark:text-gray-300 mt-2"
            :style="{ fontSize: `${financeStore.fontSizes.base}px` }"
          >
            Create and configure reusable workflow templates based on live data examples
          </p>
        </div>
        <FinanceButton
          variant="secondary"
          size="normal"
          icon-left="fas fa-download"
          @click="loadExampleTemplate"
        >
          Load Example
        </FinanceButton>
      </div>

      <!-- Template Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <FinanceValueCard
          title="Total Steps"
          :value="templateStats.totalSteps"
          rows="2-row"
          format="number"
          color="info"
          icon="fas fa-list-ol"
        />
        <FinanceValueCard
          title="Approval Steps"
          :value="templateStats.approvalSteps"
          rows="2-row"
          format="number"
          color="warning"
          icon="fas fa-clipboard-check"
        />
        <FinanceValueCard
          title="Estimated Duration"
          :value="templateStats.estimatedDays"
          rows="2-row"
          color="neutral"
          icon="fas fa-clock"
        />
        <FinanceValueCard
          title="Total Fields"
          :value="templateStats.totalFields"
          rows="2-row"
          format="number"
          color="positive"
          icon="fas fa-edit"
        />
      </div>
    </div>

    <!-- Template Configuration Form -->
    <div class="template-form space-y-8">
      <!-- Basic Template Information -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 
          class="font-bold text-gray-900 dark:text-white mb-6"
          :style="{ fontSize: `${financeStore.fontSizes.base + 4}px` }"
        >
          <i class="fas fa-info-circle mr-2 text-blue-600 dark:text-blue-400"></i>
          Template Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FinanceInput
            v-model="templateData.template_id"
            label="Template ID"
            placeholder="e.g., vendor-onboarding-v2"
            required
            :error="validationErrors.template_id"
          />
          
          <FinanceInput
            v-model="templateData.template_name"
            label="Template Name"
            placeholder="e.g., Vendor Onboarding Process"
            required
            :error="validationErrors.template_name"
          />
          
          <FinanceDropdown
            v-model="templateData.category"
            label="Category"
            :options="templateCategories"
            option-label="label"
            option-value="value"
            required
          />
          
          <FinanceDropdown
            v-model="templateData.estimated_duration"
            label="Estimated Duration"
            :options="durationOptions"
            option-label="label"
            option-value="value"
            required
          />
        </div>
        
        <div class="mt-6">
          <FinanceInput
            v-model="templateData.template_description"
            type="textarea"
            label="Template Description"
            placeholder="Describe the purpose and scope of this workflow template..."
            rows="3"
            required
            :error="validationErrors.template_description"
          />
        </div>
      </div>

      <!-- Steps Configuration -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 
            class="font-bold text-gray-900 dark:text-white"
            :style="{ fontSize: `${financeStore.fontSizes.base + 4}px` }"
          >
            <i class="fas fa-tasks mr-2 text-green-600 dark:text-green-400"></i>
            Workflow Steps ({{ templateData.steps.length }})
          </h2>
          <FinanceButton
            variant="primary"
            size="small"
            icon-left="fas fa-plus"
            @click="addStep"
          >
            Add Step
          </FinanceButton>
        </div>

        <!-- Steps List -->
        <div class="space-y-6">
          <div
            v-for="(step, stepIndex) in templateData.steps"
            :key="step.id"
            class="border border-gray-200 dark:border-gray-600 rounded-lg p-4"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 
                class="font-semibold text-gray-900 dark:text-white"
                :style="{ fontSize: `${financeStore.fontSizes.base + 2}px` }"
              >
                Step {{ step.order }}: {{ step.name }}
              </h3>
              <FinanceButton
                v-if="templateData.steps.length > 1"
                variant="danger"
                size="small"
                icon-left="fas fa-trash"
                @click="removeStep(stepIndex)"
              >
                Remove
              </FinanceButton>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <FinanceInput
                v-model="step.name"
                label="Step Name"
                placeholder="Step name"
                required
              />
              
              <FinanceInput
                v-model="step.estimated_duration"
                label="Estimated Duration"
                placeholder="e.g., 1-2 hours"
              />
            </div>

            <div class="mb-4">
              <FinanceInput
                v-model="step.description"
                type="textarea"
                label="Step Description"
                placeholder="Describe what happens in this step..."
                rows="2"
              />
            </div>

            <div class="flex items-center space-x-6 mb-4">
              <label class="flex items-center">
                <input
                  v-model="step.required"
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required Step</span>
              </label>
              
              <label class="flex items-center">
                <input
                  v-model="step.approval_required"
                  type="checkbox"
                  class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                >
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires Approval</span>
              </label>
            </div>

            <!-- Form Fields for this step -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
              <div class="flex items-center justify-between mb-3">
                <h4 
                  class="font-medium text-gray-900 dark:text-white"
                  :style="{ fontSize: `${financeStore.fontSizes.base}px` }"
                >
                  Form Fields ({{ step.form_fields.length }})
                </h4>
                <FinanceButton
                  variant="secondary"
                  size="small"
                  icon-left="fas fa-plus"
                  @click="addFieldToStep(stepIndex)"
                >
                  Add Field
                </FinanceButton>
              </div>

              <div v-if="step.form_fields.length > 0" class="space-y-3">
                <div
                  v-for="(field, fieldIndex) in step.form_fields"
                  :key="fieldIndex"
                  class="bg-gray-50 dark:bg-gray-700 p-3 rounded border"
                >
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <FinanceInput
                      v-model="field.name"
                      label="Field Name"
                      placeholder="field_name"
                      size="small"
                    />
                    
                    <FinanceInput
                      v-model="field.label"
                      label="Field Label"
                      placeholder="Display Label"
                      size="small"
                    />
                    
                    <FinanceDropdown
                      v-model="field.type"
                      label="Field Type"
                      :options="fieldTypes"
                      option-label="label"
                      option-value="value"
                      size="small"
                    />
                    
                    <div class="flex items-center space-x-2">
                      <label class="flex items-center">
                        <input
                          v-model="field.required"
                          type="checkbox"
                          class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-1 text-xs text-gray-600 dark:text-gray-400">Required</span>
                      </label>
                      
                      <FinanceButton
                        variant="danger"
                        size="small"
                        icon-left="fas fa-times"
                        @click="removeFieldFromStep(stepIndex, fieldIndex)"
                      >
                      </FinanceButton>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        <div>
          <p 
            class="text-gray-600 dark:text-gray-400"
            :style="{ fontSize: `${financeStore.fontSizes.base - 1}px` }"
          >
            Template Status: {{ isValidTemplate ? 'Ready to Save' : 'Incomplete' }}
          </p>
          <p 
            v-if="validationErrors.general"
            class="text-red-600 dark:text-red-400 mt-1"
            :style="{ fontSize: `${financeStore.fontSizes.base - 2}px` }"
          >
            {{ validationErrors.general }}
          </p>
        </div>
        
        <div class="flex space-x-4">
          <FinanceButton
            variant="secondary"
            size="normal"
            icon-left="fas fa-undo"
            @click="resetTemplate"
            :disabled="isLoading"
          >
            Reset
          </FinanceButton>
          
          <FinanceButton
            variant="primary"
            size="normal"
            icon-left="fas fa-save"
            :loading="isLoading"
            :disabled="!isValidTemplate"
            @click="saveTemplate"
          >
            Save Template
          </FinanceButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.workflow-template {
  @apply space-y-6;
}

.template-header {
  @apply mb-8;
}

.template-form {
  @apply space-y-8;
}

/* Ensure proper spacing and borders */
.template-form .border {
  @apply border-gray-200 dark:border-gray-600;
}

.template-form .bg-gray-50 {
  @apply bg-gray-50 dark:bg-gray-700;
}
</style>
