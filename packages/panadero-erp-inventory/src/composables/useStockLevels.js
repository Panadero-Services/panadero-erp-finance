import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { stockLevels as seedStockLevels } from '../data/seedData.js'

export function useStockLevels() {
  const store = useInventoryStore()
  
  // Stock levels data - Initialize with seed data
  const stockLevels = ref([...seedStockLevels])

  // Computed properties
  const getStockByStatus = computed(() => (status) => {
    return stockLevels.value.filter(stock => stock.status === status)
  })

  const getLowStockItems = computed(() => {
    return stockLevels.value.filter(stock => stock.currentStock <= stock.minStock)
  })

  const getStockValuation = computed(() => {
    return stockLevels.value.reduce((total, stock) => {
      const product = store.stockItems.find(item => item.sku === stock.sku)
      return total + (stock.currentStock * (product?.unitCost || 0))
    }, 0)
  })

  // Methods
  const updateStockLevel = (id, newQuantity, reason) => {
    const index = stockLevels.value.findIndex(s => s.id === id)
    if (index !== -1) {
      const oldQuantity = stockLevels.value[index].currentStock
      stockLevels.value[index].currentStock = newQuantity
      stockLevels.value[index].lastUpdated = new Date().toISOString()
      
      // Update status based on new quantity
      if (newQuantity <= 0) {
        stockLevels.value[index].status = 'out'
      } else if (newQuantity <= stockLevels.value[index].minStock) {
        stockLevels.value[index].status = 'low'
      } else {
        stockLevels.value[index].status = 'normal'
      }

      // Record the movement
      store.addStockMovement({
        stockItemId: id,
        movementType: newQuantity > oldQuantity ? 'in' : 'out',
        quantity: Math.abs(newQuantity - oldQuantity),
        reason: reason,
        reference: `Stock adjustment - ${reason}`,
        movementDate: new Date().toISOString()
      })

      return stockLevels.value[index]
    }
    return null
  }

  const adjustStock = (id, adjustment, reason) => {
    const stock = stockLevels.value.find(s => s.id === id)
    if (stock) {
      const newQuantity = stock.currentStock + adjustment
      return updateStockLevel(id, newQuantity, reason)
    }
    return null
  }

  return {
    stockLevels,
    getStockByStatus,
    getLowStockItems,
    getStockValuation,
    updateStockLevel,
    adjustStock
  }
}
