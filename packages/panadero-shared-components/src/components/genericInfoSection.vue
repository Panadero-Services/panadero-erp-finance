<script setup>
//v1.02 16-sep-25
  
import { computed } from 'vue';
import { useScaling } from 'panadero-shared-styling';

const props = defineProps({
  // Package configuration
  packageConfig: {
    type: Object,
    required: true,
    default: () => ({
      name: 'Package',
      title: 'Package Module',
      description: 'Package description',
      icon: 'fas fa-box',
      color: 'blue'
    })
  },
  
  // Data sources
  moduleDescription: {
    type: Object,
    required: true,
    default: () => ({
      title: 'Package Title',
      descriptions: []
    })
  },
  
  versionUpdates: {
    type: Array,
    default: () => []
  },
  
  packageTables: {
    type: Array,
    default: () => []
  },
  
  sharedEntities: {
    type: Array,
    default: () => []
  },
  
  // Callbacks
  onToggleInfoBox: {
    type: Function,
    default: () => {}
  },
  
  onNavigateToEntity: {
    type: Function,
    default: () => {}
  },
  
  // Customization
  showVersionUpdates: {
    type: Boolean,
    default: true
  },
  
  showPackageTables: {
    type: Boolean,
    default: true
  },
  
  showSharedEntities: {
    type: Boolean,
    default: true
  },
  
  showDependencies: {
    type: Boolean,
    default: true
  },
  
  // Table name transformations
  tableNamePrefix: {
    type: String,
    default: ''
  },
  
  tableNameReplacement: {
    type: String,
    default: ''
  }
});

// Use scaling composable - get the scaling object
const { scalingStyles } = useScaling();

// Helper function to get entity color classes
const getEntityColorClasses = (color) => {
  const colorClasses = {
    blue: 'bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-300',
    green: 'bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 text-green-700 dark:text-green-300',
    purple: 'bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300',
    red: 'bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300',
    yellow: 'bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
    indigo: 'bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
  };
  return colorClasses[color] || colorClasses.blue;
};

// Helper function to format table names
const formatTableName = (tableName) => {
  let formatted = tableName;
  if (props.tableNamePrefix) {
    formatted = formatted.replace(new RegExp(`^${props.tableNamePrefix}_`), '');
  }
  if (props.tableNameReplacement) {
    formatted = formatted.replace(new RegExp(props.tableNameReplacement, 'g'), ' ');
  }
  return formatted.replace(/_/g, ' ');
};

// Event handlers
const handleToggleInfoBox = () => {
  props.onToggleInfoBox();
};

const handleNavigateToEntity = (entityName) => {
  props.onNavigateToEntity(entityName);
};
</script>

