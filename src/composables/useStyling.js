import { ref, computed } from 'vue';

export function useStyling() {
  // Base scale - user just changes this number
  const baseSize = ref(14); // px
  
  // Reactive font sizes that update when base changes
  const fontSizes = computed(() => {
    const base = baseSize.value;
    
    // Map base size to custom Tailwind classes (1px increments)
    const getTailwindClass = (size) => {
      // Use our custom 1px increment classes
      if (size >= 8 && size <= 24) {
        return `text-${size}`; // text-14, text-15, text-16, etc.
      }
      // Fallback to standard Tailwind classes for extreme values
      if (size < 8) return 'text-8';
      if (size > 24) return 'text-24';
      return 'text-14'; // default
    };
    
    return {
      base: base,
      text: getTailwindClass(base),           // base (14px) → text-14
      title: getTailwindClass(base + 2),      // base + 2 (16px) → text-16
      subtitle: getTailwindClass(base + 4),   // base + 4 (18px) → text-18
      heading: getTailwindClass(base + 6),    // base + 6 (20px) → text-20
      button: getTailwindClass(base - 2),     // base - 2 (12px) → text-12
      label: getTailwindClass(base - 2),      // base - 2 (12px) → text-12
      input: getTailwindClass(base),          // base (14px) → text-14
      table: getTailwindClass(base - 2),      // base - 2 (12px) → text-12
      amount: getTailwindClass(base + 2),     // base + 2 (16px) → text-16
      description: getTailwindClass(base - 2) // base - 2 (12px) → text-12
    };
  });

  // Simple spacing utilities
  const spacing = {
    padding: 'p-6',
    paddingSmall: 'p-4', 
    paddingLarge: 'p-8',
    margin: 'mb-6',
    marginSmall: 'mb-4',
    marginLarge: 'mb-8',
    gap: 'gap-4',
    gapSmall: 'gap-2',
    gapLarge: 'gap-6'
  };

  // Simple border styles
  const borders = {
    default: 'border',
    rounded: 'rounded-lg',
    roundedSmall: 'rounded-md'
  };

  // Simple button styles (no dark mode - use darkMode.js for that)
  const buttons = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700',
    secondary: 'bg-gray-600 text-white hover:bg-gray-700',
    success: 'bg-green-600 text-white hover:bg-green-700',
    danger: 'bg-red-600 text-white hover:bg-red-700',
    warning: 'bg-yellow-600 text-white hover:bg-yellow-700'
  };

  // Simple form styles (no dark mode - use darkMode.js for that)
  const forms = {
    input: 'w-full border rounded p-2',
    label: 'block text-sm font-medium mb-2',
    select: 'w-full border rounded p-2',
    textarea: 'w-full border rounded p-2 h-20'
  };

  // Helper functions
  const increaseFontSize = () => {
    baseSize.value = Math.min(baseSize.value + 1, 24); // Max 24px
    return baseSize.value;
  };

  const decreaseFontSize = () => {
    baseSize.value = Math.max(baseSize.value - 1, 8); // Min 8px
    return baseSize.value;
  };

  const getFontSize = (type) => {
    return fontSizes.value[type] || fontSizes.value.text;
  };

  return { 
    fontSizes, 
    spacing, 
    borders, 
    buttons, 
    forms,
    increaseFontSize,
    decreaseFontSize,
    getFontSize
  };
}
