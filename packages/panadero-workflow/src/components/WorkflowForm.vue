<!--
  Workflow Form Component
  @version 1.0.9
  @description Dynamic form rendering based on step type from workflow.json
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowStore } from '../composables/workflowStore.js'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

// Props for dynamic form configuration
const props = defineProps({
  step: {
    type: Object,
    default: () => ({})
  },
  stepData: {
    type: Object,
    default: () => ({})
  }
})

// Emits for form updates
const emit = defineEmits(['update-step-data'])

// Methods
function updateStepData(fieldName, value) {
  emit('update-step-data', props.step.id, { [fieldName]: value })
}

function handleEntitySelection(entity) {
  if (props.step.sharedEntity) {
    const identifierField = props.step.sharedEntity.identifierField
    const displayField = props.step.sharedEntity.displayField
    updateStepData(identifierField, entity[identifierField])
    updateStepData(displayField, entity[displayField])
    updateStepData('selectedEntity', entity)
  }
}

function handleApproval(approved, comments = '') {
  updateStepData('approved', approved)
  updateStepData('comments', comments)
  updateStepData('approvedAt', new Date().toISOString())
}

function handleChecklistItem(itemName, checked) {
  const currentChecklist = props.stepData[props.step.id]?.checklist || {}
  currentChecklist[itemName] = checked
  updateStepData('checklist', currentChecklist)
}

// Get step type display info
function getStepTypeInfo(stepType) {
  switch (stepType) {
    case 'user_actions':
      return { title: 'User Actions', icon: 'fas fa-mouse-pointer', color: 'text-purple-600' }
    case 'form_submission':
      return { title: 'Form Submission', icon: 'fas fa-edit', color: 'text-blue-600' }
    case 'approval':
      return { title: 'Approval Required', icon: 'fas fa-user-check', color: 'text-orange-600' }
    case 'shared_entity_selection':
      return { title: 'Entity Selection', icon: 'fas fa-database', color: 'text-green-600' }
    case 'checklist':
      return { title: 'Checklist', icon: 'fas fa-tasks', color: 'text-indigo-600' }
    default:
      return { title: 'Unknown Step Type', icon: 'fas fa-circle', color: 'text-gray-600' }
  }
}

// Dynamic font sizes (same as other components)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _body = computed(() => { return `${settings.fontSizesComputed.value.body}px`; });
const _bodySmall = computed(() => { return `${settings.fontSizesComputed.value.bodySmall}px`; });
const _bodyLarge = computed(() => { return `${settings.fontSizesComputed.value.bodyLarge}px`; });
const _h4 = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
</script>

