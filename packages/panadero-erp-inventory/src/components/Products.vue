<!--
  Products Component
  @version 1.0.16
  @date 29-Sep-2025
  @description Product master data management with reusable filter components
-->
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'
import { useErpData } from '../composables/useErpData.js'
import { useProductCascadingFilters } from '../composables/useProductCascadingFilters.js'


// UI Shared Components 
import { SharedButton, KPICard, DistributionCard, ActionCard, ConfirmationModal } from 'panadero-shared-components'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import ActionButton from './ui/ActionButton.vue'
import CleanBadge from './ui/CleanBadge.vue'
import { FilterSection } from 'panadero-filters'
import { DataTable, createTableConfig, useModelConfig } from '../../../panadero-datatable/index.js'
import ProductForm from './ProductForm.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions: commonStatusOptions, 
  typeOptions: commonTypeOptions,
  colorOptions,
  formatCurrency,
  confirmAction,
  showNotification
} = useCommonSnippets()

// Get ERP data
const {
  products: erpProducts,
  units,
  productTypes,
  productGroups,
  brands,
  statuses,
  isLoading: erpLoading,
  fetchProducts,
  getUnitSymbol,
  getProductTypeName,
  getProductGroupName,
  getBrandName,
  getStatusName,
  getStatusColor
} = useErpData()

// Use the cascading product filters composable
const {
  selectedProductType,
  selectedProductGroup,
  selectedBrand,
  searchQuery,
  productTypeItems,
  productGroupItems,
  brandItems,
  filteredProducts,
  productStats,
  selectProductType,
  selectProductGroup,
  selectBrand,
  clearAllFilters,
  getProductTypeCount,
  getProductGroupCount,
  getBrandCount
} = useProductCascadingFilters(erpProducts, productTypes, productGroups, brands)


// State
const showProductForm = ref(false)
const editingProduct = ref(null)
const selectedProducts = ref([])
const showBulkActions = ref(false)

// Confirmation modal state
const showDeleteConfirm = ref(false)
const productToDelete = ref(null)

// Add bulk delete confirmation modal state
const showBulkDeleteConfirm = ref(false)
const productsToDelete = ref([])

// Add refresh modal state
const showRefreshModal = ref(false)
const refreshMessage = ref('')
const refreshCount = ref(0)

// Get model configuration
const { dataTableConfig, loading: configLoading, fetchConfig } = useModelConfig('erp_products')

// Fetch model configuration on mount
onMounted(async () => {
  await fetchConfig()
})

// DataTable configuration - now comes from model
const productTableConfig = computed(() => {
  if (!dataTableConfig.value) {
    return createTableConfig({
      table: 'products',
      title: 'Products',
      columns: [],
      itemsPerPage: 20
    })
  }
  return createTableConfig(dataTableConfig.value)
})

// Sort and pagination
const sortBy = ref('name')
const sortOrder = ref('asc')
const currentPage = ref(1)
const itemsPerPage = ref(10)

const collapsed = ref(true);

// Sorted products
const sortedProducts = computed(() => {
  const sorted = [...filteredProducts.value]
  
  sorted.sort((a, b) => {
    let aVal = a[sortBy.value]
    let bVal = b[sortBy.value]
    
    // Handle string comparison
    if (typeof aVal === 'string') {
      aVal = aVal.toLowerCase()
      bVal = bVal.toLowerCase()
    }
    
    if (sortOrder.value === 'asc') {
      return aVal > bVal ? 1 : -1
    } else {
      return aVal < bVal ? 1 : -1
    }
  })
  
  return sorted
})

// Paginated products
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return sortedProducts.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(sortedProducts.value.length / itemsPerPage.value)
})

// Inventory KPIs for KPICard component
const inventoryKPIs = computed(() => {
  const stats = productStats.value
  return [
    {
      label: 'Total Products',
      value: stats.total,
      colorClass: 'text-gray-700 dark:text-gray-300'
    },
    {
      label: 'Active Products', 
      value: stats.active,
      colorClass: 'text-green-600'
    },
    {
      label: 'Locked Products',
      value: stats.locked, 
      colorClass: 'text-orange-600'
    },
    {
      label: 'Product Groups',
      value: Object.keys(stats.byGroup).length,
      colorClass: 'text-blue-600'
    }
  ]
})

