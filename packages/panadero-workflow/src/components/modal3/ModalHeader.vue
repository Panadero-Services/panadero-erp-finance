<!--
  Modal Header Component
  @version 2.4.0
  @description Compact and elegant modal header with fully dynamic module mapping
  @fixed Replaced hardcoded module mapping with configurable data-driven approach
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  activeWorkflow: {
    type: Object,
    default: null
  },
  hasConfigWorkflow: {
    type: Boolean,
    default: false
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['close'])

// Composables
//const settings = useWorkflowSettings()

// Dynamic configuration data
const moduleDisplayNames = {
  'gl': 'General Ledger',
  'ap': 'Accounts Payable', 
  'ar': 'Accounts Receivable',
  'cf': 'Cash Flow',
  'procurement': 'Procurement',
  'inventory': 'Inventory Management',
  'hr': 'Human Resources',
  'payroll': 'Payroll',
  'sales': 'Sales',
  'marketing': 'Marketing',
  'crm': 'Customer Relationship Management',
  'project': 'Project Management',
  'asset': 'Asset Management',
  'compliance': 'Compliance',
  'audit': 'Audit',
  'tax': 'Tax Management',
  'banking': 'Banking',
  'investment': 'Investment',
  'insurance': 'Insurance',
  'workflow': 'Workflow Management'
}

const statusColors = {
  'active': 'text-blue-600 dark:text-blue-400',
  'completed': 'text-green-600 dark:text-green-400',
  'pending': 'text-yellow-600 dark:text-yellow-400',
  'failed': 'text-red-600 dark:text-red-400',
  'paused': 'text-orange-600 dark:text-orange-400',
  'cancelled': 'text-gray-600 dark:text-gray-400'
}

const complexityColors = {
  'low': 'text-green-600 dark:text-green-400',
  'medium': 'text-yellow-600 dark:text-yellow-400',
  'high': 'text-red-600 dark:text-red-400',
  'critical': 'text-purple-600 dark:text-purple-400'
}

// Computed properties
const workflowTemplate = computed(() => {
  return props.activeWorkflow?.template || {}
})

const workflowName = computed(() => {
  return workflowTemplate.value.name || props.activeWorkflow?.name || 'Workflow'
})

const workflowDescription = computed(() => {
  return workflowTemplate.value.description || props.activeWorkflow?.description
})

const stepStats = computed(() => {
  const steps = props.activeWorkflow?.steps || []
  return {
    total: steps.length,
    completed: steps.filter(s => s.status === 'completed').length,
    active: steps.filter(s => s.status === 'active').length,
    requiresApproval: steps.filter(s => s.status === 'requires_approval').length
  }
})

const progressPercentage = computed(() => {
  if (!stepStats.value.total) return 0
  return Math.round((stepStats.value.completed / stepStats.value.total) * 100)
})

// DRY: Stats configuration
const statsConfig = computed(() => [
  { key: 'total', label: 'Steps', color: 'text-blue-600 dark:text-blue-400', value: stepStats.value.total },
  { key: 'completed', label: 'Completed', color: 'text-green-600 dark:text-green-400', value: stepStats.value.completed },
  { key: 'active', label: 'Active', color: 'text-orange-600 dark:text-orange-400', value: stepStats.value.active },
  { key: 'requiresApproval', label: 'Approvals', color: 'text-purple-600 dark:text-purple-400', value: stepStats.value.requiresApproval }
])

// DRY: Left details configuration
const leftDetailsConfig = computed(() => [
  {
    key: 'module',
    label: 'Module',
    value: getModuleDisplayName(workflowTemplate.value.module || props.activeWorkflow?.module),
    show: !!(workflowTemplate.value.module || props.activeWorkflow?.module),
    valueClass: 'font-medium text-gray-900 dark:text-white'
  },
  {
    key: 'category',
    label: 'Category',
    value: formatDisplayText(workflowTemplate.value.category || props.activeWorkflow?.category),
    show: !!(workflowTemplate.value.category || props.activeWorkflow?.category),
    valueClass: 'font-medium text-gray-900 dark:text-white'
  },
  {
    key: 'duration',
    label: 'Duration',
    value: workflowTemplate.value.estimated_duration || props.activeWorkflow?.estimated_duration,
    show: !!(workflowTemplate.value.estimated_duration || props.activeWorkflow?.estimated_duration),
    valueClass: 'font-medium text-gray-900 dark:text-white'
  },
  {
    key: 'complexity',
    label: 'Complexity',
    value: formatDisplayText(workflowTemplate.value.complexity || props.activeWorkflow?.complexity),
    show: !!(workflowTemplate.value.complexity || props.activeWorkflow?.complexity),
    valueClass: `font-medium ${getComplexityColor(workflowTemplate.value.complexity || props.activeWorkflow?.complexity)}`
  }
])

// DRY: Right details configuration
const rightDetailsConfig = computed(() => [
  {
    key: 'id',
    label: 'ID',
    value: (props.activeWorkflow?.workflowNr || props.activeWorkflow?.id)?.split('-').pop(),
    show: !!(props.activeWorkflow?.workflowNr || props.activeWorkflow?.id),
    valueClass: 'bg-gray-200 dark:bg-gray-600  rounded font-mono text-gray-900 dark:text-gray-100',
    isCode: true
  }
  /*,
  {
    key: 'status',
    label: 'Status',
    value: formatDisplayText(props.activeWorkflow?.status),
    show: !!props.activeWorkflow?.status,
    valueClass: `font-medium ${getStatusColor(props.activeWorkflow?.status)}`
  }*/
])

