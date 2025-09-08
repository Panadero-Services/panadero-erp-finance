<script setup>
import { useComplianceStore } from '../../stores/complianceStore';
import { useScaling } from '../../../../shared/composables/useScaling.js';
import { onMounted } from 'vue';

const props = defineProps({
  activeTab: { type: String, required: true },
  tabs: { type: Array, required: true }
});

const emit = defineEmits(['tab-change']);

const store = useComplianceStore(); // Only for business data
const { fontSizes, scalingStyles, spacing } = useScaling(); // For styling

const handleTabClick = (tabId) => {
  emit('tab-change', tabId);
};

// Debug: Log the tabs being received
onMounted(() => {
  console.debug('ComplianceNavigation received tabs:', props.tabs);
  console.debug('Tabs structure:', props.tabs.map(tab => ({ id: tab.id, label: tab.label, icon: tab.icon })));
});
</script>

<template>
  <!-- Horizontal Bar Navigation -->
  <div :style="scalingStyles.sectionMargin" class="flex flex-row overflow-x-auto py-2">
    <div :style="scalingStyles.buttonGap" class="flex gap-2">

      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="handleTabClick(tab.id)"
        :style="scalingStyles.buttonPadding"
        :class="[
          'flex items-center gap-1 rounded-lg font-medium transition-all duration-200 ease-in-out transform hover:scale-105',
          activeTab === tab.id 
            ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' 
            : 'text-gray-700 dark:text-gray-300 bg-white/80 dark:bg-gray-800/80 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:shadow-md border border-gray-200/50 dark:border-gray-700'
        ]"
      >
        <!-- Icon with dynamic color and enhanced styling -->
        <div class="relative group">
          <i 
            :style="scalingStyles.textFontSize" 
            :class="[
              tab.icon,
              activeTab === tab.id 
                ? 'text-white drop-shadow-sm' 
                : tab.color + ' group-hover:scale-110 transition-transform duration-200 drop-shadow-sm'
            ]"
          ></i>
          <!-- Subtle glow effect for active state -->
          <div 
            v-if="activeTab === tab.id"
            class="absolute inset-0 rounded-full bg-white/20 blur-sm scale-125"
          ></div>
        </div>
        
        <!-- Label with enhanced typography -->
        <span 
          :style="{ fontSize: `${fontSizes.base}px` }" 
          class="truncate font-medium"
          :class="activeTab === tab.id ? 'text-white' : 'text-gray-700 dark:text-gray-300'"
        >
          {{ tab.label }}
        </span>
      </button>

    </div>
  </div>
</template>

<style scoped>
/* Navigation-specific styles */
nav {
  scrollbar-width: thin;
  scrollbar-color: #d1d5db #f9fafb;
}

nav::-webkit-scrollbar {
  height: 6px;
}

nav::-webkit-scrollbar-track {
  background: #f9fafb;
}

nav::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Dark mode scrollbar */
.dark nav::-webkit-scrollbar-track {
  background: #374151;
}

.dark nav::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.dark nav::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>