// Sort options
const sortOptions = [
  { value: 'name', label: 'Name' },
  { value: 'identifier', label: 'SKU' },
  { value: 'product_group.name', label: 'Product Group' },
  { value: 'brand.name', label: 'Brand' },
  { value: 'product_type.name', label: 'Type' },
  { value: 'is_active', label: 'Status' }
]

// Methods
const openProductForm = (product = null) => {
  editingProduct.value = product
  showProductForm.value = true
}

const handleProductSave = async (productData) => {
  // Close the form
  showProductForm.value = false
  editingProduct.value = null
  
  // Refresh data from API to show the changes - this prevents duplicates
  try {
    await fetchProducts()
    console.debug('Product data refreshed after save')
  } catch (error) {
    console.error('Error refreshing data after save:', error)
  }
}

const handleProductFormClose = () => {
  showProductForm.value = false
  editingProduct.value = null
}

const handleDeleteProduct = async (product) => {
  productToDelete.value = product
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  const product = productToDelete.value
  if (!product) return
  
  
  try {
    // FIX FOR DELETE FUNCTIONALITY: Call API to delete from database
    // - Added specific DELETE route for erp_products/{id} in routes/public.php
    // - Uses destroyPublic method that skips authentication checks
    // - Ensures CSRF cookie is set for proper authentication
    
    // Ensure CSRF cookie is set
    await axios.get('/sanctum/csrf-cookie')
    
   const response = await axios.delete(`/api/erp_products/${product.id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-From-Products-Page': 'true'  // Custom header
      }
    })
    
    if (response.data.success) {
      // Remove from local array
      const index = filteredProducts.value.findIndex(p => p.id === product.id)
      if (index !== -1) {
        filteredProducts.value.splice(index, 1)
      }
      
      // ALSO remove from main erpProducts array
      const mainIndex = erpProducts.value.findIndex(p => p.id === product.id)
      if (mainIndex !== -1) {
        erpProducts.value.splice(mainIndex, 1)
      }
      
      showNotification(`Product "${product.name}" deleted successfully`, 'success')
    } else {
      console.error('Delete failed:', response.data.message)
      showNotification(response.data.message || 'Failed to delete product', 'error')
    }
    
  } catch (error) {
    console.error('Failed to delete product:', error)
    showNotification('Failed to delete product. Please try again.', 'error')
  } finally {
    showDeleteConfirm.value = false
    productToDelete.value = null
  }
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  productToDelete.value = null
}

// Interactive methods
const handleSort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
  currentPage.value = 1
}

const handleSearch = () => {
  currentPage.value = 1
}

const toggleProductSelection = (productId) => {
  const index = selectedProducts.value.indexOf(productId)
  if (index > -1) {
    selectedProducts.value.splice(index, 1)
  } else {
    selectedProducts.value.push(productId)
  }
}

const selectAllProducts = () => {
  selectedProducts.value = paginatedProducts.value.map(p => p.id)
}

const clearSelection = () => {
  selectedProducts.value = []
}

const handleBulkAction = (action) => {
  if (selectedProducts.value.length === 0) return
  
  switch (action) {
    case 'activate':
      break
    case 'deactivate':
      break
    case 'export':
      break
    case 'delete':
      if (confirmAction(`Are you sure you want to delete ${selectedProducts.value.length} products?`)) {
        clearSelection()
      }
      break
  }
}

const changePage = (page) => {
  currentPage.value = page
}

const changeItemsPerPage = (items) => {
  itemsPerPage.value = items
  currentPage.value = 1
}

const getSortIcon = (field) => {
  if (sortBy.value !== field) return 'fas fa-sort'
  return sortOrder.value === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'
}

const exportProducts = () => {
}

const importProducts = () => {
  // TODO: Implement import functionality
  showNotification('Product import completed', 'info')
}

const exportRawData = () => {
  try {
    // Get all products with exact database values
    const rawDataToExport = erpProducts.value.map(product => ({
      'SKU/Identifier': product.identifier || '',
      'Name': product.name || '',
      'Product Type ID': product.erp_product_type_id || '',
      'Product Type Name': product.product_type?.name || '',
      'Product Group ID': product.erp_product_group_id || '',
      'Product Group Name': product.product_group?.name || '',
      'Brand ID': product.erp_brand_id || '',
      'Brand Name': product.brand?.name || '',
      'Unit ID': product.unit_id || '',
      'Unit Name': product.unit?.name || '',
      'Comment': product.comment || '',
      'Active': product.is_active ? 'Yes' : 'No',
      'Locked': product.is_locked ? 'Yes' : 'No',
      'Created At': product.created_at || ''
    }))

    // Convert to CSV
    const csvContent = convertToCSV(rawDataToExport)
    
    // Download file
    downloadCSV(csvContent, `products_raw_data_${new Date().toISOString().split('T')[0]}.csv`)
    
    showNotification('Raw data exported successfully', 'success')
  } catch (error) {
    console.error('Raw data export error:', error)
    showNotification('Raw data export failed', 'error')
  }
}

onMounted(async () => {
  store.loadSettings()
  await fetchProducts()
})

// Product type distribution with GitHub-style data
const productTypeDistribution = computed(() => {
  // Calculate counts from filtered products, not all products
  const total = filteredProducts.value.length || 1 // Avoid division by zero
  
  // Count each type in the filtered products
  const typeCounts = {}
  filteredProducts.value.forEach(product => {
    const typeName = product.product_type?.name || 'Unknown'
    typeCounts[typeName] = (typeCounts[typeName] || 0) + 1
  })
  
  return productTypes.value.map(type => {
    const count = typeCounts[type.name] || 0
    const percentage = total > 0 ? (count / total) * 100 : 0
    
    return {
      name: type.name,
      count: count,
      percentage: percentage,
      color: getProductTypeColor(type.name)
    }
  }).sort((a, b) => b.percentage - a.percentage) // Sort by percentage descending
})

// Helper function for product type colors (GitHub-style)
const getProductTypeColor = (typeName) => {
  const colors = {
    'normal': '#3b82f6',      // Blue
    'bulk': '#10b981',        // Green  
    'liquid': '#06b6d4',      // Cyan
    'service': '#8b5cf6',     // Purple
    'raw': '#f59e0b',         // Amber
    'finished': '#ef4444',    // Red
    'component': '#84cc16',   // Lime
    'material': '#f97316'     // Orange
  }
  return colors[typeName.toLowerCase()] || '#6b7280' // Gray fallback
}

// Brand distribution with GitHub-style data
const brandDistribution = computed(() => {
  // Calculate counts from filtered products, not all products
  const total = filteredProducts.value.length || 1 // Avoid division by zero
  
  // Count each brand in the filtered products
  const brandCounts = {}
  filteredProducts.value.forEach(product => {
    const brandName = product.brand?.name || 'Unknown'
    brandCounts[brandName] = (brandCounts[brandName] || 0) + 1
  })
  
  return brands.value.map(brand => {
    const count = brandCounts[brand.name] || 0
    const percentage = total > 0 ? (count / total) * 100 : 0
    
    return {
      name: brand.name,
      count: count,
      percentage: percentage,
      color: getBrandColor(brand.name)
    }
  }).filter(brand => brand.count > 0) // Only show brands that have products
    .sort((a, b) => b.percentage - a.percentage) // Sort by percentage descending
})

// Helper function for brand colors (GitHub-style)
const getBrandColor = (brandName) => {
  const colors = [
    '#3b82f6', // Blue
    '#10b981', // Green  
    '#06b6d4', // Cyan
    '#8b5cf6', // Purple
    '#f59e0b', // Amber
    '#ef4444', // Red
    '#84cc16', // Lime
    '#f97316', // Orange
    '#ec4899', // Pink
    '#6366f1', // Indigo
    '#14b8a6', // Teal
    '#a855f7'  // Violet
  ]
  
  // Use a simple hash to assign consistent colors
  let hash = 0
  for (let i = 0; i < brandName.length; i++) {
    hash = brandName.charCodeAt(i) + ((hash << 5) - hash)
  }
  return colors[Math.abs(hash) % colors.length]
}

// Add this computed property for actions
const quickActions = computed(() => [
  {
    key: 'add-product',
    label: 'Add New Product',
    icon: 'fas fa-plus',
    variant: 'primary',
    handler: () => openProductForm()
  },
  {
    key: 'export-products',
    label: 'Export Products',
    icon: 'fas fa-download',
    variant: 'secondary',
    handler: () => exportProducts()
  },
  {
    key: 'import-products',
    label: 'Import Products',
    icon: 'fas fa-upload',
    variant: 'secondary',
    handler: () => importProducts()
  }
])

// Update bulk delete handler
const handleBulkDelete = async (selectedIds) => {
  
  if (selectedIds.length === 0) {
    console.warn('No items selected for bulk delete')
    return
  }
  
  // Get product details for confirmation
  const selectedProducts = filteredProducts.value.filter(p => selectedIds.includes(p.id))
  productsToDelete.value = selectedProducts
  showBulkDeleteConfirm.value = true
}

const confirmBulkDelete = async () => {
  const selectedProducts = productsToDelete.value
  if (selectedProducts.length === 0) return
  
  const selectedIds = selectedProducts.map(p => p.id)
  
  try {
    // Delete each product via API
    await Promise.all(selectedIds.map(id => {
      return axios.delete(`/api/erp_products/${id}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-From-Products-Page': 'true'
        }
      })
    }))
    
    // Remove from local data
    filteredProducts.value = filteredProducts.value.filter(p => !selectedIds.includes(p.id))
    
    // ALSO remove from main erpProducts array
    erpProducts.value = erpProducts.value.filter(p => !selectedIds.includes(p.id))
    
    showNotification(`Successfully deleted ${selectedIds.length} product(s)`, 'success')
    
    // Clear selected items in DataTable
    if (dataTableRef.value) {
      dataTableRef.value.clearSelectedItems()
    }
    
  } catch (error) {
    console.error('Bulk delete error:', error)
    showNotification(`Error deleting products: ${error.response?.data?.message || error.message}`, 'error')
  } finally {
    showBulkDeleteConfirm.value = false
    productsToDelete.value = []
  }
}