// DRY: Common CSS classes
const flexClasses = {
  center: 'flex items-center',
  centerSpaced: 'flex items-center space-x-3',
  centerSpacedLarge: 'flex items-center space-x-4',
  responsive: 'flex-1 min-w-0',
  shrink: 'flex-shrink-0'
}

const textClasses = {
  title: 'font-bold text-gray-900 dark:text-white truncate',
  subtitle: 'text-gray-600 dark:text-gray-400 truncate',
  label: 'text-gray-500 dark:text-gray-400',
  detailLabel: 'text-gray-500 dark:text-gray-400',
  detailItem: 'flex items-center space-x-1'
}

// Dynamic utility functions
function getModuleDisplayName(module) {
  if (!module) return 'General'
  
  // Check if module exists in our configuration
  return moduleDisplayNames[module.toLowerCase()] || formatDisplayText(module)
}

function getStatusColor(status) {
  if (!status) return 'text-gray-600 dark:text-gray-400'
  
  // Check if status exists in our configuration  
  return statusColors[status.toLowerCase()] || 'text-gray-600 dark:text-gray-400'
}

function getComplexityColor(complexity) {
  if (!complexity) return 'text-gray-600 dark:text-gray-400'
  
  // Check if complexity exists in our configuration
  return complexityColors[complexity.toLowerCase()] || 'text-gray-600 dark:text-gray-400'
}

function formatDisplayText(text) {
  if (!text) return ''
  
  // Convert snake_case and kebab-case to Title Case
  return text
    .replace(/[_-]/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
}

// Dynamic font sizes - ONLY changing styling references
const _small = computed(() => props.scaling.font.small) // WAS: `${settings.fontSizesComputed.value.bodyVerySmall}px`
const _h2 = computed(() => props.scaling.font.heading) // WAS: `${settings.fontSizesComputed.value.h2}px`
const _h4 = computed(() => props.scaling.font.title) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`
</script>

<template>
  <!-- Compact Header -->
  <div class=" bg-gray-100 dark:bg-gray-900 border-b border-gray-300 dark:border-gray-900">
    <div class="pt-4 px-4 pb-2">
      <!-- Top Row: Title, Progress, and Close -->
      <div class="flex items-center justify-between mb-3">

        <div :class="[flexClasses.centerSpacedLarge, flexClasses.responsive]">
          
          <!-- Workflow Icon & Title -->
          <div :class="[flexClasses.centerSpaced, flexClasses.responsive]">
            

            <div :class="[flexClasses.responsive]">

              <h2 :style="{ fontSize: _h2 }" :class="textClasses.title">
              <i :style="{ fontSize: _body }" class="fas fa-sitemap text-indigo-600 dark:text-indigo-400 "></i>
                {{ workflowName }} version 3
              </h2>
              <p v-if="workflowDescription" 
                 :style="{ fontSize: _caption }" 
                 :class="textClasses.subtitle">
                {{ workflowDescription }}
              </p>
            </div>

            <!-- Progress Ring -->
            <div class="relative w-12 h-12">
              <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                <path class="text-gray-400 dark:text-blue-600" stroke="currentColor" stroke-width="3" fill="none" 
                      :stroke-dasharray="`${progressPercentage}, 100`"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
              </svg>
              <div :style="{ fontSize: _small }" class="absolute inset-0 flex items-center justify-center">
                <span class="font-bold text-gray-900 dark:text-white">{{ progressPercentage }}%</span>
              </div>
            </div>

          </div>
        </div>

        <!-- Close Button -->
        <button @click="emit('close')" 
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 ml-4">
            <i :style="{ fontSize: _small }" class="fas fa-times"></i>
        </button>
      </div>

        <div :style="{ fontSize: _small }" class="grid grid-cols-4">

          <!-- Left Details (DRY) -->
          <!-- Compact Stats -->
          <div :class="[flexClasses.centerSpaced, flexClasses.shrink]">
            <!-- Quick Stats (DRY) -->
            <div class="flex space-x-8 text-center">
              <div v-for="stat in statsConfig" :key="stat.key" >
                <div :class="['font-bold', stat.color]">{{ stat.value }}</div>
                <div :style="{ fontSize: _small }">{{ stat.label }}</div>
              </div>
            </div>
          </div>

                
          <!-- Right Details (DRY) -->
          <!-- Compact Details Row (DRY) -->
          <div class="flex items-end justify-end space-x-4 col-span-3" >

            <div class="flex items-end space-x-6">
              <div v-for="detail in leftDetailsConfig" 
                   v-show="detail.show" 
                   :key="detail.key" 
                   :class="textClasses.detailItem">
                <span :class="textClasses.detailLabel">{{ detail.label }}:</span>
                <span :class="detail.valueClass">{{ detail.value }}</span>
              </div>
            </div>

            <div v-for="detail in rightDetailsConfig" 
                 v-show="detail.show" 
                 :key="detail.key" 
                 :class="textClasses.detailItem">
              <span :class="textClasses.detailLabel">{{ detail.label }}:</span>
              <component :is="detail.isCode ? 'code' : 'span'" :class="detail.valueClass">
                {{ detail.value }}
              </component>
            </div>
          </div>


        </div>

    </div>
  </div>

  
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
