import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { incomingOrders as seedIncomingOrders, filterOptions as seedFilterOptions } from '../data/seedData.js'

export function useIncoming() {
  const store = useInventoryStore()
  
  // Incoming stock orders - Initialize with seed data
  const incomingOrders = ref([...seedIncomingOrders])
  const filterOptions = ref([...seedFilterOptions])

  // State
  const selectedFilter = ref('thisWeek')
  const customDateRange = ref({
    start: null,
    end: null
  })

  // Computed properties
  const filteredOrders = computed(() => {
    let filtered = [...incomingOrders.value]
    
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

  const getTotalIncomingValue = computed(() => {
    return filteredOrders.value.reduce((total, order) => total + order.totalValue, 0)
  })

  // Methods
  const updateOrderStatus = (id, newStatus) => {
    const index = incomingOrders.value.findIndex(order => order.id === id)
    if (index !== -1) {
      incomingOrders.value[index].status = newStatus
      return incomingOrders.value[index]
    }
    return null
  }

  const receiveItem = (orderId, itemSku, quantity) => {
    const order = incomingOrders.value.find(o => o.id === orderId)
    if (order) {
      const item = order.items.find(i => i.sku === itemSku)
      if (item) {
        item.received += quantity
        
        // Check if all items are received
        const allReceived = order.items.every(i => i.received >= i.quantity)
        if (allReceived) {
          order.status = 'completed'
        } else if (item.received > 0) {
          order.status = 'unloading'
        }
        
        return order
      }
    }
    return null
  }

  return {
    incomingOrders,
    filterOptions,
    selectedFilter,
    customDateRange,
    filteredOrders,
    getOrdersByStatus,
    getTotalIncomingValue,
    updateOrderStatus,
    receiveItem
  }
}
