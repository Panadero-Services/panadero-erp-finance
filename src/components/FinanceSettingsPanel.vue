<script setup>
import { ref, watch, onMounted } from 'vue';
import { useFinanceStore } from '../stores/financeStore';
import FinanceToggle from './ui/FinanceToggle.vue';
import FinanceButton from './ui/FinanceButton.vue';

const emit = defineEmits(['settingsChanged']);

const showPanel = ref(false);
const store = useFinanceStore();

const togglePanel = () => {
  showPanel.value = !showPanel.value;
};

const updateFontSize = () => {
  store.setFontSize(store.settings.fontSize);
  console.log('Font size updated to:', store.settings.fontSize);
  emit('settingsChanged', { 
    type: 'fontSize', 
    value: store.settings.fontSize 
  });
};

const updateSettings = () => {
  store.updateSettings(store.settings);
  emit('settingsChanged', { 
    type: 'settings', 
    value: { ...store.settings } 
  });
};

const applyFontPreset = (presetName) => {
  const presets = {
    small: 10,
    medium: 14,
    large: 18
  };
  
  if (presets[presetName]) {
    store.setFontSize(presets[presetName]);
    console.log('Font preset applied:', presetName, 'Size:', store.settings.fontSize);
  }
};

const resetToDefaults = () => {
  store.resetSettings();
  console.log('Reset to defaults, font size:', store.settings.fontSize);
  updateSettings();
};

const exportSettings = () => {
  const dataStr = JSON.stringify({
    fontSize: store.settings.fontSize,
    settings: store.settings
  }, null, 2);
  const dataBlob = new Blob([dataStr], { type: 'application/json' });
  const url = URL.createObjectURL(dataBlob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'finance-settings.json';
  link.click();
  URL.revokeObjectURL(url);
};

const importSettings = () => {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.json';
  input.onchange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        try {
          const imported = JSON.parse(e.target.result);
          if (imported.fontSize) {
            store.setFontSize(imported.fontSize);
            console.log('Font size imported:', imported.fontSize);
          }
          if (imported.settings) {
            store.updateSettings(imported.settings);
          }
        } catch (err) {
          console.error('Failed to import settings:', err);
        }
      };
      reader.readAsText(file);
    }
  };
  input.click();
};

// Load saved settings on mount
onMounted(() => {
  store.loadSettings();
  console.log('Settings panel mounted, current font size:', store.settings.fontSize);
});

// Watch for font size changes
watch(() => store.settings.fontSize, (newSize) => {
  console.log('Font size changed to:', newSize);
});
</script>

