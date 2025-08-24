/**
 * Workflow Dashboard Composable
 * @version 1.0.9
 * @date 27-Jan-2025
 * @description Centralized logic for workflow dashboard functionality - simplified for package isolation
 */
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from './workflowStore.js'

export function useWorkflowDashboard() {
  const workflowStore = useWorkflowStore()

  // Reactive state
  const selectedWorkflow = ref(null)
  const showOverlay = ref(false)
  const activeWorkflows = ref([])
  const selectedActiveWorkflow = ref(null)
  const showWorkflowModal = ref(false)
  const activeTab = ref('info')
  const configWorkflows = ref([])
  const loadingConfigWorkflows = ref(false)

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
      
      console.log('Loaded workflows from store:', configWorkflows.value)
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
  function openWorkflowOverlay(workflow) {
    selectedWorkflow.value = workflow
    showOverlay.value = true
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

  async function startWorkflowDirectly(workflow, emit) {
    try {
      // Use workflow directly from store
      const fullWorkflow = workflow

      const instance = workflowStore.createWorkflowInstance(fullWorkflow.id, {
        created_by: 'current_user',
        workflow_data: {
          name: fullWorkflow.name,
          description: fullWorkflow.description,
          category: fullWorkflow.category,
          module: fullWorkflow.module,
          complexity: fullWorkflow.complexity,
          estimated_duration: fullWorkflow.estimated_duration
        }
      })
      
      // Wait for the workflow to start and steps to be loaded
      await workflowStore.startWorkflow(instance.id)
      emit('workflow-started', instance)
      
      // Add to active workflows with unique workflowNr
      const workflowNr = `WF-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`
      const activeWorkflow = {
        workflowNr,
        instanceId: instance.id,
        name: fullWorkflow.name,
        module: fullWorkflow.module,
        category: fullWorkflow.category,
        complexity: fullWorkflow.complexity,
        startedAt: new Date(),
        status: 'active',
        template: fullWorkflow, // Store the full workflow definition
        configWorkflow: null, // No config source in simplified version
        // Add the steps from the started workflow instance
        steps: instance.steps
      }
      activeWorkflows.value.push(activeWorkflow)
      
      // Open the big modal directly
      selectedActiveWorkflow.value = activeWorkflow
      showWorkflowModal.value = true
      
      console.log(`Started workflow directly: ${fullWorkflow.name}`, {
        instance,
        activeWorkflow,
        source: 'store',
        stepsCount: instance.steps.length
      })
    } catch (error) {
      console.error('Failed to start workflow:', error)
    }
  }

  // Load workflow details (simplified)
  async function loadWorkflowDetails(workflowId) {
    try {
      // Find workflow in store templates
      const workflow = workflowStore.builtInTemplates?.find(w => w.id === workflowId)
      return workflow || null
    } catch (error) {
      console.error(`Failed to load workflow details for ${workflowId}:`, error)
      return null
    }
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
    selectedWorkflow,
    showOverlay,
    activeWorkflows,
    selectedActiveWorkflow,
    showWorkflowModal,
    activeTab,
    configWorkflows,
    loadingConfigWorkflows,
    
    // Computed
    workflowModules,
    allWorkflows,
    
    // Methods
    openWorkflowOverlay,
    closeOverlay,
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
