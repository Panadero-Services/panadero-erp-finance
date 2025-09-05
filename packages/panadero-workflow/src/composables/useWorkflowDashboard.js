/**
 * Workflow Dashboard Composable
 * @version 1.1.2
 * @date 27-Aug-2025
 * @description Centralized logic for workflow dashboard functionality with reactive store integration
 */
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from './workflowStore.js'

export function useWorkflowDashboard() {
  const workflowStore = useWorkflowStore()

  // Reactive state
  const selectedActiveWorkflow = ref(null)
  const showWorkflowModal = ref(false)
  const activeTab = ref('info')
  const configWorkflows = ref([])
  const loadingConfigWorkflows = ref(false)

  // Reactive store data - just use the store directly
// Reactive store data - FIXED: Use workflows instead of inMemoryWorkflows
const activeWorkflows = computed(() => {
  return workflowStore.workflows.map(instance => {
    // Just add template info if needed, but keep original field names
    const template = workflowStore.builtInTemplates.find(t => t.id === instance.template_id)
    
    return {
      ...instance,  // ← KEEP ALL ORIGINAL FIELDS!
      template: instance.template || template,  // ← Add template if missing
    }
  })
})
  // Load workflows from store on mount
  onMounted(async () => {
    await loadWorkflowsFromStore()
  })

  // Load workflows from store (simplified version)
  async function loadWorkflowsFromStore() {
    try {
      loadingConfigWorkflows.value = true
      
      // Use built-in templates from the workflow store
      const workflows = workflowStore.builtInTemplates || []
      configWorkflows.value = workflows.filter(Boolean)
      
      console.debug('Loaded workflows from store:', configWorkflows.value)
    } catch (error) {
      console.error('Failed to load workflows from store:', error)
    } finally {
      loadingConfigWorkflows.value = false
    }
  }

  // Computed properties - using store templates
  const workflowModules = computed(() => {
    const workflows = configWorkflows.value.length > 0 ? 
      configWorkflows.value : 
      (workflowStore.builtInTemplates || [])
    
    if (!Array.isArray(workflows) || workflows.length === 0) {
      return []
    }

    // Group workflows by module
    const modules = {}
    workflows.forEach(workflow => {
      if (!workflow || !workflow.id) return
    
      const moduleKey = workflow.module || 'general'
      if (!modules[moduleKey]) {
        modules[moduleKey] = {
          name: getModuleDisplayName(moduleKey),
          description: `Workflows for ${getModuleDisplayName(moduleKey)}`,
          icon: 'fas fa-cogs',
          color: 'blue',
          workflows: []
        }
      }
      modules[moduleKey].workflows.push(workflow)
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

  // Workflow management methods
  function openWorkflowModal(activeWorkflow) {
    selectedActiveWorkflow.value = activeWorkflow
    showWorkflowModal.value = true
  }

  function closeWorkflowModal() {
    showWorkflowModal.value = false
    selectedActiveWorkflow.value = null
    activeTab.value = 'info' // Reset to default tab
  }

async function startWorkflowDirectly(workflow) {
  try {
    // Use workflow directly from store
    const fullWorkflow = workflow

    // FIXED: Create workflow instance
    const instance = await workflowStore.createWorkflowInstance(fullWorkflow.id, {
      created_by: 'current_user',
      workflow_data: {
        name: fullWorkflow.name,
        description: fullWorkflow.description,
        // ... other data
      }
    })

    // FIXED: Start the workflow to load steps and entity data
    const startedWorkflow = await workflowStore.startWorkflow(instance.id)
    
    // Open the big modal directly
    selectedActiveWorkflow.value = startedWorkflow
    showWorkflowModal.value = true
    
    console.debug(`Started workflow: ${fullWorkflow.name}`, {
      instance: instance,
      activeWorkflow: selectedActiveWorkflow.value,
      source: "store",
      stepsCount: startedWorkflow?.steps?.length || 0,
      template: fullWorkflow
    })
  } catch (error) {
    console.error('Failed to start workflow:', error)
    throw error
  }
}

  // Load workflow details (simplified)
  async function loadWorkflowDetails(_id) {
    try {
      // Find workflow in store templates
      const workflow = workflowStore.builtInTemplates?.find(w => w.id === _id)
      return workflow || null
    } catch (error) {
      console.error(`Failed to load workflow details for ${_id}:`, error)
      return null
    }
  }

  function deleteActiveWorkflow(workflowToDelete) {
    // FIXED: Remove from workflows instead of inMemoryWorkflows
    const instanceIndex = workflowStore.workflows.findIndex(w => w.id === workflowToDelete.id)
    if (instanceIndex > -1) {
      workflowStore.workflows.splice(instanceIndex, 1)
      
      // Close modal if this workflow was selected
      if (selectedActiveWorkflow.value?.workflowNr === workflowToDelete.workflowNr) {
        closeWorkflowModal()
      }
      
      console.debug(`Deleted workflow: ${workflowToDelete.name}`)
    }
  }

  // Utility functions
  function getDifficultyColor(complexity) {
    switch (complexity) {
      case 'simple': return 'text-green-600 bg-green-100'
      case 'moderate': return 'text-yellow-600 bg-yellow-100'
      case 'complex': return 'text-red-600 bg-red-100'
      default: return 'text-gray-600 bg-gray-100'
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

  return {
    // State
    selectedActiveWorkflow,
    showWorkflowModal,
    activeTab,
    configWorkflows,
    loadingConfigWorkflows,
    
    // Computed
    activeWorkflows,  // ← ADD THIS LINE!
    workflowModules,
    allWorkflows,
    
    // Methods
    openWorkflowModal,
    closeWorkflowModal,
    startWorkflowDirectly,
    deleteActiveWorkflow,
    loadWorkflowsFromStore,
    loadWorkflowDetails,
    getDifficultyColor,
    getModuleDisplayName,
    
    // Store references
    workflowStore
  }
}
