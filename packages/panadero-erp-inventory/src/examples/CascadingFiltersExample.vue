<!--
  Cascading Filters Example
  @version 1.0.0
  @date 23-Sep-2025
  @description Example showing cascading filters for different entities
-->
<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Cascading Filters Examples</h1>
    
    <!-- Products Example -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Products: Type → Group → Brand</h2>
      <div class="grid grid-cols-3 gap-4">
        <FilterSection
          title="Product Type"
          :items="productTypeItems"
          :selected-value="selectedProductType"
          @select="selectProductType"
        />
        <FilterSection
          title="Product Group"
          :items="productGroupItems"
          :selected-value="selectedProductGroup"
          @select="selectProductGroup"
        />
        <FilterSection
          title="Brand"
          :items="brandItems"
          :selected-value="selectedBrand"
          @select="selectBrand"
        />
      </div>
      <p class="text-sm text-gray-600 mt-2">
        Selecting Product Type resets Group and Brand. Selecting Group resets Brand.
      </p>
    </div>

    <!-- Orders Example -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Orders: Status → Priority → Customer</h2>
      <div class="grid grid-cols-3 gap-4">
        <FilterSection
          title="Order Status"
          :items="orderStatusItems"
          :selected-value="selectedOrderStatus"
          @select="selectOrderStatus"
        />
        <FilterSection
          title="Priority"
          :items="priorityItems"
          :selected-value="selectedPriority"
          @select="selectPriority"
        />
        <FilterSection
          title="Customer"
          :items="customerItems"
          :selected-value="selectedCustomer"
          @select="selectCustomer"
        />
      </div>
      <p class="text-sm text-gray-600 mt-2">
        Selecting Status resets Priority and Customer. Selecting Priority resets Customer.
      </p>
    </div>

    <!-- Results -->
    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-4">Filtered Results</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <h4 class="font-medium">Products ({{ filteredProducts.length }})</h4>
          <ul class="text-sm">
            <li v-for="product in filteredProducts.slice(0, 5)" :key="product.id">
              {{ product.name }} - {{ product.product_type?.name }} - {{ product.product_group?.name }}
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-medium">Orders ({{ filteredOrders.length }})</h4>
          <ul class="text-sm">
            <li v-for="order in filteredOrders.slice(0, 5)" :key="order.id">
              {{ order.order_number }} - {{ order.status }} - {{ order.priority }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import FilterSection from '../components/ui/FilterSection.vue'
import { useCascadingFilters } from '../composables/useCascadingFilters.js'

// Mock data
const products = ref([
  { id: 1, name: 'Steel Rod', product_type: { name: 'bulk' }, product_group: { name: 'Steel' }, brand: { name: 'Toyota' } },
  { id: 2, name: 'Aluminum Sheet', product_type: { name: 'bulk' }, product_group: { name: 'Aluminum' }, brand: { name: 'BMW' } },
  { id: 3, name: 'Copper Wire', product_type: { name: 'normal' }, product_group: { name: 'Copper' }, brand: { name: 'Intel' } },
  { id: 4, name: 'Steel Beam', product_type: { name: 'bulk' }, product_group: { name: 'Steel' }, brand: { name: 'BMW' } },
  { id: 5, name: 'Aluminum Pipe', product_type: { name: 'bulk' }, product_group: { name: 'Aluminum' }, brand: { name: 'Toyota' } }
])

const orders = ref([
  { id: 1, order_number: 'ORD-001', status: 'pending', priority: 'high', customer: 'John Doe' },
  { id: 2, order_number: 'ORD-002', status: 'completed', priority: 'medium', customer: 'Jane Smith' },
  { id: 3, order_number: 'ORD-003', status: 'pending', priority: 'low', customer: 'Bob Johnson' },
  { id: 4, order_number: 'ORD-004', status: 'shipped', priority: 'high', customer: 'Alice Brown' },
  { id: 5, order_number: 'ORD-005', status: 'completed', priority: 'medium', customer: 'John Doe' }
])

// Products filters configuration
const productFilterConfigs = [
  {
    key: 'productType',
    field: 'product_type.name',
    label: 'Type',
    pluralLabel: 'Types',
    searchable: true,
    getValue: (product) => product.product_type?.name,
    getIcon: (type) => {
      const icons = { 'normal': 'fas fa-box', 'bulk': 'fas fa-weight-hanging', 'liquid': 'fas fa-tint' }
      return icons[type] || 'fas fa-cube'
    }
  },
  {
    key: 'productGroup',
    field: 'product_group.name',
    label: 'Group',
    pluralLabel: 'Groups',
    searchable: true,
    getValue: (product) => product.product_group?.name,
    getIcon: () => 'fas fa-cube'
  },
  {
    key: 'brand',
    field: 'brand.name',
    label: 'Brand',
    pluralLabel: 'Brands',
    searchable: true,
    getValue: (product) => product.brand?.name,
    getIcon: () => 'fas fa-tag'
  }
]

// Products dependencies: Type → Group → Brand
const productDependencies = [
  {
    filterKey: 'productType',
    dependsOn: [],
    resets: ['productGroup', 'brand']
  },
  {
    filterKey: 'productGroup',
    dependsOn: ['productType'],
    resets: ['brand']
  },
  {
    filterKey: 'brand',
    dependsOn: ['productType', 'productGroup'],
    resets: []
  }
]

// Orders filters configuration
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
        'shipped': 'fas fa-truck',
        'cancelled': 'fas fa-times-circle'
      }
      return icons[status] || 'fas fa-file'
    }
  },
  {
    key: 'priority',
    field: 'priority',
    label: 'Priority',
    pluralLabel: 'Priorities',
    searchable: true,
    getValue: (order) => order.priority,
    getIcon: (priority) => {
      const icons = {
        'high': 'fas fa-exclamation-triangle',
        'medium': 'fas fa-exclamation-circle',
        'low': 'fas fa-info-circle'
      }
      return icons[priority] || 'fas fa-circle'
    }
  },
  {
    key: 'customer',
    field: 'customer',
    label: 'Customer',
    pluralLabel: 'Customers',
    searchable: true,
    getValue: (order) => order.customer,
    getIcon: () => 'fas fa-user'
  }
]

