import { computed } from 'vue';
import { useFrameworkSettings } from './useFrameworkSettings.js';

export function useScaling() {
  // Get fontSize from framework settings
  const { fontSize } = useFrameworkSettings();

  const fontSizes = computed(() => {
    const base = fontSize.value || 14; // Fallback to 14 if undefined
    return {
      base: base,
      xxxs: `${Math.max(base - 3, 5)}px`,
      xxs: `${Math.max(base - 2, 6)}px`,
      xs: `${Math.max(base - 1, 7)}px`,
      body: `${Math.max(base, 8)}px`, 
      subtitle: `${Math.max(base+1, 9)}px`, 
      caption: `${Math.max(base+2, 10)}px`, 
      title: `${Math.max(base+4, 12)}px`, 
      heading: `${Math.max(base + 6, 14)}px`,
      h1: `${Math.max(base + 5, 13)}px`,
      h2: `${Math.max(base + 3, 11)}px`,
      h3: `${Math.max(base + 2, 10)}px`,
      h4: `${Math.max(base + 1, 9)}px`,
      label: `${Math.max(base - 1, 7)}px`,
      amount: `${Math.max(base + 1, 9)}px`,
      table: `${Math.max(base - 2, 6)}px`
    };
  });

  const scalingStyles = computed(() => {
    const base = fontSize.value || 14; // Fallback to 14 if undefined
    
    // Defensive programming - ensure all values are valid
    const safeBase = Math.max(8, Math.min(24, base));
    
    return {
      buttonPadding: { padding: `${Math.max(8, safeBase - 4)}px ${Math.max(12, (safeBase - 2) * 1.5)}px` },
      inputPadding: { padding: `${Math.max(2, safeBase - 6)}px ${Math.max(2, (safeBase - 2) * 1)}px` },
      textareaHeight: { height: `${Math.max(80, safeBase * 3.5)}px` },
      tableHeader: { fontSize: `${Math.max(10, safeBase - 2)}px` },
      actionButton: {
        width: `${Math.max(24, safeBase + 8)}px`,
        height: `${Math.max(24, safeBase + 8)}px`,
        fontSize: `${Math.max(12, (safeBase + 8) * 0.6)}px`
      },
      gapScale: { gap: `${Math.max(8, safeBase - 2)}px` },
      marginScale: { marginBottom: `${Math.max(16, safeBase + 2)}px` },
      paddingScale: { padding: `${Math.max(2, Math.round(safeBase * 0.2))}px` },
      borderRadius: { borderRadius: `${Math.max(4, safeBase - 8)}px` },
      cardPadding: { padding: `${Math.max(20, safeBase + 6)}px` },
      sectionMargin: { marginBottom: `${Math.max(24, safeBase + 10)}px` },
      buttonGap: { gap: `${Math.max(8, safeBase - 2)}px` },
      titleFontSize: { fontSize: `${Math.max(12, safeBase + 4)}px` },
      subtitleFontSize: { fontSize: `${Math.max(10, safeBase + 2)}px` },
      textFontSize: { fontSize: `${safeBase}px` },
      smallFontSize: { fontSize: `${Math.max(8, safeBase - 2)}px` },
      largeFontSize: { fontSize: `${Math.max(14, safeBase + 6)}px` },
      amountFontSize: { fontSize: `${Math.max(10, safeBase + 2)}px` },
      iconSize: { fontSize: `${safeBase}px` },
      iconSizeSmall: { fontSize: `${Math.max(8, safeBase - 2)}px` },
      iconSizeLarge: { fontSize: `${Math.max(10, safeBase + 2)}px` },
      iconSizeExtraLarge: { fontSize: `${Math.max(12, safeBase + 4)}px` },
      tableRowHeight: { height: `${Math.max(24, Math.round(safeBase * 1.4))}px !important` },
      tableHeaderHeight: { height: `${Math.max(28, safeBase * 2)}px` }
    };
  });

  const spacing = {
    padding: fontSize.value < 10 ? 'p-2' : 'p-24',
    paddingTiny: 'p-2', 
    paddingSmall: 'p-4', 
    paddingBase: 'p-6',
    paddingLarge: 'p-8',
    margin: 'mb-6',
    marginSmall: 'mb-4',
    marginLarge: 'mb-8',
    gap: 'gap-4',
    gapSmall: 'gap-2',
    gapLarge: 'gap-6'
  };

  return {
    fontSizes,
    scalingStyles,
    spacing,
    fontSize // expose fontSize for read access
  };
}