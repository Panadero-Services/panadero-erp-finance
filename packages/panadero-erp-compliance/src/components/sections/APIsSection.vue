<script setup>
import { ref, computed } from 'vue'
import { useComplianceStore } from '../../stores/complianceStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const props = defineProps({
  hasAccess: {
    type: Boolean,
    default: false
  }
})

const store = useComplianceStore()
const { fontSizes, scalingStyles } = useScaling()
</script>

<template>
  <div class="apis-section">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        API Documentation
      </h3>
      
      <div v-if="!hasAccess" class="text-center py-8">
        <i class="fas fa-lock text-gray-400 text-4xl mb-4"></i>
        <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-500 dark:text-gray-400">
          API access is not available in the current version
        </p>
      </div>
      
      <div v-else class="space-y-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Compliance API Endpoints
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            RESTful API endpoints for compliance data management
          </p>
        </div>
        
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Webhook Integration
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Real-time notifications for compliance events
          </p>
        </div>
        
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Authentication
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            OAuth 2.0 and API key authentication methods
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.apis-section {
  padding: 0;
}
</style>
