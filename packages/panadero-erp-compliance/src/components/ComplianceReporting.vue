<!--
  Compliance Reporting Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Compliance reporting and analytics
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
const reports = ref([])

const reportTypes = [
  { value: '', label: 'All Types' },
  { value: 'regulatory', label: 'Regulatory' },
  { value: 'internal', label: 'Internal' },
  { value: 'audit', label: 'Audit' },
  { value: 'risk', label: 'Risk' },
  { value: 'policy', label: 'Policy' }
]

const categories = [
  { value: '', label: 'All Categories' },
  { value: 'monthly', label: 'Monthly' },
  { value: 'quarterly', label: 'Quarterly' },
  { value: 'annual', label: 'Annual' },
  { value: 'ad_hoc', label: 'Ad Hoc' }
]

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'draft', label: 'Draft' },
  { value: 'generated', label: 'Generated' },
  { value: 'approved', label: 'Approved' },
  { value: 'published', label: 'Published' }
]

const filters = ref({
  type: '',
  category: '',
  status: ''
})

const mockReports = ref([
  {
    id: 'RPT-2024-001',
    title: 'Q1 2024 Compliance Dashboard',
    type: 'internal',
    category: 'quarterly',
    description: 'Quarterly compliance status report covering all regulatory requirements',
    status: 'published',
    report_date: '2024-04-01',
    period_start: '2024-01-01',
    period_end: '2024-03-31',
    created_by: 'John Smith',
    approved_by: 'Jane Doe',
    approved_at: '2024-04-01T10:00:00Z',
    file_path: '/reports/compliance/q1-2024-dashboard.pdf',
    metrics: {
      policy_compliance_rate: 92.3,
      audit_completion_rate: 85.7,
      risk_mitigation_rate: 62.5,
      training_completion_rate: 94.2
    }
  },
  {
    id: 'RPT-2024-002',
    title: 'GDPR Compliance Report',
    type: 'regulatory',
    category: 'annual',
    description: 'Annual GDPR compliance assessment and data protection impact report',
    status: 'generated',
    report_date: '2024-05-15',
    period_start: '2023-05-01',
    period_end: '2024-04-30',
    created_by: 'Mike Johnson',
    approved_by: null,
    approved_at: null,
    file_path: '/reports/gdpr/annual-2024.pdf',
    metrics: {
      breach_notification_time: 0,
      consent_withdrawal_rate: 2.1,
      data_accuracy_rate: 99.2,
      privacy_policy_acceptance: 95.8
    }
  },
  {
    id: 'RPT-2024-003',
    title: 'SOX Internal Controls Report',
    type: 'regulatory',
    category: 'annual',
    description: 'Management assessment of internal controls over financial reporting',
    status: 'draft',
    report_date: null,
    period_start: '2024-01-01',
    period_end: '2024-12-31',
    created_by: 'Sarah Wilson',
    approved_by: null,
    approved_at: null,
    file_path: null,
    metrics: {
      control_effectiveness: 94.2,
      testing_completion_rate: 84.9,
      remediation_rate: 78.6,
      management_override_incidents: 0
    }
  },
  {
    id: 'RPT-2024-004',
    title: 'Risk Management Dashboard',
    type: 'internal',
    category: 'monthly',
    description: 'Monthly risk assessment and mitigation status report',
    status: 'published',
    report_date: '2024-04-30',
    period_start: '2024-04-01',
    period_end: '2024-04-30',
    created_by: 'David Brown',
    approved_by: 'Lisa Davis',
    approved_at: '2024-04-30T15:30:00Z',
    file_path: '/reports/risk/monthly-2024-04.pdf',
    metrics: {
      risk_mitigation_rate: 25.0,
      average_mitigation_time: 45,
      risk_escalation_rate: 8.3,
      risk_acceptance_rate: 16.7
    }
  }
])

const filteredReports = computed(() => {
  let filtered = mockReports.value

  if (filters.value.type) {
    filtered = filtered.filter(report => report.type === filters.value.type)
  }

  if (filters.value.category) {
    filtered = filtered.filter(report => report.category === filters.value.category)
  }

  if (filters.value.status) {
    filtered = filtered.filter(report => report.status === filters.value.status)
  }

  return filtered
})

onMounted(() => {
  loadReportData()
})

const loadReportData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    reports.value = mockReports.value
  } catch (error) {
    console.error('Error loading report data:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const colorMap = {
    published: 'green',
    approved: 'blue',
    generated: 'yellow',
    draft: 'gray'
  }
  return colorMap[status] || 'gray'
}

const getTypeColor = (type) => {
  const colorMap = {
    regulatory: 'red',
    internal: 'blue',
    audit: 'orange',
    risk: 'purple',
    policy: 'green'
  }
  return colorMap[type] || 'gray'
}

