<script setup>
import { ref, computed, onMounted } from 'vue'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import { loadFullConfig, loadVersionsData, getColorClasses } from '../../utils/configLoader.js'
import { useFinanceStore } from '../../stores/financeStore';

const store = useFinanceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()
const config = ref(null)
const versions = ref(null)
const loading = ref(true)
const activeInfo = ref(1)

onMounted(async () => {
  try {
    // Load current package config (finance)
    const [configData, versionsData] = await Promise.all([
      loadFullConfig('./'),           // Load from current package
      loadVersionsData('./')          // Load from current package
    ])
    
    config.value = configData
    versions.value = versionsData
    console.debug('Loaded config:', configData) // Debug debug
  } catch (error) {
    console.error('Failed to load configuration:', error)
    // Component can still work with fallback/hardcoded data
  } finally {
    loading.value = false
  }
})

// Dynamic package tables computed from store and config
const packageTables = computed(() => {
  if (!config.value || !config.value.tables) return []
  
  return config.value.tables.map(table => ({
    name: table.name,
    displayName: table.displayName,
    records: getRecordCount(table),
    lastUpdated: getLastUpdated(getTableData(table), table.timestampFields),
    route: table.route
  }))
})

// Helper function to get record count
function getRecordCount(tableConfig) {
  const data = store[tableConfig.storeProperty]
  if (!data) return 0
  return tableConfig.isObject ? Object.keys(data).length : data.length
}

// Helper function to get table data
function getTableData(tableConfig) {
  return store[tableConfig.storeProperty]
}

