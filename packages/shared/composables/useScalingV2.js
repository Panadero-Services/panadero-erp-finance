// packages/shared/composables/useScalingV2.js
import { computed } from 'vue';
import { useFrameworkSettings } from './useFrameworkSettings.js';

export function useScalingV2() {
  const { fontSize } = useFrameworkSettings();

  const scaling = computed(() => {
    const base = fontSize.value || 14;
    
    return {
      // Font sizes - TRUE SCALING with no minimum constraints
      base: base,
      font: {
        // Tailwind font size names - DIRECT SCALING
        xxxs: `${Math.round(base * 0.5)}px`,      // 50% of base (7px if base=14)
        xxs: `${Math.round(base * 0.6)}px`,       // 60% of base (8px if base=14)
        xs: `${Math.round(base * 0.7)}px`,        // 70% of base (10px if base=14)
        sm: `${Math.round(base * 0.8)}px`,        // 80% of base (11px if base=14)
        base: `${base}px`,                         // Base size (14px default)
        lg: `${Math.round(base * 1.1)}px`,        // 110% of base (15px if base=14)
        xl: `${Math.round(base * 1.3)}px`,        // 130% of base (18px if base=14)
        '2xl': `${Math.round(base * 1.4)}px`,     // 140% of base (20px if base=14)
        '3xl': `${Math.round(base * 1.6)}px`,     // 160% of base (22px if base=14)
        '4xl': `${Math.round(base * 1.7)}px`,     // 170% of base (24px if base=14)
        '5xl': `${Math.round(base * 1.9)}px`,     // 190% of base (26px if base=14)
        '6xl': `${Math.round(base * 2.0)}px`,     // 200% of base (28px if base=14)

        // Semantic sizes - DIRECT SCALING
        tiny: `${Math.round(base * 0.5)}px`,      // 50% of base
        small: `${Math.round(base * 0.7)}px`,     // 70% of base
        body: `${Math.round(base * 0.9)}px`,      // 90% of base
        large: `${Math.round(base * 1.2)}px`,     // 120% of base
        xlarge: `${Math.round(base * 1.4)}px`,    // 140% of base
        xxlarge: `${Math.round(base * 1.7)}px`,   // 170% of base
        
        // Semantic sizes - DIRECT SCALING
        caption: `${Math.round(base * 0.7)}px`,   // 70% of base
        label: `${Math.round(base * 0.8)}px`,     // 80% of base
        subtitle: `${Math.round(base * 1.1)}px`,  // 110% of base
        title: `${Math.round(base * 1.3)}px`,     // 130% of base
        heading: `${Math.round(base * 1.6)}px`,   // 160% of base
        pageTitle: `${Math.round(base * 1.9)}px`  // 190% of base
      },

      // Spacing scale - DIRECT SCALING
      space: {
        tiny: `${Math.round(base * 0.3)}px`,      // 30% of base (4px if base=14)
        small: `${Math.round(base * 0.5)}px`,     // 50% of base (7px if base=14)
        medium: `${Math.round(base * 0.8)}px`,    // 80% of base (11px if base=14)
        large: `${Math.round(base * 1.2)}px`,     // 120% of base (17px if base=14)
        xlarge: `${Math.round(base * 1.5)}px`,    // 150% of base (21px if base=14)
        
        // Component-specific spacing - DIRECT SCALING
        cardPadding: `${Math.round(base * 0.8)}px`,
        sectionMargin: `${Math.round(base * 1.2)}px`,
        buttonPadding: `${Math.round(base * 0.4)}px ${Math.round(base * 0.8)}px`,
        inputPadding: `${Math.round(base * 0.3)}px ${Math.round(base * 0.6)}px`
      },

      // Component styles - DIRECT SCALING
      button: {
        padding: `${Math.round(base * 0.4)}px ${Math.round(base * 0.8)}px`,
        borderRadius: `${Math.round(base * 0.2)}px`,
        fontSize: `${Math.round(base * 0.7)}px`
      },
      
      input: {
        padding: `${Math.round(base * 0.3)}px ${Math.round(base * 0.6)}px`,
        borderRadius: `${Math.round(base * 0.2)}px`,
        fontSize: `${base}px`
      },
      
      card: {
        padding: `${Math.round(base * 0.8)}px`,
        borderRadius: `${Math.round(base * 0.4)}px`,
        marginBottom: `${Math.round(base * 1.2)}px`
      },
      
      icon: {
        small: `${Math.round(base * 0.9)}px`,     // 90% of base
        medium: `${Math.round(base * 1.2)}px`,    // 120% of base
        large: `${Math.round(base * 1.5)}px`      // 150% of base
      }
    };
  });

  return scaling;
}