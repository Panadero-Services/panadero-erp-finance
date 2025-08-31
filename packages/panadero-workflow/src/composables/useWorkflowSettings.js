// Workflow Settings Composable
// @version 1.0.9
// date 21-Aug-2025
// @description Provides styling and configuration settings for workflow components

import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from './workflowStore.js'

export function useWorkflowSettings() {
  const workflowStore = useWorkflowStore()
  
  // Base font sizes (moved from financeStore)
  const fontSizes = computed(() => {
    const base = workflowStore.settings.fontSize  // Now reactive!
    return {
      base: base,        // 14, 16, 18, etc. (changes with finance settings)
      small: base - 2,   // 12, 14, 16, etc.
      medium: base + 2,  // 16, 18, 20, etc.
      large: base + 4,   // 18, 20, 22, etc.
      xlarge: base + 6,  // 20, 22, 24, etc.
      xxlarge: base + 10 // 24, 26, 28, etc.
    }
  })

  // Dynamic font size calculations
  const getFontSize = (size = 'base', offset = 0) => {
    const baseSize = fontSizes.value[size] || fontSizes.value.base
    return `${baseSize + offset}px`
  }

  // Predefined font size combinations
  const fontSizesComputed = computed(() => {
    const base = fontSizes.value.base  // This is now reactive!
    return {
      // Headers
      h1: base + 8,      // 22, 24, 26, etc.
      h2: base + 6,      // 20, 22, 24, etc.
      h3: base + 4,      // 18
      h4: base + 2,      // 16
      
      // Body text
      body: base,         // 14, 16, 18, etc.
      bodyVerySmall: base - 2, // 13
      bodySmall: base - 1, // 13
      bodyLarge: base + 1, // 15
      bodyVeryLarge: base + 2, // 15
      
      // Labels and captions
      label: base - 1,    // 13
      caption: base - 2,  // 12, 14, 16, etc.
      
      // Buttons
      button: base - 2,   // 12
      buttonLarge: base   // 14
    }
  })

  // Color schemes
  const colors = ref({
    primary: {
      light: 'text-indigo-600',
      dark: 'text-indigo-400',
      bgLight: 'bg-indigo-50',
      bgDark: 'bg-indigo-900/20',
      borderLight: 'border-indigo-200',
      borderDark: 'border-indigo-700'
    },
    success: {
      light: 'text-green-600',
      dark: 'text-green-400',
      bgLight: 'bg-green-100',
      bgDark: 'bg-green-900/30',
      borderLight: 'border-green-200',
      borderDark: 'border-green-700'
    },
    warning: {
      light: 'text-orange-600',
      dark: 'text-orange-400',
      bgLight: 'bg-orange-100',
      bgDark: 'bg-orange-900/30',
      borderLight: 'border-orange-200',
      borderDark: 'border-orange-700'
    },
    danger: {
      light: 'text-red-600',
      dark: 'text-red-400',
      bgLight: 'bg-red-100',
      bgDark: 'bg-red-900/20',
      borderLight: 'border-red-200',
      borderDark: 'border-red-600'
    },
    info: {
      light: 'text-blue-600',
      dark: 'text-blue-400',
      bgLight: 'bg-blue-100',
      bgDark: 'bg-blue-900/30',
      borderLight: 'border-blue-200',
      borderDark: 'border-blue-700'
    }
  })

  // Spacing and layout
  const spacing = ref({
    xs: '0.25rem',    // 4px
    sm: '0.5rem',     // 8px
    md: '1rem',       // 16px
    lg: '1.5rem',     // 24px
    xl: '2rem',       // 32px
    xxl: '3rem'       // 48px
  })

  // Border radius
  const borderRadius = ref({
    sm: '0.25rem',    // 4px
    md: '0.375rem',   // 6px
    lg: '0.5rem',     // 8px
    xl: '0.75rem',    // 12px
    full: '9999px'    // Full rounded
  })

  // Transitions
  const transitions = ref({
    fast: 'transition-all duration-150 ease-in-out',
    normal: 'transition-all duration-300 ease-in-out',
    slow: 'transition-all duration-500 ease-in-out'
  })

  // Shadow utilities
  const shadows = ref({
    sm: 'shadow-sm',
    md: 'shadow',
    lg: 'shadow-lg',
    xl: 'shadow-xl',
    none: 'shadow-none'
  })

  // Responsive breakpoints
  const breakpoints = ref({
    sm: '640px',
    md: '768px',
    lg: '1024px',
    xl: '1280px',
    '2xl': '1536px'
  })

  // Utility functions
  const getResponsiveClass = (baseClass, responsiveVariants = {}) => {
    let classes = [baseClass]
    
    Object.entries(responsiveVariants).forEach(([breakpoint, variant]) => {
      classes.push(`${breakpoint}:${variant}`)
    })
    
    return classes.join(' ')
  }


const getColorClass = (colorType, variant = 'light', element = 'text') => {
    const color = colors.value[colorType]
    if (!color) return ''
    
    switch (element) {
      case 'text': return color[variant]
      case 'bg': return color[`bg${variant.charAt(0).toUpperCase() + variant.slice(1)}`]
      case 'border': return color[`border${variant.charAt(0).toUpperCase() + variant.slice(1)}`]
      default: return color[variant]
    }
  }

  // Lifecycle
  onMounted(() => {
    //console.debug("useWorkflowSettings onMounted!");
    console.debug(fontSizesComputed.value.h1);
  })

  return {
    // Font sizes
    fontSizes,
    fontSizesComputed,
    getFontSize,
    
    // Colors
    colors,
    getColorClass,
    
    // Layout
    spacing,
    borderRadius,
    transitions,
    shadows,
    breakpoints,
    
    // Utilities
    getResponsiveClass
  }
}