import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'

export function useStyling() {
  const store = useInventoryStore()

  // Color scheme for inventory module
  const colors = ref({
    primary: 'blue',
    secondary: 'gray',
    success: 'green',
    warning: 'yellow',
    danger: 'red',
    info: 'cyan'
  })

  // Typography scale
  const typography = ref({
    fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
    fontSize: {
      xs: 12,
      sm: 14,
      base: 16,
      lg: 18,
      xl: 20,
      '2xl': 24,
      '3xl': 30
    }
  })

  // Spacing scale
  const spacing = ref({
    xs: 4,
    sm: 8,
    md: 16,
    lg: 24,
    xl: 32,
    '2xl': 48
  })

  // Dark mode classes
  const darkModeClasses = computed(() => ({
    // Container
    container: store.settings.darkMode 
      ? 'bg-gray-900 text-white min-h-screen' 
      : 'bg-gray-50 text-gray-900 min-h-screen',
    
    // Cards
    card: store.settings.darkMode 
      ? 'bg-gray-800 border-gray-700 text-gray-100' 
      : 'bg-white border-gray-200 text-gray-900',
    
    // Tables
    tableHeader: store.settings.darkMode 
      ? 'bg-gray-700 text-gray-300' 
      : 'bg-gray-50 text-gray-500',
    tableRow: store.settings.darkMode 
      ? 'hover:bg-gray-700' 
      : 'hover:bg-gray-50',
    table: store.settings.darkMode 
      ? 'divide-gray-700' 
      : 'divide-gray-200',
    
    // Text
    text: store.settings.darkMode 
      ? 'text-gray-100' 
      : 'text-gray-900',
    textSecondary: store.settings.darkMode 
      ? 'text-gray-400' 
      : 'text-gray-500',
    textMuted: store.settings.darkMode 
      ? 'text-gray-500' 
      : 'text-gray-400',
    
    // Borders
    border: store.settings.darkMode 
      ? 'border-gray-700' 
      : 'border-gray-200',
    borderLight: store.settings.darkMode 
      ? 'border-gray-600' 
      : 'border-gray-300',
    
    // Inputs
    input: store.settings.darkMode 
      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500' 
      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500',
    
    // Buttons
    buttonPrimary: store.settings.darkMode 
      ? 'bg-blue-600 hover:bg-blue-700 text-white border-blue-600' 
      : 'bg-blue-600 hover:bg-blue-700 text-white border-blue-600',
    buttonSecondary: store.settings.darkMode 
      ? 'bg-gray-700 hover:bg-gray-600 text-gray-200 border-gray-600' 
      : 'bg-gray-100 hover:bg-gray-200 text-gray-700 border-gray-300',
    buttonDanger: store.settings.darkMode 
      ? 'bg-red-600 hover:bg-red-700 text-white border-red-600' 
      : 'bg-red-600 hover:bg-red-700 text-white border-red-600',
    
    // Modals
    modal: store.settings.darkMode 
      ? 'bg-gray-800 border-gray-700' 
      : 'bg-white border-gray-200',
    
    // Status indicators
    statusSuccess: store.settings.darkMode 
      ? 'bg-green-900/20 text-green-400 border-green-800' 
      : 'bg-green-50 text-green-700 border-green-200',
    statusWarning: store.settings.darkMode 
      ? 'bg-yellow-900/20 text-yellow-400 border-yellow-800' 
      : 'bg-yellow-50 text-yellow-700 border-yellow-200',
    statusDanger: store.settings.darkMode 
      ? 'bg-red-900/20 text-red-400 border-red-800' 
      : 'bg-red-50 text-red-700 border-red-200',
    statusInfo: store.settings.darkMode 
      ? 'bg-blue-900/20 text-blue-400 border-blue-800' 
      : 'bg-blue-50 text-blue-700 border-blue-200'
  }))

  // Component styles
  const componentStyles = ref({
    card: 'rounded-lg border shadow-sm',
    button: 'inline-flex items-center justify-center font-medium rounded-md transition-all duration-200 border',
    input: 'w-full rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50',
    table: 'min-w-full divide-y',
    badge: 'inline-flex items-center px-2.5 py-0.5 rounded-full font-medium text-xs border'
  })

  // Dynamic styles based on settings
  const dynamicStyles = computed(() => ({
    fontSize: `${store.settings.fontSize}px`,
    fontFamily: typography.value.fontFamily
  }))

  // Get color classes
  const getColorClasses = (color, variant = 'default') => {
    const colorMap = {
      blue: {
        default: 'text-blue-600 dark:text-blue-400',
        background: 'bg-blue-50 dark:bg-blue-900/20',
        border: 'border-blue-200 dark:border-blue-800'
      },
      green: {
        default: 'text-green-600 dark:text-green-400',
        background: 'bg-green-50 dark:bg-green-900/20',
        border: 'border-green-200 dark:border-green-800'
      },
      red: {
        default: 'text-red-600 dark:text-red-400',
        background: 'bg-red-50 dark:bg-red-900/20',
        border: 'border-red-200 dark:border-red-800'
      },
      yellow: {
        default: 'text-yellow-600 dark:text-yellow-400',
        background: 'bg-yellow-50 dark:bg-yellow-900/20',
        border: 'border-yellow-200 dark:border-yellow-800'
      },
      purple: {
        default: 'text-purple-600 dark:text-purple-400',
        background: 'bg-purple-50 dark:bg-purple-900/20',
        border: 'border-purple-200 dark:border-purple-800'
      },
      gray: {
        default: 'text-gray-600 dark:text-gray-400',
        background: 'bg-gray-50 dark:bg-gray-900/20',
        border: 'border-gray-200 dark:border-gray-800'
      }
    }
    
    return colorMap[color]?.[variant] || colorMap.gray[variant]
  }

  // Get size classes
  const getSizeClasses = (size) => {
    const sizeMap = {
      xs: 'px-2 py-1',
      sm: 'px-3 py-1.5',
      md: 'px-4 py-2',
      lg: 'px-6 py-3',
      xl: 'px-8 py-4'
    }
    
    return sizeMap[size] || sizeMap.md
  }

  return {
    colors,
    typography,
    spacing,
    componentStyles,
    dynamicStyles,
    getColorClasses,
    getSizeClasses
  }
}
