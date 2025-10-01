// v1.0.0 - Product-specific filters using generic composable
import { computed } from 'vue'
import { useGenericFilters } from './useGenericFilters.js'

export function useProductFiltersGeneric(erpProducts, productTypes, productGroups, brands) {
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
    }
  ]

  // Use the generic filters composable
  const {
    filterStates,
    searchQuery,
    allFilterItems,
    filteredData,
    stats,
    selectFilter,
    clearAllFilters,
    getFilterCount
  } = useGenericFilters(erpProducts, filterConfigs)

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

  const productTypeItems = computed(() => allFilterItems.value.productType || [])
  const productGroupItems = computed(() => allFilterItems.value.productGroup || [])
  const brandItems = computed(() => allFilterItems.value.brand || [])
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

  // Product-specific actions
  const selectProductType = (typeName) => {
    selectFilter('productType', typeName)
    selectFilter('productGroup', 'all') // Reset other filters
    selectFilter('brand', 'all')
  }

  const selectProductGroup = (groupName) => {
    const currentValue = filterStates.value.productGroup
    selectFilter('productGroup', groupName === currentValue ? 'all' : groupName)
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
