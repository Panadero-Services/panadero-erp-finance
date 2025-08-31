<!--
  Workflow Approval Component
  @version 1.1.0
  @description Handles approval step types in workflows - step type specific
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
const emit = defineEmits(['update-step-data', 'step-completed', 'approval-requested'])

// Composables
const settings = useWorkflowSettings()

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

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)
const _body = computed(() => `${settings.fontSizesComputed.value.body}px`)
const _bodySmall = computed(() => `${settings.fontSizesComputed.value.bodySmall}px`)

// Computed
const canSubmit = computed(() => {
  return approvalDecision?.value !== null && !isSubmitting.value
})

const approvalStatus = computed(() => {
  if (approvalDecision?.value === true) return { text: 'Approved', class: 'text-green-600 bg-green-100 dark:bg-green-900/30' }
  if (approvalDecision?.value === false) return { text: 'Rejected', class: 'text-red-600 bg-red-100 dark:bg-red-900/30' }
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
    emit('step-completed', {
      stepId: props.step.id,
      approved: approvalDecision.value,
      comments: comments.value,
      timestamp: new Date().toISOString()
    })
    
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
</script>

<template>
  <div class="space-y-6">
    <!-- Approval Information -->
    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h4 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white mb-3">
        <i class="fas fa-check-circle text-green-600 mr-2"></i>
        Approval Required
      </h4>
      
      <div class="space-y-3">
        <div v-if="props.step.description" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
        
        <div v-if="props.step.approver_roles" class="flex items-center space-x-2">
          <span :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
            <i class="fas fa-user-shield mr-1"></i>Approver Roles:
          </span>
          <div class="flex flex-wrap gap-1">
            <span v-for="role in props.step.approver_roles" :key="role" 
                  :style="{ fontSize: _bodySmall }" 
                  class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs">
              {{ role }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Approval Decision -->
    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i class="fas fa-gavel mr-2 text-indigo-600"></i>
        Decision
      </h5>
      
      <div class="space-y-3">
        <!-- Approval Buttons -->
        <div class="flex space-x-3">
          <button 
            @click="handleApproval(true)"
            :class="[
              'flex-1 px-4 py-3 rounded-lg border-2 font-medium transition-colors',
              approvalDecision?.value === true
                ? 'bg-green-600 border-green-600 text-white'
                : 'border-green-300 text-green-700 dark:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/20'
            ]"
            :style="{ fontSize: _bodySmall }"
          >
            <i class="fas fa-check mr-2"></i>
            Approve
          </button>
          
          <button 
            @click="handleApproval(false)"
            :class="[
              'flex-1 px-4 py-3 rounded-lg border-2 font-medium transition-colors',
              approvalDecision?.value === false
                ? 'bg-red-600 border-red-600 text-white'
                : 'border-red-300 text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20'
            ]"
            :style="{ fontSize: _bodySmall }"
          >
            <i class="fas fa-times mr-2"></i>
            Reject
          </button>
        </div>
        
        <!-- Current Status -->
        <div v-if="approvalDecision?.value !== null" class="text-center">
          <span :class="['px-3 py-1 rounded-full text-sm font-medium', approvalStatus.class]">
            {{ approvalStatus.text }}
          </span>
        </div>
      </div>
    </div>

    <!-- Comments -->
    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i class="fas fa-comment mr-2 text-blue-600"></i>
        Comments (Optional)
      </h5>
      
      <textarea
        v-model="comments"
        @input="handleCommentsChange($event.target.value)"
        placeholder="Add any comments or notes about your decision..."
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
        :style="{ fontSize: _bodySmall }"
      ></textarea>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        @click="submitApproval"
        :disabled="!canSubmit"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-paper-plane mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit Decision' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