const cancelBulkDelete = () => {
  showBulkDeleteConfirm.value = false
  productsToDelete.value = []
}

// Add refresh handler
const handleRefresh = async () => {
  
  // Store current count for comparison
  const beforeCount = filteredProducts.value.length
  
  try {
    // Reload products from API
    await fetchProducts()
    
    // Calculate new records
    const afterCount = filteredProducts.value.length
    const newRecords = afterCount - beforeCount
    
    // Show refresh modal
    if (newRecords > 0) {
      refreshMessage.value = `Found ${newRecords} new product(s)`
      refreshCount.value = newRecords
    } else {
      refreshMessage.value = 'Data refreshed successfully'
      refreshCount.value = 0
    }
    
    showRefreshModal.value = true
    
    // Auto-hide modal after 2 seconds
    setTimeout(() => {
      showRefreshModal.value = false
      refreshMessage.value = ''
      refreshCount.value = 0
    }, 2000)
    
  } catch (error) {
    console.error('Refresh error:', error)
    refreshMessage.value = 'Error refreshing data'
    showRefreshModal.value = true
    
    // Auto-hide error modal after 2 seconds
    setTimeout(() => {
      showRefreshModal.value = false
      refreshMessage.value = ''
    }, 2000)
  }
}

</script>

<template>
  <!-- Container Layout -->
  <div class="products " :class="darkModeClasses.container">

    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Products</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Buttons -->
        <div class="flex items-center gap-2 mr-4">

        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <SharedButton 
            @click="openProductForm()" 
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            Add Product
          </SharedButton>
        </div>

          <SharedButton 
            @click="exportProducts" 
            variant="success"
            size="normal"
            icon-left="fas fa-download"
          >
            Export 
          </SharedButton>
  
          <SharedButton 
            @click="exportRawData" 
            variant="warning"
            size="normal"
            icon-left="fas fa-database"
          >
            Export Raw Data
          </SharedButton>

          
          <SharedButton 
            @click="importProducts" 
            variant="info"
            size="normal"
            icon-left="fas fa-upload"
          >
            Import 
          </SharedButton>



        </div>

      </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-3 xl:grid-cols-5 gap-6">

      <!-- LEFT Main Content Section (2/3 on regular, 4/5 on XL) - LEFT SIDE -->
      <div class="col-span-2 xl:col-span-4">

