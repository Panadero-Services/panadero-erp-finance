<!--
  Workflow Info Component
  @version 2.0.0
  @description Comprehensive workflow information display with template details, step overview, and dynamic styling
  @enhanced Added detailed template information, step overview, requirements, timeline, and proper scrolling
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../../composables/useWorkflowSettings.js'

// Store
//const settings = useWorkflowSettings()

// Props
const props = defineProps({
  workflowData: {
    type: Object,
    default: () => ({})
  },
  scaling: {
    type: Object,
    default: () => ({
      font: {
        body: '14px',
        title: '18px',
        caption: '12px',
        heading: '20px',
        h2: '24px',
        h3: '20px',
        h4: '18px'
      }
    })
  }
})

// Computed properties
const currentStep = computed(() => {
  const currentIndex = props.workflowData?.currentStep || 0
  return props.workflowData?.steps?.[currentIndex] || null
})

const workflowTemplate = computed(() => {
  return props.workflowData?.template || {}
})

const stepStats = computed(() => {
  const steps = props.workflowData?.steps || []
  return {
    total: steps.length,
    completed: steps.filter(s => s.status === 'completed').length,
    active: steps.filter(s => s.status === 'active').length,
    pending: steps.filter(s => s.status === 'pending').length,
    requiresApproval: steps.filter(s => s.status === 'requires_approval').length
  }
})

const progressPercentage = computed(() => {
  if (!stepStats.value.total) return 0
  return Math.round((stepStats.value.completed / stepStats.value.total) * 100)
})

// Utility functions
function getModuleDisplayName(module) {
  if (!module) return 'General'
  
  switch (module) {
    case 'gl': return 'General Ledger'
    case 'ap': return 'Accounts Payable'
    case 'ar': return 'Accounts Receivable'
    case 'cf': return 'Cash Flow'
    case 'procurement': return 'Procurement'
    default: return module.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
  }
}

