<script setup>
import { ref, computed } from 'vue';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const props = defineProps({
  activeTab: { type: String, required: true },
  tabs: { type: Array, required: true }
});

const emit = defineEmits(['tab-change']);

const { fontSizes, scalingStyles } = useScaling();

const handleTabClick = (tabId) => {
  emit('tab-change', tabId);
};

const isActive = (tabId) => {
  return props.activeTab === tabId;
};
</script>

<template>
  <div class="hr-navigation">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            Human Resources Management
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Manage employees, recruitment, and organizational development
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <div class="text-right">
            <div :style="{ fontSize: `${fontSizes.caption}` }" class="text-gray-500 dark:text-gray-400">
              Last updated
            </div>
            <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-900 dark:text-white font-medium">
              {{ new Date().toLocaleString() }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
      <nav class="-mb-px flex space-x-8 overflow-x-auto">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="handleTabClick(tab.id)"
          :class="[
            'whitespace-nowrap py-2 px-1 border-b-2 font-medium transition-colors duration-200',
            isActive(tab.id)
              ? 'border-green-500 text-green-600 dark:text-green-400'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
          ]"
          :style="{ fontSize: `${fontSizes.label}` }"
        >
          <i :class="[tab.icon, 'mr-2', tab.color]"></i>
          {{ tab.label }}
        </button>
      </nav>
    </div>
  </div>
</template>

<style scoped>
.hr-navigation {
  padding: 0;
}

/* Custom scrollbar for horizontal navigation */
nav::-webkit-scrollbar {
  height: 4px;
}

nav::-webkit-scrollbar-track {
  background: #f1f5f9;
}

nav::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
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
