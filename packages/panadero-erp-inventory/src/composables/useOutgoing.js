import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { outgoingOrders as seedOutgoingOrders, filterOptions as seedFilterOptions } from '../data/seedData.js'

export function useOutgoing() {
  const store = useInventoryStore()
  
  // Outgoing stock orders - Initialize with seed data
  const outgoingOrders = ref([...seedOutgoingOrders])
  const filterOptions = ref([...seedFilterOptions])

  // State
  const selectedFilter = ref('thisWeek')
  const customDateRange = ref({
    start: null,
    end: null
  })

  // Computed properties
  const filteredOrders = computed(() => {
    let filtered = [...outgoingOrders.value]
    
    switch (selectedFilter.value) {
      case 'today':
        const today = new Date().toISOString().split('T')[0]
        filtered = filtered.filter(order => order.expectedDate === today)
        break
      case 'thisWeek':
        const weekStart = new Date()
        weekStart.setDate(weekStart.getDate() - weekStart.getDay())
        const weekEnd = new Date(weekStart)
        weekEnd.setDate(weekEnd.getDate() + 6)
        filtered = filtered.filter(order => {
          const orderDate = new Date(order.expectedDate)
          return orderDate >= weekStart && orderDate <= weekEnd
        })
        break
      case 'thisMonth':
        const monthStart = new Date()
        monthStart.setDate(1)
        const monthEnd = new Date(monthStart)
        monthEnd.setMonth(monthEnd.getMonth() + 1)
        monthEnd.setDate(0)
        filtered = filtered.filter(order => {
          const orderDate = new Date(order.expectedDate)
          return orderDate >= monthStart && orderDate <= monthEnd
        })
        break
      case 'custom':
        if (customDateRange.value.start && customDateRange.value.end) {
          filtered = filtered.filter(order => {
            const orderDate = new Date(order.expectedDate)
            return orderDate >= new Date(customDateRange.value.start) && 
                   orderDate <= new Date(customDateRange.value.end)
          })
        }
        break
    }
    
    return filtered
  })

  const getOrdersByStatus = computed(() => (status) => {
    return filteredOrders.value.filter(order => order.status === status)
  })

  const getTotalOutgoingValue = computed(() => {
    return filteredOrders.value.reduce((total, order) => total + order.totalValue, 0)
  })

  // Methods
  const updateOrderStatus = (id, newStatus) => {
    const index = outgoingOrders.value.findIndex(order => order.id === id)
    if (index !== -1) {
      outgoingOrders.value[index].status = newStatus
      return outgoingOrders.value[index]
    }
    return null
  }

  const shipItem = (orderId, itemSku, quantity) => {
    const order = outgoingOrders.value.find(o => o.id === orderId)
    if (order) {
      const item = order.items.find(i => i.sku === itemSku)
      if (item) {
        item.shipped += quantity
        
        // Check if all items are shipped
        const allShipped = order.items.every(i => i.shipped >= i.quantity)
        if (allShipped) {
          order.status = 'completed'
        } else if (item.shipped > 0) {
          order.status = 'shipping'
        }
        
        return order
      }
    }
    return null
  }

  return {
    outgoingOrders,
    filterOptions,
    selectedFilter,
    customDateRange,
    filteredOrders,
    getOrdersByStatus,
    getTotalOutgoingValue,
    updateOrderStatus,
    shipItem
  }
}