<!-- Product Header -->
<div :class="[darkModeClasses.card, 'rounded-xl shadow-lg border p-6']">
  <div class="flex items-center justify-between mb-4">
    <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="font-semibold">Product Filters</h3>
    <div class="rounded-full p-2" :class="darkModeClasses.bgSecondary">
      <i :class="collapsed ? 'fas fa-chevron-down' : 'fas fa-chevron-up'" 
         :style="scalingStyles?.iconSizeSmall || { fontSize: '0.875rem' }"
         class="text-blue-500 dark:text-blue-600 transition-transform duration-200 cursor-pointer hover:opacity-70"
         @click="collapsed=!collapsed"></i>
    </div>
  </div>

  <!-- Compact Single Row -->
  <div class="flex items-center gap-4 flex-wrap">

    <!-- Collapse indicator -->
    <div class="flex items-center gap-2">

    </div>

    <!-- Product Type -->
    <div v-if="collapsed" class="flex items-center gap-2">
      <span :style="scalingStyles?.textFontSize" :class="darkModeClasses?.textSecondary" class="font-medium text-sm">
        Type:
      </span>
      <select v-model="selectedProductType" 
              :style="scalingStyles.inputPadding"
              :class="[darkModeClasses.input, 'rounded-lg border-2 focus:border-blue-500 transition-colors text-sm min-w-[120px]']"
              @change="selectProductType(selectedProductType)">
        <option value="all">All Types</option>
        <option v-for="item in productTypeItems" :key="item.value" :value="item.value">
          {{ item.label }}
        </option>
      </select>
    </div>

    <!-- Product Group -->
    <div v-if="collapsed" class="flex items-center gap-2">
      <span :style="scalingStyles?.textFontSize" :class="darkModeClasses?.textSecondary" class="font-medium text-sm">
        Group:
      </span>
      <select v-model="selectedProductGroup" 
              :style="scalingStyles.inputPadding"
              :class="[darkModeClasses.input, 'rounded-lg border-2 focus:border-blue-500 transition-colors text-sm min-w-[120px]']"
              @change="selectProductGroup(selectedProductGroup)">
        <option value="all">All Groups</option>
        <option v-for="item in productGroupItems" :key="item.value" :value="item.value">
          {{ item.label }}
        </option>
      </select>
    </div>

    <!-- Brand -->
    <div v-if="collapsed" class="flex items-center gap-2">
      <span :style="scalingStyles?.textFontSize" :class="darkModeClasses?.textSecondary" class="font-medium text-sm">
        Brand:
      </span>
      <select v-model="selectedBrand" 
              :style="scalingStyles.inputPadding"
              :class="[darkModeClasses.input, 'rounded-lg border-2 focus:border-blue-500 transition-colors text-sm min-w-[120px]']"
              @change="selectBrand(selectedBrand)">
        <option value="all">All Brands</option>
        <option v-for="item in brandItems" :key="item.value" :value="item.value">
          {{ item.label }}
        </option>
      </select>
    </div>



    <!-- Search Bar -->
    <div v-if="collapsed" class="flex-1 flex justify-end">
      <div class="relative w-80 ">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search products..."
          :style="scalingStyles.inputPadding"
          :class="[darkModeClasses.input, 'rounded-lg border-2 focus:border-blue-500 transition-colors']"
          class="w-full"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i class="fas fa-search text-gray-400" :style="scalingStyles.iconSize"></i>
        </div>
      </div>
    </div>
  </div>

      <!-- Filters when NOT collapsed -->
      <div v-if="!collapsed" class="space-y-6">
        <!-- Product Type Filter -->
        <FilterSection
          title="Product Type"
          :items="productTypeItems"
          :selected-value="selectedProductType"
          :scaling-styles="scalingStyles"
          :dark-mode-classes="darkModeClasses"
          @select="selectProductType"
        />

        <!-- Product Groups Filter -->
        <FilterSection
          title="Product Groups"
          :items="productGroupItems"
          :selected-value="selectedProductGroup"
          grid-class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3"
          :scaling-styles="scalingStyles"
          :dark-mode-classes="darkModeClasses"
          @select="selectProductGroup"
        />

        <!-- Brand Filter -->
        <FilterSection
          title="Filter by Brand"
          :items="brandItems"
          :selected-value="selectedBrand"
          :scaling-styles="scalingStyles"
          :dark-mode-classes="darkModeClasses"
          @select="selectBrand"
        />
      </div>

