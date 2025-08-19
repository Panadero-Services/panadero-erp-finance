<!--
  Financial ERP Workflow Dashboard
  @version 1.0.8
  @date 18-Aug-2025
  @description Enhanced workflow management interface with direct creation and modal management
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from '../../stores/workflowStore.js'

// Props
const props = defineProps({
  showStats: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['workflow-selected', 'workflow-started'])

// Store
const workflowStore = useWorkflowStore()

// Reactive data
const selectedWorkflow = ref(null)
const showOverlay = ref(false)
const activeWorkflows = ref([])
const selectedActiveWorkflow = ref(null)
const showWorkflowModal = ref(false)
const activeTab = ref('info')

// Computed properties
const workflowModules = computed(() => {
  const templates = workflowStore.builtInTemplates || []
  
  if (!Array.isArray(templates) || templates.length === 0) {
    return []
  }

// Group templates by module
  const modules = {}
  templates.forEach(template => {
    if (!template || !template.id) return
  
    const moduleKey = template.module || 'general'
    if (!modules[moduleKey]) {
      // Format module name nicely
      let moduleName = 'General'
      let moduleDescription = 'General workflows'
      
      if (template.module) {
        switch (template.module) {
          case 'gl':
            moduleName = 'General Ledger'
            moduleDescription = 'Core accounting and journal entry workflows'
            break
          case 'ap':
            moduleName = 'Accounts Payable'
            moduleDescription = 'Vendor invoice and payment workflows'
            break
          case 'ar':
            moduleName = 'Accounts Receivable'
            moduleDescription = 'Customer invoice and collection workflows'
            break
          case 'cf':
            moduleName = 'Cash Flow'
            moduleDescription = 'Cash management and forecasting workflows'
            break
          case 'procurement':
            moduleName = 'Procurement'
            moduleDescription = 'Vendor and supplier management workflows'
            break
          default:
            moduleName = template.module.toUpperCase()
            moduleDescription = `Workflows for ${template.module} module`
        }
      }
      
      modules[moduleKey] = {
        name: moduleName,
        description: moduleDescription,
        workflows: []
      }
    }
    modules[moduleKey].workflows.push(template)
  })
  
  return Object.values(modules)
})

// Get all workflows in a flat array for the simple grid
const allWorkflows = computed(() => {
  const workflows = []
  if (workflowModules.value && Array.isArray(workflowModules.value)) {
    workflowModules.value.forEach(module => {
      if (module && module.workflows && Array.isArray(module.workflows)) {
        module.workflows.forEach(workflow => {
          if (workflow && workflow.id) {
            workflows.push(workflow)
          }
        })
      }
    })
  }
  return workflows
})

// Methods
function openWorkflowOverlay(workflow) {
  selectedWorkflow.value = workflow
  showOverlay.value = true
}

function startWorkflowDirectly(workflow) {
  try {
    const instance = workflowStore.createWorkflowInstance(workflow.id, {
      created_by: 'current_user',
      workflow_data: {
        name: workflow.name,
        description: workflow.description,
        category: workflow.category,
        module: workflow.module
      }
    })
    
    workflowStore.startWorkflow(instance.id)
    emit('workflow-started', instance)
    
    // Add to active workflows with unique workflowNr
    const workflowNr = `WF-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`
    const activeWorkflow = {
      workflowNr,
      instanceId: instance.id,
      name: workflow.name,
      module: workflow.module,
      startedAt: new Date(),
      status: 'active',
      template: workflow // Store the original template for the modal
    }
    activeWorkflows.value.push(activeWorkflow)
    
    // Open the big modal directly
    selectedActiveWorkflow.value = activeWorkflow
    showWorkflowModal.value = true
    
    console.log(`Started workflow directly: ${workflow.name}`, instance)
  } catch (error) {
    console.error('Failed to start workflow:', error)
  }
}

function closeOverlay() {
  showOverlay.value = false
  selectedWorkflow.value = null
}

function openWorkflowModal(activeWorkflow) {
  selectedActiveWorkflow.value = activeWorkflow
  showWorkflowModal.value = true
}

function closeWorkflowModal() {
  showWorkflowModal.value = false
  selectedActiveWorkflow.value = null
  activeTab.value = 'info' // Reset to default tab
}

function deleteActiveWorkflow(workflowToDelete) {
  const index = activeWorkflows.value.findIndex(w => w.workflowNr === workflowToDelete.workflowNr)
  if (index > -1) {
    activeWorkflows.value.splice(index, 1)
    
    // Close modal if this workflow was selected
    if (selectedActiveWorkflow.value?.workflowNr === workflowToDelete.workflowNr) {
      closeWorkflowModal()
    }
    
    console.log(`Deleted workflow: ${workflowToDelete.name}`)
  }
}

function startWorkflow(workflow) {
  emit('workflow-selected', workflow)
  
  try {
    const instance = workflowStore.createWorkflowInstance(workflow.id, {
      created_by: 'current_user',
      workflow_data: {
        name: workflow.name,
        description: workflow.description,
        category: workflow.category,
        module: workflow.module
      }
    })
    
    workflowStore.startWorkflow(instance.id)
    emit('workflow-started', instance)
    
    // Add to active workflows with unique workflowNr
    const workflowNr = `WF-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`
    activeWorkflows.value.push({
      workflowNr,
      instanceId: instance.id,
      name: workflow.name,
      module: workflow.module,
      startedAt: new Date(),
      status: 'active'
    })
    
    console.log(`Started workflow: ${workflow.name}`, instance)
    closeOverlay()
  } catch (error) {
    console.error('Failed to start workflow:', error)
  }
}

function getDifficultyColor(complexity) {
  switch (complexity) {
    case 'simple': return 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20'
    case 'moderate': return 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20'
    case 'complex': return 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/20'
    default: return 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-900/20'
  }
}

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

// Lifecycle
onMounted(() => {
  console.log('WorkflowDashboard mounted')
  console.log('Store object:', workflowStore)
  console.log('Available templates:', workflowStore.builtInTemplates)
  console.log('Templates type:', typeof workflowStore.builtInTemplates)
  console.log('Templates length:', workflowStore.builtInTemplates?.length)
})
</script>

<template>
  <div class="p-6">
    <!-- Version Header -->
    <div class="mb-4 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Workflow Management System</h1>
        <p class="text-sm text-gray-500">Streamline business processes with automated workflows, approvals, and tracking</p>
      </div>
      <div class="text-right">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
          v1.0.8
        </span>
        <p class="text-xs text-gray-500 mt-1">18-Aug-2025</p>
      </div>
    </div>

    <!-- Debug Info -->
    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
      <p class="text-blue-800 font-medium">
        Debug: {{ workflowStore.builtInTemplates?.length || 0 }} templates, {{ allWorkflows.length }} workflows
      </p>
    </div>

    <!-- Active Workflows -->
    <div v-if="activeWorkflows.length > 0" class="mb-8">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-play-circle mr-2 text-green-600"></i>
        Active Workflows ({{ activeWorkflows.length }})
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="activeWorkflow in activeWorkflows" :key="activeWorkflow.workflowNr"
             class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
          <div class="flex justify-between items-start mb-2">
            <h3 class="font-medium text-gray-900 dark:text-white cursor-pointer" @click="openWorkflowModal(activeWorkflow)">
              {{ activeWorkflow.name }}
            </h3>
            <div class="flex items-center space-x-2">
              <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                {{ activeWorkflow.status }}
              </span>
              <button 
                @click.stop="deleteActiveWorkflow(activeWorkflow)"
                class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                title="Delete workflow">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400 cursor-pointer" @click="openWorkflowModal(activeWorkflow)">
            <div>
              <i class="fas fa-hashtag mr-2"></i>
              {{ activeWorkflow.workflowNr }}
            </div>
            <div>
              <i class="fas fa-tag mr-2"></i>
              {{ getModuleDisplayName(activeWorkflow.module) }}
            </div>
            <div>
              <i class="fas fa-clock mr-2"></i>
              Started {{ activeWorkflow.startedAt.toLocaleTimeString() }}
            </div>
          </div>
      </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="!workflowModules.length" class="text-center py-8">
      <div class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <p class="text-gray-600 dark:text-gray-300">Loading workflows...</p>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Found {{ workflowStore.builtInTemplates?.length || 0 }} templates in store
      </p>
    </div>

    <!-- Workflow Templates -->
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="workflow in allWorkflows" :key="workflow.id" 
             class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition-all cursor-pointer"
             @click="startWorkflowDirectly(workflow)">
          
          <!-- Header with module badge -->
          <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1 leading-tight">
                {{ workflow.name }}
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                {{ workflow.description || 'No description available' }}
              </p>
            </div>
            <span v-if="workflow.module" 
                  class="ml-3 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 flex-shrink-0">
              {{ getModuleDisplayName(workflow.module) }}
            </span>
          </div>

          <!-- Stats Row -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
              <div class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ workflow.steps?.length || 0 }}
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Steps</div>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
              <div class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ workflow.estimated_duration || 'N/A' }}
        </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Duration</div>
               </div>
             </div>
            
          <!-- Details -->
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-600 dark:text-gray-400">
                <i class="fas fa-user-check mr-2"></i>Approvals
              </span>
              <span class="text-gray-900 dark:text-white font-medium">
                {{ workflow.steps?.filter(s => s.type === 'approval').length || 0 }}
              </span>
                 </div>
                 
            <div class="flex items-center justify-between">
              <span class="text-gray-600 dark:text-gray-400">
                <i class="fas fa-layer-group mr-2"></i>Category
              </span>
              <span class="text-gray-900 dark:text-white font-medium capitalize">
                {{ workflow.category?.replace('_', ' ') || 'General' }}
              </span>
                 </div>
                 
            <div v-if="workflow.complexity" class="flex items-center justify-between">
              <span class="text-gray-600 dark:text-gray-400">
                <i class="fas fa-signal mr-2"></i>Complexity
              </span>
              <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getDifficultyColor(workflow.complexity)">
                {{ workflow.complexity }}
              </span>
               </div>
            </div>
            
          <!-- Action hint -->
          <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
              <i class="fas fa-mouse-pointer mr-1"></i>
              Click to start workflow
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Workflow Start Overlay -->
    <div v-if="showOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeOverlay">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-4xl w-full mx-8 shadow-2xl" @click.stop>
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Start Workflow
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ selectedWorkflow?.name }}
            </p>
          </div>
          <button @click="closeOverlay" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <!-- Workflow Details -->
        <div class="mb-6 space-y-3">
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">
              {{ selectedWorkflow?.description || 'No description available' }}
            </p>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-500 dark:text-gray-400">Steps:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.steps?.length || 0 }}
              </span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Duration:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.estimated_duration || 'N/A' }}
              </span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Module:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ getModuleDisplayName(selectedWorkflow?.module) }}
              </span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Approvals:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.steps?.filter(s => s.type === 'approval').length || 0 }}
              </span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-3">
          <button 
            @click="closeOverlay"
            class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
          </button>
          <button 
            @click="startWorkflow(selectedWorkflow)"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-play mr-2"></i>
            Start Workflow
          </button>
        </div>
      </div>
    </div>

    <!-- Workflow Management Modal -->
    <div v-if="showWorkflowModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeWorkflowModal">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-7xl h-5/6 mx-4 flex flex-col" @click.stop>
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-600">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
              <i class="fas fa-cogs text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ selectedActiveWorkflow?.name }}
              </h2>
              <div class="flex items-center space-x-3 mt-1">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  <i class="fas fa-hashtag mr-1"></i>{{ selectedActiveWorkflow?.workflowNr }}
                </span>
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                  {{ selectedActiveWorkflow?.status }}
                </span>
              </div>
            </div>
          </div>
          <button @click="closeWorkflowModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
          </button>
        </div>

                <!-- Modal Content - 4 Columns -->
        <div class="flex-1 grid grid-cols-4 overflow-hidden w-full">

          <!-- Column 1: Workflow Stepper -->
          <div class="bg-gray-50 p-4 border-r border-gray-200 overflow-y-auto">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
              <i class="fas fa-list-ol mr-1 text-indigo-600"></i>
              Workflow Steps
            </h3>
            <div class="space-y-4">
              <!-- Step 1: Information Collection - COMPLETED -->
              <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
                <div class="flex items-center space-x-3 mb-2">
                  <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
                    1
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">Information Collection</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Gather basic vendor information</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                  <i class="fas fa-clock mr-1"></i>
                  Completed: 17-8-2025 14:32
                </p>
                <!-- Hover Tooltip -->
                <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
                  Gather basic vendor information and documents (~30 minutes)
                </div>
              </div>
              
              <!-- Step 2: Due Diligence - COMPLETED -->
              <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
                <div class="flex items-center space-x-3 mb-2">
                  <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
                    2
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">Due Diligence</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Verify credentials and background</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                  <i class="fas fa-clock mr-1"></i>
                  Completed: 17-8-2025 16:45
                </p>
                <!-- Hover Tooltip -->
                <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
                  Verify credentials and conduct background checks (~2-3 business days, procurement_manager)
                </div>
              </div>
              
              <!-- Step 3: Contract Negotiation - ACTIVE -->
              <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors relative group">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
                      3
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Contract Negotiation</span>
                  </div>
                  <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full">
                    ACTIVE
                  </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Negotiate terms and agreement</p>
                <!-- Hover Tooltip -->
                <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
                  Negotiate terms and execute binding agreement (~1-2 weeks, legal_counsel)
                </div>
              </div>

              <!-- Step 4: Final Approval - PENDING -->
              <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-colors relative group">
                <div class="flex items-center space-x-3 mb-2">
                  <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-xs font-medium">
                    4
                  </div>
                  <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Final Approval</span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">Executive sign-off and contract execution</p>
                <!-- Hover Tooltip -->
                <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
                  Executive sign-off and contract execution (~2-3 business days, ceo, cfo)
                </div>
              </div>

              <!-- Step 5: Complete - PENDING -->
              <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-colors relative group">
                <div class="flex items-center space-x-3 mb-2">
                  <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-xs font-medium">
                    5
                  </div>
                  <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Complete</span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">Vendor onboarding completed</p>
                <!-- Hover Tooltip -->
                <div class="absolute left-full ml-2 top-0 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">
                  Vendor onboarding completed (Immediate)
                </div>
              </div>
            </div>
          </div>

          <!-- Column 2: Form (spans 2 columns) -->
          <div class="col-span-2 p-6 overflow-y-auto max-h-full">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              <i class="fas fa-edit mr-2 text-green-600"></i>
              Current Step Form
            </h3>
            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Document Title
                  </label>
                  <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter document title...">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Amount
                  </label>
                  <input type="number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                  </label>
                  <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter description..."></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Category
                  </label>
                  <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>General</option>
                    <option>Operations</option>
                    <option>Finance</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Priority
                  </label>
                  <div class="flex space-x-4">
                    <label class="flex items-center">
                      <input type="radio" name="priority" value="low" class="mr-2">
                      <span class="text-sm">Low</span>
                    </label>
                    <label class="flex items-center">
                      <input type="radio" name="priority" value="medium" class="mr-2" checked>
                      <span class="text-sm">Medium</span>
                    </label>
                    <label class="flex items-center">
                      <input type="radio" name="priority" value="high" class="mr-2">
                      <span class="text-sm">High</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Column 3: Tabbed Info & History -->
          <div class="bg-gray-50 flex flex-col overflow-hidden max-h-full">

            <!-- Tab Headers -->
            <div class="flex border-b border-gray-200 flex-shrink-0">
              <button 
                @click="activeTab = 'info'"
                :class="[
                  'flex-1 px-4 py-3 text-sm font-medium transition-colors',
                  activeTab === 'info' 
                    ? 'bg-white text-blue-600 border-b-2 border-blue-600' 
                    : 'text-gray-500 hover:text-gray-700'
                ]">
                <i class="fas fa-info-circle mr-2"></i>
                Workflow Info
              </button>
              <button 
                @click="activeTab = 'history'"
                :class="[
                  'flex-1 px-4 py-3 text-sm font-medium transition-colors',
                  activeTab === 'history' 
                    ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' 
                    : 'text-gray-500 hover:text-gray-700'
                ]">
                <i class="fas fa-history mr-2"></i>
                History
              </button>
            </div>

            <!-- Tab Content -->
            <div class="flex-1 overflow-y-auto min-h-0">
              
              <!-- Workflow Info Tab -->
              <div v-if="activeTab === 'info'" class="p-4 h-full overflow-y-auto">
                <div class="space-y-4">
                  
                  <!-- Workflow Information -->
                  <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                      <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                      Vendor Onboarding Workflow
                    </h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-gray-700">
                      <p class="mb-3"><strong>Purpose:</strong> Complete vendor registration and verification process for new suppliers.</p>
                      <p class="mb-3"><strong>Scope:</strong> This workflow covers information collection, due diligence checks, contract negotiation, and final approval.</p>
                      <p><strong>Duration:</strong> Typically 2-3 weeks depending on vendor complexity and response times.</p>
                    </div>
                  </div>

                  <!-- Current Step Instructions -->
                  <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                      <i class="fas fa-tasks mr-2 text-green-600"></i>
                      Current Step: Contract Negotiation
                    </h4>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-gray-700">
                      <p class="mb-2"><strong>Instructions:</strong></p>
                      <ul class="list-disc pl-5 space-y-1">
                        <li>Review vendor's initial contract proposal</li>
                        <li>Negotiate terms including payment schedules, delivery terms, and SLAs</li>
                        <li>Ensure compliance with company procurement policies</li>
                        <li>Coordinate with Legal team for contract review</li>
                        <li>Document all negotiation points and agreements</li>
                      </ul>
                      <p class="mt-3 text-orange-600"><strong>⚠️ Important:</strong> All contract changes must be approved by Legal before proceeding.</p>
                    </div>
                  </div>

                  <!-- Required Approvals -->
                  <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                      <i class="fas fa-user-check mr-2 text-purple-600"></i>
                      Required Approvals
                    </h4>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 gap-2 text-sm">
                        <div class="flex justify-between">
                          <span>Legal Counsel:</span>
                          <span class="text-orange-600 font-medium">Pending</span>
                        </div>
                        <div class="flex justify-between">
                          <span>Procurement Director:</span>
                          <span class="text-orange-600 font-medium">Pending</span>
                        </div>
                        <div class="flex justify-between">
                          <span>Finance Manager:</span>
                          <span class="text-green-600 font-medium">Approved</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- History Tab -->
              <div v-if="activeTab === 'history'" class="p-4 h-full overflow-y-auto">
                <div class="space-y-4">
                  
                  <!-- Workflow History -->
                  <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                      <i class="fas fa-history mr-2 text-indigo-600"></i>
                      Workflow History
                    </h4>
                    <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 font-mono text-xs text-gray-800 overflow-y-auto max-h-64">
                      <div>19-8-2025, 12:28:28  created by demo_user</div>
                      <div>19-8-2025, 12:28:28  Information completed</div>
                      <div>19-8-2025, 16:45:22  Form submitted</div>
                      <div>19-8-2025, 16:45:42  Api Request Sent</div>
                      <div>19-8-2025, 16:46:11  Api Response Error</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>



        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
          <div class="flex justify-between items-center">
            <div class="flex space-x-3">
              <button class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-pause mr-2"></i>
                Pause
              </button>
              <button class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                <i class="fas fa-stop mr-2"></i>
                Cancel
              </button>
            </div>
            <div class="flex space-x-3">
              <button class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Previous
              </button>
              <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>
                Next Step
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
