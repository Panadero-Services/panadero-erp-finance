<!--
  Risk Management Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Risk management for compliance
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
const risks = ref([])

const riskCategories = [
  { value: '', label: 'All Categories' },
  { value: 'regulatory', label: 'Regulatory' },
  { value: 'operational', label: 'Operational' },
  { value: 'financial', label: 'Financial' },
  { value: 'reputational', label: 'Reputational' }
]

const riskTypes = [
  { value: '', label: 'All Types' },
  { value: 'compliance', label: 'Compliance' },
  { value: 'security', label: 'Security' },
  { value: 'data', label: 'Data' },
  { value: 'process', label: 'Process' }
]

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'identified', label: 'Identified' },
  { value: 'assessed', label: 'Assessed' },
  { value: 'mitigated', label: 'Mitigated' },
  { value: 'accepted', label: 'Accepted' },
  { value: 'closed', label: 'Closed' }
]

const filters = ref({
  category: '',
  type: '',
  status: ''
})

const mockRisks = ref([
  {
    id: 'RISK-2024-001',
    title: 'Data Breach Risk',
    description: 'Risk of unauthorized access to personal data resulting in GDPR violations',
    category: 'regulatory',
    type: 'data',
    status: 'identified',
    probability: 'medium',
    impact: 'high',
    risk_level: 'high',
    mitigation_plan: 'Implement multi-factor authentication, encryption at rest and in transit, regular security training',
    controls: 'Access controls, encryption, monitoring, incident response plan',
    identified_date: '2024-01-15',
    target_resolution_date: '2024-06-30',
    actual_resolution_date: null,
    owner: 'John Smith'
  },
  {
    id: 'RISK-2024-002',
    title: 'Financial Reporting Error',
    description: 'Risk of material misstatement in financial reports due to control weaknesses',
    category: 'regulatory',
    type: 'compliance',
    status: 'assessed',
    probability: 'low',
    impact: 'high',
    risk_level: 'medium',
    mitigation_plan: 'Strengthen segregation of duties, implement automated controls, enhance review processes',
    controls: 'Segregation of duties, automated reconciliations, management review, external audit',
    identified_date: '2024-02-01',
    target_resolution_date: '2024-08-31',
    actual_resolution_date: null,
    owner: 'Jane Doe'
  },
  {
    id: 'RISK-2024-003',
    title: 'Third-Party Vendor Risk',
    description: 'Risk of compliance violations through third-party vendors and suppliers',
    category: 'operational',
    type: 'compliance',
    status: 'mitigated',
    probability: 'medium',
    impact: 'medium',
    risk_level: 'medium',
    mitigation_plan: 'Implement vendor due diligence, contract requirements, regular assessments',
    controls: 'Vendor assessments, contract clauses, monitoring, termination procedures',
    identified_date: '2024-01-01',
    target_resolution_date: '2024-03-31',
    actual_resolution_date: '2024-03-15',
    owner: 'Mike Johnson'
  }
])

const filteredRisks = computed(() => {
  let filtered = mockRisks.value

  if (filters.value.category) {
    filtered = filtered.filter(risk => risk.category === filters.value.category)
  }

  if (filters.value.type) {
    filtered = filtered.filter(risk => risk.type === filters.value.type)
  }

  if (filters.value.status) {
    filtered = filtered.filter(risk => risk.status === filters.value.status)
  }

  return filtered
})

onMounted(() => {
  loadRiskData()
})

const loadRiskData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    risks.value = mockRisks.value
  } catch (error) {
    console.error('Error loading risk data:', error)
  } finally {
    loading.value = false
  }
}

const getRiskLevelColor = (level) => {
  const colorMap = {
    low: 'green',
    medium: 'yellow',
    high: 'orange',
    critical: 'red'
  }
  return colorMap[level] || 'gray'
}

const getStatusColor = (status) => {
  const colorMap = {
    closed: 'green',
    mitigated: 'blue',
    assessed: 'yellow',
    identified: 'orange',
    accepted: 'purple'
  }
  return colorMap[status] || 'gray'
}

