<script setup>
import { ref, computed } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'
import WorkflowDemo from '../demo/WorkflowDemo.vue'
import WorkflowStatistics from '../workflow/WorkflowStatistics.vue'
import WorkflowCard from '../workflow/WorkflowCard.vue'

const store = useFinanceStore()

// Event handlers for workflow dashboard
const handleWorkflowSelected = (workflow) => {
  console.log('Workflow selected:', workflow)
  // You can add navigation logic here
}

const handleWorkflowStarted = (instance) => {
  console.log('Workflow started:', instance)
  // You can add notification or navigation logic here
}

// Workflow statistics
const workflowStats = ref({
  totalWorkflows: 12,
  activeWorkflows: 5,
  completedWorkflows: 7,
  templatesAvailable: 8,
  averageCompletionTime: '2.3 days',
  pendingApprovals: 3
})

// Feature highlights
const workflowFeatures = [
  {
    title: 'Template-Based Workflows',
    description: 'Pre-defined workflow templates for common business processes',
    icon: 'fas fa-sitemap',
    color: 'text-blue-600 dark:text-blue-400'
  },
  {
    title: 'Multi-Step Approval',
    description: 'Role-based approval chains with automated notifications',
    icon: 'fas fa-check-circle',
    color: 'text-green-600 dark:text-green-400'
  },
  {
    title: 'Dynamic Forms',
    description: 'JSON schema-based form generation with validation',
    icon: 'fas fa-edit',
    color: 'text-purple-600 dark:text-purple-400'
  },
  {
    title: 'Audit Trail',
    description: 'Complete history tracking of all workflow activities',
    icon: 'fas fa-history',
    color: 'text-orange-600 dark:text-orange-400'
  },
  {
    title: 'SLA Management',
    description: 'Service level agreement tracking and alerts',
    icon: 'fas fa-clock',
    color: 'text-red-600 dark:text-red-400'
  },
  {
    title: 'Integration Ready',
    description: 'API endpoints for external system integration',
    icon: 'fas fa-plug',
    color: 'text-indigo-600 dark:text-indigo-400'
  }
]

// Available workflow templates
const workflowTemplates = [
  // Core Business Workflows
  { name: 'Vendor Onboarding', id: 'vendor-onboarding', steps: 5, avgTime: '3-5 days', category: 'procurement' },
  { name: 'Employee Onboarding', id: 'employee-onboarding', steps: 7, avgTime: '1-2 weeks', category: 'hr' },
  { name: 'Purchase Request', id: 'purchase-request', steps: 4, avgTime: '2-3 days', category: 'procurement' },
  { name: 'Contract Renewal', id: 'contract-renewal', steps: 6, avgTime: '2-3 weeks', category: 'legal' },
  
  // Financial ERP Workflows
  { name: 'Simple Journal Entry', id: 'gl-journal-entry-simple', steps: 2, avgTime: '30 minutes', category: 'general_ledger' },
  { name: 'Vendor Invoice Approval', id: 'ap-vendor-invoice-approval', steps: 3, avgTime: '3-5 days', category: 'accounts_payable' },
  { name: 'Customer Invoice Creation', id: 'ar-customer-invoice-creation', steps: 2, avgTime: '1-2 days', category: 'accounts_receivable' },
  { name: 'Cash Flow Forecast', id: 'cf-cash-forecast-approval', steps: 2, avgTime: '2-3 days', category: 'cash_flow' },
  { name: 'Department Budget Approval', id: 'budget-department-budget-approval', steps: 3, avgTime: '3-4 weeks', category: 'budgeting' },
  { name: 'Asset Acquisition', id: 'fa-asset-acquisition-approval', steps: 3, avgTime: '1-2 weeks', category: 'fixed_assets' },
  { name: 'Quarterly Tax Filing', id: 'tax-quarterly-filing-preparation', steps: 3, avgTime: '1 week', category: 'tax_management' },
  { name: 'Monthly Reconciliation', id: 'compliance-monthly-reconciliation', steps: 2, avgTime: '3-5 days', category: 'compliance' },
  { name: 'Financial Statements', id: 'report-financial-statements', steps: 3, avgTime: '1-2 weeks', category: 'reporting' }
]
</script>

<template>
  <div class="space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white mb-2">
            <i class="fas fa-info-circle mr-3 text-indigo-600 dark:text-indigo-400"></i>
            Workflow Info 
          </h1>
          <p :style="{ fontSize: `${store.fontSizes.base}px` }" class="text-gray-600 dark:text-gray-300">
            Streamline business processes with automated workflows, approvals, and tracking
          </p>
        </div>
        <div class="text-right">
          <div :style="{ fontSize: `${store.fontSizes.base + 6}px` }" class="font-bold text-indigo-600 dark:text-indigo-400">
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <WorkflowStatistics :stats="workflowStats" />
    
    <!-- Feature Highlights -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h2 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" class="font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-star mr-2 text-yellow-500"></i>
        Key Features
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="feature in workflowFeatures" :key="feature.title" class="flex items-start space-x-3">
          <div class="flex-shrink-0">
            <i :class="[feature.icon, feature.color]" :style="store.scalingStyles.iconSize"></i>
          </div>
          <div>
            <h3 :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-semibold text-gray-900 dark:text-white mb-1">
              {{ feature.title }}
            </h3>
            <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Workflow Templates -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h2 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" class="font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-layer-group mr-2 text-indigo-600 dark:text-indigo-400"></i>
        Available Workflow Templates
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <WorkflowCard 
          v-for="template in workflowTemplates" 
          :key="template.id"
          :workflow="template"
          :clickable="false"
          @select="handleWorkflowSelected"
        />
      </div>
    </div>


    <!-- Live Demo Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h2 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" class="font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-play mr-2 text-green-600 dark:text-green-400"></i>
        Interactive Workflow Demo
      </h2>
      <WorkflowDemo />
    </div>
  </div>
</template>

<style scoped>
/* Workflow section specific styles */
</style>
