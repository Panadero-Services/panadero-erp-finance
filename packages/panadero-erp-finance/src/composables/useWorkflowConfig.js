/**
 * Workflow Configuration Composable
 * @version 1.0.8
 * @date 27-Jan-2025
 * @description Handles loading workflows from modular JSON configuration files
 */
import { ref, computed, onMounted } from 'vue'
import workflowConfig from '../config/workflow-config.json'
import workflowIndex from '../config/workflows/workflow-index.json'

export function useWorkflowConfig() {
  // Reactive state
  const config = ref(workflowConfig)
  const index = ref(workflowIndex)
  const loadedWorkflows = ref(new Map())
  const loading = ref(false)
  const error = ref(null)

  // Computed properties
  const availableModules = computed(() => {
    return config.value?.modules || {}
  })

  const workflowCategories = computed(() => {
    return config.value?.constants?.workflowCategories || {}
  })

  const complexityLevels = computed(() => {
    return config.value?.constants?.complexityLevels || {}
  })

  const actionTypes = computed(() => {
    return {
      ...config.value?.constants?.actionTypes?.core || {},
      ...config.value?.constants?.actionTypes?.financial || {}
    }
  })

  const allWorkflowsFromIndex = computed(() => {
    return index.value?.workflows || []
  })

  const moduleStats = computed(() => {
    return index.value?.modules || {}
  })

  // Methods
  async function loadWorkflow(workflowId) {
    if (loadedWorkflows.value.has(workflowId)) {
      return loadedWorkflows.value.get(workflowId)
    }

    const workflowInfo = index.value.workflows.find(w => w.id === workflowId)
    if (!workflowInfo) {
      throw new Error(`Workflow ${workflowId} not found in index`)
    }

    try {
      loading.value = true
      // In a real implementation, you'd use fetch to load the file
      // For now, we'll simulate loading from the created files
      const workflowData = await loadWorkflowFromFile(workflowInfo.file)
      loadedWorkflows.value.set(workflowId, workflowData)
      return workflowData
    } catch (err) {
      error.value = `Failed to load workflow ${workflowId}: ${err.message}`
      throw err
    } finally {
      loading.value = false
    }
  }

  async function loadWorkflowFromFile(filePath) {
    // Simulate loading from the specific workflow files we created
    try {
      let workflowData
      
      switch (filePath) {
        case 'procurement/vendor-onboarding.workflow.json':
          workflowData = await import('../config/workflows/procurement/vendor-onboarding.workflow.json')
          break
        case 'general-ledger/simple-journal-entry.workflow.json':
          workflowData = await import('../config/workflows/general-ledger/simple-journal-entry.workflow.json')
          break
        case 'accounts-payable/vendor-invoice-approval.workflow.json':
          workflowData = await import('../config/workflows/accounts-payable/vendor-invoice-approval.workflow.json')
          break
        default:
          throw new Error(`Workflow file ${filePath} not found`)
      }
      
      return workflowData.default || workflowData
    } catch (error) {
      console.error(`Error loading workflow from ${filePath}:`, error)
      throw error
    }
  }

  async function loadAllWorkflows() {
    const workflows = []
    const activeWorkflows = index.value.workflows.filter(w => w.status === 'active')
    
    for (const workflowInfo of activeWorkflows) {
      try {
        const workflowData = await loadWorkflow(workflowInfo.id)
        workflows.push({
          ...workflowData,
          indexInfo: workflowInfo
        })
      } catch (err) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, err)
      }
    }
    
    return workflows
  }

  async function loadWorkflowsByModule(moduleId) {
    const moduleWorkflows = index.value.workflows.filter(w => 
      w.module === moduleId && w.status === 'active'
    )
    const workflows = []

    for (const workflowInfo of moduleWorkflows) {
      try {
        const workflowData = await loadWorkflow(workflowInfo.id)
        workflows.push({
          ...workflowData,
          indexInfo: workflowInfo
        })
      } catch (err) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, err)
      }
    }

    return workflows
  }

  async function loadWorkflowsByCategory(category) {
    const categoryWorkflows = index.value.workflows.filter(w => 
      w.category === category && w.status === 'active'
    )
    const workflows = []

    for (const workflowInfo of categoryWorkflows) {
      try {
        const workflowData = await loadWorkflow(workflowInfo.id)
        workflows.push({
          ...workflowData,
          indexInfo: workflowInfo
        })
      } catch (err) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, err)
      }
    }

    return workflows
  }

  // Transform loaded workflow data to match the store format
  function transformWorkflowForStore(workflowData) {
    if (!workflowData?.workflow) return null
    
    const workflow = workflowData.workflow
    return {
      id: workflow.id,
      name: workflow.name,
      description: workflow.description,
      category: workflow.category,
      module: workflow.module,
      version: workflow.version,
      complexity: workflow.complexity,
      estimated_duration: workflow.estimatedDuration,
      approval_levels: workflow.approvalLevels,
      tags: workflow.tags || [],
      steps: workflow.steps || [],
      sla_settings: workflow.slaSettings || {},
      notification_settings: workflow.notificationSettings || {},
      // Add metadata for tracking
      _metadata: workflowData.metadata,
      _source: 'config_file'
    }
  }

  // Get module display information
  function getModuleInfo(moduleId) {
    return availableModules.value[moduleId] || {
      name: moduleId,
      description: `${moduleId} workflows`,
      icon: 'fas fa-cog',
      color: 'gray'
    }
  }

  // Get category display name
  function getCategoryName(categoryKey) {
    return workflowCategories.value[categoryKey] || categoryKey
  }

  // Get complexity display information
  function getComplexityInfo(complexity) {
    return complexityLevels.value[complexity] || {
      name: complexity,
      description: `${complexity} complexity`,
      color: 'gray',
      icon: 'circle'
    }
  }

  // Validate workflow data
  function validateWorkflow(workflowData) {
    if (!workflowData?.workflow) {
      return { valid: false, error: 'Missing workflow data' }
    }

    const workflow = workflowData.workflow
    
    if (!workflow.id || !workflow.name) {
      return { valid: false, error: 'Workflow missing id or name' }
    }

    if (!workflow.steps || !Array.isArray(workflow.steps)) {
      return { valid: false, error: 'Workflow missing steps array' }
    }

    return { valid: true }
  }

  return {
    // State
    config,
    index,
    loadedWorkflows,
    loading,
    error,
    
    // Computed
    availableModules,
    workflowCategories,
    complexityLevels,
    actionTypes,
    allWorkflowsFromIndex,
    moduleStats,
    
    // Methods
    loadWorkflow,
    loadAllWorkflows,
    loadWorkflowsByModule,
    loadWorkflowsByCategory,
    transformWorkflowForStore,
    getModuleInfo,
    getCategoryName,
    getComplexityInfo,
    validateWorkflow
  }
}
