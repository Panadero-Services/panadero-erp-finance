import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'

export function useAgentPortal() {
  const store = useInventoryStore()

  // State
  const isLoading = ref(false)
  const selectedPrompt = ref(null)
  const customPrompt = ref('')
  const aiResponse = ref('')
  const promptHistory = ref([])
  const showPromptModal = ref(false)

  // Computed
  const availablePrompts = computed(() => 
    store.agentPrompts.filter(p => p.active)
  )

  const promptCategories = computed(() => {
    const cats = new Set(store.agentPrompts.map(p => p.category))
    return Array.from(cats).map(cat => ({ value: cat, label: cat }))
  })

  const recentPrompts = computed(() => 
    promptHistory.value.slice(0, 10)
  )

  const promptStats = computed(() => ({
    totalPrompts: store.agentPrompts.length,
    activePrompts: availablePrompts.value.length,
    executedToday: promptHistory.value.filter(p => 
      new Date(p.timestamp).toDateString() === new Date().toDateString()
    ).length
  }))

  // Actions
  const executePrompt = async (promptId, context = {}) => {
    isLoading.value = true
    try {
      const result = await store.executeAgentPrompt(promptId, {
        ...context,
        inventoryData: {
          totalItems: store.stockItems.length,
          lowStockItems: store.getLowStockItems.length,
          totalValue: store.getTotalInventoryValue,
          warehouses: store.warehouses.length,
          suppliers: store.suppliers.length
        }
      })
      
      if (result.success) {
        aiResponse.value = result.result
        promptHistory.value.unshift({
          id: Date.now(),
          prompt: store.agentPrompts.find(p => p.id === promptId)?.title || 'Custom Prompt',
          response: result.result,
          timestamp: new Date().toISOString()
        })
      }
    } catch (error) {
      console.error('Error executing prompt:', error)
      aiResponse.value = 'Error executing prompt. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  const executeCustomPrompt = async (prompt, context = {}) => {
    isLoading.value = true
    try {
      // Simulate AI processing for custom prompt
      await new Promise(resolve => setTimeout(resolve, 2000))
      
      const response = `AI Response to: "${prompt}"\n\nThis is a simulated AI response. In a real implementation, this would connect to an AI service like OpenAI, Claude, or a local AI model to provide intelligent insights about your inventory data.\n\nContext: ${JSON.stringify(context, null, 2)}`
      
      aiResponse.value = response
      promptHistory.value.unshift({
        id: Date.now(),
        prompt: prompt,
        response: response,
        timestamp: new Date().toISOString()
      })
    } catch (error) {
      console.error('Error processing custom prompt:', error)
      aiResponse.value = 'Error processing custom prompt. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  const addPrompt = async (promptData) => {
    isLoading.value = true
    try {
      await store.addAgentPrompt(promptData)
    } finally {
      isLoading.value = false
    }
  }

  const updatePrompt = async (promptId, updates) => {
    isLoading.value = true
    try {
      const index = store.agentPrompts.findIndex(p => p.id === promptId)
      if (index !== -1) {
        store.agentPrompts[index] = { ...store.agentPrompts[index], ...updates }
      }
    } finally {
      isLoading.value = false
    }
  }

  const deletePrompt = async (promptId) => {
    isLoading.value = true
    try {
      const index = store.agentPrompts.findIndex(p => p.id === promptId)
      if (index !== -1) {
        store.agentPrompts.splice(index, 1)
      }
    } finally {
      isLoading.value = false
    }
  }

  const clearResponse = () => {
    aiResponse.value = ''
  }

  const clearHistory = () => {
    promptHistory.value = []
  }

  const exportHistory = () => {
    const dataStr = JSON.stringify(promptHistory.value, null, 2)
    const dataBlob = new Blob([dataStr], { type: 'application/json' })
    const url = URL.createObjectURL(dataBlob)
    const link = document.createElement('a')
    link.href = url
    link.download = `ai-prompt-history-${new Date().toISOString().split('T')[0]}.json`
    link.click()
    URL.revokeObjectURL(url)
  }

  const getPromptSuggestions = (query) => {
    if (!query) return []
    
    const suggestions = [
      'Analyze stock levels and suggest reorder points',
      'Identify slow-moving inventory items',
      'Optimize warehouse layout for efficiency',
      'Analyze supplier performance metrics',
      'Predict inventory needs for next quarter',
      'Identify cost-saving opportunities',
      'Analyze seasonal inventory patterns',
      'Suggest inventory consolidation strategies'
    ]
    
    return suggestions.filter(suggestion => 
      suggestion.toLowerCase().includes(query.toLowerCase())
    )
  }

  return {
    // State
    isLoading,
    selectedPrompt,
    customPrompt,
    aiResponse,
    promptHistory,
    showPromptModal,

    // Computed
    availablePrompts,
    promptCategories,
    recentPrompts,
    promptStats,

    // Actions
    executePrompt,
    executeCustomPrompt,
    addPrompt,
    updatePrompt,
    deletePrompt,
    clearResponse,
    clearHistory,
    exportHistory,
    getPromptSuggestions
  }
}
