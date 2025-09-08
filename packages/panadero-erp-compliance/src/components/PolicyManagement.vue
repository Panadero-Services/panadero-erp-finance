<!--
  Policy Management Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Policy management for compliance
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

const policyTypes = [
  { value: '', label: 'All Types' },
  { value: 'regulatory', label: 'Regulatory' },
  { value: 'internal', label: 'Internal' },
  { value: 'industry', label: 'Industry' },
  { value: 'security', label: 'Security' }
]

const categories = [
  { value: '', label: 'All Categories' },
  { value: 'gdpr', label: 'GDPR' },
  { value: 'sox', label: 'SOX' },
  { value: 'iso', label: 'ISO' },
  { value: 'pci', label: 'PCI' },
  { value: 'hipaa', label: 'HIPAA' },
  { value: 'custom', label: 'Custom' }
]

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'active', label: 'Active' },
  { value: 'draft', label: 'Draft' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'archived', label: 'Archived' }
]

const filters = ref({
  type: '',
  category: '',
  status: ''
})

const mockPolicies = ref([
  {
    id: 1,
    name: 'GDPR Data Protection Policy',
    type: 'regulatory',
    category: 'gdpr',
    description: 'Comprehensive data protection policy in accordance with GDPR requirements',
    status: 'active',
    version: '2.1',
    effective_date: '2024-01-01',
    expiry_date: '2025-12-31',
    created_by: 'John Smith',
    approved_by: 'Jane Doe',
    approved_at: '2024-01-01T10:00:00Z',
    requirements: {
      data_subject_rights: true,
      consent_management: true,
      data_breach_notification: true,
      privacy_by_design: true
    }
  },
  {
    id: 2,
    name: 'SOX Financial Controls Policy',
    type: 'regulatory',
    category: 'sox',
    description: 'Sarbanes-Oxley compliance policy for financial reporting controls',
    status: 'active',
    version: '1.5',
    effective_date: '2024-01-01',
    expiry_date: '2025-12-31',
    created_by: 'Mike Johnson',
    approved_by: 'Sarah Wilson',
    approved_at: '2024-01-01T14:30:00Z',
    requirements: {
      management_assessment: true,
      auditor_attestation: true,
      internal_controls: true,
      disclosure_controls: true
    }
  },
  {
    id: 3,
    name: 'ISO 27001 Information Security Policy',
    type: 'industry',
    category: 'iso',
    description: 'Information security management system policy based on ISO 27001 standards',
    status: 'active',
    version: '3.0',
    effective_date: '2024-01-01',
    expiry_date: '2025-12-31',
    created_by: 'David Brown',
    approved_by: 'Lisa Davis',
    approved_at: '2024-01-01T09:15:00Z',
    requirements: {
      risk_assessment: true,
      security_controls: true,
      incident_management: true,
      continuous_improvement: true
    }
  },
  {
    id: 4,
    name: 'Internal Code of Conduct',
    type: 'internal',
    category: 'custom',
    description: 'Company-wide code of conduct and ethical standards',
    status: 'draft',
    version: '4.0',
    effective_date: null,
    expiry_date: null,
    created_by: 'Anna Taylor',
    approved_by: null,
    approved_at: null,
    requirements: {
      ethical_behavior: true,
      conflict_of_interest: true,
      whistleblower_protection: true,
      training_requirements: true
    }
  }
])

const filteredPolicies = computed(() => {
  let filtered = mockPolicies.value

  if (filters.value.type) {
    filtered = filtered.filter(policy => policy.type === filters.value.type)
  }

  if (filters.value.category) {
    filtered = filtered.filter(policy => policy.category === filters.value.category)
  }

  if (filters.value.status) {
    filtered = filtered.filter(policy => policy.status === filters.value.status)
  }

  return filtered
})

onMounted(() => {
  loadPolicyData()
})