<template>
  <div class="generic-info-section">
    <!-- Info Boxes Grid -->
    <div class="grid grid-cols-12 gap-6 mt-0 text-black dark:text-white">
      <!-- Module Description Info Box -->
      <div @click="handleToggleInfoBox" class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 cursor-pointer">
        <div class="mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">
            {{ packageConfig.title }}
          </h3>
          <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">
            {{ packageConfig.description }}
          </p>
        </div>
        
        <div class="space-y-3">
          <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <div :style="scalingStyles.subtitleFontSize" class="mt-2 text-blue-700 dark:text-blue-400">
              {{ moduleDescription.title }}
            </div>
          </div>
        </div>
      </div>

      <!-- Version Updates Info Box -->
      <div v-if="showVersionUpdates" class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <div class="mb-4">
          <p :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">Version Updates</p>
          <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">Recent updates and new features</p>
        </div>
        
        <div class="overflow-scroll">
          <div class="gap-3 flex">
            <div v-for="update in versionUpdates" :key="update.version" 
                 class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
              <div class="flex items-center justify-between w-48">
                <div>
                  <div :style="scalingStyles.subtitleFontSize" class="font-medium text-gray-900 dark:text-white">{{ update.version }}</div>
                  <div :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ update.date }}</div>
                </div>
                <div :style="scalingStyles.smallFontSize" class="text-gray-400 dark:text-gray-500">{{ update.features.length }} features</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- What's in the Package? Info Box -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">What's in the Package?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Module description -->
              <div v-for="desc in moduleDescription.descriptions" :key="desc.id" class="mb-6">
                <p :style="scalingStyles.subtitleFontSize" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ desc.title }} <span :style="scalingStyles.smallFontSize" class="ml-8 text-gray-500 dark:text-gray-400">{{ desc.count }} Total</span> 
                </p>

                <ul :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
                  <li v-for="item in desc.list" :key="item">{{ item }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- What's New? Info Box -->
      <div v-if="showVersionUpdates" class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">What's New?</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Version updates -->
              <div v-for="update in versionUpdates" :key="update.version" class="mb-6">
                <p :style="scalingStyles.subtitleFontSize" class="text-gray-700 dark:text-gray-200 mb-1 font-bold">
                  {{ update.version }} <span :style="scalingStyles.smallFontSize" class="ml-16 text-gray-500 dark:text-gray-400">{{ update.date }}</span> 
                </p>

                <ul :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-300 list-disc ml-5 space-y-1">
                  <li v-for="feature in update.features" :key="feature">{{ feature }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dependencies Info box -->
      <div v-if="showDependencies" class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="col-span-6 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">Dependencies</h3>
          </div>
          <div class="space-y-3">
            <div class="p-3 rounded-md border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 h-80 overflow-scroll">
              <!-- Package Tables -->
              <div v-if="showPackageTables">
                <p :style="scalingStyles.subtitleFontSize" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                  Package Tables <span :style="scalingStyles.smallFontSize" class="ml-8 text-gray-500 dark:text-gray-400">{{ packageTables.length }} Tables</span>
                </p>

                <div class="space-y-2 mb-4">
                  <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                    <button 
                      v-for="table in packageTables"
                      :key="table.name"
                      @click="handleNavigateToEntity(table.name)"
                      :style="scalingStyles.button"
                      :class="getEntityColorClasses(packageConfig.color)"
                      class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"
                    >
                      <div class="flex items-center justify-between">
                        <span :style="scalingStyles.textFontSize" class="font-medium">{{ formatTableName(table.name) }}</span>
                        <i :style="scalingStyles.iconSizeSmall" class="fas fa-table"></i>
                      </div>
                      <span :style="scalingStyles.smallFontSize">{{ table.records }} recs • {{ table.lastUpdated }}</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Framework Shared Entities -->
              <div v-if="showSharedEntities">
                <p :style="scalingStyles.subtitleFontSize" class="text-gray-700 dark:text-gray-200 mb-3 font-bold">
                  Framework Shared Entities <span :style="scalingStyles.smallFontSize" class="ml-8 text-gray-500 dark:text-gray-400">{{ sharedEntities.length }} Entities</span>
                </p>

                <div class="space-y-2">
                  <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                    <button 
                      v-for="entity in sharedEntities"
                      :key="entity.id"
                      @click="handleNavigateToEntity(entity.id)"
                      :style="scalingStyles.button"
                      :class="getEntityColorClasses(entity.color)"
                      class="w-full text-left rounded transition-colors cursor-pointer border dark:border-gray-500"
                    >
                      <div class="flex items-center justify-between">
                        <span :style="scalingStyles.textFontSize" class="font-medium">{{ entity.name }}</span>
                        <i :style="scalingStyles.iconSizeSmall" :class="entity.icon"></i>
                      </div>
                      <span :style="scalingStyles.smallFontSize">{{ entity.path }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Package Tables Info Box Section -->
    <div v-if="showPackageTables || showSharedEntities" class="mt-8 space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Package Tables Info Box -->
        <div v-if="showPackageTables" class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">Package Tables</h3>
            <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ packageConfig.name }} module data tables and their current status</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="table in packageTables" :key="table.name" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="handleNavigateToEntity(table.name)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="scalingStyles.iconSizeSmall" class="fas fa-table text-indigo-600 dark:text-indigo-400"></i>
                  <div>
                    <div :style="scalingStyles.textFontSize" class="text-gray-900 dark:text-white capitalize font-medium -mt-2">{{ formatTableName(table.name) }}</div>
                    <div :style="scalingStyles.smallFontSize" class="text-gray-500 dark:text-gray-400">{{ table.records }} records</div>
                  </div>
                </div>
                <div :style="scalingStyles.smallFontSize" class="text-gray-400 dark:text-gray-500 text-right">{{ table.lastUpdated }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Framework Shared Entities Info Box -->
        <div v-if="showSharedEntities" class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
          <div class="mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">Framework Shared Entities</h3>
            <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">Core business entities shared across the framework</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="entity in sharedEntities" :key="entity.id" 
                 class="p-4 rounded-lg border dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                 @click="handleNavigateToEntity(entity.id)">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <i :style="scalingStyles.iconSizeSmall" :class="`${entity.icon} text-${entity.color}-600 dark:text-${entity.color}-400`"></i>
                  <div>
                    <div :style="scalingStyles.textFontSize" class="font-medium text-gray-900 dark:text-white -mt-2">{{ entity.name }}</div>
                    <div :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ entity.records }} records</div>
                  </div>
                </div>
                <div :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-500">{{ entity.lastUpdated }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.generic-info-section {
  /* Generic styles */
}
</style>