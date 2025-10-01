// v1.0.0 - Generic filters composable for any entity
import { ref, computed } from 'vue'

export function useGenericFilters(data, filterConfigs) {
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

  // Generate filter items for each filter config
  const generateFilterItems = (config, dataArray) => {
    const uniqueValues = [...new Set(dataArray.map(item => {
      const value = config.getValue ? config.getValue(item) : item[config.field]
      return value || 'No ' + config.label
    }))]
    
    const items = uniqueValues.map(value => ({
      value,
      label: value,
      count: getFilterCount(config.key, value, dataArray),
      icon: config.getIcon ? config.getIcon(value) : 'fas fa-cube'
    }))
    
    return [
      { 
        value: 'all', 
        label: 'All ' + config.pluralLabel, 
        count: dataArray.length, 
        icon: 'fas fa-th-large' 
      },
      ...items
    ]
  }

  // Generate all filter items
  const allFilterItems = computed(() => {
    const result = {}
    filterConfigs.forEach(config => {
      result[config.key] = generateFilterItems(config, data.value)
    })
    return result
  })

  // Filtered data based on all active filters
  const filteredData = computed(() => {
    let filtered = data.value

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

  // Filter actions
  const selectFilter = (filterKey, value) => {
    filterStates.value[filterKey] = value
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
