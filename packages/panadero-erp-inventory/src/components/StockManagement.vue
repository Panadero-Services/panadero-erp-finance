<!--
  Stock Management Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Comprehensive stock management with three stock types and dynamic scaling
-->
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'
import { useErpData } from '../composables/useErpData.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions: commonStatusOptions, 
  typeOptions: commonTypeOptions,
  formatCurrency,
  formatDate,
  confirmAction 
} = useCommonSnippets()

// Get ERP data
const {
  products: erpProducts,
  stocks: erpStocks,
  storages: erpStorages,
  sites: erpSites,
  units: erpUnits,
  statuses: erpStatuses,
  isLoading: erpLoading,
  fetchProducts,
  fetchStocks,
  fetchStorages,
  fetchSites,
  fetchUnits,
  fetchStatuses,
  getProductName,
  getSiteName,
  getUnitSymbol,
  getStatusName,
  getStatusColor
} = useErpData()

// Dark mode classes are now provided by useCommonSnippets

// State
const isLoading = ref(false)
const searchTerm = ref('')
const selectedStockType = ref('all')
const selectedCategory = ref('all')
const showAddForm = ref(false)
const editingItem = ref(null)
const viewMode = ref('table') // 'table', 'cards', 'projections'
const projectionDays = ref(7)

// Form data for new/editing items
const formData = ref({
  sku: '',
  name: '',
  description: '',
  category: '',
  subcategory: '',
  stockType: 'items',
  currentStock: 0,
  minStock: 0,
  maxStock: 0,
  unit: 'units',
  unitCost: 0,
  unitPrice: 0,
  warehouseId: null,
  zone: '',
  shelf: '',
  bin: '',
  // Type-specific properties
  typeSpecificProperties: {}
})

// Stock type specific form fields
const getTypeSpecificFields = (stockType) => {
  switch (stockType) {
    case 'items':
      return {
        serialNumbers: [],
        condition: 'new',
        warranty: '',
        model: '',
        brand: ''
      }
    case 'bulk':
      return {
        grade: '',
        origin: '',
        harvestDate: '',
        batchNumber: '',
        moistureContent: 0,
        source: ''
      }
    case 'liquids':
      return {
        viscosity: '',
        temperature: 20,
        expiryDate: '',
        batchNumber: '',
        containerType: 'bulk_tank',
        phLevel: 0
      }
    default:
      return {}
  }
}

