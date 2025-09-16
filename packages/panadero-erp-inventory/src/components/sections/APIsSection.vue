<script setup>
import { ref } from 'vue'
import { useScaling } from 'panadero-shared-styling'

const props = defineProps({
  hasAccess: {
    type: Boolean,
    default: false
  }
})

const { fontSizes, scalingStyles } = useScaling()

const apiEndpoints = ref([
  {
    method: 'GET',
    endpoint: '/api/inventory/items',
    description: 'Retrieve all inventory items',
    parameters: [
      { name: 'page', type: 'integer', required: false, description: 'Page number for pagination' },
      { name: 'limit', type: 'integer', required: false, description: 'Number of items per page' },
      { name: 'category', type: 'string', required: false, description: 'Filter by category' }
    ]
  },
  {
    method: 'POST',
    endpoint: '/api/inventory/items',
    description: 'Create a new inventory item',
    parameters: [
      { name: 'sku', type: 'string', required: true, description: 'Item SKU' },
      { name: 'name', type: 'string', required: true, description: 'Item name' },
      { name: 'category', type: 'string', required: true, description: 'Item category' },
      { name: 'currentStock', type: 'integer', required: true, description: 'Current stock level' }
    ]
  },
  {
    method: 'PUT',
    endpoint: '/api/inventory/items/{id}',
    description: 'Update an existing inventory item',
    parameters: [
      { name: 'id', type: 'integer', required: true, description: 'Item ID' },
      { name: 'name', type: 'string', required: false, description: 'Item name' },
      { name: 'currentStock', type: 'integer', required: false, description: 'Current stock level' }
    ]
  },
  {
    method: 'DELETE',
    endpoint: '/api/inventory/items/{id}',
    description: 'Delete an inventory item',
    parameters: [
      { name: 'id', type: 'integer', required: true, description: 'Item ID' }
    ]
  },
  {
    method: 'GET',
    endpoint: '/api/inventory/warehouses',
    description: 'Retrieve all warehouses',
    parameters: []
  },
  {
    method: 'GET',
    endpoint: '/api/inventory/reports/stock-levels',
    description: 'Generate stock levels report',
    parameters: [
      { name: 'startDate', type: 'date', required: false, description: 'Report start date' },
      { name: 'endDate', type: 'date', required: false, description: 'Report end date' }
    ]
  }
])

const getMethodColor = (method) => {
  const colors = {
    GET: 'text-green-600 bg-green-100 dark:bg-green-900/20',
    POST: 'text-blue-600 bg-blue-100 dark:bg-blue-900/20',
    PUT: 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20',
    DELETE: 'text-red-600 bg-red-100 dark:bg-red-900/20'
  }
  return colors[method] || colors.GET
}
</script>

<template>
  <div class="apis-section">
    <!-- Header -->
    <div class="mb-8">
      <h2 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        API Documentation
      </h2>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        RESTful API endpoints for inventory management
      </p>
    </div>

    <!-- Access Notice -->
    <div v-if="!hasAccess" class="mb-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
      <div class="flex items-center">
        <i class="fas fa-lock text-yellow-600 dark:text-yellow-400 mr-2"></i>
        <span class="text-yellow-800 dark:text-yellow-200 font-medium">API Access Restricted</span>
      </div>
      <p :style="scalingStyles.smallFontSize" class="text-yellow-700 dark:text-yellow-300 mt-1 ">
        This feature requires additional permissions to access API documentation.
      </p>
    </div>

    <!-- API Endpoints -->
    <div v-if="hasAccess" class="space-y-6">
      <div
        v-for="endpoint in apiEndpoints"
        :key="endpoint.endpoint"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <span
                :class="getMethodColor(endpoint.method)"
                class="px-2 py-1 rounded  font-medium"
              >
                {{ endpoint.method }}
              </span>
              <code class=" font-mono text-gray-900 dark:text-white">
                {{ endpoint.endpoint }}
              </code>
            </div>
          </div>
          <p class="mt-2  text-gray-600 dark:text-gray-400">
            {{ endpoint.description }}
          </p>
        </div>
        
        <div v-if="endpoint.parameters.length > 0" class="px-6 py-4">
          <h4 class=" font-medium text-gray-900 dark:text-white mb-3">Parameters</h4>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Required
                  </th>
                  <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Description
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="param in endpoint.parameters" :key="param.name">
                  <td class="px-4 py-2  font-mono text-gray-900 dark:text-white">
                    {{ param.name }}
                  </td>
                  <td class="px-4 py-2  text-gray-600 dark:text-gray-400">
                    {{ param.type }}
                  </td>
                  <td class="px-4 py-2 ">
                    <span
                      :class="param.required ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
                    >
                      {{ param.required ? 'Yes' : 'No' }}
                    </span>
                  </td>
                  <td class="px-4 py-2  text-gray-600 dark:text-gray-400">
                    {{ param.description }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- No Access State -->
    <div v-else class="text-center py-12">
      <i :style="scalingStyles.iconSizeLarge" class="fas fa-lock text-gray-400 mb-4"></i>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">API documentation is not available</p>
    </div>
  </div>
</template>

<style scoped>
.apis-section {
  padding: 0;
}
</style>
