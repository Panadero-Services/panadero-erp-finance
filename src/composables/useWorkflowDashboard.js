/**
 * Workflow Dashboard Composable
 * @version 1.0.8
 * @date 18-Aug-2025
 * @description Centralized logic for workflow dashboard functionality
 */
import { ref, computed } from 'vue'
import { useWorkflowStore } from '../stores/workflowStore.js'

export function useWorkflowDashboard() {
  const workflowStore = useWorkflowStore()

  // Reactive state
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

  function startWorkflowDirectly(workflow, emit) {
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
    getDifficultyColor,
    getModuleDisplayName,
    
    // Store reference
    workflowStore
  }
}
