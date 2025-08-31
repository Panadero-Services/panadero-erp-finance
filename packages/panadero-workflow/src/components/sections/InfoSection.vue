<script setup>
import { useWorkflowStore } from '../../composables/workflowStore.js'
import { useInfoBoxes } from '../../composables/useInfoBoxes.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const store = useWorkflowStore()
const { 
  moduleDescription, 
  versionUpdates, 
  packageTables, 
  sharedEntities,
  toggleInfoBox,
  navigateToEntity,
  getEntityColorClasses
} = useInfoBoxes()
</script>

<template>
  <div class="info-section">
    <!-- Info Boxes Grid -->
    <div class="grid grid-cols-12 gap-6 mt-0 text-black dark:text-white">
      <!-- Module Description Info Box -->
      <div @click="toggleInfoBox" class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 cursor-pointer">
        <div class="mb-4">
          <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">ERP Module</h3>
          <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Descriptions of the ERP Package</p>
        </div>
        
        <div class="space-y-3">
          <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <div :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="mt-2 text-blue-700 dark:text-blue-400">{{ moduleDescription.title }}</div>
          </div>
        </div>
      </div>

      <!-- Version Updates Info Box -->
      <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <div class="mb-4">
          <p :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Version Updates</p>
          <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Recent updates and new features</p>
        </div>
        
        <div class="overflow-scroll">
          <div class="gap-3 flex">
            <div v-for="update in versionUpdates" :key="update.version" 
                 class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
              <div class="flex items-center justify-between w-48">
                <div>
                  <div :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-medium text-gray-900 dark:text-white">{{ update.version }}</div>
                  <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">{{ update.date }}</div>
                </div>
                <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-400 dark:text-gray-500">{{ update.features.length }} features</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- What's in the Package? Info Box -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">What's in the Package?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Module description -->
              <div v-for="desc in moduleDescription.descriptions" :key="desc.id" class="mb-6">
                <p :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ desc.title }} <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="ml-8 text-gray-500 dark:text-gray-400">{{ desc.count }} Total</span> 
                </p>

                <ul :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
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
            <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">What's New?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Version updates -->
              <div v-for="update in versionUpdates" :key="update.version" class="mb-6">
                <p :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ update.version }} <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="ml-16 text-gray-500 dark:text-gray-400">{{ update.date }}</span> 
                </p>

                <ul :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
                  <li v-for="feature in update.features" :key="feature">{{ feature }}</li>
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
            <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Dependencies</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Package Tables -->
              <p :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                Package Tables <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="ml-8 text-gray-500 dark:text-gray-400">{{ packageTables.length }} Tables</span>
              </p>

              <div class="space-y-2 mb-4">
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                  <button 
                    v-for="table in packageTables"
                    :key="table.name"
                    @click="navigateToEntity(table.name)"
                    :style="store.scalingStyles.buttonPadding"

                    :class="getEntityColorClasses('blue')"
                    class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"

                  >
                    <div class="flex items-center justify-between">
                      <span :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-medium">{{ table.name.replace('finance_', '').replace(/_/g, ' ') }}</span>
                      <i :style="store.scalingStyles.textFontSize"  class="fas fa-table"></i>
                    </div>
                    <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }">{{ table.records }} recs • {{ table.lastUpdated }}</span>
                  </button>
                </div>
              </div>

              <!-- Framework Shared Entities -->
              <p :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                Framework Shared Entities <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="ml-8 text-gray-500 dark:text-gray-400">{{ sharedEntities.length }} Entities</span>
              </p>

              <div class="space-y-2">
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                  <button 
                    v-for="entity in sharedEntities"
                    :key="entity.id"
                    @click="navigateToEntity(entity.id)"
                    :style="store.scalingStyles.buttonPadding"
                    :class="getEntityColorClasses(entity.color)"
                    class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"
                  >
                    <div class="flex items-center justify-between">
                      <span :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-medium">{{ entity.name }}</span>
                      <i :style="store.scalingStyles.textFontSize"  :class="entity.icon"></i>
                    </div>
                    <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }">{{ entity.path }}</span>
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
            <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Package Tables</h3>
            <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Finance module data tables and their current status</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="table in packageTables" :key="table.name" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="navigateToEntity(table.name)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="store.scalingStyles.textFontSize" class="fas fa-table text-indigo-600 dark:text-indigo-400"></i>
                  <div>
                    <div :style="{ fontSize: `${store.fontSizes.base }px` }" class="text-gray-900 dark:text-white capitalize font-medium -mt-2">{{ table.name.replace('finance_', '').replace(/_/g, ' ')}}</div>
                    <div :style="{ fontSize: `${store.fontSizes.base - 4}px` }" class="text-gray-500 dark:text-gray-400">{{ table.records }} records</div>
                  </div>
                </div>
                <div :style="{ fontSize: `${store.fontSizes.base - 5}px` }" class="text-gray-400 dark:text-gray-500 text-right">{{ table.lastUpdated }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Framework Shared Entities Info Box -->
        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Framework Shared Entities</h3>
            <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">Core business entities shared across the framework</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="entity in sharedEntities" :key="entity.id" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="navigateToEntity(entity.id)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="store.scalingStyles.textFontSize" :class="`${entity.icon} text-${entity.color}-600 dark:text-${entity.color}-400`"></i>
                  <div>
                    <div :style="{ fontSize: `${store.fontSizes.base }px` }" class="font-medium text-gray-900 dark:text-white -mt-2">{{ entity.name }}</div>
                    <div :style="{ fontSize: `${store.fontSizes.base - 4}px` }" class="text-gray-500 dark:text-gray-400">{{ entity.records }} records</div>
                  </div>
                </div>
                <div :style="{ fontSize: `${store.fontSizes.base - 5}px` }" class="text-xs text-gray-400 dark:text-gray-500">{{ entity.lastUpdated }}</div>
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
