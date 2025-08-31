import { ref, computed, watch, nextTick } from 'vue';
import frameworkConfig from '../config/panadero-packages-config.json';

// Global reactive settings - single source of truth
const globalSettings = ref({
  fontSize: frameworkConfig.framework.settings.fontSize || 14,
  darkMode: frameworkConfig.framework.settings.darkMode || true,
  compactLayout: frameworkConfig.framework.settings.compactLayout || false,
  autoSave: frameworkConfig.framework.settings.autoSave || true
});

// Debounced save to prevent rapid updates
let saveTimeout = null;
const debouncedSave = () => {
  if (saveTimeout) clearTimeout(saveTimeout);
  saveTimeout = setTimeout(() => {
    try {
      localStorage.setItem('panadero-framework-settings', JSON.stringify(globalSettings.value));
    } catch (error) {
      console.error('Failed to save framework settings:', error);
    }
  }, 300); // 300ms debounce
};

// Load settings from localStorage on module load
const loadFrameworkSettings = () => {
  try {
    const saved = localStorage.getItem('panadero-framework-settings');
    if (saved) {
      const parsed = JSON.parse(saved);
      // Use nextTick to ensure Vue updates are handled properly
      nextTick(() => {
        Object.assign(globalSettings.value, parsed);
      });
    }
  } catch (error) {
    console.error('Failed to load framework settings:', error);
  }
};

// Watch for changes with debouncing
watch(globalSettings, debouncedSave, { deep: true });

// Load settings on module initialization
loadFrameworkSettings();

export function useFrameworkSettings() {
  const updateSetting = (key, value) => {
    if (key in globalSettings.value) {
      // Use nextTick to prevent immediate reactivity issues
      nextTick(() => {
        globalSettings.value[key] = value;
      });
    }
  };

  const setFontSize = (size) => {
    // Validate font size to prevent extreme values
    const validSize = Math.max(8, Math.min(24, parseInt(size) || 14));
    nextTick(() => {
      globalSettings.value.fontSize = validSize;
    });
  };

  const resetSettings = () => {
    nextTick(() => {
      Object.assign(globalSettings.value, frameworkConfig.framework.settings);
    });
  };

  return {
    // Reactive settings - use computed to make them read-only
    settings: computed(() => globalSettings.value),
    fontSize: computed(() => globalSettings.value.fontSize),
    
    // Actions
    updateSetting,
    setFontSize,
    resetSettings,
    
    // Utils
    loadFrameworkSettings
  };
}