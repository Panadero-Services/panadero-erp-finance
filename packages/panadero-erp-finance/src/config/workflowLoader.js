/**
 * Workflow Configuration Loader
 * Handles loading of modular workflow configurations
 */

import workflowConfig from './workflow-config.json'
import workflowIndex from './workflows/workflow-index.json'

class WorkflowLoader {
  constructor() {
    this.config = workflowConfig
    this.index = workflowIndex
    this.loadedWorkflows = new Map()
    this.cache = new Map()
  }

  /**
   * Get main workflow configuration
   */
  getConfig() {
    return this.config
  }

  /**
   * Get workflow index
   */
  getIndex() {
    return this.index
  }

  /**
   * Load a specific workflow by ID
   */
  async loadWorkflow(workflowId) {
    if (this.loadedWorkflows.has(workflowId)) {
      return this.loadedWorkflows.get(workflowId)
    }

    const workflowInfo = this.index.workflows.find(w => w.id === workflowId)
    if (!workflowInfo) {
      throw new Error(`Workflow ${workflowId} not found in index`)
    }

    try {
      // In a real implementation, you'd use fetch or fs to load the file
      const workflowData = await import(`./workflows/${workflowInfo.file}`)
      this.loadedWorkflows.set(workflowId, workflowData.default || workflowData)
      return this.loadedWorkflows.get(workflowId)
    } catch (error) {
      throw new Error(`Failed to load workflow ${workflowId}: ${error.message}`)
    }
  }

  /**
   * Load all workflows for a specific module
   */
  async loadModuleWorkflows(moduleId) {
    const moduleWorkflows = this.index.workflows.filter(w => w.module === moduleId)
    const workflows = []

    for (const workflowInfo of moduleWorkflows) {
      try {
        const workflow = await this.loadWorkflow(workflowInfo.id)
        workflows.push(workflow)
      } catch (error) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, error)
      }
    }

    return workflows
  }

  /**
   * Load all active workflows
   */
  async loadAllWorkflows() {
    const activeWorkflows = this.index.workflows.filter(w => w.status === 'active')
    const workflows = []

    for (const workflowInfo of activeWorkflows) {
      try {
        const workflow = await this.loadWorkflow(workflowInfo.id)
        workflows.push(workflow)
      } catch (error) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, error)
      }
    }

    return workflows
  }

  /**
   * Get workflows by category
   */
  async loadWorkflowsByCategory(category) {
    const categoryWorkflows = this.index.workflows.filter(w => w.category === category)
    const workflows = []

    for (const workflowInfo of categoryWorkflows) {
      try {
        const workflow = await this.loadWorkflow(workflowInfo.id)
        workflows.push(workflow)
      } catch (error) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, error)
      }
    }

    return workflows
  }

  /**
   * Get workflows by complexity
   */
  async loadWorkflowsByComplexity(complexity) {
    const complexityWorkflows = this.index.workflows.filter(w => w.complexity === complexity)
    const workflows = []

    for (const workflowInfo of complexityWorkflows) {
      try {
        const workflow = await this.loadWorkflow(workflowInfo.id)
        workflows.push(workflow)
      } catch (error) {
        console.error(`Failed to load workflow ${workflowInfo.id}:`, error)
      }
    }

    return workflows
  }

  /**
   * Create a new workflow file
   */
  async createWorkflow(workflowData, moduleId) {
    const workflowId = workflowData.workflow.id
    const filename = `${workflowId}.workflow.json`
    const filepath = `${moduleId}/${filename}`

    // Validate workflow data
    this.validateWorkflowData(workflowData)

    // In a real implementation, you'd save to filesystem
    console.log(`Creating workflow file: workflows/${filepath}`)

    // Update index
    this.index.workflows.push({
      id: workflowId,
      file: filepath,
      module: moduleId,
      category: workflowData.workflow.category,
      complexity: workflowData.workflow.complexity,
      status: 'active'
    })

    // Update module count
    if (this.index.modules[moduleId]) {
      this.index.modules[moduleId].workflowCount++
      this.index.modules[moduleId].lastUpdated = new Date().toISOString().split('T')[0]
    }

    return { success: true, filepath }
  }

  /**
   * Update an existing workflow
   */
  async updateWorkflow(workflowId, workflowData) {
    const workflowInfo = this.index.workflows.find(w => w.id === workflowId)
    if (!workflowInfo) {
      throw new Error(`Workflow ${workflowId} not found`)
    }

    // Validate workflow data
    this.validateWorkflowData(workflowData)

    // Update metadata
    workflowData.metadata.lastUpdated = new Date().toISOString().split('T')[0]

    // In a real implementation, you'd save to filesystem
    console.log(`Updating workflow file: workflows/${workflowInfo.file}`)

    // Clear cache
    this.loadedWorkflows.delete(workflowId)

    return { success: true, filepath: workflowInfo.file }
  }

  /**
   * Delete a workflow
   */
  async deleteWorkflow(workflowId) {
    const workflowIndex = this.index.workflows.findIndex(w => w.id === workflowId)
    if (workflowIndex === -1) {
      throw new Error(`Workflow ${workflowId} not found`)
    }

    const workflowInfo = this.index.workflows[workflowIndex]

    // In a real implementation, you'd delete from filesystem
    console.log(`Deleting workflow file: workflows/${workflowInfo.file}`)

    // Remove from index
    this.index.workflows.splice(workflowIndex, 1)

    // Update module count
    if (this.index.modules[workflowInfo.module]) {
      this.index.modules[workflowInfo.module].workflowCount--
      this.index.modules[workflowInfo.module].lastUpdated = new Date().toISOString().split('T')[0]
    }

    // Clear cache
    this.loadedWorkflows.delete(workflowId)

    return { success: true }
  }

  /**
   * Validate workflow data structure
   */
  validateWorkflowData(workflowData) {
    if (!workflowData.metadata || !workflowData.workflow) {
      throw new Error('Invalid workflow structure: missing metadata or workflow')
    }

    if (!workflowData.workflow.id || !workflowData.workflow.name) {
      throw new Error('Invalid workflow: missing id or name')
    }

    if (!workflowData.workflow.steps || !Array.isArray(workflowData.workflow.steps)) {
      throw new Error('Invalid workflow: missing or invalid steps')
    }

    return true
  }

  /**
   * Get available modules
   */
  getModules() {
    return this.config.modules
  }

  /**
   * Get workflow categories
   */
  getCategories() {
    return this.config.constants.workflowCategories
  }

  /**
   * Get complexity levels
   */
  getComplexityLevels() {
    return this.config.constants.complexityLevels
  }

  /**
   * Get field types
   */
  getFieldTypes() {
    return this.config.fieldTypes
  }

  /**
   * Get validation rules
   */
  getValidationRules() {
    return this.config.validationRules
  }

  /**
   * Get role definitions
   */
  getRoleDefinitions() {
    return this.config.roleDefinitions
  }
}

// Export singleton instance
export default new WorkflowLoader()
