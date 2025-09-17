import { computed, inject } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore'
import { useScaling } from 'panadero-shared-styling'
import { 
  statusOptions as seedStatusOptions, 
  typeOptions as seedTypeOptions, 
  priorityOptions as seedPriorityOptions, 
  colorOptions as seedColorOptions 
} from '../data/seedData.js'

export function useCommonSnippets() {
  // Get settings store from parent
  const settingsStore = inject('settingsStore', { 
    dark: false, 
    fontSize: 14,
    compactLayout: false,
    autoSave: true
  })
  
  // Get inventory store
  const store = useInventoryStore()
  
  // Get scaling styles
  const { scalingStyles } = useScaling()
  
  // Dark mode classes
  const darkModeClasses = computed(() => {
    const isDark = settingsStore.dark === true
    return {
      container: isDark ? 'bg-gray-800 text-white' : 'bg-white text-gray-900',
      card: isDark ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-200',
      tableHeader: isDark ? 'bg-gray-700 text-gray-300' : 'bg-gray-50 text-gray-500',
      tableRow: isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-50',
      modal: isDark ? 'bg-gray-800' : 'bg-white',
      text: isDark ? 'text-gray-100' : 'text-gray-900',
      textSecondary: isDark ? 'text-gray-400' : 'text-gray-500',
      border: isDark ? 'border-gray-700' : 'border-gray-200',
      input: isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300',
      button: isDark ? 'bg-gray-600 hover:bg-gray-500 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white',
      buttonSecondary: isDark ? 'bg-gray-500 hover:bg-gray-400 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-900'
    }
  })
  
  // Common computed properties
  const isDark = computed(() => settingsStore.dark === true)
  const fontSize = computed(() => settingsStore.fontSize || 14)
  const compactLayout = computed(() => settingsStore.compactLayout || false)
  
  // Common options
  // Options from seed data
  const statusOptions = seedStatusOptions
  const typeOptions = seedTypeOptions
  const priorityOptions = seedPriorityOptions
  const colorOptions = seedColorOptions
  
  // Common utility functions
  const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount)
  }
  
  const formatDate = (date, options = {}) => {
    const defaultOptions = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }
    return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date))
  }
  
  const formatNumber = (number, decimals = 2) => {
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    }).format(number)
  }
  
  const generateId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2)
  }
  
  const debounce = (func, wait) => {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  }
  
  const validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
  }
  
  const validatePhone = (phone) => {
    const re = /^[\+]?[1-9][\d]{0,15}$/
    return re.test(phone.replace(/\s/g, ''))
  }
  
  const copyToClipboard = async (text) => {
    try {
      await navigator.clipboard.writeText(text)
      return true
    } catch (err) {
      console.error('Failed to copy text: ', err)
      return false
    }
  }
  
  const downloadFile = (content, filename, type = 'text/plain') => {
    const blob = new Blob([content], { type })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  }
  
  const showNotification = (message, type = 'info') => {
    // This would integrate with your notification system
    console.log(`${type.toUpperCase()}: ${message}`)
  }
  
  const confirmAction = (message, title = 'Confirm Action') => {
    return window.confirm(`${title}\n\n${message}`)
  }
  
  return {
    // Stores
    settingsStore,
    store,
    scalingStyles,
    
    // Computed properties
    darkModeClasses,
    isDark,
    fontSize,
    compactLayout,
    
    // Options
    statusOptions,
    typeOptions,
    priorityOptions,
    colorOptions,
    
    // Utility functions
    formatCurrency,
    formatDate,
    formatNumber,
    generateId,
    debounce,
    validateEmail,
    validatePhone,
    copyToClipboard,
    downloadFile,
    showNotification,
    confirmAction
  }
}
