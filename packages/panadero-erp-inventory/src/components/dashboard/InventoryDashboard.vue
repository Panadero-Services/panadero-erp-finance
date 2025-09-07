<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../../stores/inventoryStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import { useInventoryInfoBoxes } from '../../composables/useInventoryInfoBoxes.js'

// UI Components
import InventoryValueCard from '../ui/InventoryValueCard.vue'
import StatusBadge from '../ui/StatusBadge.vue'
import InventoryButton from '../ui/InventoryButton.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles, spacing } = useScaling()
const { infoBoxes } = useInventoryInfoBoxes()

// Dashboard state
const isLoading = ref(false)
const selectedPeriod = ref('current')

// Computed properties
const dashboardStats = computed(() => ({
  totalItems: store.stockItems.length,
  lowStockItems: store.getLowStockItems.length,
  totalValue: store.getTotalInventoryValue,
  activeWarehouses: store.warehouses.filter(w => w.status === 'active').length,
  pendingOrders: store.purchaseOrders.filter(po => po.status === 'pending').length,
  activeSuppliers: store.suppliers.filter(s => s.status === 'active').length
}))

const recentMovements = computed(() => 
  store.stockMovements.slice(-5).reverse()
)

const lowStockAlerts = computed(() => 
  store.getLowStockItems.map(item => ({
    sku: item.sku,
    name: item.name,
    currentStock: item.currentStock,
    minStock: item.minStock,
    urgency: item.currentStock === 0 ? 'critical' : 'warning'
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
  <div class="inventory-dashboard">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: `${fontSizes.large}px` }" class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Inventory Dashboard
      </h1>
      <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
        Overview of your inventory management system
      </p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <div class="flex flex-wrap gap-3">
        <InventoryButton
          @click="handleQuickAction('add-item')"
          variant="primary"
          size="md"
        >
          <i class="fas fa-plus mr-2"></i>
          Add Stock Item
        </InventoryButton>
        <InventoryButton
          @click="handleQuickAction('create-po')"
          variant="secondary"
          size="md"
        >
          <i class="fas fa-shopping-cart mr-2"></i>
          Create Purchase Order
        </InventoryButton>
        <InventoryButton
          @click="handleQuickAction('adjust-stock')"
          variant="outline"
          size="md"
        >
          <i class="fas fa-edit mr-2"></i>
          Adjust Stock
        </InventoryButton>
        <InventoryButton
          @click="refreshDashboard"
          variant="ghost"
          size="md"
          :loading="isLoading"
        >
          <i class="fas fa-sync-alt mr-2"></i>
          Refresh
        </InventoryButton>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
      <InventoryValueCard
        title="Total Items"
        :value="dashboardStats.totalItems"
        icon="fas fa-boxes"
        color="blue"
        :trend="5"
      />
      <InventoryValueCard
        title="Low Stock Items"
        :value="dashboardStats.lowStockItems"
        icon="fas fa-exclamation-triangle"
        color="red"
        :trend="-2"
      />
      <InventoryValueCard
        title="Total Value"
        :value="`$${dashboardStats.totalValue.toLocaleString()}`"
        icon="fas fa-dollar-sign"
        color="green"
        :trend="12"
      />
      <InventoryValueCard
        title="Active Warehouses"
        :value="dashboardStats.activeWarehouses"
        icon="fas fa-warehouse"
        color="purple"
        :trend="0"
      />
      <InventoryValueCard
        title="Pending Orders"
        :value="dashboardStats.pendingOrders"
        icon="fas fa-shopping-cart"
        color="orange"
        :trend="3"
      />
      <InventoryValueCard
        title="Active Suppliers"
        :value="dashboardStats.activeSuppliers"
        icon="fas fa-truck"
        color="cyan"
        :trend="1"
      />
    </div>

    <!-- Alerts Section -->
    <div v-if="lowStockAlerts.length > 0" class="mb-8">
      <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Low Stock Alerts
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
      <!-- Recent Stock Movements -->
      <div>
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Recent Stock Movements
        </h2>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    SKU
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Quantity
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Time
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="movement in recentMovements" :key="movement.id">
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    {{ movement.sku }}
                  </td>
                  <td class="px-4 py-3">
                    <StatusBadge
                      :status="movement.movementType === 'in' ? 'success' : 'warning'"
                      :text="movement.movementType === 'in' ? 'In' : 'Out'"
                    />
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    {{ movement.quantity }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(movement.timestamp).toLocaleString() }}
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
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Stock Level</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ Math.round(store.stockItems.reduce((sum, item) => sum + item.currentStock, 0) / store.stockItems.length) }}
                </p>
              </div>
              <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
            </div>
          </div>
          
          <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Categories</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ new Set(store.stockItems.map(item => item.category)).size }}
                </p>
              </div>
              <i class="fas fa-tags text-green-500 text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.inventory-dashboard {
  padding: 0;
}
</style>
