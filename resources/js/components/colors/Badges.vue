<script setup>
import { computed } from 'vue';
import { 
  DocumentCheckIcon,
  GlobeAltIcon,
  StarIcon,
  LockClosedIcon,
  UserIcon,
  LightBulbIcon,
  CheckCircleIcon,
  ArchiveBoxIcon
} from '@heroicons/vue/24/solid'

// Props
const props = defineProps({
  status: {
    type: String,
    required: true
  },
  statusMapping: {
    type: Object,
    default: () => ({})
  },
  size: {
    type: String,
    default: 'sm', // 'xxxs', 'xxs', 'xs', 'sm', 'md', 'lg'
    validator: (value) => ['xxxs', 'xxs', 'xs', 'sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'badge', // 'badge', 'pill', 'tag', 'color'
    validator: (value) => ['badge', 'pill', 'tag', 'color'].includes(value)
  },
  showIcon: {
    type: Boolean,
    default: false
  },
  customText: {
    type: String,
    default: null
  }
});

// Color variables for status mapping
const statusColors = {
  // Primary Colors
  'blue': 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-blue-600/20 dark:ring-blue-400/20',
  'red': 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/20 dark:ring-red-400/20',
  'green': 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
  'yellow': 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20',
  
  // Rich Colors
  'indigo': 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 ring-indigo-600/20 dark:ring-indigo-400/20',
  'purple': 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 ring-purple-600/20 dark:ring-purple-400/20',
  'violet': 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 ring-violet-600/20 dark:ring-violet-400/20',
  'fuchsia': 'bg-fuchsia-50 dark:bg-fuchsia-900/20 text-fuchsia-700 dark:text-fuchsia-300 ring-fuchsia-600/20 dark:ring-fuchsia-400/20',
  'pink': 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 ring-pink-600/20 dark:ring-pink-400/20',
  'rose': 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 ring-rose-600/20 dark:ring-rose-400/20',
  
  // Nature Colors
  'emerald': 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20 dark:ring-emerald-400/20',
  'teal': 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 ring-teal-600/20 dark:ring-teal-400/20',
  'cyan': 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 ring-cyan-600/20 dark:ring-cyan-400/20',
  'sky': 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 ring-sky-600/20 dark:ring-sky-400/20',
  'lime': 'bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-300 ring-lime-600/20 dark:ring-lime-400/20',
  'amber': 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 ring-amber-600/20 dark:ring-amber-400/20',
  
  // Warm Colors
  'orange': 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 ring-orange-600/20 dark:ring-orange-400/20',
  
  // Monochrome
  'slate': 'bg-slate-50 dark:bg-slate-900/20 text-slate-700 dark:text-slate-300 ring-slate-600/20 dark:ring-slate-400/20',
  'zinc': 'bg-zinc-50 dark:bg-zinc-900/20 text-zinc-700 dark:text-zinc-300 ring-zinc-600/20 dark:ring-zinc-400/20',
  'gray': 'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-400/20',
  'neutral': 'bg-neutral-50 dark:bg-neutral-900/20 text-neutral-700 dark:text-neutral-300 ring-neutral-600/20 dark:ring-neutral-400/20',
  'stone': 'bg-stone-50 dark:bg-stone-900/20 text-stone-700 dark:text-stone-300 ring-stone-600/20 dark:ring-stone-400/20',

  // Fallbacks for additional colors (using closest Tailwind colors)
  'coral': 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 ring-red-700/20 dark:ring-red-300/20',
  'crimson': 'bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-200 ring-rose-700/20 dark:ring-rose-300/20',
  'maroon': 'bg-red-200 dark:bg-red-900/40 text-red-900 dark:text-red-100 ring-red-800/20 dark:ring-red-200/20',
  'sienna': 'bg-orange-200 dark:bg-orange-900/40 text-orange-900 dark:text-orange-100 ring-orange-800/20 dark:ring-orange-200/20',
  'azure': 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 ring-sky-600/20 dark:ring-sky-400/20',
  'cerulean': 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 ring-blue-700/20 dark:ring-blue-300/20',
  'sapphire': 'bg-blue-200 dark:bg-blue-900/40 text-blue-900 dark:text-blue-100 ring-blue-800/20 dark:ring-blue-200/20',
  'turquoise': 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 ring-teal-600/20 dark:ring-teal-400/20',
  'aqua': 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200 ring-cyan-700/20 dark:ring-cyan-300/20',
  'brown': 'bg-amber-200 dark:bg-amber-900/40 text-amber-900 dark:text-amber-100 ring-amber-800/20 dark:ring-amber-200/20',
  'bronze': 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 ring-orange-700/20 dark:ring-orange-300/20',
  'copper': 'bg-orange-200 dark:bg-orange-900/40 text-orange-900 dark:text-orange-100 ring-orange-800/20 dark:ring-orange-200/20',
  'sepia': 'bg-yellow-200 dark:bg-yellow-900/40 text-yellow-900 dark:text-yellow-100 ring-yellow-800/20 dark:ring-yellow-200/20',
  'umber': 'bg-amber-300 dark:bg-amber-900/50 text-amber-900 dark:text-amber-100 ring-amber-800/20 dark:ring-amber-200/20',
  'ruby': 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 ring-red-700/20 dark:ring-red-300/20',
  'amethyst': 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 ring-purple-700/20 dark:ring-purple-300/20',
  'topaz': 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 ring-amber-700/20 dark:ring-amber-300/20',
  'jade': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 ring-emerald-700/20 dark:ring-emerald-300/20',
  'pearl': 'bg-slate-50 dark:bg-slate-900/20 text-slate-700 dark:text-slate-300 ring-slate-600/20 dark:ring-slate-400/20'
};

// Complete status mapping with all the statuses you specified
const defaultStatusMap = {
  'idle': 'gray',
  'in_progress': 'blue',
  'completed': 'green',
  'deprecated': 'red',
  'featured': 'purple',
  'active': 'green',
  'inactive': 'red',
  'pending': 'yellow',
  'failed': 'red',
  'processing': 'blue',
  'draft': 'gray',
  'published': 'green',
  'archived': 'gray',
  'deleted': 'red',
  'suspended': 'orange',
  'approved': 'green',
  'rejected': 'red',
  'under_review': 'yellow',
  'maintenance': 'orange',
  'beta': 'purple',
  'alpha': 'pink',
  'experimental': 'cyan',
  'stable': 'green',
  'unstable': 'red',
  'testing': 'yellow',
  'production': 'green',
  'development': 'blue',
  'staging': 'orange',
  'blocked': 'red',
  'ready': 'green',
  'incomplete': 'yellow',
  'verified': 'green',
  'unverified': 'gray',
  'critical': 'red',
  'warning': 'yellow',
  'info': 'blue',
  'success': 'green',
  'error': 'red'
};

// Size classes - reduced y-padding with scaled minimum width
const sizeClasses = {
  xxxs: 'px-0.5 py-0 text-[8px]', // Very small text
  xxs: 'px-1 py-0 text-[10px]', 
  xs: 'px-1.5 py-0 text-xs',
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-3 py-1 text-sm',
  lg: 'px-4 py-1.5 text-base'
};

// Minimum width values - start with 40px, increase by 4px per size
const minWidthValues = {
  xxxs: '40px',
  xxs: '44px', 
  xs: '48px',
  sm: '52px',
  md: '56px',
  lg: '60px'
};

// Variant classes
const variantClasses = {
  badge: 'inline-flex items-center justify-center rounded-md ring-1 ring-inset',
  pill: 'inline-flex items-center justify-center rounded-full ring-1 ring-inset',
  tag: 'inline-flex items-center justify-center rounded border',
  color: 'inline-flex items-center bg-gray-100 dark:bg-gray-800 rounded-full pr-2 w-fit pl-1'
};

// Status icons
const statusIcons = {
  green: '✓',
  red: '✗',
  yellow: '⚠',
  blue: '⏳',
  gray: '○',
  orange: '⚡',
  purple: '★',
  pink: '♡',
  cyan: '🔬',
  teal: '🌊',
  emerald: '🌿',
  lime: '',
  amber: '🔥',
  rose: '',
  violet: '🔮',
  fuchsia: '🌟',
  slate: '🗿',
  zinc: '⚙',
  neutral: '●',
  stone: '🪨'
};

// Add this after the statusIcons mapping and before the computed properties
const booleanIcons = {
  'is_published': 'DocumentCheckIcon', // Paper with checkmark - represents published state
  'is_public': 'GlobeAltIcon', // Globe icon - represents public visibility
  'is_featured': 'StarIcon', // Star - represents featured status
  'is_locked': 'LockClosedIcon', // Lock - represents locked state
  'is_self': 'UserIcon', // User - represents self-owned/personal items
  'is_smart': 'LightBulbIcon', // Lightbulb - represents smart/intelligent features
  'is_active': 'CheckCircleIcon', // Checkmark in circle - represents active state
  'is_archived': 'ArchiveBoxIcon' // Archive box - represents archived state
};

// Add a helper function to get the icon for a boolean field
const getBooleanIcon = (fieldName) => {
  return booleanIcons[fieldName] || 'QuestionMarkCircleIcon'; // Default fallback icon
};

// Computed properties
const colorKey = computed(() => {
  // First check the provided status mapping
  const mappedColor = props.statusMapping[props.status?.toLowerCase()];
  if (mappedColor && statusColors[mappedColor]) {
    return mappedColor;
  }
  
  // Fallback to default mapping
  const defaultColor = defaultStatusMap[props.status?.toLowerCase()];
  return defaultColor || 'gray';
});

const colorClasses = computed(() => {
  return statusColors[colorKey.value] || statusColors.gray;
});

const sizeClass = computed(() => {
  return sizeClasses[props.size] || sizeClasses.sm;
});

const minWidthStyle = computed(() => {
  const value = minWidthValues[props.size] || minWidthValues.sm;
  return { 
    minWidth: value
  };
});

const variantClass = computed(() => {
  return variantClasses[props.variant] || variantClasses.badge;
});

const displayText = computed(() => {
  return props.customText || props.status;
});

const icon = computed(() => {
  if (!props.showIcon) return null;
  return statusIcons[colorKey.value] || statusIcons.gray;
});

// Expose the getStatusColor function for external use
const getStatusColor = (status, customMapping = {}) => {
  const mapping = { ...defaultStatusMap, ...customMapping };
  const colorKey = mapping[status?.toLowerCase()] || 'gray';
  return statusColors[colorKey] || statusColors.gray;
};

// Add color dot classes
const colorDotClasses = computed(() => {
  if (props.variant !== 'color') return '';
  return 'w-2 h-2 rounded-full mr-1.5 ml-0.5';
});

// Add color style
const colorStyle = computed(() => {
  if (props.variant !== 'color') return minWidthStyle.value;
  return {
    minWidth: minWidthValues[props.size] || minWidthValues.sm
  };
});

// Add color dot background style
const colorDotStyle = computed(() => {
  if (props.variant !== 'color') return {};
  return {
    backgroundColor: props.status || '#3b82f6'
  };
});

// Expose for external use
defineExpose({
  getStatusColor,
  statusColors,
  booleanIcons,
  getBooleanIcon
});
</script>

<template>
  <span 
    :class="[
      variantClass,
      sizeClass,
      props.variant !== 'color' ? colorClasses : '',
      'inline-block'
    ]"
    :style="colorStyle"
  >
    <!-- Regular badge icon -->
    <span v-if="icon && props.variant !== 'color'" class="mr-1">{{ icon }}</span>

    <!-- Color variant -->
    <template v-if="props.variant === 'color'">
      <div 
        class="w-2 h-2 rounded-full mr-1.5 ml-0.5"
        :style="colorDotStyle"
      ></div>
      <span class="text-[9px] text-gray-600 dark:text-gray-300">
        {{ displayText }}
      </span>
    </template>

    <!-- Regular badge text -->
    <template v-else>
      {{ displayText }}
    </template>
  </span>
</template> 