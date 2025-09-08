<!--
  Root Cause Analysis Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Root Cause Analysis management with AI-powered features
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useComplianceStore } from '../stores/complianceStore'
import { useStyling } from '../composables/useStyling'
import StatusBadge from './ui/StatusBadge.vue'
import ComplianceButton from './ui/ComplianceButton.vue'

const store = useComplianceStore()
const { getFontSizeClasses, getColorClasses, componentStyles } = useStyling()

const loading = ref(false)
const selectedRCA = ref(null)
const showAIAnalysis = ref(false)

const rcaCases = ref([
  {
    id: 'RCA-2024-001',
    title: 'Data Processing Consent Violation',
    description: 'Investigation into unauthorized data processing without proper consent',
    incident_type: 'compliance_violation',
    severity: 'high',
    status: 'completed',
    problem_statement: 'Customer data was processed for marketing purposes without explicit consent, violating GDPR Article 6 requirements.',
    immediate_actions: 'Immediately halt all unauthorized data processing, notify data subjects, and implement consent verification system.',
    root_causes: [
      'Insufficient consent management system',
      'Lack of data processing documentation',
      'Inadequate staff training on GDPR requirements',
      'Missing automated consent verification'
    ],
    contributing_factors: [
      'Legacy system integration issues',
      'Rapid business growth outpacing compliance processes',
      'Insufficient oversight of third-party data processors',
      'Lack of regular compliance audits'
    ],
    corrective_actions: [
      'Implement automated consent management system',
      'Update data processing documentation',
      'Conduct comprehensive GDPR training',
      'Establish regular compliance monitoring'
    ],
    preventive_measures: [
      'Regular compliance training programs',
      'Automated consent verification workflows',
      'Quarterly compliance assessments',
      'Enhanced data processing oversight'
    ],
    incident_date: '2024-02-15',
    discovery_date: '2024-02-20',
    target_resolution_date: '2024-04-15',
    actual_resolution_date: '2024-04-10',
    ai_analysis: {
      risk_score: 8.5,
      similar_incidents: 3,
      recommended_actions: [
        'Implement AI-powered consent monitoring',
        'Deploy automated compliance checking',
        'Enhance data lineage tracking'
      ],
      predicted_likelihood: 'low',
      confidence_score: 0.87
    },
    created_at: '2024-02-15T10:00:00Z'
  },
  {
    id: 'RCA-2024-002',
    title: 'Financial Control Weakness',
    description: 'Root cause analysis of identified control deficiency in accounts payable process',
    incident_type: 'audit_finding',
    severity: 'medium',
    status: 'in_progress',
    problem_statement: 'Lack of segregation of duties in the accounts payable approval process, creating risk of unauthorized payments.',
    immediate_actions: 'Implement temporary dual approval for all payments over $10,000, conduct immediate review of recent transactions.',
    root_causes: [
      'Insufficient segregation of duties',
      'Lack of automated approval workflows',
      'Inadequate monitoring of payment approvals',
      'Missing exception reporting'
    ],
    contributing_factors: [
      'Staff shortage in accounting department',
      'Legacy system limitations',
      'Insufficient training on control requirements',
      'Lack of regular control testing'
    ],
    corrective_actions: [
      'Implement automated approval workflows',
      'Establish segregation of duties matrix',
      'Deploy exception reporting system',
      'Conduct control effectiveness testing'
    ],
    preventive_measures: [
      'Regular control testing and monitoring',
      'Automated workflow controls',
      'Enhanced staff training programs',
      'Quarterly control assessments'
    ],
    incident_date: '2024-03-01',
    discovery_date: '2024-03-05',
    target_resolution_date: '2024-06-30',
    actual_resolution_date: null,
    ai_analysis: {
      risk_score: 6.2,
      similar_incidents: 1,
      recommended_actions: [
        'Deploy AI-powered anomaly detection',
        'Implement predictive control monitoring',
        'Enhance automated workflow controls'
      ],
      predicted_likelihood: 'medium',
      confidence_score: 0.73
    },
    created_at: '2024-03-01T14:30:00Z'
  }
])

const filters = ref({
  incident_type: '',
  severity: '',
  status: ''
})

const filteredRCAs = computed(() => {
  let filtered = rcaCases.value

  if (filters.value.incident_type) {
    filtered = filtered.filter(rca => rca.incident_type === filters.value.incident_type)
  }

  if (filters.value.severity) {
    filtered = filtered.filter(rca => rca.severity === filters.value.severity)
  }

  if (filters.value.status) {
    filtered = filtered.filter(rca => rca.status === filters.value.status)
  }

  return filtered
})

const incidentTypes = [
  { value: '', label: 'All Types' },
  { value: 'compliance_violation', label: 'Compliance Violation' },
  { value: 'audit_finding', label: 'Audit Finding' },
  { value: 'risk_event', label: 'Risk Event' },
  { value: 'policy_breach', label: 'Policy Breach' }
]

const severityLevels = [
  { value: '', label: 'All Severities' },
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'critical', label: 'Critical' }
]

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'open', label: 'Open' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
  { value: 'closed', label: 'Closed' }
]

onMounted(() => {
  loadRCAData()
})

const loadRCAData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
  } catch (error) {
    console.error('Error loading RCA data:', error)
  } finally {
    loading.value = false
  }
}

const selectRCA = (rca) => {
  selectedRCA.value = rca
}

const runAIAnalysis = async (rcaId) => {
  try {
    showAIAnalysis.value = true
    // Simulate AI analysis
    await new Promise(resolve => setTimeout(resolve, 2000))
  } catch (error) {
    console.error('Error running AI analysis:', error)
  }
}

