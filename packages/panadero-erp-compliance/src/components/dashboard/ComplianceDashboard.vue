<script setup>
import { ref, computed, onMounted } from 'vue'
import { useComplianceStore } from '../../stores/complianceStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import { useComplianceInfoBoxes } from '../../composables/useComplianceInfoBoxes.js'

// UI Components
import ComplianceValueCard from '../ui/ComplianceValueCard.vue'
import StatusBadge from '../ui/StatusBadge.vue'
import ComplianceButton from '../ui/ComplianceButton.vue'

const store = useComplianceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()
const { infoBoxes } = useComplianceInfoBoxes()

// Dashboard state
const isLoading = ref(false)
const selectedPeriod = ref('current')

// Computed properties
const dashboardStats = computed(() => ({
  totalPolicies: store.policies.length,
  lowStockItems: store.policies.filter(p => p.status === 'draft').length,
  totalValue: store.audits.filter(a => a.status === 'completed').length,
  activeWarehouses: store.risks.filter(r => r.level === 'high').length,
  pendingOrders: store.audits.filter(a => a.status === 'in-progress').length,
  activeSuppliers: store.policies.filter(p => p.status === 'active').length
}))

const recentMovements = computed(() => 
  store.audits.slice(-5).reverse()
)

const lowStockAlerts = computed(() => 
  store.risks.filter(r => r.level === 'high' || r.level === 'critical').map(risk => ({
    sku: risk.id,
    name: risk.title,
    currentStock: risk.level === 'critical' ? 0 : 1,
    minStock: 1,
    urgency: risk.level === 'critical' ? 'critical' : 'warning'
  }))
)

// Actions
const refreshDashboard = async () => {
  isLoading.value = true
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  isLoading.value = false
}

const handleQuickAction = (action) => {
  console.log('Quick action:', action)
  // Implement quick actions
}

onMounted(() => {
  refreshDashboard()
})
</script>

<template>
  <div class="compliance-dashboard">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: `${fontSizes.large}px` }" class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Compliance Dashboard
      </h1>
      <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
        Overview of your compliance management system
      </p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <div class="flex flex-wrap gap-3">
        <ComplianceButton
          @click="handleQuickAction('add-policy')"
          variant="primary"
          size="md"
        >
          <i class="fas fa-plus mr-2"></i>
          Add Policy
        </ComplianceButton>
        <ComplianceButton
          @click="handleQuickAction('create-audit')"
          variant="secondary"
          size="md"
        >
          <i class="fas fa-clipboard-check mr-2"></i>
          Create Audit
        </ComplianceButton>
        <ComplianceButton
          @click="handleQuickAction('assess-risk')"
          variant="outline"
          size="md"
        >
          <i class="fas fa-exclamation-triangle mr-2"></i>
          Assess Risk
        </ComplianceButton>
        <ComplianceButton
          @click="refreshDashboard"
          variant="ghost"
          size="md"
          :loading="isLoading"
        >
          <i class="fas fa-sync-alt mr-2"></i>
          Refresh
        </ComplianceButton>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
      <ComplianceValueCard
        title="Total Policies"
        :value="dashboardStats.totalPolicies"
        icon="fas fa-file-contract"
        color="teal"
        :trend="5"
      />
      <ComplianceValueCard
        title="Draft Policies"
        :value="dashboardStats.lowStockItems"
        icon="fas fa-edit"
        color="yellow"
        :trend="-2"
      />
      <ComplianceValueCard
        title="Completed Audits"
        :value="dashboardStats.totalValue"
        icon="fas fa-check-circle"
        color="green"
        :trend="12"
      />
      <ComplianceValueCard
        title="High Risks"
        :value="dashboardStats.activeWarehouses"
        icon="fas fa-exclamation-triangle"
        color="red"
        :trend="-8"
      />
      <ComplianceValueCard
        title="In Progress"
        :value="dashboardStats.pendingOrders"
        icon="fas fa-clock"
        color="blue"
        :trend="3"
      />
      <ComplianceValueCard
        title="Active Policies"
        :value="dashboardStats.activeSuppliers"
        icon="fas fa-shield-alt"
        color="purple"
        :trend="1"
      />
    </div>

    <!-- Alerts Section -->
    <div v-if="lowStockAlerts.length > 0" class="mb-8">
      <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Risk Alerts
      </h2>
      <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-center mb-3">
          <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
          <span class="font-medium text-red-800 dark:text-red-200">Attention Required</span>
        </div>
        <div class="space-y-2">
          <div
            v-for="alert in lowStockAlerts"
            :key="alert.sku"
            class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border"
          >
            <div>
              <span class="font-medium">{{ alert.name }}</span>
              <span class="text-gray-500 ml-2">({{ alert.sku }})</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">
                {{ alert.currentStock }} / {{ alert.minStock }}
              </span>
              <StatusBadge
                :status="alert.urgency"
                :text="alert.urgency === 'critical' ? 'Critical' : 'Warning'"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Audits -->
      <div>
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Recent Audits
        </h2>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Audit ID
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Score
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Date
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="audit in recentMovements" :key="audit.id">
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    {{ audit.id }}
                  </td>
                  <td class="px-4 py-3">
                    <StatusBadge
                      :status="audit.status === 'completed' ? 'success' : 'warning'"
                      :text="audit.status"
                    />
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    {{ audit.score }}%
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(audit.auditDate).toLocaleString() }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div>
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Quick Stats
        </h2>
        <div class="space-y-4">
          <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Compliance Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ Math.round((dashboardStats.activeSuppliers / dashboardStats.totalPolicies) * 100) }}%
                </p>
              </div>
              <i class="fas fa-chart-line text-teal-500 text-2xl"></i>
            </div>
          </div>
          
          <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Risk Mitigation</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ Math.round((dashboardStats.totalValue / (dashboardStats.totalValue + dashboardStats.activeWarehouses)) * 100) }}%
                </p>
              </div>
              <i class="fas fa-shield-alt text-green-500 text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.compliance-dashboard {
  padding: 0;
}
</style>