// Orders dependencies: Status → Priority → Customer
const orderDependencies = [
  {
    filterKey: 'status',
    dependsOn: [],
    resets: ['priority', 'customer']
  },
  {
    filterKey: 'priority',
    dependsOn: ['status'],
    resets: ['customer']
  },
  {
    filterKey: 'customer',
    dependsOn: ['status', 'priority'],
    resets: []
  }
]

// Use cascading filters for products
const {
  filterStates: productFilterStates,
  allFilterItems: productFilterItems,
  filteredData: filteredProducts,
  selectFilter: selectProductFilter
} = useCascadingFilters(products, productFilterConfigs, productDependencies)

// Use cascading filters for orders
const {
  filterStates: orderFilterStates,
  allFilterItems: orderFilterItems,
  filteredData: filteredOrders,
  selectFilter: selectOrderFilter
} = useCascadingFilters(orders, orderFilterConfigs, orderDependencies)

// Products computed properties
const selectedProductType = computed({
  get: () => productFilterStates.value.productType,
  set: (value) => selectProductFilter('productType', value)
})

const selectedProductGroup = computed({
  get: () => productFilterStates.value.productGroup,
  set: (value) => selectProductFilter('productGroup', value)
})

const selectedBrand = computed({
  get: () => productFilterStates.value.brand,
  set: (value) => selectProductFilter('brand', value)
})

const productTypeItems = computed(() => productFilterItems.value.productType || [])
const productGroupItems = computed(() => productFilterItems.value.productGroup || [])
const brandItems = computed(() => productFilterItems.value.brand || [])

// Orders computed properties
const selectedOrderStatus = computed({
  get: () => orderFilterStates.value.status,
  set: (value) => selectOrderFilter('status', value)
})

const selectedPriority = computed({
  get: () => orderFilterStates.value.priority,
  set: (value) => selectOrderFilter('priority', value)
})

const selectedCustomer = computed({
  get: () => orderFilterStates.value.customer,
  set: (value) => selectOrderFilter('customer', value)
})

const orderStatusItems = computed(() => orderFilterItems.value.status || [])
const priorityItems = computed(() => orderFilterItems.value.priority || [])
const customerItems = computed(() => orderFilterItems.value.customer || [])

// Action methods
const selectProductType = (type) => selectProductFilter('productType', type)
const selectProductGroup = (group) => selectProductFilter('productGroup', group)
const selectBrand = (brand) => selectProductFilter('brand', brand)

const selectOrderStatus = (status) => selectOrderFilter('status', status)
const selectPriority = (priority) => selectOrderFilter('priority', priority)
const selectCustomer = (customer) => selectOrderFilter('customer', customer)
</script>