const loadPolicyData = async () => {
  try {
    loading.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    policies.value = mockPolicies.value
  } catch (error) {
    console.error('Error loading policy data:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const colorMap = {
    active: 'green',
    draft: 'yellow',
    inactive: 'gray',
    archived: 'red'
  }
  return colorMap[status] || 'gray'
}

const getTypeColor = (type) => {
  const colorMap = {
    regulatory: 'red',
    internal: 'blue',
    industry: 'green',
    security: 'purple'
  }
  return colorMap[type] || 'gray'
}

const getCategoryColor = (category) => {
  const colorMap = {
    gdpr: 'blue',
    sox: 'green',
    iso: 'orange',
    pci: 'purple',
    hipaa: 'red',
    custom: 'gray'
  }
  return colorMap[category] || 'gray'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const isExpiringSoon = (expiryDate) => {
  if (!expiryDate) return false
  const expiry = new Date(expiryDate)
  const now = new Date()
  const daysUntilExpiry = Math.ceil((expiry - now) / (1000 * 60 * 60 * 24))
  return daysUntilExpiry <= 30 && daysUntilExpiry > 0
}

const isExpired = (expiryDate) => {
  if (!expiryDate) return false
  return new Date(expiryDate) < new Date()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 :class="[getFontSizeClasses('3xl'), 'font-bold text-gray-900 dark:text-white']">
          Policy Management
        </h1>
        <p :class="[getFontSizeClasses('lg'), 'text-gray-600 dark:text-gray-400']">
          Manage compliance policies and procedures
        </p>
      </div>
      <div class="flex items-center space-x-4">
        <ComplianceButton variant="primary" size="md">
          <i class="fas fa-plus mr-2"></i>
          Create Policy
        </ComplianceButton>
      </div>
    </div>

    <!-- Policy Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-file-contract text-green-600 dark:text-green-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Active Policies
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockPolicies.filter(p => p.status === 'active').length }}
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
              Expiring Soon
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockPolicies.filter(p => isExpiringSoon(p.expiry_date)).length }}
            </p>
          </div>
        </div>
      </div>

      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Expired
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockPolicies.filter(p => isExpired(p.expiry_date)).length }}
            </p>
          </div>
        </div>
      </div>

      <div :class="[componentStyles.card, 'p-6']">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-edit text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div class="ml-4">
            <h3 :class="[getFontSizeClasses('sm'), 'font-medium text-gray-600 dark:text-gray-400']">
              Draft Policies
            </h3>
            <p :class="[getFontSizeClasses('2xl'), 'font-bold text-gray-900 dark:text-white']">
              {{ mockPolicies.filter(p => p.status === 'draft').length }}
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
            Type
          </label>
          <select
            v-model="filters.type"
            :class="[
              'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
              getFontSizeClasses('sm')
            ]"
          >
            <option v-for="type in policyTypes" :key="type.value" :value="type.value">
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

    <!-- Policy Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="policy in filteredPolicies"
        :key="policy.id"
        :class="[componentStyles.card, 'p-6 hover:shadow-md transition-shadow duration-200']"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :class="[getFontSizeClasses('lg'), 'font-semibold text-gray-900 dark:text-white mb-1']">
              {{ policy.name }}
            </h3>
            <p :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400 mb-2']">
              {{ policy.description }}
            </p>
            <div class="flex items-center space-x-2">
              <StatusBadge 
                :status="policy.type" 
                :color="getTypeColor(policy.type)"
                size="sm"
              />
              <StatusBadge 
                :status="policy.category.toUpperCase()" 
                :color="getCategoryColor(policy.category)"
                size="sm"
              />
              <StatusBadge 
                :status="policy.status" 
                :color="getStatusColor(policy.status)"
                size="sm"
              />
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Version:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ policy.version }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Effective Date:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ formatDate(policy.effective_date) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Expiry Date:
            </span>
            <span :class="[
              getFontSizeClasses('sm'),
              'text-gray-900 dark:text-white',
              isExpiringSoon(policy.expiry_date) ? 'text-yellow-600 dark:text-yellow-400' : '',
              isExpired(policy.expiry_date) ? 'text-red-600 dark:text-red-400' : ''
            ]">
              {{ formatDate(policy.expiry_date) }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Created By:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ policy.created_by }}
            </span>
          </div>

          <div v-if="policy.approved_by" class="flex items-center justify-between">
            <span :class="[getFontSizeClasses('sm'), 'text-gray-600 dark:text-gray-400']">
              Approved By:
            </span>
            <span :class="[getFontSizeClasses('sm'), 'text-gray-900 dark:text-white']">
              {{ policy.approved_by }}
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