function formatDate(dateString) {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getStepTypeIcon(type) {
  switch (type) {
    case 'entity_selection': return 'fas fa-mouse-pointer'
    case 'form_submission': return 'fas fa-file-alt'
    case 'checklist': return 'fas fa-check-square'
    case 'approval': return 'fas fa-user-check'
    case 'notification': return 'fas fa-bell'
    default: return 'fas fa-cog'
  }
}

function getStepTypeColor(type) {
  switch (type) {
    case 'entity_selection': return 'text-blue-600 dark:text-blue-400'
    case 'form_submission': return 'text-green-600 dark:text-green-400'
    case 'checklist': return 'text-purple-600 dark:text-purple-400'
    case 'approval': return 'text-orange-600 dark:text-orange-400'
    case 'notification': return 'text-indigo-600 dark:text-indigo-400'
    default: return 'text-gray-600 dark:text-gray-400'
  }
}

function getComplexityBadgeColor(complexity) {
  switch (complexity) {
    case 'low': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
    case 'medium': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300'
    case 'high': return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300'
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

// Dynamic font sizes - ONLY changing styling references
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _value = computed(() => props.scaling.font.title) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`
const _h2 = computed(() => props.scaling.font.heading) // WAS: `${settings.fontSizesComputed.value.h2}px`
const _h3 = computed(() => props.scaling.font.heading) // WAS: `${settings.fontSizesComputed.value.h3}px`
const _h4 = computed(() => props.scaling.font.title) // WAS: `${settings.fontSizesComputed.value.h4}px`
</script>

<template>
  <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-700/30">
    <div class="p-4 space-y-6">
      
      <!-- Workflow Header -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h2 :style="{ fontSize: _h2 }" class="font-bold text-gray-900 dark:text-white mb-2">
              <i class="fas fa-sitemap mr-3 text-blue-600 dark:text-blue-400"></i>
              {{ workflowTemplate.name || workflowData?.name || 'Workflow Information' }}
            </h2>
            <p v-if="workflowTemplate.description || workflowData?.description" 
               :style="{ fontSize: _body }" 
               class="text-gray-700 dark:text-gray-300 leading-relaxed">
              {{ workflowTemplate.description || workflowData.description }}
            </p>
          </div>
          
          <!-- Progress Ring -->
          <div class="flex-shrink-0 ml-6">
            <div class="relative w-20 h-20">
              <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                <path class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                <path class="text-blue-600 dark:text-blue-400" stroke="currentColor" stroke-width="3" fill="none" 
                      :stroke-dasharray="`${progressPercentage}, 100`"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span :style="{ fontSize: _caption }" class="font-bold text-gray-900 dark:text-white">{{ progressPercentage }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="text-center">
            <div :style="{ fontSize: _h4 }" class="font-bold text-blue-600 dark:text-blue-400">{{ stepStats.total }}</div>
            <div :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">Total Steps</div>
          </div>
          <div class="text-center">
            <div :style="{ fontSize: _h4 }" class="font-bold text-green-600 dark:text-green-400">{{ stepStats.completed }}</div>
            <div :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">Completed</div>
          </div>
          <div class="text-center">
            <div :style="{ fontSize: _h4 }" class="font-bold text-orange-600 dark:text-orange-400">{{ stepStats.active }}</div>
            <div :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">Active</div>
          </div>
          <div class="text-center">
            <div :style="{ fontSize: _h4 }" class="font-bold text-purple-600 dark:text-purple-400">{{ stepStats.requiresApproval }}</div>
            <div :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">Approvals</div>
          </div>
        </div>
      </div>

      <!-- Workflow Details -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl p-6">
        <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
          <i class="fas fa-info-circle mr-2 text-blue-600 dark:text-blue-400"></i>
          Workflow Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column -->
          <div class="space-y-4">
            <div v-if="workflowTemplate.module || workflowData?.module">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Module</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-cube mr-2 text-indigo-600 dark:text-indigo-400"></i>
                {{ getModuleDisplayName(workflowTemplate.module || workflowData.module) }}
              </div>
            </div>

            <div v-if="workflowTemplate.category || workflowData?.category">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Category</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-tag mr-2 text-green-600 dark:text-green-400"></i>
                {{ (workflowTemplate.category || workflowData.category).replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
              </div>
            </div>

            <div v-if="workflowTemplate.estimated_duration || workflowData?.estimated_duration">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Estimated Duration</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-clock mr-2 text-orange-600 dark:text-orange-400"></i>
                {{ workflowTemplate.estimated_duration || workflowData.estimated_duration }}
              </div>
            </div>

            <div v-if="workflowTemplate.complexity || workflowData?.complexity">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Complexity</label>
              <div class="flex items-center">
                <i class="fas fa-layer-group mr-2 text-purple-600 dark:text-purple-400"></i>
                <span :style="{ fontSize: _caption }" 
                      :class="getComplexityBadgeColor(workflowTemplate.complexity || workflowData.complexity)"
                      class="px-2 py-1 rounded-full font-medium">
                  {{ (workflowTemplate.complexity || workflowData.complexity).replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-4">
            <div v-if="workflowData?.started_at">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Started</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-play mr-2 text-green-600 dark:text-green-400"></i>
                {{ formatDate(workflowData.started_at) }}
              </div>
            </div>

            <div v-if="workflowData?.completed_at">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Completed</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-600 dark:text-green-400"></i>
                {{ formatDate(workflowData.completed_at) }}
              </div>
            </div>

            <div v-if="workflowData?.workflowNr">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Workflow Number</label>
              <div :style="{ fontSize: _body }" class="flex items-center">
                <i class="fas fa-hashtag mr-2 text-blue-600 dark:text-blue-400"></i>
                <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm">{{ workflowData.workflowNr }}</code>
              </div>
            </div>

            <div v-if="workflowData?.status">
              <label :style="{ fontSize: _caption }" class="block text-gray-500 dark:text-gray-400 font-medium mb-1">Status</label>
              <div class="flex items-center">
                <i class="fas fa-circle mr-2" :class="{
                  'text-green-500': workflowData.status === 'completed',
                  'text-blue-500': workflowData.status === 'active',
                  'text-yellow-500': workflowData.status === 'pending',
                  'text-red-500': workflowData.status === 'failed',
                  'text-gray-500': !['completed', 'active', 'pending', 'failed'].includes(workflowData.status)
                }"></i>
                <span :style="{ fontSize: _body }" class="capitalize">{{ workflowData.status }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Current Step Information -->
      <div v-if="currentStep" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl p-6">
        <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
          <i class="fas fa-play-circle mr-2 text-green-600 dark:text-green-400"></i>
          Current Step: {{ currentStep.name }}
        </h3>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
          <div class="flex items-center mb-2">
            <i :class="[getStepTypeIcon(currentStep.type), getStepTypeColor(currentStep.type), 'mr-2']"></i>
            <span :style="{ fontSize: _body }" class="font-medium">{{ currentStep.type?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</span>
          </div>
          
          <p v-if="currentStep.description" :style="{ fontSize: _body }" class="text-gray-700 dark:text-gray-300 mb-3">
            {{ currentStep.description }}
          </p>

          <div v-if="currentStep.instructions && currentStep.instructions.length > 0" class="mb-3">
            <h4 :style="{ fontSize: _h4 }" class="font-medium text-gray-900 dark:text-white mb-2">Instructions:</h4>
            <ul class="list-disc pl-5 space-y-1">
              <li v-for="instruction in currentStep.instructions" :key="instruction" :style="{ fontSize: _body }" class="text-gray-700 dark:text-gray-300">
                {{ instruction }}
              </li>
            </ul>
          </div>

          <div v-if="currentStep.notes && currentStep.notes.length > 0" class="space-y-2">
            <div v-for="note in currentStep.notes" :key="note.type" 
                 :style="{ fontSize: _caption }"
                 :class="[
                   'p-3 rounded-lg',
                   note.type === 'warning' ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 
                   note.type === 'info' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700' :
                   'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600'
                 ]">
              <div class="flex items-start">
                <i :class="[
                  'mr-2 mt-0.5',
                  note.type === 'warning' ? 'fas fa-exclamation-triangle' :
                  note.type === 'info' ? 'fas fa-info-circle' :
                  'fas fa-sticky-note'
                ]"></i>
                <div>
                  <strong>{{ note.type === 'warning' ? 'Warning' : note.type === 'info' ? 'Information' : 'Note' }}:</strong>
                  {{ note.message }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Step Overview -->
      <div v-if="workflowData?.steps && workflowData.steps.length > 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl p-6">
        <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
          <i class="fas fa-list-ol mr-2 text-purple-600 dark:text-purple-400"></i>
          Step Overview
        </h3>

        <div class="space-y-3">
          <div v-for="(step, index) in workflowData.steps" :key="step.id || index"
               class="flex items-center p-3 rounded-lg transition-colors"
               :class="{
                 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700': step.status === 'completed',
                 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700': step.status === 'active',
                 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700': step.status === 'requires_approval',
                 'bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600': step.status === 'pending'
               }">
            <div class="flex-shrink-0 mr-4">
              <div :class="[
                'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                step.status === 'completed' ? 'bg-green-500 text-white' :
                step.status === 'active' ? 'bg-blue-500 text-white' :
                step.status === 'requires_approval' ? 'bg-yellow-500 text-white' :
                'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300'
              ]">
                <i v-if="step.status === 'completed'" class="fas fa-check"></i>
                <i v-else-if="step.status === 'active'" class="fas fa-play"></i>
                <i v-else-if="step.status === 'requires_approval'" class="fas fa-clock"></i>
                <span v-else>{{ index + 1 }}</span>
              </div>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <h4 :style="{ fontSize: _h4 }" class="font-medium text-gray-900 dark:text-white truncate">
                  {{ step.name || `Step ${index + 1}` }}
                </h4>
                <div class="flex items-center space-x-2 ml-4">
                  <i :class="[getStepTypeIcon(step.type), getStepTypeColor(step.type)]"></i>
                  <span :style="{ fontSize: _caption }" :class="getStepTypeColor(step.type)" class="font-medium">
                    {{ step.type?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                  </span>
                </div>
              </div>
              
              <p v-if="step.description" :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400 mt-1 truncate">
                {{ step.description }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Requirements & Approvals -->
      <div v-if="workflowTemplate.approval_levels || workflowTemplate.notifications" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl p-6">
        <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
          <i class="fas fa-shield-alt mr-2 text-orange-600 dark:text-orange-400"></i>
          Requirements & Approvals
        </h3>

        <div class="space-y-4">
          <div v-if="workflowTemplate.approval_levels">
            <h4 :style="{ fontSize: _h4 }" class="font-medium text-gray-900 dark:text-white mb-2">
              <i class="fas fa-user-check mr-2 text-purple-600 dark:text-purple-400"></i>
              Approval Levels Required: {{ workflowTemplate.approval_levels }}
            </h4>
          </div>

          <div v-if="workflowTemplate.notifications" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
            <h4 :style="{ fontSize: _h4 }" class="font-medium text-amber-900 dark:text-amber-100 mb-2">
              <i class="fas fa-bell mr-2"></i>
              Notification Settings
            </h4>
            
            <div v-if="workflowTemplate.notifications.approval_required" class="mb-2">
              <span :style="{ fontSize: _caption }" class="text-amber-800 dark:text-amber-200 font-medium">Approval Required:</span>
              <div :style="{ fontSize: _body }" class="text-amber-700 dark:text-amber-300 mt-1">
                {{ workflowTemplate.notifications.approval_required.join(', ') }}
              </div>
            </div>
            
            <div v-if="workflowTemplate.notifications.completion" class="mb-2">
              <span :style="{ fontSize: _caption }" class="text-amber-800 dark:text-amber-200 font-medium">Completion:</span>
              <div :style="{ fontSize: _body }" class="text-amber-700 dark:text-amber-300 mt-1">
                {{ workflowTemplate.notifications.completion.join(', ') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!workflowData || Object.keys(workflowData).length === 0" class="text-center py-12">
        <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
        <h3 :style="{ fontSize: _h3 }" class="font-medium text-gray-900 dark:text-white mb-2">No Workflow Information Available</h3>
        <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
          Workflow data could not be loaded or is not available.
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
