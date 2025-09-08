<!--
  Audit Trails Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Audit trails management
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
const audits = ref([])

const auditTypes = [
  { value: '', label: 'All Types' },
  { value: 'internal', label: 'Internal' },
  { value: 'external', label: 'External' },
  { value: 'regulatory', label: 'Regulatory' },
  { value: 'security', label: 'Security' }
]

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'planned', label: 'Planned' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' }
]

const riskLevels = [
  { value: '', label: 'All Risk Levels' },
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'critical', label: 'Critical' }
]

const filters = ref({
  type: '',
  status: '',
  risk_level: ''
})

const mockAudits = ref([
  {
    id: 'AUD-2024-001',
    title: 'Q1 2024 GDPR Compliance Audit',
    type: 'regulatory',
    scope: 'full',
    status: 'completed',
    risk_level: 'medium',
    start_date: '2024-01-15',
    end_date: '2024-03-15',
    due_date: '2024-03-31',
    auditor: 'John Smith',
    findings: {
      critical: 0,
      high: 2,
      medium: 5,
      low: 8
    },
    total_findings: 15
  },
  {
    id: 'AUD-2024-002',
    title: 'SOX Financial Controls Review',
    type: 'regulatory',
    scope: 'full',
    status: 'in_progress',
    risk_level: 'high',
    start_date: '2024-04-01',
    end_date: null,
    due_date: '2024-06-30',
    auditor: 'Jane Doe',
    findings: {
      critical: 0,
      high: 1,
      medium: 3,
      low: 4
    },
    total_findings: 8
  },
  {
    id: 'AUD-2024-003',
    title: 'ISO 27001 Security Audit',
    type: 'external',
    scope: 'full',
    status: 'planned',
    risk_level: 'medium',
    start_date: '2024-07-01',
    end_date: null,
    due_date: '2024-09-30',
    auditor: 'Mike Johnson',
    findings: null,
    total_findings: 0
  }
])

const filteredAudits = computed(() => {
  let filtered = mockAudits.value

  if (filters.value.type) {
    filtered = filtered.filter(audit => audit.type === filters.value.type)
  }

  if (filters.value.status) {
    filtered = filtered.filter(audit => audit.status === filters.value.status)
  }

  if (filters.value.risk_level) {
    filtered = filtered.filter(audit => audit.risk_level === filters.value.risk_level)
  }

  return filtered
})

onMounted(() => {
  loadAuditData()
})

const loadAuditData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    audits.value = mockAudits.value
  } catch (error) {
    console.error('Error loading audit data:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const colorMap = {
    completed: 'green',
    in_progress: 'blue',
    planned: 'yellow',
    cancelled: 'red'
  }
  return colorMap[status] || 'gray'
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

const getTypeColor = (type) => {
  const colorMap = {
    internal: 'blue',
    external: 'green',
    regulatory: 'red',
    security: 'purple'
  }
  return colorMap[type] || 'gray'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Audit Trails
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Track and manage compliance audits
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton variant="primary" size="md">
          <i class="fas fa-plus mr-2"></i>
          Schedule Audit
        </ComplianceButton>
      </div>
    </div>

    <!-- Filters -->
    <div :class="[componentStyles.card, 'p-4']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Audit Type
          </label>
          <select
            v-model="filters.type"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="type in auditTypes" :key="type.value" :value="type.value">
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
        
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Risk Level
          </label>
          <select
            v-model="filters.risk_level"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="level in riskLevels" :key="level.value" :value="level.value">
              {{ level.label }}
            </option>
          </select>
        </div>
        
        <div class="flex items-end">
          <ComplianceButton
            variant="secondary"
            size="md"
            @click="filters = { type: '', status: '', risk_level: '' }"
          >
            Clear Filters
          </ComplianceButton>
        </div>
      </div>
    </div>

    <!-- Audit Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="audit in filteredAudits"
        :key="audit.id"
        :class="[componentStyles.card, 'p-6 hover:shadow-md transition-shadow duration-200']"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-1']">
              {{ audit.title }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400 mb-2']">
              {{ audit.id }} • {{ audit.scope }} scope
            </p>
            <div class="flex items-center space-x-2">
              <StatusBadge 
                :status="audit.type" 
                :color="getTypeColor(audit.type)"
                size="sm"
              />
              <StatusBadge 
                :status="audit.status" 
                :color="getStatusColor(audit.status)"
                size="sm"
              />
              <StatusBadge 
                :status="audit.risk_level" 
                :color="getRiskLevelColor(audit.risk_level)"
                size="sm"
              />
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Auditor:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ audit.auditor }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Start Date:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(audit.start_date) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              End Date:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(audit.end_date) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Due Date:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(audit.due_date) }}
            </span>
          </div>

          <div v-if="audit.findings" class="pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <span :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white']">
                Findings
              </span>
              <span :class="[getFontSizeClasses('sm'), 'font-bold text-gray-900 dark:text-white']">
                {{ audit.total_findings }}
              </span>
            </div>
            <div class="grid grid-cols-4 gap-2">
              <div class="text-center">
                <div :class="[getFontSizeClasses('lg'), 'font-bold text-red-600']">
                  {{ audit.findings.critical }}
                </div>
                <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                  Critical
                </div>
              </div>
              <div class="text-center">
                <div :class="[getFontSizeClasses('lg'), 'font-bold text-orange-600']">
                  {{ audit.findings.high }}
                </div>
                <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                  High
                </div>
              </div>
              <div class="text-center">
                <div :class="[getFontSizeClasses('lg'), 'font-bold text-yellow-600']">
                  {{ audit.findings.medium }}
                </div>
                <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                  Medium
                </div>
              </div>
              <div class="text-center">
                <div :class="[getFontSizeClasses('lg'), 'font-bold text-green-600']">
                  {{ audit.findings.low }}
                </div>
                <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                  Low
                </div>
              </div>
            </div>
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