const getProbabilityColor = (probability) => {
  const colorMap = {
    low: 'green',
    medium: 'yellow',
    high: 'orange',
    very_high: 'red'
  }
  return colorMap[probability] || 'gray'
}

const getImpactColor = (impact) => {
  const colorMap = {
    low: 'green',
    medium: 'yellow',
    high: 'orange',
    critical: 'red'
  }
  return colorMap[impact] || 'gray'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const isOverdue = (targetDate) => {
  if (!targetDate) return false
  return new Date(targetDate) < new Date()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Risk Management
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Identify, assess, and mitigate compliance risks
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton variant="primary" size="md">
          <i class="fas fa-plus mr-2"></i>
          Add Risk
        </ComplianceButton>
      </div>
    </div>

    <!-- Risk Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              High Risks
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockRisks.filter(r => r.risk_level === 'high').length }}
            </p>
          </div>
        </div>
      </div>

      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Overdue
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockRisks.filter(r => isOverdue(r.target_resolution_date)).length }}
            </p>
          </div>
        </div>
      </div>

      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Mitigated
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockRisks.filter(r => r.status === 'mitigated').length }}
            </p>
          </div>
        </div>
      </div>

      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Total Risks
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockRisks.length }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div :class="[componentStyles.card, 'p-4']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Category
          </label>
          <select
            v-model="filters.category"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="category in riskCategories" :key="category.value" :value="category.value">
              {{ category.label }}
            </option>
          </select>
        </div>
        
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Type
          </label>
          <select
            v-model="filters.type"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="type in riskTypes" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </select>
        </div>
        
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Status
          </label>
          <select
            v-model="filters.status"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
              {{ status.label }}
            </option>
          </select>
        </div>
        
        <div class="flex items-end">
          <ComplianceButton
            variant="secondary"
            size="md"
            @click="filters = { category: '', type: '', status: '' }"
          >
            Clear Filters
          </ComplianceButton>
        </div>
      </div>
    </div>

    <!-- Risk Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="risk in filteredRisks"
        :key="risk.id"
        :class="[componentStyles.card, 'p-6 hover:shadow-md transition-shadow duration-200']"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-1']">
              {{ risk.title }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400 mb-2']">
              {{ risk.description }}
            </p>
            <div class="flex items-center space-x-2">
              <StatusBadge 
                :status="risk.category" 
                :color="getRiskLevelColor(risk.risk_level)"
                size="sm"
              />
              <StatusBadge 
                :status="risk.status" 
                :color="getStatusColor(risk.status)"
                size="sm"
              />
              <StatusBadge 
                :status="risk.risk_level" 
                :color="getRiskLevelColor(risk.risk_level)"
                size="sm"
              />
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Owner:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ risk.owner }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Probability:
            </span>
            <StatusBadge 
              :status="risk.probability" 
              :color="getProbabilityColor(risk.probability)"
              size="sm"
            />
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Impact:
            </span>
            <StatusBadge 
              :status="risk.impact" 
              :color="getImpactColor(risk.impact)"
              size="sm"
            />
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Target Resolution:
            </span>
            <span :class="[
              getFontSizeClasses('sm'),
              'text-gray-900 dark:text-white',
              isOverdue(risk.target_resolution_date) ? 'text-red-600 dark:text-red-400' : ''
            ]">
              {{ formatDate(risk.target_resolution_date) }}
            </span>
          </div>

          <div v-if="risk.actual_resolution_date" class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Resolved:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-green-600 dark:text-green-400']">
              {{ formatDate(risk.actual_resolution_date) }}
            </span>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <ComplianceButton variant="outline" size="sm">
            <i class="fas fa-eye mr-1"></i>
            View
          </ComplianceButton>
          <ComplianceButton variant="ghost" size="sm">
            <i class="fas fa-edit mr-1"></i>
            Edit
          </ComplianceButton>
        </div>
      </div>
    </div>
  </div>
</template>
