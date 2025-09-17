import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { products as seedProducts, productGroups as seedProductGroups, productCategories as seedProductCategories } from '../data/seedData.js'

export function useProducts() {
  const store = useInventoryStore()
  
  // Product master data - Initialize with seed data
  const products = ref([...seedProducts])
  const productGroups = ref([...seedProductGroups])
  const productCategories = ref([...seedProductCategories])

  // Computed properties
  const getProductsByGroup = computed(() => (groupId) => {
    return products.value.filter(product => 
      productGroups.value.find(g => g.id === groupId)?.name === product.group
    )
  })

  const getProductsByCategory = computed(() => (categoryId) => {
    return products.value.filter(product => 
      productCategories.value.find(c => c.id === categoryId)?.name === product.category
    )
  })

  const getProductsByType = computed(() => (type) => {
    return products.value.filter(product => product.type === type)
  })

  // Methods
  const addProduct = (productData) => {
    const newProduct = {
      id: Date.now(),
      ...productData,
      createdAt: new Date().toISOString().split('T')[0],
      updatedAt: new Date().toISOString().split('T')[0]
    }
    products.value.push(newProduct)
    return newProduct
  }

  const updateProduct = (id, updates) => {
    const index = products.value.findIndex(p => p.id === id)
    if (index !== -1) {
      products.value[index] = { ...products.value[index], ...updates, updatedAt: new Date().toISOString().split('T')[0] }
      return products.value[index]
    }
    return null
  }

  const deleteProduct = (id) => {
    const index = products.value.findIndex(p => p.id === id)
    if (index !== -1) {
      products.value.splice(index, 1)
      return true
    }
    return false
  }

  return {
    products,
    productGroups,
    productCategories,
    getProductsByGroup,
    getProductsByCategory,
    getProductsByType,
    addProduct,
    updateProduct,
    deleteProduct
  }
}