// Helper function to calculate last updated time
function getLastUpdated(dataArray, timestampFields) {
  if (!dataArray || (Array.isArray(dataArray) && dataArray.length === 0)) {
    return 'never'
  }
  
  if (!timestampFields || timestampFields.length === 0) return 'never'
  
  const timestamps = []
  const items = Array.isArray(dataArray) ? dataArray : Object.values(dataArray)
  
  items.forEach(item => {
    for (const field of timestampFields) {
      if (item[field]) {
        timestamps.push(new Date(item[field]))
        break // Use first available timestamp field
      }
    }
  })
  
  if (timestamps.length === 0) return 'never'
  
  const mostRecent = new Date(Math.max(...timestamps))
  const now = new Date()
  const diffDays = Math.floor((now - mostRecent) / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) return 'today'
  if (diffDays === 1) return '1 day ago'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`
  return `${Math.floor(diffDays / 365)} years ago`
}

// Use the config data
const moduleDescription = computed(() => {
  if (!config.value) return { title: 'Loading...', descriptions: [] }
  
  return {
    title: `${config.value.packageInfo.displayName} v${versions.value?.current || '1.0.0'}`,
    description: config.value.packageInfo.description,
    descriptions: config.value.descriptions || []
  }
})

// Shared entities from config
const sharedEntities = computed(() => {
  return config.value?.sharedEntities || []
})

// Version updates
const versionUpdates = computed(() => {
  return versions.value?.releases?.slice(0, 5) || [] // Show latest 5
})

// Navigation function
function navigateToEntity(entityType) {
  if (!config.value) return
  
  const table = config.value.tables?.find(t => t.name === entityType)
  const entity = config.value.sharedEntities?.find(e => e.id === entityType)
  
  const route = table?.route || entity?.path
  if (!route) return
  
  if (route.startsWith('#')) {
    // Handle hash-based navigation
    const tabId = route.substring(1)
    const eventName = config.value.navigation?.eventName || 'switchFinanceTab'
    window.dispatchEvent(new CustomEvent(eventName, { 
      detail: { tabId } 
    }))
  } else {
    // External navigation
    if (window.Inertia) {
      window.Inertia.visit(route)
    } else {
      window.location.href = route
    }
  }
}

// Toggle info box
function toggleInfoBox() {
  activeInfo.value = !activeInfo.value
}

// Get color classes for entities
function getEntityColorClasses(color) {
  if (!config.value?.colorScheme) return ''
  return getColorClasses(color, config.value)
}
</script>

<template>
  <div v-if="loading" class="info-section">
    <div class="text-center p-8">
      <div class="text-gray-600 dark:text-gray-400">Loading configuration...</div>
    </div>
  </div>
  
  <div v-else class="info-section">
    <!-- Info Boxes Grid -->
    <div class="grid grid-cols-12 gap-6 mt-0 text-black dark:text-white">
      <!-- Module Description Info Box -->
      <div @click="toggleInfoBox" class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 cursor-pointer">
        <div class="mb-4">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">ERP Module</h3>
          <p :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Descriptions of the ERP Package</p>
        </div>
        
        <div class="space-y-3">
          <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <div :style="{ fontSize: `${fontSizes.base + 2}px` }" class="mt-2 text-blue-700 dark:text-blue-400">{{ moduleDescription.title }}</div>
          </div>
        </div>
      </div>

      <!-- Version Updates Info Box -->
      <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <div class="mb-4">
          <p :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Version Updates</p>
          <p :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Recent updates and new features</p>
        </div>
        
        <div class="overflow-scroll">
          <div class="gap-3 flex">
            <div v-for="update in versionUpdates" :key="update.version" 
                 class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
              <div class="flex items-center justify-between w-48">
                <div>
                  <div :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-medium text-gray-900 dark:text-white">{{ update.version }}</div>
                  <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">{{ update.date }}</div>
                </div>
                <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-400 dark:text-gray-500">{{ update.features?.length || 0 }} features</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- What's in the Package? Info Box -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">What's in the Package?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Module description -->
              <div v-for="desc in moduleDescription.descriptions" :key="desc.id" class="mb-6">
                <p :style="{ fontSize: `${fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ desc.title }} <span :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ desc.count }} Total</span> 
                </p>

                <ul :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
                  <li v-for="item in desc.list" :key="item">{{ item }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- What's New? Info Box -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">What's New?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Version updates -->
              <div v-for="update in versionUpdates" :key="update.version" class="mb-6">
                <p :style="{ fontSize: `${fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ update.version }} <span :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ update.date }}</span> 
                </p>

                <ul :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
                  <li v-for="feature in (update.features || [])" :key="feature">{{ feature }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dependencies Info box -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Dependencies</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Package Tables -->
              <p :style="{ fontSize: `${fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                Package Tables <span :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ packageTables.length }} Tables</span>
              </p>

              <div class="space-y-2 mb-4">
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                  <button 
                    v-for="table in packageTables"
                    :key="table.name"
                    @click="navigateToEntity(table.name)"
                    :style="scalingStyles.buttonPadding"
                    :class="getEntityColorClasses('blue')"
                    class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"
                  >
                    <div class="flex items-center justify-between">
                      <span :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium">{{ table.displayName }}</span>
                      <i :style="scalingStyles.textFontSize" class="fas fa-table"></i>
                    </div>
                    <span :style="{ fontSize: `${fontSizes.base - 2}px` }">{{ table.records }} recs • {{ table.lastUpdated }}</span>
                  </button>
                </div>
              </div>

              <!-- Framework Shared Entities -->
              <p :style="{ fontSize: `${fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                Framework Shared Entities <span :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ sharedEntities.length }} Entities</span>
              </p>

              <div class="space-y-2">
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                  <button 
                    v-for="entity in sharedEntities"
                    :key="entity.id"
                    @click="navigateToEntity(entity.id)"
                    :style="scalingStyles.buttonPadding"
                    :class="getEntityColorClasses(entity.color)"
                    class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"
                  >
                    <div class="flex items-center justify-between">
                      <span :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium">{{ entity.name }}</span>
                      <i :style="scalingStyles.textFontSize" :class="entity.icon"></i>
                    </div>
                    <span :style="{ fontSize: `${fontSizes.base - 2}px` }">{{ entity.path }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Package Tables Info Box Section -->
    <div class="mt-8 space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Package Tables Info Box -->
        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Package Tables</h3>
            <p :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Finance module data tables and their current status</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="table in packageTables" :key="table.name" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="navigateToEntity(table.name)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="scalingStyles.textFontSize" class="fas fa-table text-indigo-600 dark:text-indigo-400"></i>
                  <div>
                    <div :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-900 dark:text-white capitalize font-medium -mt-2">{{ table.displayName }}</div>
                    <div :style="{ fontSize: `${fontSizes.base - 4}px` }" class="text-gray-400 dark:text-gray-500">{{ table.records }} records</div>
                  </div>
                </div>
                <div :style="{ fontSize: `${fontSizes.base - 5}px` }" class="text-gray-400 dark:text-gray-500 text-right">{{ table.lastUpdated }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Framework Shared Entities Info Box -->
        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Framework Shared Entities</h3>
            <p :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Core business entities shared across the framework</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="entity in sharedEntities" :key="entity.id" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="navigateToEntity(entity.id)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="scalingStyles.textFontSize" :class="`${entity.icon} text-${entity.color}-600 dark:text-${entity.color}-400`"></i>
                  <div>
                    <div :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white -mt-2">{{ entity.name }}</div>
                    <div :style="{ fontSize: `${fontSizes.base - 4}px` }" class="text-gray-400 dark:text-gray-500">{{ entity.description || 'Core entity' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Info section specific styles if needed */
</style>
