// v1.0.0 - Product-specific cascading filters using generic composable
import { ref, computed } from 'vue'
import { useCascadingFilters } from './useCascadingFilters.js'

export function useProductCascadingFilters(erpProducts, productTypes, productGroups, brands) {
  // Define filter configurations for products
  const filterConfigs = [
    {
      key: 'productType',
      field: 'product_type.name',
      label: 'Type',
      pluralLabel: 'Types',
      searchable: true,
      getValue: (product) => product.product_type?.name,
      getIcon: (typeName) => {
        const icons = {
          'normal': 'fas fa-box',
          'bulk': 'fas fa-weight-hanging',
          'liquid': 'fas fa-tint',
          'service': 'fas fa-cogs'
        }
        return icons[typeName] || 'fas fa-cube'
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
    },
    // General search configuration for all searchable fields
    {
      key: 'generalSearch',
      field: 'name',
      label: 'General Search',
      searchable: true,
      getValue: (product) => {
        // Search across multiple fields
        const searchFields = [
          product.name,
          product.identifier,
          product.comment,
          product.product_group?.name,
          product.product_type?.name,
          product.brand?.name
        ]
        return searchFields.filter(Boolean).join(' ')
      },
      getIcon: () => 'fas fa-search'
    }
  ]

  // Define filter dependencies: Product Type → Product Group → Brand
  // Note: generalSearch is excluded from dependencies as it's only used for search
  const dependencies = [
    {
      filterKey: 'productType',        // When Product Type changes
      dependsOn: [],                   // It doesn't depend on any other filter
      resets: ['productGroup', 'brand'] // Reset Product Group and Brand
    },
    {
      filterKey: 'productGroup',       // When Product Group changes
      dependsOn: ['productType'],      // It depends on Product Type selection
      resets: ['brand']                // Reset Brand only
    },
    {
      filterKey: 'brand',              // When Brand changes
      dependsOn: ['productType', 'productGroup'], // It depends on both Product Type and Group
      resets: []                       // No dependent filters to reset
    }
  ]

  // Filter out generalSearch from regular filter configs for cascading filters
  const regularFilterConfigs = filterConfigs.filter(config => config.key !== 'generalSearch')

// Use the generic cascading filters composable with regular filter configs only
// Pass empty searchQuery to prevent double search filtering
const {
  filterStates,
  searchQuery: baseSearchQuery, // Rename to avoid conflict
  allFilterItems,
  filteredData: baseFilteredData,
  stats,
  selectFilter,
  clearAllFilters,
  getFilterCount
} = useCascadingFilters(erpProducts, regularFilterConfigs, dependencies)

// Use our own searchQuery for the general search
const searchQuery = ref('')


// Apply search filtering on top of the base filtered data
const filteredData = computed(() => {
  let filtered = baseFilteredData.value

  // Apply search filtering if search query exists
  if (searchQuery.value && searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    
    const searchConfig = filterConfigs.find(config => config.key === 'generalSearch')
    
    if (searchConfig) {
      const beforeCount = filtered.length
      filtered = filtered.filter(item => {
        const searchText = searchConfig.getValue ? searchConfig.getValue(item) : ''
        const matches = searchText.toLowerCase().includes(query)
        return matches
      })
    }
  }

  return filtered
})

  // Create product-specific computed properties for backward compatibility
  const selectedProductType = computed({
    get: () => filterStates.value.productType,
    set: (value) => selectFilter('productType', value)
  })

  const selectedProductGroup = computed({
    get: () => filterStates.value.productGroup,
    set: (value) => selectFilter('productGroup', value)
  })

  const selectedBrand = computed({
    get: () => filterStates.value.brand,
    set: (value) => selectFilter('brand', value)
  })

  // Filter items with dependency awareness
  const productTypeItems = computed(() => allFilterItems.value.productType || [])
  const productGroupItems = computed(() => allFilterItems.value.productGroup || [])
  const brandItems = computed(() => allFilterItems.value.brand || [])
  
  // Filtered products
  const filteredProducts = computed(() => filteredData.value)
  
  // Enhanced product stats with additional properties expected by template
  const productStats = computed(() => {
    const baseStats = stats.value
    const total = baseStats.total
    const active = filteredData.value.filter(p => p.is_active).length
    const locked = filteredData.value.filter(p => p.is_locked).length
    const byGroup = baseStats.byFilter?.productGroup || {}
    
    return { 
      total, 
      active, 
      locked, 
      byGroup,
      byFilter: baseStats.byFilter 
    }
  })

  // Product-specific actions (these now handle cascading automatically)
  const selectProductType = (typeName) => {
    selectFilter('productType', typeName)
    // Cascading reset is handled automatically by the generic composable
  }

  const selectProductGroup = (groupName) => {
    const currentValue = filterStates.value.productGroup
    selectFilter('productGroup', groupName === currentValue ? 'all' : groupName)
    // Cascading reset is handled automatically by the generic composable
  }

  const selectBrand = (brandName) => {
    const currentValue = filterStates.value.brand
    selectFilter('brand', brandName === currentValue ? 'all' : brandName)
  }

  // Helper functions for backward compatibility
  const getProductTypeCount = (typeName) => getFilterCount('productType', typeName, erpProducts.value)
  const getProductGroupCount = (groupName) => getFilterCount('productGroup', groupName, erpProducts.value)
  const getBrandCount = (brandName) => getFilterCount('brand', brandName, erpProducts.value)

  return {
    // State
    selectedProductType,
    selectedProductGroup,
    selectedBrand,
    searchQuery,
    
    // Computed
    productTypeItems,
    productGroupItems,
    brandItems,
    filteredProducts,
    productStats,
    
    // Actions
    selectProductType,
    selectProductGroup,
    selectBrand,
    clearAllFilters,
    
    // Helper functions
    getProductTypeCount,
    getProductGroupCount,
    getBrandCount
  }
}
