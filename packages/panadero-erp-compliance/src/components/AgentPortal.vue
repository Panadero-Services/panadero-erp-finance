<!--
  AI Agent Portal Component
  @version 1.0.0
  @date 15-Jan-2025
  @description AI-powered compliance assistance portal
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useComplianceStore } from '../stores/complianceStore'
import { useStyling } from '../composables/useStyling'
import ComplianceButton from './ui/ComplianceButton.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useComplianceStore()
const { getFontSizeClasses, getColorClasses, componentStyles } = useStyling()

const loading = ref(false)
const chatMessages = ref([])
const userInput = ref('')
const selectedPrompt = ref('')

const aiPrompts = ref([
  {
    id: 'compliance-check',
    title: 'Compliance Check',
    description: 'Analyze current compliance status and identify potential issues',
    prompt: 'Please analyze our current compliance status and identify any potential issues or areas for improvement.',
    icon: 'fas fa-shield-check',
    color: 'blue'
  },
  {
    id: 'risk-assessment',
    title: 'Risk Assessment',
    description: 'Evaluate compliance risks and provide mitigation strategies',
    prompt: 'Conduct a comprehensive risk assessment of our compliance program and suggest mitigation strategies.',
    icon: 'fas fa-exclamation-triangle',
    color: 'red'
  },
  {
    id: 'policy-review',
    title: 'Policy Review',
    description: 'Review existing policies and suggest updates or improvements',
    prompt: 'Review our current compliance policies and suggest updates or improvements based on latest regulations.',
    icon: 'fas fa-file-contract',
    color: 'green'
  },
  {
    id: 'audit-preparation',
    title: 'Audit Preparation',
    description: 'Help prepare for upcoming compliance audits',
    prompt: 'Help me prepare for our upcoming compliance audit by identifying key areas to focus on and documentation needed.',
    icon: 'fas fa-clipboard-check',
    color: 'orange'
  },
  {
    id: 'incident-analysis',
    title: 'Incident Analysis',
    description: 'Analyze compliance incidents and suggest preventive measures',
    prompt: 'Analyze recent compliance incidents and suggest preventive measures to avoid similar issues in the future.',
    icon: 'fas fa-search',
    color: 'purple'
  },
  {
    id: 'training-recommendations',
    title: 'Training Recommendations',
    description: 'Suggest compliance training programs and materials',
    prompt: 'Recommend compliance training programs and materials for our team based on current regulations and best practices.',
    icon: 'fas fa-graduation-cap',
    color: 'indigo'
  }
])

const quickActions = ref([
  {
    id: 'generate-report',
    title: 'Generate Compliance Report',
    description: 'Create a comprehensive compliance report',
    action: 'generateReport',
    icon: 'fas fa-file-alt',
    color: 'blue'
  },
  {
    id: 'schedule-audit',
    title: 'Schedule Audit',
    description: 'Schedule a new compliance audit',
    action: 'scheduleAudit',
    icon: 'fas fa-calendar-plus',
    color: 'green'
  },
  {
    id: 'create-policy',
    title: 'Create Policy',
    description: 'Draft a new compliance policy',
    action: 'createPolicy',
    icon: 'fas fa-file-plus',
    color: 'purple'
  },
  {
    id: 'risk-analysis',
    title: 'Risk Analysis',
    description: 'Perform AI-powered risk analysis',
    action: 'riskAnalysis',
    icon: 'fas fa-chart-line',
    color: 'red'
  }
])

const aiCapabilities = ref([
  {
    title: 'Natural Language Processing',
    description: 'Understand and respond to complex compliance queries',
    icon: 'fas fa-language',
    status: 'active'
  },
  {
    title: 'Pattern Recognition',
    description: 'Identify compliance patterns and anomalies',
    icon: 'fas fa-search',
    status: 'active'
  },
  {
    title: 'Predictive Analytics',
    description: 'Predict potential compliance risks',
    icon: 'fas fa-crystal-ball',
    status: 'active'
  },
  {
    title: 'Automated Reporting',
    description: 'Generate compliance reports automatically',
    icon: 'fas fa-robot',
    status: 'active'
  },
  {
    title: 'Regulatory Updates',
    description: 'Monitor and alert on regulatory changes',
    icon: 'fas fa-bell',
    status: 'active'
  },
  {
    title: 'Document Analysis',
    description: 'Analyze compliance documents and contracts',
    icon: 'fas fa-file-search',
    status: 'active'
  }
])

const recentQueries = ref([
  {
    id: 1,
    query: 'What are the latest GDPR requirements?',
    response: 'The latest GDPR requirements include enhanced data subject rights, stricter consent mechanisms, and increased penalties for non-compliance...',
    timestamp: '2 hours ago',
    status: 'completed'
  },
  {
    id: 2,
    query: 'Analyze our SOX compliance status',
    response: 'Based on your current SOX compliance status, I found 3 areas that need attention: internal controls, documentation, and monitoring...',
    timestamp: '4 hours ago',
    status: 'completed'
  },
  {
    id: 3,
    query: 'Generate monthly compliance report',
    response: 'I have generated your monthly compliance report covering all regulatory requirements, audit findings, and risk assessments...',
    timestamp: '1 day ago',
    status: 'completed'
  }
])

onMounted(() => {
  loadAgentData()
})

const loadAgentData = async () => {
  try {
    loading.value = true
    // Simulate loading agent data
    await new Promise(resolve => setTimeout(resolve, 1000))
  } catch (error) {
    console.error('Error loading agent data:', error)
  } finally {
    loading.value = false
  }
}

