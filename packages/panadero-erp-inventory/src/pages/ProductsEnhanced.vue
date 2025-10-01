<!--
  Products Enhanced Page
  @version 1.0.0
  @date 29-Sep-2025
  @description Enhanced products page using generic DataTableComplete
-->
<script setup>
import { ref, onMounted } from 'vue'
import { DataTableComplete } from 'panadero-datatable'
import ProductForm from '../components/ProductForm.vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'
import axios from 'axios'

// Get common functionality for dark mode and scaling
const { darkModeClasses, scalingStyles } = useCommonSnippets()

const enhancedConfig = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    // Fetch configuration from model via API
    const response = await axios.get('/api/erp_products/config')
    const modelConfig = response.data
    
    // Build enhanced configuration from model
    enhancedConfig.value = {
      title: modelConfig.title || 'Products',
      icon: modelConfig.icon || 'fas fa-box',
      // Search fields from model
      searchFields: modelConfig.searchFields || ['name', 'identifier', 'comment'],
      // Filter dependencies from model
      filterDependencies: [
        {
          filterKey: 'product_type',
          dependsOn: [],
          resets: ['product_group', 'brand']
        },
        {
          filterKey: 'product_group',
          dependsOn: ['product_type'],
          resets: ['brand']
        },
        {
          filterKey: 'brand',
          dependsOn: ['product_type', 'product_group'],
          resets: []
        }
      ],
      // Export fields from model columns
      exportFields: (modelConfig.columns || []).map(col => ({
        key: col.key,
        label: col.label,
        transform: col.key === 'is_active' ? (value) => value ? 'Yes' : 'No' : undefined
      })),
      // Use model configuration
      columns: modelConfig.columns || [],
      kpis: modelConfig.kpis || [],
      filters: (modelConfig.filters || []).map(filter => ({
        key: filter.key,
        label: filter.label,
        field: filter.field || `${filter.relationship}.name`
      })),
      dropdowns: [
        { key: 'productTypes', endpoint: '/api/erp_product_types' },
        { key: 'productGroups', endpoint: '/api/erp_product_groups' },
        { key: 'brands', endpoint: '/api/erp_brands' },
        { key: 'units', endpoint: '/api/erp_units' }
      ],
      formComponent: ProductForm,
      formProps: {
        productTypes: 'productTypes',
        productGroups: 'productGroups',
        brands: 'brands',
        units: 'units'
      },
      apiHeaders: {
        'X-From-Products-Page': 'true'
      }
    }
  } catch (err) {
    console.error('Failed to load configuration:', err)
    error.value = `Failed to load configuration: ${err.message}`
    // Don't set enhancedConfig - let the error state show
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="products-enhanced" :class="darkModeClasses.container">
    <DataTableComplete 
      v-if="enhancedConfig && !error"
      :config="enhancedConfig"
      :api-endpoint="'/api/erp_products'"
      :table-name="'erp_products'"
      :dark-mode-classes="darkModeClasses"
      :scaling-styles="scalingStyles"
    />
    <div v-else-if="loading" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p :class="darkModeClasses.textSecondary">Loading configuration...</p>
      </div>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="text-red-500 text-6xl mb-4">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 :class="darkModeClasses.text" class="text-xl font-semibold mb-2">Configuration Error</h3>
        <p :class="[darkModeClasses.textSecondary, 'text-red-600 dark:text-red-400']" class="mb-4">{{ error }}</p>
        <button 
          @click="location.reload()" 
          :style="scalingStyles.button"
          :class="[darkModeClasses.button, 'px-4 py-2 rounded-lg hover:opacity-90 transition-colors']"
        >
          <i class="fas fa-refresh mr-2"></i>
          Retry
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.products-enhanced {
  @apply p-6;
}
</style>
