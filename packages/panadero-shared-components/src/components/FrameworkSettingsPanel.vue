<script setup>
//v1.02 16-sep-25
import { ref, watch, nextTick } from 'vue';
import { useFrameworkSettings } from 'panadero-shared-styling';

const emit = defineEmits(['settingsChanged']);

const showPanel = ref(false);
const { settings, setFontSize, updateSetting, resetSettings } = useFrameworkSettings();

// Local copy for smooth slider interaction
const localFontSize = ref(settings.value.fontSize);

// Watch for external changes to fontSize
watch(() => settings.value.fontSize, (newSize) => {
  localFontSize.value = newSize;
});

const togglePanel = () => {
  showPanel.value = !showPanel.value;
};

// Debounced font size update to prevent rapid changes
let fontSizeTimeout = null;
const updateFontSize = () => {
  if (fontSizeTimeout) clearTimeout(fontSizeTimeout);
  
  fontSizeTimeout = setTimeout(() => {
    setFontSize(localFontSize.value);
    console.debug('Framework font size updated to:', localFontSize.value);
    emit('settingsChanged', { 
      type: 'fontSize', 
      value: localFontSize.value 
    });
  }, 150); // Debounce for smooth slider interaction
};

const applyFontPreset = (presetName) => {
  const presets = {
    small: 10,
    medium: 14,
    large: 18
  };
  
  if (presets[presetName]) {
    localFontSize.value = presets[presetName];
    setFontSize(presets[presetName]);
    console.debug('Font preset applied:', presetName, 'Size:', presets[presetName]);
  }
};

const handleSettingChange = (key, value) => {
  updateSetting(key, value);
  emit('settingsChanged', { 
    type: 'setting', 
    key,
    value 
  });
};
</script>

<template>
  <!-- Settings Panel -->
  <div v-if="showPanel" class="fixed top-4 right-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-gray-700 dark:text-gray-200 z-50 min-w-80 max-w-96 shadow-lg">
    <!-- Header -->
    <div class="flex justify-between items-center mb-5 pb-3">
      <h3 class="m-0 text-lg text-gray-900 dark:text-white font-semibold">⚙️ Framework Settings</h3>
      <button @click="togglePanel" class="text-gray-500 dark:text-gray-400 text-2xl p-1 w-7 h-7 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
        ×
      </button>
    </div>

    <!-- Font Size Section -->
    <div class="mb-6">
      <h4 class="m-0 mb-3 text-sm text-blue-600 dark:text-blue-400 font-semibold">🔤 Font Size</h4>
      
      <div class="mb-4">
        <label class="block text-sm mb-2 text-gray-600 dark:text-gray-300 font-medium">
          Current Size: {{ localFontSize }}px
        </label>
        <input 
          type="range" 
          v-model="localFontSize" 
          min="8" 
          max="24" 
          step="1"
          @input="updateFontSize"
          class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer"
        >
      </div>

      <!-- Font Presets -->
      <div class="grid grid-cols-3 gap-2">
        <button @click="applyFontPreset('small')" class="px-3 py-1 text-sm border border-green-500 text-green-600 rounded hover:bg-green-50">
          Small
        </button>
        <button @click="applyFontPreset('medium')" class="px-3 py-1 text-sm border border-blue-500 text-blue-600 rounded hover:bg-blue-50">
          Medium
        </button>
        <button @click="applyFontPreset('large')" class="px-3 py-1 text-sm border border-orange-500 text-orange-600 rounded hover:bg-orange-50">
          Large
        </button>
      </div>
    </div>

    <!-- Display Settings -->
    <div class="mt-8">
      <h4 class="m-0 text-sm text-blue-600 dark:text-blue-400 font-semibold mb-3">🎨 Display</h4>
      
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Dark Mode</label>
          <input 
            type="checkbox" 
            :checked="settings.darkMode" 
            @change="handleSettingChange('darkMode', $event.target.checked)"
            class="rounded"
          >
        </div>
        
        <div class="flex items-center justify-between">
          <label class="text-xs text-gray-600 dark:text-gray-300">Compact Layout</label>
          <input 
            type="checkbox" 
            :checked="settings.compactLayout"
            @change="handleSettingChange('compactLayout', $event.target.checked)"
            class="rounded"
          >
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex gap-3 mt-8">
      <button @click="resetSettings" class="flex-1 px-3 py-2 text-sm border border-red-500 text-red-600 rounded hover:bg-red-50">
        Reset
      </button>
    </div>
  </div>

  <!-- Toggle Button -->
  <button @click="togglePanel" class="fixed text-xs top-4 right-4 w-8 h-8 rounded-full bg-white dark:bg-indigo-900 hover:scale-105 text-indigo-400 text-lg z-40 hover:bg-indigo-600 shadow-lg">
    ⚙️
  </button>
</template>