<template>
  <!-- Settings Panel -->
  <div v-if="showPanel" class="fixed top-4 right-4 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-gray-700 dark:text-gray-200 z-50 min-w-80 max-w-96 max-h-[90vh] overflow-y-auto backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-5 pb-3 border-blue-500 dark:border-blue-400">
      <h3 class="m-0 text-lg text-gray-900 dark:text-white font-semibold">⚙️ Finance Settings</h3>
      <FinanceButton @click="togglePanel" variant="ghost" size="small" class="text-gray-500 dark:text-gray-400 text-2xl p-1 w-7 h-7">
        ×
      </FinanceButton>
    </div>

    <!-- Font Size Section -->
    <div class="mb-6">
      <h4 class="m-0 mb-3 text-sm text-blue-600 dark:text-blue-400 font-semibold border-gray-200 dark:border-gray-600 pb-2">🔤 Font Size</h4>
      
      <div class="mb-4">
        <label class="block text-sm mb-2 text-gray-600 dark:text-gray-300 font-medium">Current Size: {{ store.settings.fontSize }}px</label>
        <input 
          type="range" 
          v-model="store.settings.fontSize" 
          min="8" 
          max="24" 
          step="1"
          @input="updateFontSize"
          class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer slider"
        >
        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
          <span>8px</span>
          <span>24px</span>
        </div>
      </div>

      <!-- Font Presets -->
      <div class="grid grid-cols-3 gap-2">
        <FinanceButton @click="applyFontPreset('small')" variant="outline" size="small" class="border-green-500 text-green-600 dark:border-green-400 dark:text-green-400">
          Small
        </FinanceButton>
        <FinanceButton @click="applyFontPreset('medium')" variant="outline" size="small" class="border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400">
          Medium
        </FinanceButton>
        <FinanceButton @click="applyFontPreset('large')" variant="outline" size="small" class="border-orange-500 text-orange-600 dark:border-orange-400 dark:text-orange-400">
          Large
        </FinanceButton>
      </div>
    </div>

    <!-- Display Settings -->
    <div class="mt-8">
      <h4 class="m-0 text-sm text-blue-600 dark:text-blue-400 font-semibold  border-gray-200 dark:border-gray-600 pb-2">🎨 Display</h4>
      
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Dark Mode</label>
          
          <FinanceToggle 
            v-model="store.settings.darkMode" 
            label="Dark Mode"
            variant="blue"
            size="small"
          />
        </div>
        
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Compact Layout</label>
          <FinanceToggle 
            v-model="store.settings.compactLayout" 
            label="Compact Layout"
            variant="green"
            size="small"
          />
        </div>
        
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Show Debug Info</label>
          <FinanceToggle 
            v-model="store.settings.showDebug" 
            label="Show Debug Info"
            variant="green"
            size="small"
          />
        </div>
      </div>
    </div>

    <!-- Table Settings -->
    <div class="mt-8">
      <h4 class="m-0 text-sm text-blue-600 dark:text-blue-400 font-semibold border-gray-200 dark:border-gray-600 ">📊 Tables</h4>
      
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Rows Per Page</label>
          <select v-model="store.settings.rowsPerPage" @change="updateSettings" class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-4 py-1 rounded-md text-xs outline-none transition-colors duration-200 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Show Row Numbers</label>
          <FinanceToggle 
            v-model="store.settings.showRowNumbers" 
            label="Show Row Numbers"
            variant="green"
            size="small"
          />
        </div>
        
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Sticky Headers</label>
          <FinanceToggle 
            v-model="store.settings.stickyHeaders" 
            label="Sticky Headers"
            variant="green"
            size="small"
          />
        </div>
      </div>
    </div>

    <!-- Export Settings -->
    <div class="mt-8">
      <h4 class="m-0 mb-3 text-blue-600 dark:text-blue-400 font-semibold border-gray-200 dark:border-gray-600">📤 Export</h4>
      
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Default Format</label>
          <select v-model="store.settings.defaultExportFormat" @change="updateSettings" class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-md text-sm outline-none transition-colors duration-200 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20">
            <option value="csv">CSV</option>
            <option value="excel">Excel</option>
            <option value="pdf">PDF</option>
          </select>
        </div>

        <div class="flex items-center justify-between">
          <label class=" text-xs text-gray-600 dark:text-gray-300">Include Headers</label>
          <FinanceToggle 
            v-model="store.settings.includeHeaders" 
            label="Include Headers"
            variant="green"
            size="small"
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Auto-export on Save</label>
          <FinanceToggle 
            v-model="store.settings.autoExport" 
            label="Auto-export on Save"
            variant="green"
            size="small"
          />
        </div>
      </div>
    </div>

    <!-- Data Settings -->
    <div class="mt-8">
      <h4 class="text-sm text-blue-600 dark:text-blue-400 font-semibold border-gray-200 dark:border-gray-600">💾 Data</h4>
      
      <div class="m-3 space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Auto-refresh Interval ({{ store.settings.autoRefreshInterval }}s)</label>
          <input type="range" 
                 v-model="store.settings.autoRefreshInterval" 
                 min="0" 
                 max="60" 
                 step="5"
                 @input="updateSettings"
                 class="w-20 h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer">
          <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold ml-2">{{ store.settings.autoRefreshInterval }}s</span>
        </div>

        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Lazy Loading</label>
          <FinanceToggle 
            v-model="store.settings.lazyLoading" 
            label="Lazy Loading"
            variant="green"
            size="small"
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Cache Data</label>
          <FinanceToggle 
            v-model="store.settings.cacheData" 
            label="Cache Data"
            variant="green"
            size="small"
          />
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex gap-3 mt-8">
      <FinanceButton @click="resetToDefaults" variant="outline" size="normal" full-width class="border-red-500 text-red-600 dark:border-red-400 dark:text-red-400">
        Reset
      </FinanceButton>
      <FinanceButton @click="exportSettings" variant="outline" size="normal" full-width class="border-green-500 text-green-600 dark:border-green-400 dark:text-green-400">
        Export
      </FinanceButton>
      <FinanceButton @click="importSettings" variant="outline" size="normal" full-width class="border-purple-500 text-purple-600 dark:border-purple-400 dark:text-purple-400">
        Import
      </FinanceButton>
    </div>
  </div>

  <!-- Toggle Button (always visible) -->
  <FinanceButton @click="togglePanel" variant="ghost" size="large" class="fixed top-4 right-4 w-11 h-11 rounded-full text-blue-500 dark:text-blue-400 text-lg z-40 transition-all duration-300 shadow-lg dark:shadow-gray-900/50 hover:scale-105 hover:shadow-xl dark:hover:shadow-gray-900/70" :class="{ 'rotate-90': showPanel }">
    ⚙️
  </FinanceButton>
</template>

<style scoped>
/* Custom slider styling for range input */
.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Dark mode slider adjustments */
.dark .slider::-webkit-slider-thumb {
  background: #60a5fa;
  border-color: #1f2937;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.dark .slider::-moz-range-thumb {
  background: #60a5fa;
  border-color: #1f2937;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
</style>
