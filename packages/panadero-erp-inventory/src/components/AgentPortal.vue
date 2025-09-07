<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from '../../../shared/composables/useScaling.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const selectedPrompt = ref(null)
const customPrompt = ref('')
const aiResponse = ref('')
const showPromptModal = ref(false)
const showConfigModal = ref(false)
const promptHistory = ref([])

// AI Service Configuration
const aiServiceConfig = ref({
  provider: 'openai', // openai, claude, ollama, custom
  apiKey: '',
  baseUrl: '',
  model: '',
  temperature: 0.7,
  maxTokens: 500,
  customHeaders: {}
})

// AI Service Options
const aiProviders = [
  { value: 'openai', label: 'OpenAI (GPT-4, GPT-3.5)' },
  { value: 'claude', label: 'Anthropic Claude' },
  { value: 'ollama', label: 'Ollama (Local)' },
  { value: 'custom', label: 'Custom API' }
]

const openaiModels = [
  { value: 'gpt-4', label: 'GPT-4' },
  { value: 'gpt-4-turbo', label: 'GPT-4 Turbo' },
  { value: 'gpt-3.5-turbo', label: 'GPT-3.5 Turbo' }
]

const claudeModels = [
  { value: 'claude-3-5-sonnet-20241022', label: 'Claude 3.5 Sonnet' },
  { value: 'claude-3-5-haiku-20241022', label: 'Claude 3.5 Haiku' },
  { value: 'claude-3-opus-20240229', label: 'Claude 3 Opus' },
  { value: 'claude-3-sonnet-20240229', label: 'Claude 3 Sonnet' },
  { value: 'claude-3-haiku-20240307', label: 'Claude 3 Haiku' },
  { value: 'claude-sonnet-4-20250514', label: 'Claude Sonnet 4' }
]

const ollamaModels = [
  { value: 'llama2', label: 'Llama 2' },
  { value: 'codellama', label: 'Code Llama' },
  { value: 'mistral', label: 'Mistral' },
  { value: 'neural-chat', label: 'Neural Chat' }
]

// Computed
const availablePrompts = computed(() => store.agentPrompts.filter(p => p.active))
const availableModels = computed(() => {
  switch (aiServiceConfig.value.provider) {
    case 'openai': return openaiModels
    case 'claude': return claudeModels
    case 'ollama': return ollamaModels
    default: return []
  }
})

const isConfigured = computed(() => {
  if (aiServiceConfig.value.provider === 'ollama') {
    return aiServiceConfig.value.baseUrl.trim() !== ''
  }
  return aiServiceConfig.value.apiKey.trim() !== '' && aiServiceConfig.value.model !== ''
})

const promptCategories = computed(() => {
  const cats = new Set(store.agentPrompts.map(p => p.category))
  return Array.from(cats).map(cat => ({ value: cat, label: cat }))
})

// Actions
const handleExecutePrompt = async (promptId) => {
  if (!isConfigured.value) {
    aiResponse.value = 'Please configure your AI service first in the settings.'
    return
  }

  isLoading.value = true
  try {
    const result = await store.executeAgentPrompt(promptId, {
      inventoryData: {
        totalItems: store.stockItems.length,
        lowStockItems: store.getLowStockItems.length,
        totalValue: store.getTotalInventoryValue
      },
      aiConfig: aiServiceConfig.value
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
    aiResponse.value = 'Error executing prompt. Please check your AI service configuration.'
  } finally {
    isLoading.value = false
  }
}

const handleCustomPrompt = async () => {
  if (!customPrompt.value.trim()) return
  if (!isConfigured.value) {
    aiResponse.value = 'Please configure your AI service first in the settings.'
    return
  }
  
  isLoading.value = true
  try {
    const result = await callAIService(customPrompt.value, {
      inventoryData: {
        totalItems: store.stockItems.length,
        lowStockItems: store.getLowStockItems.length,
        totalValue: store.getTotalInventoryValue
      }
    })
    
    aiResponse.value = result
    promptHistory.value.unshift({
      id: Date.now(),
      prompt: customPrompt.value,
      response: result,
      timestamp: new Date().toISOString()
    })
    
    customPrompt.value = ''
  } catch (error) {
    console.error('Error processing custom prompt:', error)
    aiResponse.value = `Error: ${error.message}. Check console for details.`
  } finally {
    isLoading.value = false
  }
}


const callAIService = async (prompt, context = {}) => {
  const config = aiServiceConfig.value
  
  const response = await fetch('/api/ai/call', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      provider: config.provider,
      prompt: prompt,
      context: context,
      config: config
    })
  })

  if (!response.ok) {
    throw new Error(`AI Response Error: ${response.status} ${response.statusText}`)
  }

  const data = await response.json()
  return data.response
}

