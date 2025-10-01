<!--
  Generic Filters Example
  @version 1.0.0
  @date 23-Sep-2025
  @description Example showing how to use generic filters for different entities
-->
<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Generic Filters Example</h1>
    
    <!-- Products Example -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Products Filters</h2>
      <FilterSection
        title="Filter by Product Type"
        :items="productTypeItems"
        :selected-value="selectedProductType"
        @select="selectProductType"
      />
    </div>

    <!-- Customers Example -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Customers Filters</h2>
      <FilterSection
        title="Filter by Status"
        :items="customerStatusItems"
        :selected-value="selectedCustomerStatus"
        @select="selectCustomerStatus"
      />
    </div>

    <!-- Orders Example -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Orders Filters</h2>
      <FilterSection
        title="Filter by Order Status"
        :items="orderStatusItems"
        :selected-value="selectedOrderStatus"
        @select="selectOrderStatus"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import FilterSection from '../components/ui/FilterSection.vue'
import { useProductFiltersGeneric } from '../composables/useProductFiltersGeneric.js'
import { useGenericFilters } from '../composables/useGenericFilters.js'

// Mock data
const products = ref([
  { id: 1, name: 'Steel Rod', product_type: { name: 'bulk' }, product_group: { name: 'Steel' }, brand: { name: 'Toyota' } },
  { id: 2, name: 'Aluminum Sheet', product_type: { name: 'bulk' }, product_group: { name: 'Aluminum' }, brand: { name: 'BMW' } },
  { id: 3, name: 'Copper Wire', product_type: { name: 'normal' }, product_group: { name: 'Copper' }, brand: { name: 'Intel' } }
])

const customers = ref([
  { id: 1, name: 'John Doe', status: 'active', country: 'USA', customer_type: 'individual' },
  { id: 2, name: 'Acme Corp', status: 'active', country: 'Canada', customer_type: 'business' },
  { id: 3, name: 'Jane Smith', status: 'pending', country: 'UK', customer_type: 'individual' }
])

const orders = ref([
  { id: 1, order_number: 'ORD-001', status: 'pending', total: 100.00 },
  { id: 2, order_number: 'ORD-002', status: 'completed', total: 250.00 },
  { id: 3, order_number: 'ORD-003', status: 'cancelled', total: 75.00 }
])

// Products filters (using product-specific wrapper)
const {
  selectedProductType,
  productTypeItems,
  selectProductType
} = useProductFiltersGeneric(products, [], [], [])

// Customers filters (using generic composable directly)
const customerFilterConfigs = [
  {
    key: 'status',
    field: 'status',
    label: 'Status',
    pluralLabel: 'Statuses',
    searchable: true,
    getValue: (customer) => customer.status,
    getIcon: (status) => {
      const icons = {
        'active': 'fas fa-check-circle',
        'pending': 'fas fa-clock',
        'inactive': 'fas fa-pause-circle'
      }
      return icons[status] || 'fas fa-user'
    }
  }
]

const {
  filterStates: customerFilterStates,
  allFilterItems: customerFilterItems,
  selectFilter: selectCustomerFilter
} = useGenericFilters(customers, customerFilterConfigs)

const selectedCustomerStatus = computed({
  get: () => customerFilterStates.value.status,
  set: (value) => selectCustomerFilter('status', value)
})

const customerStatusItems = computed(() => customerFilterItems.value.status || [])

const selectCustomerStatus = (status) => {
  selectCustomerFilter('status', status)
}

// Orders filters (using generic composable directly)
const orderFilterConfigs = [
  {
    key: 'status',
    field: 'status',
    label: 'Status',
    pluralLabel: 'Statuses',
    searchable: true,
    getValue: (order) => order.status,
    getIcon: (status) => {
      const icons = {
        'pending': 'fas fa-clock',
        'completed': 'fas fa-check-circle',
        'cancelled': 'fas fa-times-circle',
        'shipped': 'fas fa-truck'
      }
      return icons[status] || 'fas fa-file'
    }
  }
]

const {
  filterStates: orderFilterStates,
  allFilterItems: orderFilterItems,
  selectFilter: selectOrderFilter
} = useGenericFilters(orders, orderFilterConfigs)

const selectedOrderStatus = computed({
  get: () => orderFilterStates.value.status,
  set: (value) => selectOrderFilter('status', value)
})

const orderStatusItems = computed(() => orderFilterItems.value.status || [])

const selectOrderStatus = (status) => {
  selectOrderFilter('status', status)
}
</script>
