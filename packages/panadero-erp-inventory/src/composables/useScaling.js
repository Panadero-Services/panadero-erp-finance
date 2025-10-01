import { computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'

export function useScaling() {
  const store = useInventoryStore()

  // Base font sizes
  const baseFontSize = computed(() => store.settings.fontSize || 14)
  
  // Scaling multiplier based on user preference
  const scaleMultiplier = computed(() => {
    const fontSize = baseFontSize.value
    if (fontSize <= 12) return 0.8
    if (fontSize <= 14) return 1.0
    if (fontSize <= 16) return 1.1
    if (fontSize <= 18) return 1.2
    return 1.3
  })

  // Dynamic scaling styles
  const scalingStyles = computed(() => {
    const scale = scaleMultiplier.value
    const base = baseFontSize.value
    
    return {
      // Font sizes
      tinyFontSize: `font-size: ${Math.round(base * 0.7 * scale)}px`,
      smallFontSize: `font-size: ${Math.round(base * 0.85 * scale)}px`,
      textFontSize: `font-size: ${Math.round(base * scale)}px`,
      subtitleFontSize: `font-size: ${Math.round(base * 1.1 * scale)}px`,
      titleFontSize: `font-size: ${Math.round(base * 1.3 * scale)}px`,
      largeFontSize: `font-size: ${Math.round(base * 1.5 * scale)}px`,
      
      // Icon sizes
      iconSize: `font-size: ${Math.round(base * 1.2 * scale)}px`,
      smallIconSize: `font-size: ${Math.round(base * 0.9 * scale)}px`,
      largeIconSize: `font-size: ${Math.round(base * 1.5 * scale)}px`,
      
      // Button styles
      button: `font-size: ${Math.round(base * 0.9 * scale)}px; padding: ${Math.round(8 * scale)}px ${Math.round(16 * scale)}px`,
      buttonSmall: `font-size: ${Math.round(base * 0.8 * scale)}px; padding: ${Math.round(6 * scale)}px ${Math.round(12 * scale)}px`,
      buttonLarge: `font-size: ${Math.round(base * 1.1 * scale)}px; padding: ${Math.round(12 * scale)}px ${Math.round(24 * scale)}px`,
      
      // Input styles
      input: `font-size: ${Math.round(base * scale)}px; padding: ${Math.round(8 * scale)}px ${Math.round(12 * scale)}px`,
      select: `font-size: ${Math.round(base * scale)}px; padding: ${Math.round(8 * scale)}px ${Math.round(12 * scale)}px`,
      textarea: `font-size: ${Math.round(base * scale)}px; padding: ${Math.round(8 * scale)}px ${Math.round(12 * scale)}px`,
      
      // Table styles
      tableHeader: `font-size: ${Math.round(base * 0.8 * scale)}px; font-weight: 600`,
      tableCell: `font-size: ${Math.round(base * 0.9 * scale)}px`,
      
      // Spacing
      spacing: {
        xs: `${Math.round(4 * scale)}px`,
        sm: `${Math.round(8 * scale)}px`,
        md: `${Math.round(16 * scale)}px`,
        lg: `${Math.round(24 * scale)}px`,
        xl: `${Math.round(32 * scale)}px`,
        '2xl': `${Math.round(48 * scale)}px`
      }
    }
  })

  // Helper function to get scaled value
  const getScaledValue = (value) => {
    return Math.round(value * scaleMultiplier.value)
  }

  // Helper function to get scaled font size
  const getScaledFontSize = (multiplier = 1) => {
    return `${Math.round(baseFontSize.value * multiplier * scaleMultiplier.value)}px`
  }

  return {
    scalingStyles,
    scaleMultiplier,
    baseFontSize,
    getScaledValue,
    getScaledFontSize
  }
}