const sendMessage = async () => {
  if (!userInput.value.trim()) return

  const userMessage = {
    id: Date.now(),
    type: 'user',
    content: userInput.value,
    timestamp: new Date().toISOString()
  }

  chatMessages.value.push(userMessage)
  
  const query = userInput.value
  userInput.value = ''

  try {
    loading.value = true
    
    // Simulate AI response
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    const aiResponse = {
      id: Date.now() + 1,
      type: 'ai',
      content: `I understand you're asking about "${query}". Based on your compliance data, here's my analysis and recommendations...`,
      timestamp: new Date().toISOString(),
      confidence: 0.87,
      sources: ['GDPR Guidelines', 'SOX Compliance Manual', 'Internal Policies']
    }

    chatMessages.value.push(aiResponse)
    
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    loading.value = false
  }
}

const usePrompt = (prompt) => {
  userInput.value = prompt.prompt
  selectedPrompt.value = prompt.id
}

const executeQuickAction = async (action) => {
  try {
    loading.value = true
    
    // Simulate action execution
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    const response = {
      id: Date.now(),
      type: 'ai',
      content: `I've successfully executed the ${action.title} action. The process is now running in the background and you'll receive a notification when complete.`,
      timestamp: new Date().toISOString(),
      action: action.action
    }

    chatMessages.value.push(response)
    
  } catch (error) {
    console.error('Error executing action:', error)
  } finally {
    loading.value = false
  }
}

const clearChat = () => {
  chatMessages.value = []
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          AI Agent Portal
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Intelligent compliance assistance powered by AI
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <StatusBadge status="online" color="green" />
        <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
          AI Agent Active
        </span>
      </div>
    </div>

    <!-- AI Capabilities -->
    <div :class="[componentStyles.card, 'p-6']">
      <h2 :class="[getFontSizeClasses('xl'), 'font-semibold text-gray-900 dark:text-white mb-4']">
        AI Capabilities
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="capability in aiCapabilities"
          :key="capability.title"
          class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          <div :class="[
            'w-10 h-10 rounded-lg flex items-center justify-center',
            getColorClasses('blue').background
          ]">
            <i :class="[capability.icon, getFontSizeClasses('lg'), getColorClasses('blue').default]"></i>
          </div>
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white']">
              {{ capability.title }}
            </h3>
            <p :class="[getFontSizeClasses('xs'), 'text-gray-600 dark:text-gray-400']">
              {{ capability.description }}
            </p>
          </div>
          <StatusBadge :status="capability.status" color="green" size="sm" />
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Chat Interface -->
      <div class="lg:col-span-2 space-y-4">
        <!-- Chat Messages -->
        <div :class="[componentStyles.card, 'p-6 h-96 overflow-y-auto']">
          <div class="space-y-4">
            <div
              v-for="message in chatMessages"
              :key="message.id"
              :class="[
                'flex',
                message.type === 'user' ? 'justify-end' : 'justify-start'
              ]"
            >
              <div :class="[
                'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                message.type === 'user'
                  ? 'bg-gray-600 text-white'
                  : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
              ]">
                <p :class="[getFontSizeClasses('sm')]">
                  {{ message.content }}
                </p>
                <p :class="[getFontSizeClasses('xs'), 'mt-1 opacity-70']">
                  {{ new Date(message.timestamp).toLocaleTimeString() }}
                </p>
              </div>
            </div>
            
            <div v-if="loading" class="flex justify-start">
              <div class="bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span :class="[getFontSizeClasses('sm')]">AI is thinking...</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat Input -->
        <div class="flex space-x-2">
          <input
            v-model="userInput"
            @keyup.enter="sendMessage"
            :class="[
              'flex-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
            placeholder="Ask me anything about compliance..."
            :disabled="loading"
          />
          <ComplianceButton
            variant="primary"
            size="md"
            :loading="loading"
            @click="sendMessage"
          >
            <i class="fas fa-paper-plane"></i>
          </ComplianceButton>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Quick Actions -->
        <div :class="[componentStyles.card, 'p-4']">
          <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-4']">
            Quick Actions
          </h3>
          <div class="space-y-2">
            <ComplianceButton
              v-for="action in quickActions"
              :key="action.id"
              variant="outline"
              size="sm"
              class="w-full justify-start"
              @click="executeQuickAction(action)"
            >
              <i :class="[action.icon, 'mr-2']"></i>
              {{ action.title }}
            </ComplianceButton>
          </div>
        </div>

        <!-- AI Prompts -->
        <div :class="[componentStyles.card, 'p-4']">
          <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-4']">
            AI Prompts
          </h3>
          <div class="space-y-2">
            <button
              v-for="prompt in aiPrompts"
              :key="prompt.id"
              :class="[
                'w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors',
                selectedPrompt === prompt.id ? 'bg-gray-100 dark:bg-gray-700' : ''
              ]"
              @click="usePrompt(prompt)"
            >
              <div class="flex items-center space-x-2 mb-1">
                <i :class="[prompt.icon, getFontSizeClasses('sm'), getColorClasses(prompt.color).default]"></i>
                <span :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white']">
                  {{ prompt.title }}
                </span>
              </div>
              <p :class="[getFontSizeClasses('xs'), 'text-gray-600 dark:text-gray-400']">
                {{ prompt.description }}
              </p>
            </button>
          </div>
        </div>

        <!-- Recent Queries -->
        <div :class="[componentStyles.card, 'p-4']">
          <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-4']">
            Recent Queries
          </h3>
          <div class="space-y-3">
            <div
              v-for="query in recentQueries"
              :key="query.id"
              class="p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
            >
              <p :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white mb-1']">
                {{ query.query }}
              </p>
              <p :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                {{ query.timestamp }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
