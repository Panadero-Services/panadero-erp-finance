import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { closeoutPeriods as seedCloseoutPeriods } from '../data/seedData.js'

export function useCloseout() {
  const store = useInventoryStore()
  
  // Closeout periods - Initialize with seed data
  const closeoutPeriods = ref([...seedCloseoutPeriods])

  // State
  const selectedPeriod = ref('monthly')
  const selectedDateRange = ref({
    start: null,
    end: null
  })

  // Computed properties
  const getCloseoutsByPeriod = computed(() => (period) => {
    return closeoutPeriods.value.filter(closeout => closeout.type === period)
  })

  const getCloseoutsByStatus = computed(() => (status) => {
    return closeoutPeriods.value.filter(closeout => closeout.status === status)
  })

  const getCurrentPeriodCloseout = computed(() => {
    const currentDate = new Date()
    const currentMonth = currentDate.toISOString().slice(0, 7) // YYYY-MM format
    
    return closeoutPeriods.value.find(closeout => 
      closeout.period === currentMonth && closeout.type === 'monthly'
    )
  })

  const getCloseoutSummary = computed(() => {
    const totalCloseouts = closeoutPeriods.value.length
    const completedCloseouts = closeoutPeriods.value.filter(c => c.status === 'completed').length
    const inProgressCloseouts = closeoutPeriods.value.filter(c => c.status === 'in_progress').length
    const draftCloseouts = closeoutPeriods.value.filter(c => c.status === 'draft').length

    return {
      totalCloseouts,
      completedCloseouts,
      inProgressCloseouts,
      draftCloseouts,
      completionRate: totalCloseouts > 0 ? (completedCloseouts / totalCloseouts) * 100 : 0
    }
  })

  // Methods
  const createCloseout = (periodData) => {
    const newCloseout = {
      id: Date.now(),
      ...periodData,
      status: 'draft',
      products: [],
      totalStartValue: 0,
      totalIncomingValue: 0,
      totalOutgoingValue: 0,
      totalAdjustmentsValue: 0,
      totalEndValue: 0,
      totalVariance: 0,
      createdAt: new Date().toISOString(),
      completedAt: null
    }
    
    closeoutPeriods.value.push(newCloseout)
    return newCloseout
  }

  const updateCloseout = (id, updates) => {
    const index = closeoutPeriods.value.findIndex(c => c.id === id)
    if (index !== -1) {
      closeoutPeriods.value[index] = { ...closeoutPeriods.value[index], ...updates }
      return closeoutPeriods.value[index]
    }
    return null
  }

  const completeCloseout = (id) => {
    const closeout = closeoutPeriods.value.find(c => c.id === id)
    if (closeout) {
      // Calculate totals
      closeout.totalStartValue = closeout.products.reduce((sum, product) => sum + (product.startStock * getProductCost(product.sku)), 0)
      closeout.totalIncomingValue = closeout.products.reduce((sum, product) => sum + (product.incoming * getProductCost(product.sku)), 0)
      closeout.totalOutgoingValue = closeout.products.reduce((sum, product) => sum + (product.outgoing * getProductCost(product.sku)), 0)
      closeout.totalAdjustmentsValue = closeout.products.reduce((sum, product) => sum + (product.adjustments * getProductCost(product.sku)), 0)
      closeout.totalEndValue = closeout.products.reduce((sum, product) => sum + (product.endStock * getProductCost(product.sku)), 0)
      closeout.totalVariance = closeout.totalEndValue - (closeout.totalStartValue + closeout.totalIncomingValue - closeout.totalOutgoingValue + closeout.totalAdjustmentsValue)
      
      closeout.status = 'completed'
      closeout.completedAt = new Date().toISOString()
      
      return closeout
    }
    return null
  }

  const getProductCost = (sku) => {
    const product = store.stockItems.find(item => item.sku === sku)
    return product ? product.unitCost : 0
  }

  return {
    closeoutPeriods,
    selectedPeriod,
    selectedDateRange,
    getCloseoutsByPeriod,
    getCloseoutsByStatus,
    getCurrentPeriodCloseout,
    getCloseoutSummary,
    createCloseout,
    updateCloseout,
    completeCloseout
  }
}
