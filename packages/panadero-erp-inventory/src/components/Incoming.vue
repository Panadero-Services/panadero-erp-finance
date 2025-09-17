<!--
  Incoming Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Manage incoming stock orders, receipts, and deliveries
-->
<template>
  <div class="incoming" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Incoming Stock
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Manage incoming stock orders, receipts, and deliveries
      </p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Planned Orders"
        :value="getOrdersByStatus('planned').length"
        subtitle="Scheduled"
        icon="fas fa-calendar"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Unloading"
        :value="getOrdersByStatus('unloading').length"
        subtitle="In Progress"
        icon="fas fa-truck-loading"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Completed"
        :value="getOrdersByStatus('completed').length"
        subtitle="This Period"
        icon="fas fa-check-circle"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Total Value"
        :value="formatCurrency(getTotalIncomingValue)"
        subtitle="Expected Value"
        icon="fas fa-dollar-sign"
        color="purple"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Filters -->
    <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-6']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InventoryDropdown
          v-model="selectedFilter"
          :options="filterOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <div v-if="selectedFilter === 'custom'" class="grid grid-cols-2 gap-2">
          <InventoryInput
            v-model="customDateRange.start"
            type="date"
            label="Start Date"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
          <InventoryInput
            v-model="customDateRange.end"
            type="date"
            label="End Date"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
        </div>

        <InventoryInput
          v-model="searchTerm"
          placeholder="Search orders..."
          :style="scalingStyles.input"
          :class="darkModeClasses.input"
        >
          <template #prefix>
            <i class="fas fa-search"></i>
          </template>
        </InventoryInput>

        <InventoryButton
          @click="openOrderForm()"
          variant="primary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-plus mr-2"></i>
          New Order
        </InventoryButton>
      </div>
    </div>

    <!-- Orders Table -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Order #</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Supplier</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Expected Date</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Items</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Value</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="order in filteredOrders" :key="order.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ order.orderNumber }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ order.supplier }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatDate(order.expectedDate) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ order.items.length }} items
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(order.totalValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <StatusBadge 
                  :status="order.status === 'planned' ? 'info' : order.status === 'unloading' ? 'warning' : 'success'"
                  :text="order.status.charAt(0).toUpperCase() + order.status.slice(1)"
                  :scaling-styles="scalingStyles"
                  :dark-mode="store.settings.darkMode"
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton  variant="view" @click="viewOrderDetails(order)" />
                  <ActionButton  variant="success" @click="updateOrderStatus(order.id, getNextStatus(order.status))" :disabled="order.status === 'completed'" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderDetails" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            Order Details: {{ selectedOrder?.orderNumber }}
          </h3>
          <button @click="showOrderDetails = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div v-if="selectedOrder" class="space-y-4">
          <!-- Order Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium">Supplier</label>
              <p :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm">{{ selectedOrder.supplier }}</p>
            </div>
            <div>
              <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium">Expected Date</label>
              <p :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm">{{ formatDate(selectedOrder.expectedDate) }}</p>
            </div>
            <div>
              <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium">Total Value</label>
              <p :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm">{{ formatCurrency(selectedOrder.totalValue) }}</p>
            </div>
            <div>
              <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium">Status</label>
              <StatusBadge 
                :status="selectedOrder.status === 'planned' ? 'info' : selectedOrder.status === 'unloading' ? 'warning' : 'success'"
                :text="selectedOrder.status.charAt(0).toUpperCase() + selectedOrder.status.slice(1)"
                :scaling-styles="scalingStyles"
                :dark-mode="store.settings.darkMode"
              />
            </div>
          </div>

          <!-- Items Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">SKU</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Quantity</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Received</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
                <tr v-for="item in selectedOrder.items" :key="item.sku" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ item.sku }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.name }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.quantity }} {{ item.unit }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.received }} {{ item.unit }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <InventoryButton
                      @click="receiveItem(selectedOrder.id, item.sku, item.quantity - item.received)"
                      size="sm"
                      variant="success"
                      :style="scalingStyles.button"
                      :disabled="item.received >= item.quantity"
                    >
                      <i class="fas fa-check"></i>
                    </InventoryButton>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useIncoming } from '../composables/useIncoming.js'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'
import ActionButton from './ui/ActionButton.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions, 
  typeOptions,
  formatCurrency,
  formatDate,
  confirmAction 
} = useCommonSnippets()
const { 
  incomingOrders,
  filterOptions,
  selectedFilter,
  customDateRange,
  filteredOrders,
  getOrdersByStatus,
  getTotalIncomingValue,
  updateOrderStatus,
  receiveItem 
} = useIncoming()

// State
const searchTerm = ref('')
const showOrderDetails = ref(false)
const selectedOrder = ref(null)

// Dark mode classes are now provided by useCommonSnippets

// Methods
const openOrderForm = () => {
  console.log('Open new order form')
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
  showOrderDetails.value = true
}

const getNextStatus = (currentStatus) => {
  switch (currentStatus) {
    case 'planned': return 'unloading'
    case 'unloading': return 'completed'
    default: return currentStatus
  }
}

// formatDate is now provided by useCommonSnippets

// formatCurrency is now provided by useCommonSnippets

onMounted(() => {
  store.loadSettings()
})
</script>
