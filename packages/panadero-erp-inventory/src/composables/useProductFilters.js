// v1.0.0 - Product filters composable for panadero-erp-inventory
import { ref, computed } from 'vue'

export function useProductFilters(erpProducts, productTypes, productGroups, brands) {
  // Filter states
  const selectedProductType = ref('all')
  const selectedProductGroup = ref('all')
  const selectedBrand = ref('all')
  const searchQuery = ref('')

  // Helper function to get product type icon
  const getProductTypeIcon = (typeName) => {
    const icons = {
      'normal': 'fas fa-box',
      'bulk': 'fas fa-weight-hanging',
      'liquid': 'fas fa-tint',
      'service': 'fas fa-cogs'
    }
    return icons[typeName] || 'fas fa-cube'
  }

  // Helper function to get product type count
  const getProductTypeCount = (typeName) => {
    if (typeName === 'all') return erpProducts.value.length
    return erpProducts.value.filter(product => product.product_type?.name === typeName).length
  }

  // Helper function to get product group count
  const getProductGroupCount = (groupName) => {
    if (groupName === 'all') return erpProducts.value.length
    return erpProducts.value.filter(product => product.product_group?.name === groupName).length
  }

  // Helper function to get brand count
  const getBrandCount = (brandName) => {
    if (brandName === 'all') return erpProducts.value.length
    return erpProducts.value.filter(product => product.brand?.name === brandName).length
  }

  // Product Type Filter Items
  const productTypeItems = computed(() => {
    const items = productTypes.value.map(type => ({
      value: type.name,
      label: type.name,
      count: getProductTypeCount(type.name),
      icon: getProductTypeIcon(type.name)
    }))
    
    return [
      { value: 'all', label: 'All Types', count: erpProducts.value.length, icon: 'fas fa-th-large' },
      ...items
    ]
  })

  // Product Group Filter Items
  const productGroupItems = computed(() => {
    const groups = [...new Set(erpProducts.value.map(p => p.product_group?.name || 'No Group'))]
    const items = groups.map(group => ({
      value: group,
      label: group,
      count: getProductGroupCount(group),
      icon: 'fas fa-cube'
    }))
    
    return [
      { value: 'all', label: 'All Groups', count: erpProducts.value.length, icon: 'fas fa-th-large' },
      ...items
    ]
  })

  // Brand Filter Items
  const brandItems = computed(() => {
    const brandNames = [...new Set(erpProducts.value.map(p => p.brand?.name || 'No Brand'))]
    const items = brandNames.map(brand => ({
      value: brand,
      label: brand,
      count: getBrandCount(brand),
      icon: 'fas fa-tag'
    }))
    
    return [
      { value: 'all', label: 'All Brands', count: erpProducts.value.length, icon: 'fas fa-tags' },
      ...items
    ]
  })

  // Filtered Products based on all active filters
  const filteredProducts = computed(() => {
    let filtered = erpProducts.value

    // Product type filter
    if (selectedProductType.value !== 'all') {
      filtered = filtered.filter(product => product.product_type?.name === selectedProductType.value)
    }

    // Product group filter
    if (selectedProductGroup.value !== 'all') {
      filtered = filtered.filter(product => product.product_group?.name === selectedProductGroup.value)
    }

    // Brand filter
    if (selectedBrand.value !== 'all') {
      filtered = filtered.filter(product => product.brand?.name === selectedBrand.value)
    }

    // Search filter
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(product => 
        product.name.toLowerCase().includes(query) ||
        product.identifier.toLowerCase().includes(query) ||
        (product.comment || '').toLowerCase().includes(query) ||
        (product.product_group?.name || '').toLowerCase().includes(query)
      )
    }

    return filtered
  })

  // Filter actions
  const selectProductType = (typeName) => {
    selectedProductType.value = typeName
    selectedProductGroup.value = 'all' // Reset other filters
    selectedBrand.value = 'all'
  }

  const selectProductGroup = (groupName) => {
    selectedProductGroup.value = groupName === selectedProductGroup.value ? 'all' : groupName
  }

  const selectBrand = (brandName) => {
    selectedBrand.value = brandName === selectedBrand.value ? 'all' : brandName
  }

  const clearAllFilters = () => {
    selectedProductType.value = 'all'
    selectedProductGroup.value = 'all'
    selectedBrand.value = 'all'
    searchQuery.value = ''
  }

  // Statistics
  const productStats = computed(() => {
    const total = filteredProducts.value.length
    const active = filteredProducts.value.filter(p => p.is_active).length
    const locked = filteredProducts.value.filter(p => p.is_locked).length
    const byGroup = {}
    
    filteredProducts.value.forEach(product => {
      const groupName = product.product_group?.name || 'No Group'
      byGroup[groupName] = (byGroup[groupName] || 0) + 1
    })
    
    return { total, active, locked, byGroup }
  })

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
