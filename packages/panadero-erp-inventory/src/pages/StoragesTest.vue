<!--
  Storages Enhanced Page
  @version 1.0.0
  @date 29-Sep-2025
  @description Enhanced storages page using generic DataTableComplete
-->
<script setup>
import { ref, onMounted } from 'vue'
import { DataTableComplete } from 'panadero-datatable'
import StorageForm from '../components/StorageForm.vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'
import axios from 'axios'

// Get common functionality for dark mode and scaling
const { darkModeClasses, scalingStyles } = useCommonSnippets()

const enhancedConfig = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {

    const response = await axios.get('/api/erp_storages/config')
    const modelConfig = response.data

    // Override formComponent with actual component
    enhancedConfig.value = {
      ...modelConfig,
      formComponent: StorageForm // Pass actual component instead of string
    }

  } catch (err) {
    console.error('Failed to load configuration:', err)
    error.value = `Failed to load configuration: ${err.message}`
  } finally {
    loading.value = false
  }
})


</script>

<template>
  <div class="storages-enhanced" :class="darkModeClasses.container">
    <DataTableComplete 
      v-if="enhancedConfig && !error"
      :config="enhancedConfig"
      :api-endpoint="'/api/erp_storages'"
      :table-name="'erp_storages'"
      :dark-mode-classes="darkModeClasses"
      :scaling-styles="scalingStyles"
    />
    <div v-else-if="loading" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p :class="darkModeClasses.textSecondary">Loading configuration...</p>
      </div>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="text-red-500 text-6xl mb-4">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 :class="darkModeClasses.text" class="text-xl font-semibold mb-2">Configuration Error</h3>
        <p :class="[darkModeClasses.textSecondary, 'text-red-600 dark:text-red-400']" class="mb-4">{{ error }}</p>
        <button 
          @click="location.reload()" 
          :style="scalingStyles.button"
          :class="[darkModeClasses.button, 'px-4 py-2 rounded-lg hover:opacity-90 transition-colors']"
        >
          <i class="fas fa-refresh mr-2"></i>
          Retry
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.storages-enhanced {
  @apply p-6;
}
</style>
