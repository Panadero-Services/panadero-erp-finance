<!--
  Workflow Approval Component
  @version 1.1.0
  @description Handles approval step types in workflows - step type specific
-->
<script setup>
import { ref, computed } from 'vue'
import { useColors } from './useColors.js'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Color system
const { colors } = useColors()

// Props
const props = defineProps({
  step: {
    type: Object,
    required: true
  },
  stepData: {
    type: Object,
    default: () => ({})
  },
  scaling: {
    type: Object,
    required: true
  },
  infoOnly: {
    type: Boolean,
    default: false
  },
  workflowStore: {
    type: Object,
    required: true
  },
  workflowId: {
    type: [String, Number],
    required: true
  }
})

// Get activeWorkflow from store reactively (SSOT)
const activeWorkflow = computed(() => {
  return props.workflowStore.workflows.find(w => w.id === props.workflowId) || null
})

// Emits
const emit = defineEmits(['update-step-data'])

// Composables
//const settings = useWorkflowSettings()

// State - extract approval form from template form_schema
const initializeApprovalItems = () => {
  const items = []
  
  // Extract from form_schema sections
  if (props.step?.form_schema?.sections) {
    props.step.form_schema.sections.forEach(section => {
      const sectionData = {
        title: section.title,
        fields: section.fields.map(field => ({
          name: field.name,
          label: field.label,
          type: field.type,
          required: field.required || false,
          options: field.options || null,
          value: props.stepData[field.name] || ''
        }))
      }
      items.push(sectionData)
    })
  }
  
  return items
}

const approvalItems = ref(initializeApprovalItems())
const approvalDecision = ref(props.stepData?.approved ?? null)
const comments = ref(props.stepData?.comments ?? '')
const isSubmitting = ref(false)

// Dynamic font sizes - ONLY changing styling references
const _value = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _bodySmall = computed(() => props.scaling.font.small) // WAS: `${settings.fontSizesComputed.value.bodySmall}px`

// Computed
const canSubmit = computed(() => {
  return approvalDecision.value !== null && !isSubmitting.value
})

