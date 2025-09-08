import { ref, computed } from 'vue'
import { useComplianceStore } from '../stores/complianceStore.js'

export function useStyling() {
  const store = useComplianceStore()

  // Color scheme for compliance module
  const colors = ref({
    primary: 'gray',
    secondary: 'blue',
    success: 'green',
    warning: 'yellow',
    danger: 'red',
    info: 'cyan',
    compliance: 'purple',
    audit: 'orange',
    risk: 'red',
    policy: 'blue',
    rca: 'indigo'
  })

  // Typography scale - using store font sizes
  const typography = ref({
    fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
    fontSize: store.fontSizes
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
    input: 'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500',
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
      teal: {
        default: 'text-teal-600 dark:text-teal-400',
        background: 'bg-teal-50 dark:bg-teal-900/20',
        border: 'border-teal-200 dark:border-teal-800',
        button: 'bg-teal-600 hover:bg-teal-700 text-white'
      },
      gray: {
        default: 'text-gray-600 dark:text-gray-400',
        background: 'bg-gray-50 dark:bg-gray-900/20',
        border: 'border-gray-200 dark:border-gray-800',
        button: 'bg-gray-600 hover:bg-gray-700 text-white'
      },
      blue: {
        default: 'text-blue-600 dark:text-blue-400',
        background: 'bg-blue-50 dark:bg-blue-900/20',
        border: 'border-blue-200 dark:border-blue-800',
        button: 'bg-blue-600 hover:bg-blue-700 text-white'
      },
      green: {
        default: 'text-green-600 dark:text-green-400',
        background: 'bg-green-50 dark:bg-green-900/20',
        border: 'border-green-200 dark:border-green-800',
        button: 'bg-green-600 hover:bg-green-700 text-white'
      },
      red: {
        default: 'text-red-600 dark:text-red-400',
        background: 'bg-red-50 dark:bg-red-900/20',
        border: 'border-red-200 dark:border-red-800',
        button: 'bg-red-600 hover:bg-red-700 text-white'
      },
      yellow: {
        default: 'text-yellow-600 dark:text-yellow-400',
        background: 'bg-yellow-50 dark:bg-yellow-900/20',
        border: 'border-yellow-200 dark:border-yellow-800',
        button: 'bg-yellow-600 hover:bg-yellow-700 text-white'
      },
      orange: {
        default: 'text-orange-600 dark:text-orange-400',
        background: 'bg-orange-50 dark:bg-orange-900/20',
        border: 'border-orange-200 dark:border-orange-800',
        button: 'bg-orange-600 hover:bg-orange-700 text-white'
      },
      purple: {
        default: 'text-purple-600 dark:text-purple-400',
        background: 'bg-purple-50 dark:bg-purple-900/20',
        border: 'border-purple-200 dark:border-purple-800',
        button: 'bg-purple-600 hover:bg-purple-700 text-white'
      },
      indigo: {
        default: 'text-indigo-600 dark:text-indigo-400',
        background: 'bg-indigo-50 dark:bg-indigo-900/20',
        border: 'border-indigo-200 dark:border-indigo-800',
        button: 'bg-indigo-600 hover:bg-indigo-700 text-white'
      }
    }
    
    return colorMap[color]?.[variant] || colorMap.gray[variant]
  }

  // Get size classes with dynamic font sizing
  const getSizeClasses = (size) => {
    const baseFontSize = store.settings.fontSize
    const sizeMap = {
      xs: `px-2 py-1`,
      sm: `px-3 py-1.5`,
      md: `px-4 py-2`,
      lg: `px-6 py-3`,
      xl: `px-8 py-4`
    }
    
    return sizeMap[size] || sizeMap.md
  }

  // Get font size classes
  const getFontSizeClasses = (size) => {
    const baseFontSize = store.settings.fontSize
    const sizeMap = {
      xs: `text-[${baseFontSize * 0.75}px]`,
      sm: `text-[${baseFontSize * 0.875}px]`,
      base: `text-[${baseFontSize}px]`,
      lg: `text-[${baseFontSize * 1.125}px]`,
      xl: `text-[${baseFontSize * 1.25}px]`,
      '2xl': `text-[${baseFontSize * 1.5}px]`,
      '3xl': `text-[${baseFontSize * 1.875}px]`,
      '4xl': `text-[${baseFontSize * 2.25}px]`
    }
    
    return sizeMap[size] || sizeMap.base
  }

  // Get compliance-specific color classes
  const getComplianceColorClasses = (type) => {
    const typeMap = {
      policy: 'gray',
      audit: 'orange',
      risk: 'red',
      report: 'blue',
      rca: 'indigo',
      regulatory: 'purple',
      internal: 'blue',
      external: 'green'
    }
    
    return getColorClasses(typeMap[type] || 'gray')
  }

  return {
    colors,
    typography,
    spacing,
    componentStyles,
    dynamicStyles,
    getColorClasses,
    getSizeClasses,
    getFontSizeClasses,
    getComplianceColorClasses
  }
}