const callOpenAI = async (prompt, context, config) => {
  const response = await fetch('https://api.openai.com/v1/chat/completions', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${config.apiKey}`,
      ...config.customHeaders
    },
    body: JSON.stringify({
      model: config.model,
      messages: [
        {
          role: 'system',
          content: 'You are an inventory management AI assistant. Provide helpful insights about inventory data, stock optimization, and supply chain management.'
        },
        {
          role: 'user',
          content: `${prompt}\n\nContext: ${JSON.stringify(context)}`
        }
      ],
      max_tokens: config.maxTokens,
      temperature: config.temperature
    })
  })

  if (!response.ok) {
    throw new Error(`OpenAI API error: ${response.statusText}`)
  }

  const data = await response.json()
  return data.choices[0].message.content
}

const callClaude = async (prompt, context, config) => {
  const response = await fetch('https://api.anthropic.com/v1/messages', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'x-api-key': config.apiKey,
      'anthropic-version': '2023-06-01',
      ...config.customHeaders
    },
    body: JSON.stringify({
      model: config.model,
      max_tokens: config.maxTokens,
      messages: [{
        role: 'user',
        content: `You are an inventory management AI assistant. ${prompt}\n\nContext: ${JSON.stringify(context)}`
      }]
    })
  })

  if (!response.ok) {
    throw new Error(`Claude API error: ${response.statusText}`)
  }

  const data = await response.json()
  return data.content[0].text
}

const callOllama = async (prompt, context, config) => {
  const baseUrl = config.baseUrl || 'http://localhost:11434'
  const response = await fetch(`${baseUrl}/api/generate`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      ...config.customHeaders
    },
    body: JSON.stringify({
      model: config.model,
      prompt: `You are an inventory management AI assistant. ${prompt}\n\nContext: ${JSON.stringify(context)}`,
      stream: false,
      options: {
        temperature: config.temperature
      }
    })
  })

  if (!response.ok) {
    throw new Error(`Ollama API error: ${response.statusText}`)
  }

  const data = await response.json()
  return data.response
}

const callCustomAPI = async (prompt, context, config) => {
  const response = await fetch(config.baseUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      ...config.customHeaders
    },
    body: JSON.stringify({
      prompt,
      context,
      model: config.model,
      temperature: config.temperature,
      max_tokens: config.maxTokens
    })
  })

  if (!response.ok) {
    throw new Error(`Custom API error: ${response.statusText}`)
  }

  const data = await response.json()
  return data.response || data.content || data.message
}

const handleAddPrompt = () => {
  showPromptModal.value = true
}

const handleSavePrompt = async () => {
  if (!selectedPrompt.value) return
  
  const newPrompt = {
    title: selectedPrompt.value.title,
    prompt: selectedPrompt.value.prompt,
    category: selectedPrompt.value.category || 'custom'
  }
  
  await store.addAgentPrompt(newPrompt)
  showPromptModal.value = false
  selectedPrompt.value = null
}

const clearResponse = () => {
  aiResponse.value = ''
}

const saveAIConfig = () => {
  localStorage.setItem('aiServiceConfig', JSON.stringify(aiServiceConfig.value))
  showConfigModal.value = false
}

const loadAIConfig = () => {
  const saved = localStorage.getItem('aiServiceConfig')
  if (saved) {
    aiServiceConfig.value = { ...aiServiceConfig.value, ...JSON.parse(saved) }
  }
}

const testConnection = async () => {
  if (!isConfigured.value) return
  
  isLoading.value = true
  try {
    const result = await callAIService('Hello, this is a test message. Please respond with "Connection successful!"', {})
    aiResponse.value = `Connection Test Result: ${result}`
  } catch (error) {
    aiResponse.value = `Connection Test Failed: ${error.message}`
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadAIConfig()
})

//{ "base": 16, "xxxs": "13px", "xxs": "14px", "xs": "15px", "body": "16px", "subtitle": "17px", "caption": "18px", "title": "20px", "heading": "22px", "h1": "21px", "h2": "19px", "h3": "18px", "h4": "17px", "label": "15px", "amount": "17px", "table": "14px" }
</script>

<template>
  <div class="agent-portal">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            AI Agent Portal
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Leverage AI to analyze and optimize your inventory management
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <StatusBadge
            :status="isConfigured ? 'success' : 'warning'"
            :text="isConfigured ? 'AI Configured' : 'AI Not Configured'"
          />
          <InventoryButton
            @click="showConfigModal = true"
            variant="secondary"
            size="md"
          >
            <i class="fas fa-cog mr-2"></i>
            Configure AI
          </InventoryButton>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <div class="flex flex-wrap gap-3">
        <InventoryButton
          @click="handleAddPrompt"
          variant="primary"
          size="md"
        >
          <i class="fas fa-plus mr-2"></i>
          Add Custom Prompt
        </InventoryButton>
        <InventoryButton
          @click="testConnection"
          variant="secondary"
          size="md"
          :loading="isLoading"
          :disabled="!isConfigured"
        >
          <i class="fas fa-wifi mr-2"></i>
          Test Connection
        </InventoryButton>
        <InventoryButton
          @click="clearResponse"
          variant="ghost"
          size="md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear Response
        </InventoryButton>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- AI Prompts -->
      <div>
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Available AI Prompts
        </h2>
        
        <div class="space-y-4">
          <div
            v-for="prompt in availablePrompts"
            :key="prompt.id"
            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
          >
            <div class="flex items-start justify-between mb-2">
              <h3 :style="{ fontSize: `${fontSizes.subtitle}` }"  class="text-gray-900 dark:text-white">{{ prompt.title }}</h3>
              <StatusBadge
                :status="prompt.active ? 'success' : 'inactive'"
                :text="prompt.active ? 'Active' : 'Inactive'"
              />
            </div>
            <p :style="{ fontSize: `${fontSizes.xs}` }" class=" text-gray-600 dark:text-gray-400 mb-3">{{ prompt.prompt }}</p>
            <div class="flex items-center justify-between">
              <span :style="{ fontSize: `${fontSizes.xxs}` }" class="text-gray-500 dark:text-gray-400 capitalize">{{ prompt.category }}</span>
              <InventoryButton
                @click="handleExecutePrompt(prompt.id)"
                variant="primary"
                size="sm"
                :loading="isLoading"
              >
                <i class="fas fa-play mr-1"></i>
                Execute
              </InventoryButton>
            </div>
          </div>
        </div>

        <!-- Custom Prompt -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
          <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-medium text-gray-900 dark:text-white mb-3">Custom AI Prompt</h3>
          <div class="space-y-3">
            <InventoryInput
              v-model="customPrompt"
              placeholder="Enter your custom prompt for AI analysis..."
              type="textarea"
              rows="3"
            />
            <InventoryButton
              @click="handleCustomPrompt"
              variant="primary"
              size="md"
              :loading="isLoading"
              :disabled="!customPrompt.trim()"
            >
              <i class="fas fa-robot mr-2"></i>
              Execute Custom Prompt
            </InventoryButton>
          </div>
        </div>
      </div>

      <!-- AI Response -->
      <div>
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          AI Response
        </h2>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 min-h-[400px]">
          <div v-if="aiResponse" class="whitespace-pre-wrap text-gray-900 dark:text-white">
            {{ aiResponse }}
          </div>
          <div v-else class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
            <div class="text-center">
              <i class="fas fa-robot mb-2" :style="{ fontSize: `${fontSizes.title}` }"></i>
              <p>Execute a prompt to see AI responses here</p>
            </div>
          </div>
        </div>

        <!-- Prompt History -->
        <div v-if="promptHistory.length > 0" class="mt-6">
          <h3 :style="{ fontSize: `${fontSizes.small}px` }" class="font-medium text-gray-900 dark:text-white mb-3">
            Recent Prompts
          </h3>
          <div class="space-y-2 max-h-48 overflow-y-auto">
            <div
              v-for="history in promptHistory.slice(0, 5)"
              :key="history.id"
              class="bg-gray-50 dark:bg-gray-700 rounded p-3"
              :style="{ fontSize: `${fontSizes.table}` }"
            >
              <div class="font-medium text-gray-900 dark:text-white mb-1">
                {{ history.prompt }}
              </div>
              <div :style="{ fontSize: `${fontSizes.xxxs}` }" class="text-gray-600 dark:text-gray-400">
                {{ new Date(history.timestamp).toLocaleString() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Prompt Modal -->
    <div v-if="showPromptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Add Custom Prompt
        </h2>
        
        <form @submit.prevent="handleSavePrompt" class="space-y-4">
          <InventoryInput
            v-model="selectedPrompt.title"
            label="Prompt Title"
            placeholder="Enter prompt title"
            required
          />
          
          <InventoryInput
            v-model="selectedPrompt.prompt"
            label="Prompt Text"
            placeholder="Enter the AI prompt"
            type="textarea"
            rows="3"
            required
          />
          
          <InventoryDropdown
            v-model="selectedPrompt.category"
            :options="promptCategories"
            label="Category"
            placeholder="Select category"
          />
          
          <div class="flex justify-end space-x-3 pt-4">
            <InventoryButton
              @click="showPromptModal = false"
              variant="ghost"
              type="button"
            >
              Cancel
            </InventoryButton>
            <InventoryButton
              type="submit"
              variant="primary"
            >
              Add Prompt
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>

    <!-- AI Configuration Modal -->
    <div v-if="showConfigModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          AI Service Configuration
        </h2>
        
        <form @submit.prevent="saveAIConfig" class="space-y-6">
          <!-- Provider Selection -->
          <div>
            <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              AI Provider
            </label>
            <InventoryDropdown
              v-model="aiServiceConfig.provider"
              :options="aiProviders"
              placeholder="Select AI provider"
            />
          </div>

          <!-- Model Selection -->
          <div v-if="availableModels.length > 0">
            <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Model
            </label>
            <InventoryDropdown
              v-model="aiServiceConfig.model"
              :options="availableModels"
              placeholder="Select model"
            />
          </div>

          <!-- API Key (for OpenAI, Claude) -->
          <div v-if="['openai', 'claude'].includes(aiServiceConfig.provider)">
            <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              API Key
            </label>
            <InventoryInput
              v-model="aiServiceConfig.apiKey"
              type="password"
              placeholder="Enter your API key"
            />
          </div>

          <!-- Base URL (for Ollama, Custom) -->
          <div v-if="['ollama', 'custom'].includes(aiServiceConfig.provider)">
            <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Base URL
            </label>
            <InventoryInput
              v-model="aiServiceConfig.baseUrl"
              placeholder="http://localhost:11434 (for Ollama) or your custom API URL"
            />
          </div>

          <!-- Advanced Settings -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
                Temperature (0.0 - 1.0)
              </label>
              <InventoryInput
                v-model="aiServiceConfig.temperature"
                type="number"
                min="0"
                max="1"
                step="0.1"
              />
            </div>
            <div>
              <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
                Max Tokens
              </label>
              <InventoryInput
                v-model="aiServiceConfig.maxTokens"
                type="number"
                min="100"
                max="4000"
              />
            </div>
          </div>

          <!-- Custom Headers (for advanced users) -->
          <div>
            <label :style="{ fontSize: `${fontSizes.label}` }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Custom Headers (JSON format)
            </label>
            <InventoryInput
              v-model="aiServiceConfig.customHeaders"
              type="textarea"
              rows="3"
              placeholder='{"Authorization": "Bearer token", "X-Custom-Header": "value"}'
            />
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <InventoryButton
              @click="showConfigModal = false"
              variant="ghost"
              type="button"
            >
              Cancel
            </InventoryButton>
            <InventoryButton
              type="submit"
              variant="primary"
            >
              Save Configuration
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.agent-portal {
  padding: 0;
}
</style>
