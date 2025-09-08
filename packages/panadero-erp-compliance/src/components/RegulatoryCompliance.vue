<!--
  Regulatory Compliance Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Regulatory compliance management
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
const policies = ref([])

const regulatoryFrameworks = ref([
  {
    name: 'GDPR',
    fullName: 'General Data Protection Regulation',
    status: 'compliant',
    lastAudit: '2024-01-15',
    nextAudit: '2024-07-15',
    complianceScore: 95,
    requirements: 12,
    completed: 11,
    color: 'blue'
  },
  {
    name: 'SOX',
    fullName: 'Sarbanes-Oxley Act',
    status: 'compliant',
    lastAudit: '2024-02-01',
    nextAudit: '2024-08-01',
    complianceScore: 88,
    requirements: 8,
    completed: 7,
    color: 'green'
  },
  {
    name: 'ISO 27001',
    fullName: 'Information Security Management',
    status: 'in_progress',
    lastAudit: '2024-01-20',
    nextAudit: '2024-04-20',
    complianceScore: 76,
    requirements: 15,
    completed: 11,
    color: 'orange'
  },
  {
    name: 'PCI DSS',
    fullName: 'Payment Card Industry Data Security',
    status: 'compliant',
    lastAudit: '2024-01-10',
    nextAudit: '2024-07-10',
    complianceScore: 92,
    requirements: 6,
    completed: 6,
    color: 'purple'
  },
  {
    name: 'HIPAA',
    fullName: 'Health Insurance Portability and Accountability',
    status: 'review',
    lastAudit: '2023-12-15',
    nextAudit: '2024-06-15',
    complianceScore: 84,
    requirements: 10,
    completed: 8,
    color: 'red'
  }
])

onMounted(() => {
  loadRegulatoryData()
})

const loadRegulatoryData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
  } catch (error) {
    console.error('Error loading regulatory data:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const colorMap = {
    compliant: 'green',
    in_progress: 'yellow',
    review: 'orange',
    non_compliant: 'red'
  }
  return colorMap[status] || 'gray'
}

const getComplianceColor = (score) => {
  if (score >= 90) return 'green'
  if (score >= 75) return 'yellow'
  if (score >= 60) return 'orange'
  return 'red'
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Regulatory Compliance
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Monitor and manage compliance with regulatory frameworks
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton variant="primary" size="md">
          <i class="fas fa-plus mr-2"></i>
          Add Framework
        </ComplianceButton>
      </div>
    </div>

    <!-- Compliance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="framework in regulatoryFrameworks"
        :key="framework.name"
        :class="[componentStyles.card, 'p-6']"
      >
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white']">
              {{ framework.name }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              {{ framework.fullName }}
            </p>
          </div>
          <StatusBadge 
            :status="framework.status" 
            :color="getStatusColor(framework.status)"
          />
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Compliance Score
            </span>
            <span :class="[
              getFontSizeClasses('lg'),
              'font-bold',
              getColorClasses(getComplianceColor(framework.complianceScore)).default
            ]">
              {{ framework.complianceScore }}%
            </span>
          </div>

          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div 
              :class="[
                'h-2 rounded-full transition-all duration-300',
                getColorClasses(getComplianceColor(framework.complianceScore), 'background')
              ]"
              :style="{ width: `${framework.complianceScore}%` }"
            ></div>
          </div>

          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
              Requirements: {{ framework.completed }}/{{ framework.requirements }}
            </span>
            <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
              Next Audit: {{ framework.nextAudit }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Compliance Table -->
    <div :class="[componentStyles.card, 'p-6']">
      <div class="flex items-center justify-between mb-6">
        <h2 :class="[getFontSizeClasses('xl'), 'font-semibold text-gray-900 dark:text-white']">
          Compliance Details
        </h2>
        <ComplianceButton variant="outline" size="sm">
          <i class="fas fa-download mr-2"></i>
          Export Report
        </ComplianceButton>
      </div>

      <div class="overflow-x-auto">
        <table :class="[componentStyles.table, 'w-full']">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Framework
              </th>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Status
              </th>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Score
              </th>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Progress
              </th>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Last Audit
              </th>
              <th :class="[getFontSizeClasses('sm'), 'px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="framework in regulatoryFrameworks" :key="framework.name">
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap']">
                <div>
                  <div :class="[getFontSizeClasses('sm'), 'font-medium text-gray-900 dark:text-white']">
                    {{ framework.name }}
                  </div>
                  <div :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                    {{ framework.fullName }}
                  </div>
                </div>
              </td>
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap']">
                <StatusBadge 
                  :status="framework.status" 
                  :color="getStatusColor(framework.status)"
                />
              </td>
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap']">
                <span :class="[
                  getFontSizeClasses('sm'),
                  'font-medium',
                  getColorClasses(getComplianceColor(framework.complianceScore)).default
                ]">
                  {{ framework.complianceScore }}%
                </span>
              </td>
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap']">
                <div class="flex items-center">
                  <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                    <div 
                      :class="[
                        'h-2 rounded-full',
                        getColorClasses(getComplianceColor(framework.complianceScore), 'background')
                      ]"
                      :style="{ width: `${framework.complianceScore}%` }"
                    ></div>
                  </div>
                  <span :class="[getFontSizeClasses('xs'), 'text-gray-500 dark:text-gray-400']">
                    {{ framework.completed }}/{{ framework.requirements }}
                  </span>
                </div>
              </td>
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400']">
                {{ framework.lastAudit }}
              </td>
              <td :class="[getFontSizeClasses('sm'), 'px-6 py-4 whitespace-nowrap']">
                <div class="flex space-x-2">
                  <ComplianceButton variant="ghost" size="sm">
                    <i class="fas fa-eye"></i>
                  </ComplianceButton>
                  <ComplianceButton variant="ghost" size="sm">
                    <i class="fas fa-edit"></i>
                  </ComplianceButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