const getSeverityColor = (severity) => {
  const colorMap = {
    low: 'green',
    medium: 'yellow',
    high: 'orange',
    critical: 'red'
  }
  return colorMap[severity] || 'gray'
}

const getIncidentTypeColor = (type) => {
  const colorMap = {
    compliance_violation: 'red',
    audit_finding: 'orange',
    risk_event: 'purple',
    policy_breach: 'yellow'
  }
  return colorMap[type] || 'gray'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Root Cause Analysis
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          AI-powered incident investigation and analysis
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton
          variant="primary"
          size="md"
          @click="runAIAnalysis('new')"
        >
          <i class="fas fa-robot mr-2"></i>
          Run AI Analysis
        </ComplianceButton>
      </div>
    </div>

    <!-- Filters -->
    <div :class="[componentStyles.card, 'p-4']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Incident Type
          </label>
          <select
            v-model="filters.incident_type"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="type in incidentTypes" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </select>
        </div>
        
        <div>
          <label :class="[getFontSizeClasses('sm'), 'block font-medium text-gray-700 dark:text-gray-300 mb-1']">
            Severity
          </label>
          <select
            v-model="filters.severity"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="severity in severityLevels" :key="severity.value" :value="severity.value">
              {{ severity.label }}
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
            @click="filters = { incident_type: '', severity: '', status: '' }"
          >
            Clear Filters
          </ComplianceButton>
        </div>
      </div>
    </div>

    <!-- RCA Cases List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div
        v-for="rca in filteredRCAs"
        :key="rca.id"
        :class="[
          componentStyles.card,
          'p-6 cursor-pointer hover:shadow-md transition-shadow duration-200',
          selectedRCA?.id === rca.id ? 'ring-2 ring-indigo-500' : ''
        ]"
        @click="selectRCA(rca)"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-1']">
              {{ rca.title }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400 mb-2']">
              {{ rca.description }}
            </p>
            <div class="flex items-center space-x-2">
              <StatusBadge 
                :status="rca.incident_type.replace('_', ' ')" 
                :color="getIncidentTypeColor(rca.incident_type)"
                size="sm"
              />
              <StatusBadge 
                :status="rca.severity" 
                :color="getSeverityColor(rca.severity)"
                size="sm"
              />
              <StatusBadge 
                :status="rca.status" 
                :color="store.getStatusColor(rca.status)"
                size="sm"
              />
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
              Incident Date:
            </span>
            <span :class="[getFontSizeClasses('xs'), 'text-gray-900 dark:text-white']">
              {{ formatDate(rca.incident_date) }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
              Target Resolution:
            </span>
            <span :class="[getFontSizeClasses('xs'), 'text-gray-900 dark:text-white']">
              {{ formatDate(rca.target_resolution_date) }}
            </span>
          </div>
          <div v-if="rca.ai_analysis" class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
              AI Risk Score:
            </span>
            <span :class="[getFontSizeClasses('xs'), 'font-medium text-gray-900 dark:text-white']">
              {{ rca.ai_analysis.risk_score }}/10
            </span>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <ComplianceButton
            variant="outline"
            size="sm"
            @click.stop="selectRCA(rca)"
          >
            View Details
          </ComplianceButton>
          <ComplianceButton
            variant="primary"
            size="sm"
            @click.stop="runAIAnalysis(rca.id)"
          >
            <i class="fas fa-robot mr-1"></i>
            AI Analysis
          </ComplianceButton>
        </div>
      </div>
    </div>

    <!-- RCA Details Modal -->
    <div v-if="selectedRCA" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ selectedRCA.title }}
            </h2>
            <button
              @click="selectedRCA = null"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>

          <div class="space-y-6">
            <!-- Problem Statement -->
            <div>
              <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-2']">
                Problem Statement
              </h3>
              <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
                {{ selectedRCA.problem_statement }}
              </p>
            </div>

            <!-- Immediate Actions -->
            <div>
              <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-2']">
                Immediate Actions
              </h3>
              <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
                {{ selectedRCA.immediate_actions }}
              </p>
            </div>

            <!-- Root Causes -->
            <div>
              <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-2']">
                Root Causes
              </h3>
              <ul class="list-disc list-inside space-y-1">
                <li
                  v-for="cause in selectedRCA.root_causes"
                  :key="cause"
                  :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']"
                >
                  {{ cause }}
                </li>
              </ul>
            </div>

            <!-- Corrective Actions -->
            <div>
              <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-2']">
                Corrective Actions
              </h3>
              <ul class="list-disc list-inside space-y-1">
                <li
                  v-for="action in selectedRCA.corrective_actions"
                  :key="action"
                  :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']"
                >
                  {{ action }}
                </li>
              </ul>
            </div>

            <!-- AI Analysis -->
            <div v-if="selectedRCA.ai_analysis">
              <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-2']">
                AI Analysis
              </h3>
              <div :class="[componentStyles.card, 'p-4']">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div class="text-center">
                    <div :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
                      {{ selectedRCA.ai_analysis.risk_score }}/10
                    </div>
                    <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                      Risk Score
                    </div>
                  </div>
                  <div class="text-center">
                    <div :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
                      {{ selectedRCA.ai_analysis.similar_incidents }}
                    </div>
                    <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                      Similar Cases
                    </div>
                  </div>
                  <div class="text-center">
                    <div :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
                      {{ Math.round(selectedRCA.ai_analysis.confidence_score * 100) }}%
                    </div>
                    <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                      Confidence
                    </div>
                  </div>
                  <div class="text-center">
                    <div :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
                      {{ selectedRCA.ai_analysis.predicted_likelihood }}
                    </div>
                    <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                      Likelihood
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