// Computed properties
const filteredItems = computed(() => {
  let items = store.stockItems
  
  if (searchTerm.value) {
    items = items.filter(item => 
      item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      item.sku.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      item.description?.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (selectedStockType.value !== 'all') {
    items = items.filter(item => item.stockType === selectedStockType.value)
  }
  
  if (selectedCategory.value !== 'all') {
    items = items.filter(item => item.category === selectedCategory.value)
  }
  
  return items
})

const stockTypeOptions = [
  { value: 'all', label: 'All Stock Types' },
  { value: 'items', label: 'Items' },
  { value: 'bulk', label: 'Bulk Materials' },
  { value: 'liquids', label: 'Liquids' }
]

const categoryOptions = computed(() => {
  const categories = [...new Set(store.stockItems.map(item => item.category))]
  return [
    { value: 'all', label: 'All Categories' },
    ...categories.map(cat => ({ value: cat, label: cat }))
  ]
})

const unitOptions = computed(() => {
  const stockType = formData.value.stockType
  switch (stockType) {
    case 'items':
      return ['units', 'pieces', 'boxes', 'pallets']
    case 'bulk':
      return ['kg', 'tons', 'cubic meters', 'cubic feet', 'pounds']
    case 'liquids':
      return ['liters', 'gallons', 'barrels', 'cubic meters']
    default:
      return ['units']
  }
})

// Stock projections for selected time period
const stockProjections = computed(() => {
  return filteredItems.value.map(item => {
    const projections = []
    for (let day = 1; day <= projectionDays.value; day++) {
      const projectedStock = store.getProjectedStock(item.sku, day)
      const incoming = item.incoming
        ?.filter(inc => {
          const incDate = new Date(inc.expectedDate)
          const targetDate = new Date(Date.now() + (day * 24 * 60 * 60 * 1000))
          return incDate.toDateString() === targetDate.toDateString()
        })
        .reduce((sum, inc) => sum + inc.quantity, 0) || 0
      
      const outgoing = item.outgoing
        ?.filter(out => {
          const outDate = new Date(out.scheduledDate)
          const targetDate = new Date(Date.now() + (day * 24 * 60 * 60 * 1000))
          return outDate.toDateString() === targetDate.toDateString()
        })
        .reduce((sum, out) => sum + out.quantity, 0) || 0
      
      projections.push({
        day,
        date: new Date(Date.now() + (day * 24 * 60 * 60 * 1000)).toISOString().split('T')[0],
        projectedStock,
        incoming,
        outgoing,
        status: projectedStock <= 0 ? 'critical' : 
                projectedStock <= item.minStock ? 'low' : 'normal'
      })
    }
    return { ...item, projections }
  })
})

// Methods
const openAddForm = () => {
  editingItem.value = null
  formData.value = {
    sku: '',
    name: '',
    description: '',
    category: '',
    subcategory: '',
    stockType: 'items',
    currentStock: 0,
    minStock: 0,
    maxStock: 0,
    unit: 'units',
    unitCost: 0,
    unitPrice: 0,
    warehouseId: null,
    zone: '',
    shelf: '',
    bin: '',
    typeSpecificProperties: getTypeSpecificFields('items')
  }
  showAddForm.value = true
}

const openEditForm = (item) => {
  editingItem.value = item
  formData.value = {
    ...item,
    typeSpecificProperties: item.typeSpecificProperties || getTypeSpecificFields(item.stockType)
  }
  showAddForm.value = true
}

const saveItem = async () => {
  isLoading.value = true
  try {
    if (editingItem.value) {
      // Update existing item
      const index = store.stockItems.findIndex(item => item.id === editingItem.value.id)
      if (index !== -1) {
        store.stockItems[index] = { ...formData.value, id: editingItem.value.id }
      }
    } else {
      // Add new item
      await store.addStockItem(formData.value)
    }
    showAddForm.value = false
    editingItem.value = null
  } catch (error) {
    console.error('Error saving item:', error)
  } finally {
    isLoading.value = false
  }
}

const deleteItem = async (item) => {
  if (confirm(`Are you sure you want to delete ${item.name}?`)) {
    const index = store.stockItems.findIndex(i => i.id === item.id)
    if (index !== -1) {
      store.stockItems.splice(index, 1)
    }
  }
}

const updateStockLevel = async (item, newQuantity, reason) => {
  await store.updateStockLevel(item.id, newQuantity, reason, 'Manual Adjustment')
}

// Watch for stock type changes to update form fields
watch(() => formData.value.stockType, (newType) => {
  formData.value.typeSpecificProperties = getTypeSpecificFields(newType)
  formData.value.unit = unitOptions.value[0]
})

onMounted(() => {
  store.loadSettings()
})
</script>

<template>
  <div class="stock-management" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex justify-between items-center mb-4">
        <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Stock Management
        </h2>
        <div class="flex gap-2">
          <InventoryButton 
            @click="openAddForm"
            variant="primary"
            :style="scalingStyles.buttonPadding"
          >
            <i class="fas fa-plus mr-2"></i>
            Add Stock Item
          </InventoryButton>
        </div>
    </div>

      <!-- Filters and Controls -->
      <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-4']">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InventoryInput
          v-model="searchTerm"
            placeholder="Search items..."
            :style="scalingStyles.inputPadding"
            :class="darkModeClasses.input"
          >
            <template #prefix>
              <i class="fas fa-search"></i>
            </template>
          </InventoryInput>

          <InventoryDropdown
            v-model="selectedStockType"
            :options="stockTypeOptions"
            :style="scalingStyles.inputPadding"
            :class="darkModeClasses.input"
          />

        <InventoryDropdown
          v-model="selectedCategory"
            :options="categoryOptions"
            :style="scalingStyles.inputPadding"
            :class="darkModeClasses.input"
          />

          <div class="flex gap-2">
            <InventoryButton
              @click="viewMode = 'table'"
              :variant="viewMode === 'table' ? 'primary' : 'secondary'"
              size="sm"
              :style="scalingStyles.buttonPadding"
            >
              <i class="fas fa-table"></i>
            </InventoryButton>
            <InventoryButton
              @click="viewMode = 'cards'"
              :variant="viewMode === 'cards' ? 'primary' : 'secondary'"
              size="sm"
              :style="scalingStyles.buttonPadding"
            >
              <i class="fas fa-th"></i>
            </InventoryButton>
            <InventoryButton
              @click="viewMode = 'projections'"
              :variant="viewMode === 'projections' ? 'primary' : 'secondary'"
              size="sm"
              :style="scalingStyles.buttonPadding"
            >
              <i class="fas fa-chart-line"></i>
            </InventoryButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Stock Type Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <InventoryValueCard
        title="Items"
        :value="store.getStockTypeMetrics('items').totalItems"
        subtitle="Discrete Units"
        icon="fas fa-box"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Bulk Materials"
        :value="store.getStockTypeMetrics('bulk').totalItems"
        subtitle="Granular Materials"
        icon="fas fa-mountain"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Liquids"
        :value="store.getStockTypeMetrics('liquids').totalItems"
        subtitle="Fluid Materials"
        icon="fas fa-tint"
        color="cyan"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Table View -->
    <div v-if="viewMode === 'table'" :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">SKU</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Current Stock</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Min/Max</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="item in filteredItems" :key="item.id" :class="[darkModeClasses.tableRow, 'divide-x', darkModeClasses.border]">
              <td :style="scalingStyles.tableCell" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ item.sku }}
              </td>
              <td :style="scalingStyles.tableCell" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.tableCell" :class="darkModeClasses.text" class="text-sm font-medium">{{ item.name }}</div>
                <div :style="scalingStyles.tableCellSubtext" :class="darkModeClasses.textSecondary" class="text-sm">{{ item.category }}</div>
              </td>
              <td :style="scalingStyles.tableCell" class="px-4 py-4 whitespace-nowrap">
                <StatusBadge 
                  :status="item.stockType"
                  :text="item.stockType.charAt(0).toUpperCase() + item.stockType.slice(1)"
                  :scaling-styles="scalingStyles"
                  :dark-mode="store.settings.darkMode"
                />
              </td>
              <td :style="scalingStyles.tableCell" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ item.currentStock }} {{ item.unit }}
              </td>
              <td :style="scalingStyles.tableCell" :class="darkModeClasses.textSecondary" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ item.minStock }} / {{ item.maxStock }}
              </td>
              <td :style="scalingStyles.tableCell" class="px-4 py-4 whitespace-nowrap">
                <StatusBadge
                  :status="item.currentStock <= item.minStock ? 'warning' : 'success'"
                  :text="item.currentStock <= item.minStock ? 'Low Stock' : 'Normal'"
                  :scaling-styles="scalingStyles"
                  :dark-mode="store.settings.darkMode"
                />
              </td>
              <td :style="scalingStyles.tableCell" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-2">
                  <InventoryButton
                    @click="openEditForm(item)"
                    variant="secondary"
                    size="sm"
                    :style="scalingStyles.buttonPadding"
                  >
                    <i class="fas fa-edit"></i>
                  </InventoryButton>
                  <InventoryButton
                    @click="deleteItem(item)"
                    variant="danger"
                    size="sm"
                    :style="scalingStyles.buttonPadding"
                  >
                    <i class="fas fa-trash"></i>
                  </InventoryButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Projections View -->
    <div v-if="viewMode === 'projections'" class="space-y-4">
      <div class="flex justify-between items-center mb-4">
        <h3 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="text-lg font-semibold">Stock Projections</h3>
        <div class="flex items-center gap-2">
          <label :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium">Projection Period:</label>
          <InventoryDropdown
            v-model="projectionDays"
            :options="[
              { value: 1, label: '1 Day' },
              { value: 3, label: '3 Days' },
              { value: 7, label: '1 Week' },
              { value: 14, label: '2 Weeks' },
              { value: 30, label: '1 Month' }
            ]"
            :style="scalingStyles.inputPadding"
            :class="darkModeClasses.input"
          />
        </div>
      </div>

      <div v-for="item in stockProjections" :key="item.id" :class="[darkModeClasses.card, 'rounded-lg shadow-sm border p-4']">
        <div class="flex justify-between items-center mb-3">
          <div>
            <h4 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="font-semibold">{{ item.name }}</h4>
            <p :style="scalingStyles.tableCellSubtext" :class="darkModeClasses.textSecondary" class="text-sm">{{ item.sku }} • {{ item.stockType }}</p>
          </div>
          <div class="text-right">
            <div :style="scalingStyles.value" :class="darkModeClasses.text" class="text-lg font-bold">{{ item.currentStock }} {{ item.unit }}</div>
            <div :style="scalingStyles.tableCellSubtext" :class="darkModeClasses.textSecondary" class="text-sm">Current Stock</div>
          </div>
        </div>
        
        <div class="grid grid-cols-7 gap-2">
          <div v-for="projection in item.projections" :key="projection.day" 
               class="text-center p-2 rounded"
               :class="{
                 'bg-red-100 text-red-800': projection.status === 'critical',
                 'bg-yellow-100 text-yellow-800': projection.status === 'low',
                 'bg-green-100 text-green-800': projection.status === 'normal'
               }">
            <div :style="scalingStyles.tableCellSubtext" class="text-xs font-medium">{{ projection.date }}</div>
            <div :style="scalingStyles.value" class="text-sm font-bold">{{ projection.projectedStock }}</div>
            <div :style="scalingStyles.tableCellSubtext" class="text-xs">
              <div v-if="projection.incoming > 0" class="text-green-600">+{{ projection.incoming }}</div>
              <div v-if="projection.outgoing > 0" class="text-red-600">-{{ projection.outgoing }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Form Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ editingItem ? 'Edit Stock Item' : 'Add New Stock Item' }}
          </h3>
          <button @click="showAddForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveItem" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="formData.sku"
              label="SKU"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="formData.name"
              label="Name"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
          </div>

          <InventoryInput
            v-model="formData.description"
            label="Description"
            type="textarea"
            :style="scalingStyles.textarea"
            :class="darkModeClasses.input"
          />

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InventoryDropdown
              v-model="formData.stockType"
              label="Stock Type"
              :options="stockTypeOptions.filter(opt => opt.value !== 'all')"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="formData.category"
              label="Category"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="formData.subcategory"
              label="Subcategory"
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InventoryInput
              v-model.number="formData.currentStock"
              label="Current Stock"
              type="number"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="formData.minStock"
              label="Min Stock"
              type="number"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="formData.maxStock"
              label="Max Stock"
              type="number"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InventoryDropdown
              v-model="formData.unit"
              label="Unit"
              :options="unitOptions.map(unit => ({ value: unit, label: unit }))"
              required
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="formData.unitCost"
              label="Unit Cost"
              type="number"
              step="0.01"
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="formData.unitPrice"
              label="Unit Price"
              type="number"
              step="0.01"
              :style="scalingStyles.inputPadding"
              :class="darkModeClasses.input"
            />
          </div>
          
          <!-- Type-specific properties -->
          <div v-if="formData.stockType === 'items'" class="space-y-4">
            <h4 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="font-medium">Item Properties</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InventoryInput
                v-model="formData.typeSpecificProperties.condition"
                label="Condition"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
              <InventoryInput
                v-model="formData.typeSpecificProperties.warranty"
                label="Warranty"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
            </div>
          </div>

          <div v-if="formData.stockType === 'bulk'" class="space-y-4">
            <h4 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="font-medium">Bulk Properties</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InventoryInput
                v-model="formData.typeSpecificProperties.grade"
                label="Grade"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
              <InventoryInput
                v-model="formData.typeSpecificProperties.origin"
                label="Origin"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
            </div>
          </div>

          <div v-if="formData.stockType === 'liquids'" class="space-y-4">
            <h4 :style="scalingStyles.subtitle" :class="darkModeClasses.text" class="font-medium">Liquid Properties</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InventoryInput
                v-model="formData.typeSpecificProperties.viscosity"
                label="Viscosity"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
          <InventoryInput
                v-model="formData.typeSpecificProperties.temperature"
                label="Temperature (°C)"
                type="number"
                :style="scalingStyles.inputPadding"
                :class="darkModeClasses.input"
              />
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-4">
            <InventoryButton
              @click="showAddForm = false"
              variant="secondary"
              :style="scalingStyles.buttonPadding"
            >
              Cancel
            </InventoryButton>
            <InventoryButton
              type="submit"
              variant="primary"
              :loading="isLoading"
              :style="scalingStyles.buttonPadding"
            >
              {{ editingItem ? 'Update' : 'Create' }} Item
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stock-management {
  @apply p-6;
}
</style>
.. 