const getCategoryColor = (category) => {
  const colorMap = {
    monthly: 'blue',
    quarterly: 'green',
    annual: 'purple',
    ad_hoc: 'orange'
  }
  return colorMap[category] || 'gray'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const generateReport = async (type, category) => {
  try {
    loading.value = true
    // Simulate report generation
    await new Promise(resolve => setTimeout(resolve, 2000))
    console.log(`Generating ${type} ${category} report...`)
  } catch (error) {
    console.error('Error generating report:', error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Compliance Reports
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Generate and manage compliance reports and analytics
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton variant="primary" size="md" @click="generateReport('internal', 'monthly')">
          <i class="fas fa-plus mr-2"></i>
          Generate Report
        </ComplianceButton>
      </div>
    </div>

    <!-- Report Generation Quick Actions -->
    <div :class="[componentStyles.card, 'p-6']">
      <h2 :class="[getFontSizeClasses('xl'), 'font-semibold text-gray-900 dark:text-white mb-4']">
        Quick Report Generation
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <ComplianceButton
          variant="outline"
          size="md"
          class="h-20 flex-col"
          @click="generateReport('regulatory', 'monthly')"
        >
          <i class="fas fa-gavel text-2xl mb-2"></i>
          <span :class="[getFontSizeClasses('sm'), 'font-medium']">Regulatory Report</span>
        </ComplianceButton>
        
        <ComplianceButton
          variant="outline"
          size="md"
          class="h-20 flex-col"
          @click="generateReport('internal', 'quarterly')"
        >
          <i class="fas fa-chart-line text-2xl mb-2"></i>
          <span :class="[getFontSizeClasses('sm'), 'font-medium']">Quarterly Dashboard</span>
        </ComplianceButton>
        
        <ComplianceButton
          variant="outline"
          size="md"
          class="h-20 flex-col"
          @click="generateReport('audit', 'annual')"
        >
          <i class="fas fa-clipboard-check text-2xl mb-2"></i>
          <span :class="[getFontSizeClasses('sm'), 'font-medium']">Audit Summary</span>
        </ComplianceButton>
        
        <ComplianceButton
          variant="outline"
          size="md"
          class="h-20 flex-col"
          @click="generateReport('risk', 'monthly')"
        >
          <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
          <span :class="[getFontSizeClasses('sm'), 'font-medium']">Risk Assessment</span>
        </ComplianceButton>
      </div>
    </div>

    <!-- Filters -->
    <div :class="[componentStyles.card, 'p-4']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Report Type
          </label>
          <select
            v-model="filters.type"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="type in reportTypes" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </select>
        </div>
        
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
            <option v-for="category in categories" :key="category.value" :value="category.value">
              {{ category.label }}
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
            @click="filters = { type: '', category: '', status: '' }"
          >
            Clear Filters
          </ComplianceButton>
        </div>
      </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="report in filteredReports"
        :key="report.id"
        :class="[componentStyles.card, 'p-6 hover:shadow-md transition-shadow duration-200']"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-1']">
              {{ report.title }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400 mb-2']">
              {{ report.description }}
            </p>
            <div class="flex items-center space-x-2">
              <StatusBadge 
                :status="report.type" 
                :color="getTypeColor(report.type)"
                size="sm"
              />
              <StatusBadge 
                :status="report.category" 
                :color="getCategoryColor(report.category)"
                size="sm"
              />
              <StatusBadge 
                :status="report.status" 
                :color="getStatusColor(report.status)"
                size="sm"
              />
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Report Date:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(report.report_date) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Period:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(report.period_start) }} - {{ formatDate(report.period_end) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Created By:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ report.created_by }}
            </span>
          </div>

          <div v-if="report.approved_by" class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Approved By:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ report.approved_by }}
            </span>
          </div>

          <!-- Key Metrics -->
          <div v-if="report.metrics" class="pt-3 border-t border-gray-200 dark:border-gray-700">
            <h4 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white mb-2']">
              Key Metrics
            </h4>
            <div class="grid grid-cols-2 gap-2">
              <div v-for="(value, key) in report.metrics" :key="key" class="text-center">
                <div :class="[getFontSizeClasses('lg'), 'font-bold text-gray-900 dark:text-white']">
                  {{ typeof value === 'number' ? value.toFixed(1) : value }}
                </div>
                <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400 capitalize']">
                  {{ key.replace(/_/g, ' ') }}
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
          <ComplianceButton 
            v-if="report.file_path" 
            variant="ghost" 
            size="sm"
          >
            <i class="fas fa-download mr-1"></i>
            Download
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
