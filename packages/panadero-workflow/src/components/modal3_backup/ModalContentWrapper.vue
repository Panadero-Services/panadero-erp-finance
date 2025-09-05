<!--
  Modal Content Wrapper Component
  @version 1.1.2
  @description Wraps different step type components in the modal content area - modular extraction
  @debug Added explicit component rendering and debug output for troubleshooting
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowApproval from './WorkflowApproval.vue'
import WorkflowChecklist from './WorkflowChecklist.vue'
import WorkflowEntitySelection from './WorkflowEntitySelection.vue'
import WorkflowDatabaseSubmit from './WorkflowDatabaseSubmit.vue'

// Props
const props = defineProps({
  currentStep: {
    type: Object,
    default: null
  },
  stepData: {
    type: Object,
    default: () => ({})
  },
  scaling: {
    type: Object,
    required: true
  },
  activeWorkflow: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['update-step-data', 'step-completed', 'approval-requested'])

// Composables
//const settings = useWorkflowSettings()

// Dynamic font sizes - ONLY changing styling references
const _value = computed(() => props.scaling.font.title) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`

// Computed
const stepTypeComponent = computed(() => {
  
  if (!props.currentStep) return null
  
  switch (props.currentStep.type) {
    case 'form_submission':
      return 'WorkflowForm'
    case 'approval':
      return 'WorkflowApproval'
    case 'checklist':
      return 'WorkflowChecklist'
    case 'shared_entity_selection':
      return 'WorkflowEntitySelection'
    case 'submit_database':
      return 'WorkflowDatabaseSubmit'
    default:
      return 'WorkflowForm' // Fallback to form
  }
})

const stepTypeConfig = computed(() => {
  if (!props.currentStep) return {}
  
  switch (props.currentStep.type) {
    case 'form_submission':
      return { 
        icon: 'fas fa-edit', 
        color: 'text-blue-600',
        title: 'Form Submission',
        description: 'Complete the required information'
      }
    case 'approval':
      return { 
        icon: 'fas fa-check-circle', 
        color: 'text-green-600',
        title: 'Approval Required',
        description: 'Review and approve this step'
      }
    case 'checklist':
      return { 
        icon: 'fas fa-tasks', 
        color: 'text-orange-600',
        title: 'Checklist',
        description: 'Complete required checklist items'
      }
    case 'shared_entity_selection':
      return { 
        icon: 'fas fa-users', 
        color: 'text-purple-600',
        title: 'Entity Selection',
        description: 'Select the required entity'
      }
    case 'submit_database':
      return { 
        icon: 'fas fa-database', 
        color: 'text-indigo-600',
        title: 'Database Submission',
        description: 'Submit data to database'
      }
    default:
      return { 
        icon: 'fas fa-question-circle', 
        color: 'text-gray-600',
        title: 'Unknown Step Type',
        description: 'Step type not recognized'
      }
  }
})
</script>

<template>
  <div class="w-full lg:w-3/5 flex flex-col bg-gray-50 dark:bg-gray-700/30" 
       style="min-width: 0; flex-shrink: 0; flex-basis: 60%;">


    <!-- Step Type Content -->
    <div class="flex-1 overflow-y-auto p-2 sm:p-4">
      <div v-if="currentStep" class="space-y-4">
        
        <!-- Step Type Header - removed manual.. too much intro fields
        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
          <div class="flex items-center space-x-3 mb-2">
            <i :class="[stepTypeConfig.icon, stepTypeConfig.color, 'text-xl']"></i>
            <h4 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
              {{ stepTypeConfig.title }}
            </h4>
          </div>
          <p :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
            {{ stepTypeConfig.description }}
          </p>
        </div>-->

                  <!-- Dynamic Step Type Component -->
          <div v-if="stepTypeComponent" class="space-y-4">

            
            <!-- Entity Selection Component -->
            <WorkflowEntitySelection 
              v-if="stepTypeComponent === 'WorkflowEntitySelection'"
              :step="currentStep"
              :step-data="stepData"
              :scaling="scaling"
              :active-workflow="activeWorkflow"
              @update-step-data="emit('update-step-data', $event)"
              @step-completed="emit('step-completed', $event)"
            />
            
            <!-- Form Component -->
            <WorkflowForm 
              v-if="stepTypeComponent === 'WorkflowForm'"
              :step="currentStep"
              :step-data="stepData"
              :scaling="scaling"
              :active-workflow="activeWorkflow"
              @update-step-data="emit('update-step-data', $event)"
              @step-completed="emit('step-completed', $event)"
            />

            <!-- Checklist Component -->
            <WorkflowChecklist 
              v-if="stepTypeComponent === 'WorkflowChecklist'"
              :step="currentStep"
              :step-data="stepData"
              :scaling="scaling"
              :active-workflow="activeWorkflow"
              @update-step-data="emit('update-step-data', $event)"
              @step-completed="emit('step-completed', $event)"
            />

            <!-- Approval Component -->
            <WorkflowApproval 
              v-if="stepTypeComponent === 'WorkflowApproval'"
              :step="currentStep"
              :step-data="stepData"
              :scaling="scaling"
              :active-workflow="activeWorkflow"
              @update-step-data="emit('update-step-data', $event)"
              @step-completed="emit('step-completed', $event)"
              @approval-requested="emit('approval-requested', $event)"
            />

            <!-- Database Submit Component -->
            <WorkflowDatabaseSubmit 
              v-if="stepTypeComponent === 'WorkflowDatabaseSubmit'"
              :step="currentStep"
              :step-data="stepData"
              :scaling="scaling"
              @update-step-data="emit('update-step-data', $event)"
              @step-completed="emit('step-completed', $event)"
            />
          </div>
      </div>
      
      <div v-else class="text-center py-16">
        <i class="fas fa-info-circle text-gray-400 text-3xl mb-4"></i>
        <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
          No current step available
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
