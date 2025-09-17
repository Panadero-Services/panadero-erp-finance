import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { vendors as seedVendors, customers as seedCustomers, forwarders as seedForwarders } from '../data/seedData.js'

export function useVendors() {
  const store = useInventoryStore()
  
  // Partners data - Initialize with seed data
  const vendors = ref([...seedVendors])
  const customers = ref([...seedCustomers])
  const forwarders = ref([...seedForwarders])


  // Computed properties
  const getAllPartners = computed(() => {
    return [...vendors.value, ...customers.value, ...forwarders.value]
  })

  const getPartnersByType = computed(() => (type) => {
    return getAllPartners.value.filter(partner => partner.type === type)
  })

  const getActivePartners = computed(() => {
    return getAllPartners.value.filter(partner => partner.status === 'active')
  })

  const getPartnerMetrics = computed(() => {
    const totalVendors = vendors.value.length
    const totalCustomers = customers.value.length
    const totalForwarders = forwarders.value.length
    const activeVendors = vendors.value.filter(v => v.status === 'active').length
    const activeCustomers = customers.value.filter(c => c.status === 'active').length
    const activeForwarders = forwarders.value.filter(f => f.status === 'active').length

    return {
      totalVendors,
      totalCustomers,
      totalForwarders,
      activeVendors,
      activeCustomers,
      activeForwarders,
      totalPartners: totalVendors + totalCustomers + totalForwarders,
      activePartners: activeVendors + activeCustomers + activeForwarders
    }
  })

  // Methods
  const addPartner = (partnerData) => {
    const newPartner = {
      id: Date.now(),
      ...partnerData,
      createdAt: new Date().toISOString().split('T')[0],
      updatedAt: new Date().toISOString().split('T')[0]
    }
    
    switch (partnerData.type) {
      case 'vendor':
        vendors.value.push(newPartner)
        break
      case 'customer':
        customers.value.push(newPartner)
        break
      case 'forwarder':
        forwarders.value.push(newPartner)
        break
    }
    
    return newPartner
  }

  const updatePartner = (id, updates) => {
    let partner = null
    
    // Try to find in vendors
    let index = vendors.value.findIndex(p => p.id === id)
    if (index !== -1) {
      vendors.value[index] = { ...vendors.value[index], ...updates, updatedAt: new Date().toISOString().split('T')[0] }
      partner = vendors.value[index]
    } else {
      // Try to find in customers
      index = customers.value.findIndex(p => p.id === id)
      if (index !== -1) {
        customers.value[index] = { ...customers.value[index], ...updates, updatedAt: new Date().toISOString().split('T')[0] }
        partner = customers.value[index]
      } else {
        // Try to find in forwarders
        index = forwarders.value.findIndex(p => p.id === id)
        if (index !== -1) {
          forwarders.value[index] = { ...forwarders.value[index], ...updates, updatedAt: new Date().toISOString().split('T')[0] }
          partner = forwarders.value[index]
        }
      }
    }
    
    return partner
  }

  const deletePartner = (id) => {
    // Try to find and delete in each array
    let index = vendors.value.findIndex(p => p.id === id)
    if (index !== -1) {
      vendors.value.splice(index, 1)
      return true
    }
    
    index = customers.value.findIndex(p => p.id === id)
    if (index !== -1) {
      customers.value.splice(index, 1)
      return true
    }
    
    index = forwarders.value.findIndex(p => p.id === id)
    if (index !== -1) {
      forwarders.value.splice(index, 1)
      return true
    }
    
    return false
  }

  return {
    vendors,
    customers,
    forwarders,
    getAllPartners,
    getPartnersByType,
    getActivePartners,
    getPartnerMetrics,
    addPartner,
    updatePartner,
    deletePartner
  }
}
