/*
  Color System Composable
  @version 1.0.0
  @description Centralized color system for modal2 components
*/import { computed } from 'vue'

export function useColors() {
  const colors = computed(() => ({
    // Primary colors - main content, headers, titles
    primary: {
      text: 'text-gray-900 dark:text-white',
      textMuted: 'text-gray-700 dark:text-gray-300',
      bg: 'bg-white dark:bg-gray-800',
      bgMuted: 'bg-gray-50 dark:bg-gray-800'
    },
    
    // Secondary colors - borders, dividers, subtle elements  
    secondary: {
      text: 'text-gray-500 dark:text-gray-400',
      border: 'border-gray-200 dark:border-gray-600',
      bg: 'bg-gray-100 dark:bg-gray-800'
    },
    
    // Form colors - interactive elements, inputs, buttons
    form: {
      border: 'border-gray-300 dark:border-gray-600',
      focus: 'focus:ring-purple-500 focus:border-purple-500',
      button: 'bg-indigo-600 hover:bg-purple-700',
      input: 'bg-gray-50 dark:bg-gray-900'
    }
  }))

  return {
    colors
  }
}