<template>
  <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
    <!-- Step Header -->
    <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-600">
      <div class="flex items-center space-x-3">
        <i :class="[getStepTypeInfo(props.step.type).icon, getStepTypeInfo(props.step.type).color, 'text-xl']"></i>
        <div>
          <h3 :style="{ fontSize: _h4 }" class="font-semibold text-gray-900 dark:text-white">
            {{ getStepTypeInfo(props.step.type).title }}
          </h3>
          <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
            {{ props.step.description }}
          </p>
        </div>
      </div>
    </div>

    <!-- Dynamic Content Based on Step Type -->
    <div class="space-y-6">
      
      <!-- 1. USER_ACTIONS Step Type -->
      <div v-if="props.step.type === 'user_actions'" class="space-y-4">
        <div v-for="action in props.step.actions" :key="action.name" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
            {{ action.label }}
          </h4>
          
          <!-- Dropdown -->
          <select v-if="action.type === 'dropdown'" 
                  :style="{ fontSize: _bodySmall }"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                  @change="updateStepData(action.name, $event.target.value)">
            <option value="">Select {{ action.label }}</option>
            <option v-for="option in action.options" :key="option" :value="option">{{ option }}</option>
          </select>
          
          <!-- Button -->
          <button v-else-if="action.type === 'button'"
                  :style="{ fontSize: _bodySmall }"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  @click="updateStepData(action.name, true)">
            {{ action.label }}
          </button>
        </div>
      </div>

      <!-- 2. FORM_SUBMISSION Step Type -->
      <div v-if="props.step.type === 'form_submission'" class="space-y-6">
        <div v-for="section in props.step.formSchema?.sections" :key="section.title" class="space-y-4">
          <h4 :style="{ fontSize: _bodyLarge }" class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">
            {{ section.title }}
          </h4>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div v-for="field in section.fields" :key="field.name" class="space-y-2">
              <label :style="{ fontSize: _bodySmall }" class="block font-medium text-gray-700 dark:text-gray-300">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
              </label>
              
              <!-- Text Input -->
              <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel' || field.type === 'url'"
                     :type="field.type"
                     :style="{ fontSize: _bodySmall }"
                     class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                     :placeholder="`Enter ${field.label.toLowerCase()}`"
                     @input="updateStepData(field.name, $event.target.value)">
              
              <!-- Textarea -->
              <textarea v-else-if="field.type === 'textarea'"
                        :style="{ fontSize: _bodySmall }"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                        :rows="field.attributes?.rows || 3"
                        :placeholder="`Enter ${field.label.toLowerCase()}`"
                        @input="updateStepData(field.name, $event.target.value)"></textarea>
              
              <!-- Select -->
              <select v-else-if="field.type === 'select'"
                      :style="{ fontSize: _bodySmall }"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                      @change="updateStepData(field.name, $event.target.value)">
                <option value="">Select {{ field.label.toLowerCase() }}</option>
                <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
              </select>
              
              <!-- Checkbox -->
              <label v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" 
                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600"
                       @change="updateStepData(field.name, $event.target.checked)">
                <span :style="{ fontSize: _bodySmall }" class="text-gray-700 dark:text-gray-300">
                  {{ field.label }}
                </span>
              </label>
              
              <!-- File Upload -->
              <div v-else-if="field.type === 'file'" 
                   class="w-full px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500 mb-2"></i>
                <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
                  Upload {{ field.label.toLowerCase() }}
                </p>
                <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-500 mt-1">
                  {{ field.attributes?.accept || 'All files' }} • Max {{ field.attributes?.maxSize || '10MB' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. APPROVAL Step Type -->
      <div v-if="props.step.type === 'approval'" class="space-y-4">
        <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-600 rounded-lg">
          <h4 :style="{ fontSize: _body }" class="font-medium text-orange-800 dark:text-orange-200 mb-2">
            <i class="fas fa-user-check mr-2"></i>
            Approval Required
          </h4>
          <p :style="{ fontSize: _bodySmall }" class="text-orange-700 dark:text-orange-300">
            This step requires approval from: <strong>{{ props.step.approverRoles?.join(', ') || 'Authorized roles' }}</strong>
          </p>
        </div>
        
        <div class="space-y-4">
          <div>
            <label :style="{ fontSize: _bodySmall }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Comments
            </label>
            <textarea :style="{ fontSize: _bodySmall }"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                      rows="3"
                      placeholder="Add approval comments..."
                      @input="updateStepData('comments', $event.target.value)"></textarea>
          </div>
          
          <div class="flex space-x-4">
            <button @click="handleApproval(true)"
                    :style="{ fontSize: _body }"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              <i class="fas fa-check mr-2"></i>Approve
            </button>
            <button @click="handleApproval(false)"
                    :style="{ fontSize: _body }"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
              <i class="fas fa-times mr-2"></i>Reject
            </button>
          </div>
        </div>
      </div>

      <!-- 4. SHARED_ENTITY_SELECTION Step Type -->
      <div v-if="props.step.type === 'shared_entity_selection'" class="space-y-4">
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600 rounded-lg">
          <h4 :style="{ fontSize: _body }" class="font-medium text-green-800 dark:text-green-200 mb-2">
            <i class="fas fa-database mr-2"></i>
            Select {{ props.step.sharedEntity?.type?.charAt(0).toUpperCase() + props.step.sharedEntity?.type?.slice(1) }}
          </h4>
          <p :style="{ fontSize: _bodySmall }" class="text-green-700 dark:text-green-300">
            Choose from available {{ props.step.sharedEntity?.type }} in the system
          </p>
        </div>
        
        <!-- Entity Selection Interface -->
        <div class="text-center py-8">
          <i class="fas fa-database text-gray-400 text-3xl mb-2"></i>
          <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
            Entity selection interface will be loaded here
          </p>
          <p :style="{ fontSize: _caption }" class="text-gray-400 dark:text-gray-500 mt-1">
            Loading from: {{ props.step.sharedEntity?.apiEndpoint }}
          </p>
        </div>
      </div>

      <!-- 5. CHECKLIST Step Type -->
      <div v-if="props.step.type === 'checklist'" class="space-y-4">
        <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-600 rounded-lg">
          <h4 :style="{ fontSize: _body }" class="font-medium text-indigo-800 dark:text-indigo-200 mb-2">
            <i class="fas fa-tasks mr-2"></i>
            Checklist Items
          </h4>
          <p :style="{ fontSize: _bodySmall }" class="text-indigo-700 dark:text-indigo-200">
            Complete all required checklist items to proceed
          </p>
        </div>
        
        <div class="space-y-3">
          <div v-for="item in props.step.checklistItems" :key="item.name" class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <input type="checkbox" 
                   :id="item.name"
                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600"
                   @change="handleChecklistItem(item.name, $event.target.checked)">
            <label :for="item.name" 
                   :style="{ fontSize: _bodySmall }" 
                   class="flex-1 text-gray-700 dark:text-gray-300 cursor-pointer">
              {{ item.label }}
              <span v-if="item.required" class="text-red-500 ml-1">*</span>
            </label>
            <span v-if="item.description" 
                  :style="{ fontSize: _caption }" 
                  class="text-gray-500 dark:text-gray-400">
              {{ item.description }}
            </span>
          </div>
        </div>
      </div>

      <!-- Unknown Step Type -->
      <div v-if="!['user_actions', 'form_submission', 'approval', 'shared_entity_selection', 'checklist'].includes(props.step.type)" class="text-center py-8">
        <i class="fas fa-question-circle text-gray-400 text-3xl mb-2"></i>
        <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
          Unknown step type: {{ props.step.type }}
        </p>
        <p :style="{ fontSize: _caption }" class="text-gray-400 dark:text-gray-500 mt-1">
          This step type is not yet implemented
        </p>
      </div>
    </div>
  </div>
</template>