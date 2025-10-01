import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export function useErpData() {
  // Reactive data
  const customers = ref([])
  const suppliers = ref([])
  const products = ref([])
  const ordersIn = ref([])
  const ordersOut = ref([])
  const orderInLines = ref([])
  const orderOutLines = ref([])
  const sites = ref([])
  const storages = ref([])
  const stocks = ref([])
  const statuses = ref([])
  const units = ref([])
  const productTypes = ref([])
  const eventHandlers = ref([])
  const stockHandlers = ref([])
  const samples = ref([])
  const analyses = ref([])
  const analyseProperties = ref([])
  const analyseLines = ref([])
  const productGroups = ref([])
  const brands = ref([])

  // Loading states
  const isLoading = ref(false)
  const error = ref(null)

  // API base URL - using public routes that work
  const apiBase = '/api'

  // Generic fetch function
  const fetchData = async (tableName) => {
    try {
      isLoading.value = true
      error.value = null
      
      
      const response = await axios.get(`${apiBase}/${tableName}`, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      })
      
      const data = Array.isArray(response.data) ? response.data : (response.data.data || [])
      
      
      return data
    } catch (err) {
      console.error(`useErpData: Error fetching ${tableName}:`, err)
      error.value = err.message
      return []
    } finally {
      isLoading.value = false
    }
  }

  // Fetch all ERP data
  const fetchAllErpData = async () => {
    try {
      isLoading.value = true
      
      // Fetch all ERP data from the database
      const [
        customersData,
        suppliersData,
        productsData,
        ordersInData,
        ordersOutData,
        orderInLinesData,
        orderOutLinesData,
        sitesData,
        storagesData,
        stocksData,
        statusesData,
        unitsData,
        productTypesData,
        eventHandlersData,
        stockHandlersData,
        samplesData,
        analysesData,
        analysePropertiesData,
        analyseLinesData,
        productGroupsData,
        brandsData
      ] = await Promise.all([
        fetchData('erp_customers'),
        fetchData('erp_suppliers'),
        fetchData('erp_products'),
        fetchData('erp_orders_in'),
        fetchData('erp_orders_out'),
        fetchData('erp_order_in_lines'),
        fetchData('erp_order_out_lines'),
        fetchData('erp_sites'),
        fetchData('erp_storages'),
        fetchData('erp_stocks'),
        fetchData('erp_status'),
        fetchData('erp_units'),
        fetchData('erp_product_types'),
        fetchData('erp_event_handlers'),
        fetchData('erp_stock_handlers'),
        fetchData('erp_samples'),
        fetchData('erp_analyses'),
        fetchData('erp_analyse_properties'),
        fetchData('erp_analyse_lines'),
        fetchData('erp_product_groups'),
        fetchData('erp_brands')
      ])
      
      // Assign the fetched data
      customers.value = customersData
      suppliers.value = suppliersData
      products.value = productsData
      ordersIn.value = ordersInData
      ordersOut.value = ordersOutData
      orderInLines.value = orderInLinesData
      orderOutLines.value = orderOutLinesData
      sites.value = sitesData
      storages.value = storagesData
      stocks.value = stocksData
      statuses.value = statusesData
      units.value = unitsData
      productTypes.value = productTypesData
      eventHandlers.value = eventHandlersData
      stockHandlers.value = stockHandlersData
      samples.value = samplesData
      analyses.value = analysesData
      analyseProperties.value = analysePropertiesData
      analyseLines.value = analyseLinesData
      productGroups.value = productGroupsData
      brands.value = brandsData
      


    } catch (err) {
      console.error('Error loading ERP data:', err)
      error.value = err.message
    } finally {
      isLoading.value = false
    }
  }

  // Individual fetch functions
  const fetchCustomers = async () => {
    const data = await fetchData('erp_customers')
    customers.value = data
    return data 
  }
  
  const fetchSuppliers = async () => {
    const data = await fetchData('erp_suppliers')
    suppliers.value = data
    return data 
  }
  
  const fetchProducts = async () => {
    const data = await fetchData('erp_products')
    // Transform the data to match frontend expectations
    const transformedData = data.map(product => ({
      ...product,
      sku: product.identifier,
      description: product.comment,
      category: product.productGroup?.name || 'Uncategorized',
      subcategory: product.productType?.name || 'Unknown',
      // Keep the original object structure for DataTable
      status: product.is_active ? 'active' : 'inactive'
    }))
    products.value = transformedData
    return transformedData 
  }
  
  const fetchOrdersIn = async () => {
    const data = await fetchData('orders-in')
    ordersIn.value = data
    return data 
  }
  
  const fetchOrdersOut = async () => {
    const data = await fetchData('orders-out')
    ordersOut.value = data
    return data 
  }
  
  const fetchOrderInLines = async () => {
    const data = await fetchData('order-in-lines')
    orderInLines.value = data
    return data 
  }
  
  const fetchOrderOutLines = async () => {
    const data = await fetchData('order-out-lines')
    orderOutLines.value = data
    return data 
  }
  
  const fetchSites = async () => {
    const data = await fetchData('sites')
    sites.value = data
    return data 
  }
  
  const fetchStorages = async () => {
    const data = await fetchData('storages')
    storages.value = data
    return data 
  }
  
  const fetchStocks = async () => {
    const data = await fetchData('stocks')
    stocks.value = data
    return data 
  }
  
  const fetchStatuses = async () => {
    const data = await fetchData('status')
    statuses.value = data
    return data 
  }
  
  const fetchUnits = async () => {
    const data = await fetchData('erp_units')
    units.value = data
    return data 
  }
  
  const fetchProductTypes = async () => {
    const data = await fetchData('erp_product_types')
    productTypes.value = data
    return data 
  }
  
  const fetchEventHandlers = async () => {
    const data = await fetchData('event-handlers')
    eventHandlers.value = data
    return data 
  }
  
  const fetchStockHandlers = async () => {
    const data = await fetchData('stock-handlers')
    stockHandlers.value = data
    return data 
  }
  
  const fetchSamples = async () => {
    const data = await fetchData('samples')
    samples.value = data
    return data 
  }
  
  const fetchAnalyses = async () => {
    const data = await fetchData('analyses')
    analyses.value = data
    return data 
  }
  
  const fetchAnalyseProperties = async () => {
    const data = await fetchData('analyse-properties')
    analyseProperties.value = data
    return data 
  }
  
  const fetchAnalyseLines = async () => {
    const data = await fetchData('analyse-lines')
    analyseLines.value = data
    return data 
  }

  const fetchProductGroups = async () => {
    const data = await fetchData('erp_product_groups')
    productGroups.value = data
    return data 
  }
  
  const fetchBrands = async () => {
    const data = await fetchData('erp_brands')
    brands.value = data
    return data
  }

  // Computed properties for lookups
  const customersById = computed(() => {
    const lookup = {}
    customers.value.forEach(customer => {
      lookup[customer.id] = customer
    })
    return lookup
  })

  const suppliersById = computed(() => {
    const lookup = {}
    suppliers.value.forEach(supplier => {
      lookup[supplier.id] = supplier
    })
    return lookup
  })

  const productsById = computed(() => {
    const lookup = {}
    products.value.forEach(product => {
      lookup[product.id] = product
    })
    return lookup
  })

  const sitesById = computed(() => {
    const lookup = {}
    sites.value.forEach(site => {
      lookup[site.id] = site
    })
    return lookup
  })

  const statusesById = computed(() => {
    const lookup = {}
    statuses.value.forEach(status => {
      lookup[status.id] = status
    })
    return lookup
  })

  const statusesByCode = computed(() => {
    const lookup = {}
    statuses.value.forEach(status => {
      lookup[status.code] = status
    })
    return lookup
  })

  const unitsById = computed(() => {
    const lookup = {}
    units.value.forEach(unit => {
      lookup[unit.id] = unit
    })
    return lookup
  })

  const productTypesById = computed(() => {
    const lookup = {}
    productTypes.value.forEach(type => {
      lookup[type.id] = type
    })
    return lookup
  })

  const productTypesByCode = computed(() => {
    const lookup = {}
    productTypes.value.forEach(type => {
      lookup[type.code] = type
    })
    return lookup
  })

  const productGroupsById = computed(() => {
    const lookup = {}
    productGroups.value.forEach(group => {
      lookup[group.id] = group
    })
    return lookup
  })

  const brandsById = computed(() => {
    const lookup = {}
    brands.value.forEach(brand => {
      lookup[brand.id] = brand
    })
    return lookup
  })

  // Helper functions
  const getCustomerName = (customerId) => {
    return customersById.value[customerId]?.name || 'Unknown Customer'
  }

  const getSupplierName = (supplierId) => {
    return suppliersById.value[supplierId]?.name || 'Unknown Supplier'
  }

  const getProductName = (productId) => {
    return productsById.value[productId]?.name || 'Unknown Product'
  }

  const getSiteName = (siteId) => {
    return sitesById.value[siteId]?.name || 'Unknown Site'
  }

  const getStatusName = (statusId) => {
    return statusesById.value[statusId]?.name || 'Unknown Status'
  }

  const getStatusColor = (statusId) => {
    return statusesById.value[statusId]?.color || 'gray'
  }

  const getUnitSymbol = (unitId) => {
    return unitsById.value[unitId]?.symbol || 'pcs'
  }

  const getProductTypeName = (typeId) => {
    return productTypesById.value[typeId]?.name || 'Unknown Type'
  }

  const getProductGroupName = (groupId) => {
    return productGroupsById.value[groupId]?.name || 'No Group'
  }

  const getProductGroupCode = (groupId) => {
    return productGroupsById.value[groupId]?.code || ''
  }

  const getBrandName = (brandId) => {
    return brandsById.value[brandId]?.name || 'No Brand'
  }

  const getBrandIdentifier = (brandId) => {
    return brandsById.value[brandId]?.identifier || ''
  }

  // Auto-fetch on mount
  onMounted(() => {
    fetchAllErpData()
  })

  return {
    // Data
    customers,
    suppliers,
    products,
    ordersIn,
    ordersOut,
    orderInLines,
    orderOutLines,
    sites,
    storages,
    stocks,
    statuses,
    units,
    productTypes,
    eventHandlers,
    stockHandlers,
    samples,
    analyses,
    analyseProperties,
    analyseLines,
    productGroups,
    brands,

    // Loading states
    isLoading,
    error,

    // Fetch functions
    fetchAllErpData,
    fetchCustomers,
    fetchSuppliers,
    fetchProducts,
    fetchOrdersIn,
    fetchOrdersOut,
    fetchOrderInLines,
    fetchOrderOutLines,
    fetchSites,
    fetchStorages,
    fetchStocks,
    fetchStatuses,
    fetchUnits,
    fetchProductTypes,
    fetchEventHandlers,
    fetchStockHandlers,
    fetchSamples,
    fetchAnalyses,
    fetchAnalyseProperties,
    fetchAnalyseLines,
    fetchProductGroups,
    fetchBrands,

    // Lookup objects
    customersById,
    suppliersById,
    productsById,
    sitesById,
    statusesById,
    statusesByCode,
    unitsById,
    productTypesById,
    productTypesByCode,
    productGroupsById,
    brandsById,

    // Helper functions
    getCustomerName,
    getSupplierName,
    getProductName,
    getSiteName,
    getStatusName,
    getStatusColor,
    getUnitSymbol,
    getProductTypeName,
    getProductGroupName,
    getProductGroupCode,
    getBrandName,
    getBrandIdentifier
  }
}