const approvalStatus = computed(() => {
  if (approvalDecision.value === true) return { text: 'Approved', class: 'text-green-600 bg-green-100 dark:bg-green-900/30' }
  if (approvalDecision.value === false) return { text: 'Rejected', class: 'text-red-600 bg-red-100 dark:bg-red-900/30' }
  return { text: 'Pending', class: 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30' }
})

// Methods
function handleApproval(approved) {
  approvalDecision.value = approved
  emit('update-step-data', props.step.id, { approved })
}

function handleCommentsChange(value) {
  comments.value = value
  emit('update-step-data', props.step.id, { comments: value })
}

async function submitApproval() {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  
  try {
    // Emit the approval decision
    
    // Update step data
    emit('update-step-data', props.step.id, {
      approved: approvalDecision.value,
      comments: comments.value,
      approvedAt: new Date().toISOString()
    })
    
  } catch (error) {
    console.error('Failed to submit approval:', error)
  } finally {
    isSubmitting.value = false
  }
}

// Get all collected data from all steps
function getAllCollectedData() {
  if (!activeWorkflow.value?.steps) return null
  
  const allData = {}
  
  // Collect data from all steps
  activeWorkflow.value.steps.forEach((step, index) => {
    if (step.data) {
      Object.assign(allData, step.data)
    }
  })
  
  // Add entity data structure fields (empty if not filled)
  if (activeWorkflow.value.entityDataStructure?.fields) {
    activeWorkflow.value.entityDataStructure.fields.forEach(field => {
      if (!(field.name in allData)) {
        allData[field.name] = null // Show empty fields
      }
    })
  }
  
  return Object.keys(allData).length > 0 ? allData : null
}
</script>

<template>
  <div class="space-y-4">

    <!--
    <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
      Step {{ props.step.order || '?' }} -  Input section
    </h3>-->
    
    <!-- Info Only Mode - For Column 2 Display -->
    <div v-if="infoOnly" :class="['rounded-lg p-3', colors.primary.bg]">
      <h4 :style="{ fontSize: scaling.font.caption }" :class="['font-medium mb-2', colors.primary.textMuted]">
        <i class="fas fa-check-circle text-green-600 mr-1"></i>
        Approval Required
      </h4>
      <div class="space-y-2">
        <div v-if="props.step.approver_roles" class="flex items-center space-x-2">
          <span :style="{ fontSize: scaling.font.caption }" :class="colors.secondary.text">
            <i class="fas fa-user-shield mr-1"></i>Approver Roles:
          </span>
          <div class="flex flex-wrap gap-1">
            <span v-for="role in props.step.approver_roles" :key="role" 
                  :style="{ fontSize: scaling.font.caption }" 
                  class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs">
              {{ role }}
            </span>
          </div>
        </div>
        
        <!-- Current Step Data Only -->
        <div v-if="approvalDecision !== null || comments" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
          <div :style="{ fontSize: scaling.font.small }" :class="['font-medium mb-2', colors.primary.textMuted]">
            <i class="fas fa-database mr-1 text-blue-600"></i>
            Step Data
          </div>
          <div :class="['bg-gray-100 dark:bg-gray-700 rounded p-2 font-mono text-xs overflow-x-auto', colors.secondary.text]">
            {{ JSON.stringify({ approved: approvalDecision, comments: comments }, null, 2) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Full Component Mode - For Column 3 Display -->
    <div v-else class="space-y-6">
    
    <!-- Approval Information -->
    <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
          <i class="fas fa-user-check text-blue-600 mr-2"></i>
          Approval Required
        </h4>
      </div>
      
      <div class="space-y-3">
        <div v-if="props.step.description" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
      </div>
    </div>

    <!-- Approval Decision -->
    <div :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
      <h5 :style="{ fontSize: _bodySmall }" :class="['font-medium mb-3', colors.primary.text]">
        Decision
      </h5>
      
      <div class="space-y-3">
        <!-- Approval Buttons -->
        <div class="flex space-x-3">
          <button 
            @click="handleApproval(true)"
            :class="[
              'flex-1 px-4 py-3 rounded-lg border-2 font-medium transition-colors',
              approvalDecision === true
                ? 'bg-green-600 border-green-600 text-white'
                : 'border-green-300 text-green-700 dark:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/20'
            ]"
            :style="{ fontSize: _body }"
          >
            <i class="fas fa-check mr-2"></i>
            Approve
          </button>
          
          <button 
            @click="handleApproval(false)"
            :class="[
              'flex-1 px-4 py-3 rounded-lg border-2 font-medium transition-colors',
              approvalDecision === false
                ? 'bg-red-600 border-red-600 text-white'
                : 'border-red-300 text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20'
            ]"
            :style="{ fontSize: _body }"
          >
            <i class="fas fa-times mr-2"></i>
            Reject
          </button>
        </div>
        
        <!-- Current Status -->
        <div v-if="approvalDecision !== null" class="text-center">
          <span :class="['px-3 py-1 rounded-full text-sm font-medium', approvalStatus.class]">
            {{ approvalStatus.text }}
          </span>
        </div>
      </div>
    </div>

    <!-- Comments -->
    <div :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
      <h5 :style="{ fontSize: _bodySmall }" :class="['font-medium mb-3', colors.primary.text]">
        Comments (Optional)
      </h5>
      
      <textarea
        v-model="comments"
        @input="handleCommentsChange($event.target.value)"
        placeholder="Add any comments or notes about your decision..."
        rows="3"
        :class="['w-full px-3 py-2 border rounded-md text-sm transition-colors resize-vertical', colors.form.border, colors.form.focus, colors.form.input, colors.primary.text]"
        :style="{ fontSize: _body }"
      ></textarea>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        @click="submitApproval"
        :disabled="!canSubmit"
        :class="['px-6 py-3 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed', colors.form.button]"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-paper-plane mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit Decision' }}
      </button>
    </div>
  </div>
</div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
