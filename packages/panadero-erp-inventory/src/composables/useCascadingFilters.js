// v1.0.0 - Generic cascading filters composable for any entity
import { ref, computed } from 'vue'

export function useCascadingFilters(data, filterConfigs, dependencies = []) {
  // Filter states - dynamic based on config
  const filterStates = ref({})
  const searchQuery = ref('')

  // Initialize filter states
  filterConfigs.forEach(config => {
    filterStates.value[config.key] = 'all'
  })

  // Helper function to get count for a specific filter value
  const getFilterCount = (filterKey, filterValue, dataArray) => {
    if (filterValue === 'all') return dataArray.length
    
    const config = filterConfigs.find(c => c.key === filterKey)
    if (!config) return 0
    
    return dataArray.filter(item => {
      const value = config.getValue ? config.getValue(item) : item[config.field]
      return value === filterValue
    }).length
  }

  // Get filtered data based on current filter states (for dependency calculations)
  const getCurrentFilteredData = () => {
    let filtered = data.value

    // Apply all current filters
    filterConfigs.forEach(config => {
      const filterValue = filterStates.value[config.key]
      if (filterValue !== 'all') {
        filtered = filtered.filter(item => {
          const value = config.getValue ? config.getValue(item) : item[config.field]
          return value === filterValue
        })
      }
    })

    return filtered
  }

  // Generate filter items for each filter config with dependency filtering
  const generateFilterItems = (config, dataArray) => {
    // Check if this filter has dependencies
    const dependency = dependencies.find(dep => dep.filterKey === config.key)
    
    let sourceData = dataArray
    
    // If this filter has dependencies, filter the source data first
    if (dependency) {
      // Apply parent filter states to get available options
      let filtered = data.value
      
      // Apply all parent filters (filters this one depends on)
      dependency.dependsOn.forEach(parentKey => {
        const parentValue = filterStates.value[parentKey]
        if (parentValue !== 'all') {
          const parentConfig = filterConfigs.find(c => c.key === parentKey)
          if (parentConfig) {
            filtered = filtered.filter(item => {
              const value = parentConfig.getValue ? parentConfig.getValue(item) : item[parentConfig.field]
              return value === parentValue
            })
          }
        }
      })
      
      sourceData = filtered
    }

    // Get unique values from the filtered source data
    const uniqueValues = [...new Set(sourceData.map(item => {
      const value = config.getValue ? config.getValue(item) : item[config.field]
      return value || 'No ' + config.label
    }))]
    
    const items = uniqueValues.map(value => ({
      value,
      label: value,
      count: getFilterCount(config.key, value, sourceData),
      icon: config.getIcon ? config.getIcon(value) : 'fas fa-cube',
      // Add disabled state if this option is not available in current context
      disabled: dependency && sourceData.length === 0
    }))
    
    return [
      { 
        value: 'all', 
        label: 'All ' + config.pluralLabel, 
        count: sourceData.length, 
        icon: 'fas fa-th-large',
        disabled: false
      },
      ...items
    ]
  }

  // Generate all filter items with dependency awareness
  const allFilterItems = computed(() => {
    const result = {}
    filterConfigs.forEach(config => {
      result[config.key] = generateFilterItems(config, data.value)
    })
    return result
  })

  // Filtered data based on all active filters
  const filteredData = computed(() => {
    let filtered = data.value || []

    // Apply each filter
    filterConfigs.forEach(config => {
      const filterValue = filterStates.value[config.key]
      if (filterValue !== 'all') {
        filtered = filtered.filter(item => {
          const value = config.getValue ? config.getValue(item) : item[config.field]
          return value === filterValue
        })
      }
    })

    // Apply search filter
    if (searchQuery.value && filterConfigs.some(c => c.searchable)) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(item => {
        return filterConfigs.some(config => {
          if (!config.searchable) return false
          const value = config.getValue ? config.getValue(item) : item[config.field]
          return (value || '').toLowerCase().includes(query)
        })
      })
    }

    return filtered
  })

  // Enhanced filter actions with cascading reset
  const selectFilter = (filterKey, value) => {
    // Set the filter value
    filterStates.value[filterKey] = value
    
    // Find dependencies for this filter
    const dependency = dependencies.find(dep => dep.filterKey === filterKey)
    
    if (dependency) {
      // Reset all dependent filters when parent changes
      dependency.resets.forEach(dependentKey => {
        filterStates.value[dependentKey] = 'all'
      })
    }
  }

  const clearAllFilters = () => {
    filterConfigs.forEach(config => {
      filterStates.value[config.key] = 'all'
    })
    searchQuery.value = ''
  }

  // Statistics
  const stats = computed(() => {
    const total = filteredData.value.length
    const byFilter = {}
    
    filterConfigs.forEach(config => {
      byFilter[config.key] = {}
      filteredData.value.forEach(item => {
        const value = config.getValue ? config.getValue(item) : item[config.field]
        const key = value || 'No ' + config.label
        byFilter[config.key][key] = (byFilter[config.key][key] || 0) + 1
      })
    })
    
    return { total, byFilter }
  })

  return {
    // State
    filterStates,
    searchQuery,
    
    // Computed
    allFilterItems,
    filteredData,
    stats,
    
    // Actions
    selectFilter,
    clearAllFilters,
    
    // Helper
    getFilterCount
  }
}
