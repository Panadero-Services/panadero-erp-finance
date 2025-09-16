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

  // Component styles
  const componentStyles = ref({
    card: 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm',
    button: 'inline-flex items-center justify-center font-medium rounded-md transition-all duration-200',
    input: 'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
    table: 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',
    badge: 'inline-flex items-center px-2.5 py-0.5 rounded-full font-medium'
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
