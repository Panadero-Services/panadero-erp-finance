// v1.0.0 - Customer filters using generic composable
import { computed } from 'vue'
import { useGenericFilters } from './useGenericFilters.js'

export function useCustomerFilters(customers) {
  // Define filter configurations for customers
  const filterConfigs = [
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
          'inactive': 'fas fa-pause-circle',
          'pending': 'fas fa-clock',
          'suspended': 'fas fa-ban'
        }
        return icons[status] || 'fas fa-user'
      }
    },
    {
      key: 'country',
      field: 'country',
      label: 'Country',
      pluralLabel: 'Countries',
      searchable: true,
      getValue: (customer) => customer.country,
      getIcon: () => 'fas fa-flag'
    },
    {
      key: 'customerType',
      field: 'customer_type',
      label: 'Type',
      pluralLabel: 'Types',
      searchable: true,
      getValue: (customer) => customer.customer_type,
      getIcon: (type) => {
        const icons = {
          'individual': 'fas fa-user',
          'business': 'fas fa-building',
          'wholesale': 'fas fa-truck',
          'retail': 'fas fa-store'
        }
        return icons[type] || 'fas fa-user'
      }
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
    clearAllFilters
  } = useGenericFilters(customers, filterConfigs)

  // Create customer-specific computed properties
  const selectedStatus = computed({
    get: () => filterStates.value.status,
    set: (value) => selectFilter('status', value)
  })

  const selectedCountry = computed({
    get: () => filterStates.value.country,
    set: (value) => selectFilter('country', value)
  })

  const selectedCustomerType = computed({
    get: () => filterStates.value.customerType,
    set: (value) => selectFilter('customerType', value)
  })

  const statusItems = computed(() => allFilterItems.value.status || [])
  const countryItems = computed(() => allFilterItems.value.country || [])
  const customerTypeItems = computed(() => allFilterItems.value.customerType || [])
  const filteredCustomers = computed(() => filteredData.value)
  const customerStats = computed(() => stats.value)

  return {
    // State
    selectedStatus,
    selectedCountry,
    selectedCustomerType,
    searchQuery,
    
    // Computed
    statusItems,
    countryItems,
    customerTypeItems,
    filteredCustomers,
    customerStats,
    
    // Actions
    selectFilter,
    clearAllFilters
  }
}