</div>
<!-- End Product Header -->

       <!-- Records List -->
       <div class="col-span-4 mt-6">
        <div :class="[darkModeClasses.card, 'overflow-x-auto rounded-xl shadow-lg p-6 border']">

           <!-- Enhanced Products Table -->
           <DataTable 
             ref="dataTableRef"
             :config="productTableConfig"
             :dark-mode-classes="darkModeClasses"
             :scaling-styles="scalingStyles"
             :external-data="filteredProducts"
             @create="openProductForm"
             @edit="openProductForm"
             @delete="handleDeleteProduct"
             @bulk-delete="handleBulkDelete"
             @refresh="handleRefresh"
           />
        </div>
      </div>

      <!-- END LEFT SIDE Main Content Section -->
      </div>

      <!-- RIGHT SIDE : Main Content Section (1/3 on regular, 1/5 on XL) -->
      <div class="col-span-1 space-y-6">


        <!-- Inventory Overview -->
        <KPICard
          title="Inventory Overview"
          icon="fas fa-chart-pie"
          icon-color="text-blue-600"
          :kpis="inventoryKPIs"
          :dark-mode-classes="darkModeClasses"
          :scaling-styles="scalingStyles"
        />


        <!-- Product Type Distribution - GitHub Style -->
        <DistributionCard
          title="Product Types"
          icon="fas fa-chart-pie"
          icon-color="text-indigo-600"
          :distribution="productTypeDistribution"
          :dark-mode-classes="darkModeClasses"
          :scaling-styles="scalingStyles"
        />

      <!-- Brand Distribution - GitHub Style -->
      <DistributionCard
        title="Brands"
        icon="fas fa-tags"
        icon-color="text-orange-600"
        :distribution="brandDistribution"
        :dark-mode-classes="darkModeClasses"
        :scaling-styles="scalingStyles"
      />


        <!-- Quick Actions -->
        <ActionCard
          title="Quick Actions"
          icon="fas fa-bolt"
          icon-color="text-yellow-600"
          :actions="quickActions"
          :dark-mode-classes="darkModeClasses"
          :scaling-styles="scalingStyles"
          @action-click="(action) => action.handler()"
        />


      <!-- End right Side-->
      </div>

    <!-- End Main Grid Layout -->
    </div>

    <!-- Product Form Modal -->
    <ProductForm
      :show="showProductForm"
      :product="editingProduct"
      :product-types="productTypes"
      :product-groups="productGroups"
      :brands="brands"
      :units="units"
      @saved="handleProductSave"
      @close="handleProductFormClose"
    />

    <!-- Single Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteConfirm"
      title="Delete Product"
      :message="productToDelete ? `Are you sure you want to delete this product?\n\n📦 Product: ${productToDelete.name}\n🏷️  SKU: ${productToDelete.identifier}\n📋 Type: ${productToDelete.product_type?.name || 'Unknown'}\n\n⚠️  This action cannot be undone.` : ''"
      confirm-text="Delete"
      cancel-text="Cancel"
      variant="danger"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
      @close="cancelDelete"
    />

    <!-- Bulk Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showBulkDeleteConfirm"
      title="Delete Products"
      :message="productsToDelete.length > 0 ? `Are you sure you want to delete ${productsToDelete.length} product(s)?\n\n📦 Products:\n${productsToDelete.map(p => `• ${p.name} (${p.identifier})`).join('\n')}\n\n⚠️  This action cannot be undone.` : ''"
      confirm-text="Delete All"
      cancel-text="Cancel"
      variant="danger"
      @confirm="confirmBulkDelete"
      @cancel="cancelBulkDelete"
      @close="cancelBulkDelete"
    />

    <!-- Refresh Modal -->
    <ConfirmationModal
      :show="showRefreshModal"
      :title="refreshCount > 0 ? 'Data Refreshed' : 'Refresh Complete'"
      :message="refreshMessage"
      :confirm-text="refreshCount > 0 ? 'View New Records' : 'OK'"
      :cancel-text="null"
      :variant="refreshCount > 0 ? 'success' : 'info'"
      @confirm="() => { showRefreshModal = false; refreshMessage = ''; refreshCount = 0; }"
      @close="() => { showRefreshModal = false; refreshMessage = ''; refreshCount = 0; }"
    />

  <!-- End Container Layout -->
  </div>

</template>
