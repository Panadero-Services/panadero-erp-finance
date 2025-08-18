<script setup>
import { ref, computed } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'
import WorkflowDemo from '../demo/WorkflowDemo.vue'

const store = useFinanceStore()

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
  { name: 'Vendor Onboarding', id: 'vendor-onboarding', steps: 5, avgTime: '3-5 days' },
  { name: 'Employee Onboarding', id: 'employee-onboarding', steps: 7, avgTime: '1-2 weeks' },
  { name: 'Purchase Request', id: 'purchase-request', steps: 4, avgTime: '2-3 days' },
  { name: 'Invoice Approval', id: 'invoice-approval', steps: 3, avgTime: '1-2 days' },
  { name: 'Document Review', id: 'document-review', steps: 3, avgTime: '1-3 days' },
  { name: 'Budget Approval', id: 'budget-approval', steps: 5, avgTime: '1 week' },
  { name: 'Contract Renewal', id: 'contract-renewal', steps: 6, avgTime: '2-3 weeks' },
  { name: 'Asset Management', id: 'asset-management', steps: 4, avgTime: '1 week' }
]
</script>

<template>
  <div class="space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white mb-2">
            <i class="fas fa-sitemap mr-3 text-indigo-600 dark:text-indigo-400"></i>
            Workflow Management System
          </h1>
          <p :style="{ fontSize: `${store.fontSizes.base}px` }" class="text-gray-600 dark:text-gray-300">
            Streamline business processes with automated workflows, approvals, and tracking
          </p>
        </div>
        <div class="text-right">
          <div :style="{ fontSize: `${store.fontSizes.base + 6}px` }" class="font-bold text-indigo-600 dark:text-indigo-400">
            v1.0.7
          </div>
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">
            Production Ready
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Total Workflows
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white">
              {{ workflowStats.totalWorkflows }}
            </div>
          </div>
          <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <i class="fas fa-sitemap text-blue-600 dark:text-blue-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Active Workflows
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-green-600 dark:text-green-400">
              {{ workflowStats.activeWorkflows }}
            </div>
          </div>
          <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <i class="fas fa-play-circle text-green-600 dark:text-green-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Pending Approvals
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-orange-600 dark:text-orange-400">
              {{ workflowStats.pendingApprovals }}
            </div>
          </div>
          <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <i class="fas fa-clock text-orange-600 dark:text-orange-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Completed Workflows
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-purple-600 dark:text-purple-400">
              {{ workflowStats.completedWorkflows }}
            </div>
          </div>
          <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <i class="fas fa-check-circle text-purple-600 dark:text-purple-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Templates Available
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-indigo-600 dark:text-indigo-400">
              {{ workflowStats.templatesAvailable }}
            </div>
          </div>
          <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
            <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 font-medium">
              Avg. Completion Time
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white">
              {{ workflowStats.averageCompletionTime }}
            </div>
          </div>
          <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <i class="fas fa-stopwatch text-gray-600 dark:text-gray-400" :style="store.scalingStyles.iconSize"></i>
          </div>
        </div>
      </div>
    </div>

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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="template in workflowTemplates" :key="template.id" 
             class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-indigo-300 dark:hover:border-indigo-500 transition-colors">
          <h3 :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-semibold text-gray-900 dark:text-white mb-2">
            {{ template.name }}
          </h3>
          <div class="space-y-1">
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">
              <i class="fas fa-list-ol mr-2"></i>
              {{ template.steps }} steps
            </div>
            <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">
              <i class="fas fa-clock mr-2"></i>
              {{ template.avgTime }}
            </div>
          </div>
        </div>
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
