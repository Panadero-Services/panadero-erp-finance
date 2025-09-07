import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'

export function useStockManagement() {
  const store = useInventoryStore()

  // State
  const isLoading = ref(false)
  const searchTerm = ref('')
  const selectedCategory = ref('all')
  const sortBy = ref('name')
  const sortOrder = ref('asc')

  // Computed
  const filteredItems = computed(() => {
    let items = store.stockItems

    // Filter by search term
    if (searchTerm.value) {
      items = items.filter(item => 
        item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        item.sku.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        item.description?.toLowerCase().includes(searchTerm.value.toLowerCase())
      )
    }

    // Filter by category
    if (selectedCategory.value !== 'all') {
      items = items.filter(item => item.category === selectedCategory.value)
    }

    // Sort items
    items.sort((a, b) => {
      let aValue = a[sortBy.value]
      let bValue = b[sortBy.value]

      if (typeof aValue === 'string') {
        aValue = aValue.toLowerCase()
        bValue = bValue.toLowerCase()
      }

      if (sortOrder.value === 'asc') {
        return aValue > bValue ? 1 : -1
      } else {
        return aValue < bValue ? 1 : -1
      }
    })

    return items
  })

  const categories = computed(() => {
    const cats = new Set(store.stockItems.map(item => item.category))
    return Array.from(cats).map(cat => ({ value: cat, label: cat }))
  })

  const lowStockItems = computed(() => 
    store.stockItems.filter(item => item.currentStock <= item.minStock)
  )

  const outOfStockItems = computed(() => 
    store.stockItems.filter(item => item.currentStock === 0)
  )

  const totalInventoryValue = computed(() => 
    store.stockItems.reduce((total, item) => 
      total + (item.currentStock * item.unitCost), 0
    )
  )

  // Actions
  const addStockItem = async (itemData) => {
    isLoading.value = true
    try {
      await store.addStockItem(itemData)
    } finally {
      isLoading.value = false
    }
  }

  const updateStockItem = async (itemId, updates) => {
    isLoading.value = true
    try {
      const index = store.stockItems.findIndex(item => item.id === itemId)
      if (index !== -1) {
        store.stockItems[index] = { ...store.stockItems[index], ...updates }
      }
    } finally {
      isLoading.value = false
    }
  }

  const deleteStockItem = async (itemId) => {
    isLoading.value = true
    try {
      const index = store.stockItems.findIndex(item => item.id === itemId)
      if (index !== -1) {
        store.stockItems.splice(index, 1)
      }
    } finally {
      isLoading.value = false
    }
  }

  const adjustStock = async (itemId, newQuantity, reason, reference) => {
    isLoading.value = true
    try {
      await store.updateStockLevel(itemId, newQuantity, reason, reference)
    } finally {
      isLoading.value = false
    }
  }

  const getStockHistory = (itemId) => {
    return store.stockMovements.filter(movement => movement.itemId === itemId)
  }

  const searchItems = (query) => {
    searchTerm.value = query
  }

  const filterByCategory = (category) => {
    selectedCategory.value = category
  }

  const sortItems = (field, order) => {
    sortBy.value = field
    sortOrder.value = order
  }

  return {
    // State
    isLoading,
    searchTerm,
    selectedCategory,
    sortBy,
    sortOrder,

    // Computed
    filteredItems,
    categories,
    lowStockItems,
    outOfStockItems,
    totalInventoryValue,

    // Actions
    addStockItem,
    updateStockItem,
    deleteStockItem,
    adjustStock,
    getStockHistory,
    searchItems,
    filterByCategory,
    sortItems
  }
}
