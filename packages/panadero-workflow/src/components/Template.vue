<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from '../composables/workflowStore.js'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
// REMOVE: All Finance UI component imports
// import FinanceButton from '../../../panadero-erp-finance/src/components/ui/FinanceButton.vue'
// import FinanceInput from '../../../panadero-erp-finance/src/components/ui/FinanceInput.vue'
// import FinanceDropdown from '../../../panadero-erp-finance/src/components/ui/FinanceDropdown.vue'
// import FinanceValueCard from '../../../panadero-erp-finance/src/components/ui/FinanceValueCard.vue'

const props = defineProps({
  mode: {
    type: String,
    default: 'create', // 'create', 'view', 'edit'
    validator: value => ['create', 'view', 'edit'].includes(value)
  }
})

const emit = defineEmits(['template-created', 'template-updated'])

const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

// Add these computed properties after the existing ones:
const _h2 = computed(() => { return `${settings.fontSizesComputed.value.h2}px`; });
const _h3 = computed(() => { return `${settings.fontSizesComputed.value.h3}px`; });
const _h4 = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _body = computed(() => { return `${settings.fontSizesComputed.value.body}px`; });

// ... rest of the component logic stays the same
</script>

<template>
  <!-- Replace FinanceButton with standard button -->
  <button 
    type="button"
    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
    @click="loadExampleTemplate"
  >
    <i class="fas fa-download mr-2"></i>
    Load Example
  </button>

  <!-- Replace FinanceValueCard with standard divs -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Total Steps</div>
          <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ templateStats.totalSteps }}</div>
        </div>
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
          <i class="fas fa-list-ol text-blue-600 dark:text-blue-400 text-xl"></i>
        </div>
      </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Approval Steps</div>
          <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ templateStats.approvalSteps }}</div>
        </div>
        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
          <i class="fas fa-clipboard-check text-orange-600 dark:text-orange-400 text-xl"></i>
        </div>
      </div>
      </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Estimated Duration</div>
          <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ templateStats.estimatedDays }}</div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <i class="fas fa-clock text-gray-600 dark:text-gray-400 text-xl"></i>
        </div>
      </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-gray-500 dark:text-gray-400 font-medium text-sm">Total Fields</div>
          <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ templateStats.totalFields }}</div>
        </div>
        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <i class="fas fa-edit text-green-600 dark:text-green-400 text-xl"></i>
        </div>
      </div>
      </div>
    </div>

    <!-- Template Configuration Form -->
    <div class="template-form space-y-8">
      <!-- Basic Template Information -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 
          class="font-bold text-gray-900 dark:text-white mb-6"
        :style="{ fontSize: settings.fontSizesComputed.h2 }"
        >
          <i class="fas fa-info-circle mr-2 text-blue-600 dark:text-blue-400"></i>
          Template Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Replace FinanceInput with standard input -->
        <div class="space-y-2">
          <label class="block font-medium text-gray-700 dark:text-gray-300">Template ID</label>
          <input 
            v-model="templateData.template_id"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            placeholder="e.g., vendor-onboarding-v2"
            required
          />
          <div v-if="validationErrors.template_id" class="text-red-600 text-sm">{{ validationErrors.template_id }}</div>
        </div>
          
        <div class="space-y-2">
          <label class="block font-medium text-gray-700 dark:text-gray-300">Template Name</label>
          <input 
            v-model="templateData.template_name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            placeholder="e.g., Vendor Onboarding Process"
            required
          />
          <div v-if="validationErrors.template_name" class="text-red-600 text-sm">{{ validationErrors.template_name }}</div>
        </div>
          
        <!-- Replace FinanceDropdown with standard select -->
        <div class="space-y-2">
          <label class="block font-medium text-gray-700 dark:text-gray-300">Category</label>
          <select 
            v-model="templateData.category"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            required
          >
            <option value="">Select category</option>
            <option v-for="category in templateCategories" :key="category.value" :value="category.value">
              {{ category.label }}
            </option>
          </select>
        </div>
        
        <div class="space-y-2">
          <label class="block font-medium text-gray-700 dark:text-gray-300">Estimated Duration</label>
          <select 
            v-model="templateData.estimated_duration"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            required
          >
            <option value="">Select duration</option>
            <option v-for="duration in durationOptions" :key="duration.value" :value="duration.value">
              {{ duration.label }}
            </option>
          </select>
        </div>
        </div>
        
        <div class="mt-6">
        <div class="space-y-2">
          <label class="block font-medium text-gray-700 dark:text-gray-300">Template Description</label>
          <textarea 
            v-model="templateData.template_description"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            placeholder="Describe the purpose and scope of this workflow template..."
            rows="3"
            required
          ></textarea>
          <div v-if="validationErrors.template_description" class="text-red-600 text-sm">{{ validationErrors.template_description }}</div>
        </div>
        </div>
      </div>

      <!-- Steps Configuration -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 
            class="font-bold text-gray-900 dark:text-white"
          :style="{ fontSize: settings.fontSizesComputed.h2 }"
          >
            <i class="fas fa-tasks mr-2 text-green-600 dark:text-green-400"></i>
            Workflow Steps ({{ templateData.steps.length }})
          </h2>
        <!-- Replace FinanceButton with standard button -->
        <button
          type="button"
          class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
            @click="addStep"
          >
          <i class="fas fa-plus mr-2"></i>
            Add Step
        </button>
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
              :style="{ fontSize: settings.fontSizesComputed.h3 }"
              >
                Step {{ step.order }}: {{ step.name }}
              </h3>
            <!-- Replace FinanceButton with standard button -->
            <button
                v-if="templateData.steps.length > 1"
              type="button"
              class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                @click="removeStep(stepIndex)"
              >
              <i class="fas fa-trash mr-2"></i>
                Remove
            </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Replace FinanceInput with standard input -->
            <div class="space-y-2">
              <label class="block font-medium text-gray-700 dark:text-gray-300">Step Name</label>
              <input
                v-model="step.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                placeholder="Step name"
                required
              />
            </div>
              
            <div class="space-y-2">
              <label class="block font-medium text-gray-700 dark:text-gray-300">Estimated Duration</label>
              <input
                v-model="step.estimated_duration"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                placeholder="e.g., 1-2 hours"
              />
            </div>
            </div>

            <div class="mb-4">
            <div class="space-y-2">
              <label class="block font-medium text-gray-700 dark:text-gray-300">Step Description</label>
              <textarea
                v-model="step.description"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                placeholder="Describe what happens in this step..."
                rows="2"
              ></textarea>
            </div>
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
                :style="{ fontSize: settings.fontSizesComputed.h4 }"
                >
                  Form Fields ({{ step.form_fields.length }})
                </h4>
              <!-- Replace FinanceButton with standard button -->
              <button
                type="button"
                class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm"
                  @click="addFieldToStep(stepIndex)"
                >
                <i class="fas fa-plus mr-2"></i>
                  Add Field
              </button>
              </div>

              <div v-if="step.form_fields.length > 0" class="space-y-3">
                <div
                  v-for="(field, fieldIndex) in step.form_fields"
                  :key="fieldIndex"
                  class="bg-gray-50 dark:bg-gray-700 p-3 rounded border"
                >
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                  <!-- Replace FinanceInput with standard input -->
                  <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Field Name</label>
                    <input
                      v-model="field.name"
                      type="text"
                      class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                      placeholder="field_name"
                    />
                  </div>
                    
                  <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Field Label</label>
                    <input
                      v-model="field.label"
                      type="text"
                      class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                      placeholder="Display Label"
                    />
                  </div>
                    
                  <!-- Replace FinanceDropdown with standard select -->
                  <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Field Type</label>
                    <select
                      v-model="field.type"
                      class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                    >
                      <option value="">Select field type</option>
                      <option v-for="option in fieldTypes" :key="option.value" :value="option.value">
                        {{ option.label }}
                      </option>
                    </select>
                  </div>
                    
                    <div class="flex items-center space-x-2">
                      <label class="flex items-center">
                        <input
                          v-model="field.required"
                          type="checkbox"
                          class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-1 text-xs text-gray-600 dark:text-gray-400">Required</span>
                      </label>
                      
                    <!-- Replace FinanceButton with standard button -->
                    <button
                      type="button"
                      class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-xs"
                        @click="removeFieldFromStep(stepIndex, fieldIndex)"
                      >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
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
        :style="{ fontSize: settings.fontSizesComputed.body }"
          >
            Template Status: {{ isValidTemplate ? 'Ready to Save' : 'Incomplete' }}
          </p>
          <p 
            v-if="validationErrors.general"
            class="text-red-600 dark:text-red-400 mt-1"
        :style="{ fontSize: settings.fontSizesComputed.body }"
          >
            {{ validationErrors.general }}
          </p>
        </div>
        
        <div class="flex space-x-4">
      <!-- Replace FinanceButton with standard button -->
      <button
        type="button"
        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            @click="resetTemplate"
            :disabled="isLoading"
          >
        <i class="fas fa-undo mr-2"></i>
            Reset
      </button>
      
      <button
        type="button"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        :disabled="!isValidTemplate || isLoading"
            @click="saveTemplate"
          >
        <i v-if="isLoading" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-save mr-2"></i>
            Save Template
      </button>
    </div>
  </div>
</template>