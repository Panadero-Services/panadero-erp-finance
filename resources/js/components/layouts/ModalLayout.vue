<script setup>
import { ref, computed } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  record: {
    type: Object,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  timeAgo: {
    type: String,
    required: true
  },
  initials: {
    type: String,
    required: true
  },
  id: {
    type: [String, Number],
    required: true
  },
  tabs: {
    type: Array,
    default: () => []
  },
  activeTab: {
    type: String,
    required: true
  },
  modalSize: {
    type: String,
    default: 'standard'
  },
  fontSize: {
    type: String,
    default: 'sm'
  },
  showRawData: {
    type: Boolean,
    default: false
  },
  statusFlags: {
    type: Array,
    default: () => []
  },
  relatedLinks: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits([
  'close',
  'update:activeTab',
  'update:modalSize',
  'update:fontSize',
  'update:showRawData',
  'navigate'
]);

// Size configurations
const sizeConfig = {
  standard: { width: 'max-w-2xl', height: 'h-[600px]' },
  large: { width: 'max-w-6xl', height: 'h-[800px]' },
  full: { width: 'max-w-[95vw]', height: 'h-[95vh]' }
};

// Font size configurations
const fontSizeConfig = {
  xxxs: 'text-[10px]',
  xxs: 'text-[11px]',
  xs: 'text-xs',
  sm: 'text-sm',
  base: 'text-base',
  lg: 'text-lg',
  xl: 'text-xl'
};

// Size options for popup
const sizeOptions = [
  { id: 'standard', name: 'Standard' },
  { id: 'large', name: 'Large' },
  { id: 'full', name: 'Full' }
];

// Font size options for popup
const fontSizeOptions = [
  { id: 'xxxs', name: 'XXXS' },
  { id: 'xxs', name: 'XXS' },
  { id: 'xs', name: 'XS' },
  { id: 'sm', name: 'SM' },
  { id: 'base', name: 'Base' },
  { id: 'lg', name: 'LG' },
  { id: 'xl', name: 'XL' }
];

// Popup states
const showSizePopup = ref(false);
const showFontPopup = ref(false);

// Computed for modal classes
const modalClasses = computed(() => ({
  width: sizeConfig[props.modalSize].width,
  height: sizeConfig[props.modalSize].height
}));

// Computed for font size class
const fontSizeClass = computed(() => fontSizeConfig[props.fontSize]);

// Close popups when clicking outside
const closePopups = () => {
  showSizePopup.value = false;
  showFontPopup.value = false;
};

function isJsonLike(val) {
  if (val === null || val === undefined) return false;
  if (typeof val === 'object') return true;
  if (typeof val === 'string') {
    try {
      const parsed = JSON.parse(val);
      return typeof parsed === 'object' && parsed !== null;
    } catch (e) {
      return false;
    }
  }
  return false;
}

function prettyJson(val) {
  if (typeof val === 'object' && val !== null) {
    return JSON.stringify(val, null, 2);
  }
  if (typeof val === 'string') {
    try {
      const parsed = JSON.parse(val);
      return JSON.stringify(parsed, null, 2);
    } catch (e) {
      return val;
    }
  }
  return val;
}

// Add type detection functions
function getFieldType(value) {
  if (value === null || value === undefined) return 'empty';
  if (typeof value === 'object') return 'object';
  if (typeof value === 'number') return 'number';
  if (typeof value === 'boolean') return 'boolean';
  if (typeof value === 'string') {
    // Check if it's a date string
    if (/^\d{4}-\d{2}-\d{2}/.test(value) || /^\d{2}\/\d{2}\/\d{4}/.test(value)) {
      return 'datetime';
    }
    // Check if it's a JSON string
    try {
      JSON.parse(value);
      return 'json';
    } catch (e) {
      return 'varchar';
    }
  }
  return 'empty';
}

// Add computed property for sorted fields
const sortedRecordFields = computed(() => {
  const typeOrder = {
    'id': 0,
    'number': 1,
    'boolean': 2,
    'datetime': 3,
    'varchar': 4,
    'json': 5,
    'object': 6,
    'empty': 7,
    'other': 8
  };

  return Object.entries(props.record).sort(([keyA, valueA], [keyB, valueB]) => {
    const typeA = getFieldType(valueA);
    const typeB = getFieldType(valueB);
    return typeOrder[typeA] - typeOrder[typeB];
  });
});

// Add computed property for props definition
const propsDefinition = computed(() => ({
  record: { type: 'Object', required: true },
  title: { type: 'String', required: true },
  timeAgo: { type: 'String', required: true },
  initials: { type: 'String', required: true },
  id: { type: '[String, Number]', required: true },
  tabs: { type: 'Array', default: '[]' },
  activeTab: { type: 'String', required: true },
  modalSize: { type: 'String', default: "'standard'" },
  fontSize: { type: 'String', default: "'sm'" },
  showRawData: { type: 'Boolean', default: 'false' },
  statusFlags: { type: 'Array', default: '[]' },
  relatedLinks: { type: 'Array', default: '[]' }
}));
</script>

<template>
  <div>
    <!-- Modal backdrop -->
    <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-80" @click="$emit('close')"></div>
    
    <!-- Modal container with PostCard styling -->
    <div :class="[
      'z-30 fixed top-1/2 left-1/2 w-full transform -translate-x-1/2 -translate-y-1/2',
      modalClasses.width,
      modalClasses.height
    ]">
      <div class="relative block w-full h-full">
        <div class="p-2 h-full opacity-90 from-gray-950/50 via-transparent rounded-sm shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-300 focus:outline focus:outline-2 focus:outline-purple-500 bg-white text-gray-600 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300" @click="closePopups">
          <div class="flex flex-col h-full min-h-24 p-1 sm:px-6 sm:pt-3 tracking-tight leading-4">
            
            <!-- Header section with close button -->
            <div class="flex items-center mb-1 w-full">
              <div class="h-12 w-12 bg-blue-200 dark:bg-blue-900 flex items-center justify-center rounded-full mr-4 flex-shrink-0 absolute top-3 -mt-8 left-1">
                <div class="text-blue-600 dark:text-blue-300 font-bold text-base absolute top-2">{{ initials }}</div>
              </div>
              <div class="flex-1 text-center sm:text-lg">
                <h3 class="line-clamp-1 font-semibold text-gray-900 dark:text-white">
                  {{ title }}
                </h3>
                <p class="text-xxxs sm:text-xxs text-gray-500 dark:text-gray-500">
                  Updated {{ timeAgo }}
                </p>
              </div>
              <!-- Close button -->
              <button
                @click="$emit('close')"
                class="absolute top-2 right-2 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>

            <!-- Tabs -->
            <div class="flex mb-2 w-full border-b border-gray-200 dark:border-gray-700 grid grid-cols-3">
              <div class="flex flex-wrap col-span-2">
                <button 
                  v-for="tab in tabs"
                  :key="tab"
                  @click="$emit('update:activeTab', tab)"
                  :class="[
                    'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                    activeTab === tab 
                      ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
                  ]"
                >
                  {{ tab }}
                </button>
              </div>
            </div>

            <!-- Content area -->
            <div class="flex-1 w-full overflow-auto">
    

            <!-- Record Tab -->
              <div v-if="activeTab === 'record'" class="flex w-full">
                <div v-if="showRawData" :class="[
                  'text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto font-mono',
                  fontSizeClass
                ]">
                  {{ record }}
                </div>
                <div v-else class="grid gap-2 w-full" :class="{
                  'grid-cols-2': modalSize === 'standard',
                  'grid-cols-3': modalSize === 'large',
                  'grid-cols-4': modalSize === 'full'
                }">
                  <!-- Props Section -->
                  <div class="col-span-full mb-2">
                    <h3 :class="['font-medium text-gray-900 dark:text-white mb-2', fontSizeClass]">Component Props</h3>
                    <div class="grid gap-2" :class="{
                      'grid-cols-2': modalSize === 'standard',
                      'grid-cols-3': modalSize === 'large',
                      'grid-cols-4': modalSize === 'full'
                    }">
                      <div v-for="(prop, key) in propsDefinition" :key="key" class="flex items-start p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                        <div class="flex-1">
                          <p :class="['font-medium text-blue-700 dark:text-blue-400 capitalize', fontSizeClass]">
                            {{ key }}
                          </p>
                          <div :class="['text-gray-500 dark:text-gray-400 mt-1 break-words', fontSizeClass]">
                            <div>Type: {{ prop.type }}</div>
                            <div>Required: {{ prop.required ? 'Yes' : 'No' }}</div>
                            <div v-if="prop.default">Default: {{ prop.default }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Record Fields -->
                  <div v-for="[key, value] in sortedRecordFields" :key="key" class="flex items-start p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                      <p :class="['font-medium text-blue-700 dark:text-blue-400 capitalize', fontSizeClass]">
                        {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                      </p>
                      <div :class="['text-gray-500 dark:text-gray-400 mt-1 break-words', fontSizeClass]">
                        <template v-if="isJsonLike(value)">
                          <pre :class="['bg-gray-100 dark:bg-gray-800 rounded p-2 overflow-x-auto', fontSizeClass]">{{ prettyJson(value) }}</pre>
                        </template>
                        <template v-else>
                          {{ value === undefined || value === null || value === '' ? 'N/A' : value }}
                        </template>
                      </div>
                    </div>
                  </div>
                </div>
              </div>



              <!-- Content Tab -->
              <div v-if="activeTab === 'content'" class="flex">
                <slot></slot>
              </div>

            



              <!-- Status Tab -->
              <div v-else-if="activeTab === 'status'" class="space-y-2">
                <div class="grid sm:grid-cols-2 gap-2">
                  <div v-for="(flag, idx) in statusFlags" 
                       :key="idx" 
                       :class="['flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700', fontSizeClass]">
                    <span class="font-medium text-blue-700 dark:text-blue-400">{{ flag.label }}:</span>
                    <span :class="[
                      'px-2 py-1 rounded',
                      flag.active 
                        ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' 
                        : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400'
                    ]">
                      {{ flag.active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Details Tab -->
              <div v-else-if="activeTab === 'details'" :class="['grid grid-cols-1 gap-2', fontSizeClass]">
                <slot></slot>
              </div>

              <!-- Relations Tab -->
              <div v-else-if="activeTab === 'relations'" :class="['space-y-2', fontSizeClass]">
                <!-- Debug info (only when raw data is shown) -->
                <div v-if="showRawData && relatedLinks.length > 0" class="mb-2 p-2 bg-blue-50 dark:bg-blue-900/20 text-xs text-blue-800 dark:text-blue-200 rounded border">
                  Raw JSON: {{ JSON.stringify(relatedLinks).substring(0, 200) }}{{ JSON.stringify(relatedLinks).length > 200 ? '...' : '' }}
                  <br>Parsed links count: {{ relatedLinks.length }}
                </div>
                
                <div v-if="relatedLinks.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4">
                  <div>No relations found</div>
                </div>
                <div v-else class="grid gap-2" :class="modalSize=='standard' ? 'grid-cols-2' : 'grid-cols-3'">
                  <div v-for="(link, index) in relatedLinks" 
                       :key="index" 
                       class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors cursor-pointer"
                       @click="$emit('navigate', link)">
                    <div class="flex items-center space-x-3">
                      <div class="h-8 w-8 bg-green-200 dark:bg-green-900 flex items-center justify-center rounded-full flex-shrink-0">
                        <span class="text-green-600 dark:text-green-300 font-bold text-xs">{{ link.link_id }}</span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="font-medium text-blue-700 dark:text-blue-400 truncate">
                          {{ link.link_title }}
                        </p>
                        <p class="text-gray-500 dark:text-gray-400">
                          Type: {{ (link.type || 'unknown').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                        </p>
                        <!-- Debug info (remove in production) -->
                        <p v-if="showRawData" class="text-xs text-gray-400 font-mono mt-1">
                          Raw: {{ JSON.stringify(link).substring(0, 60) }}...
                        </p>
                      </div>
                    </div>
                    <div class="text-gray-400 dark:text-gray-500 flex-shrink-0">
                      →
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="mt-auto relative">
              <div class="flex items-center justify-between w-full mt-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <!-- Left side: ID -->
                <div class="text-xs text-gray-500 dark:text-gray-500">
                  ID: {{ id }} 
                </div>
                
                <!-- Center: Controls -->
                <div class="flex items-center space-x-2">
                  <!-- Screen Size Button -->
                  <div class="relative">
                    <button 
                      @click.stop="showSizePopup = !showSizePopup"
                      class="px-3 py-1.5 text-xs rounded-md ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 transition-colors"
                    >
                      Size: {{ sizeOptions.find(s => s.id === modalSize)?.name }}
                    </button>
                    
                    <!-- Size Popup -->
                    <div v-if="showSizePopup" @click.stop class="absolute bottom-full mb-2 left-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg p-2 z-50 min-w-[120px]">
                      <div class="space-y-1">
                        <button
                          v-for="option in sizeOptions"
                          :key="option.id"
                          @click="$emit('update:modalSize', option.id); showSizePopup = false"
                          :class="[
                            'w-full px-3 py-1.5 text-xs rounded-md ring-1 ring-inset transition-colors text-left',
                            modalSize === option.id
                              ? 'bg-blue-50 text-blue-700 ring-blue-300 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-600'
                              : 'text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400'
                          ]"
                        >
                          {{ option.name }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Font Size Button -->
                  <div class="relative">
                    <button 
                      @click.stop="showFontPopup = !showFontPopup"
                      class="px-3 py-1.5 text-xs rounded-md ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 transition-colors"
                    >
                      Text: {{ fontSizeOptions.find(f => f.id === fontSize)?.name }}
                    </button>
                    
                    <!-- Font Size Popup -->
                    <div v-if="showFontPopup" @click.stop class="absolute bottom-full mb-2 left-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg p-2 z-50 min-w-[100px]">
                      <div class="space-y-1">
                        <button
                          v-for="option in fontSizeOptions"
                          :key="option.id"
                          @click="$emit('update:fontSize', option.id); showFontPopup = false"
                          :class="[
                            'w-full px-3 py-1.5 text-xs rounded-md ring-1 ring-inset transition-colors text-left',
                            fontSize === option.id
                              ? 'bg-green-50 text-green-700 ring-green-300 dark:bg-green-900/20 dark:text-green-400 dark:ring-green-600'
                              : 'text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400'
                          ]"
                        >
                          {{ option.name }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Raw Data Toggle -->
                  <button 
                    @click="$emit('update:showRawData', !showRawData)"
                    :class="[
                      'px-3 py-1.5 text-xs rounded-md ring-1 ring-inset transition-colors',
                      showRawData 
                        ? 'bg-yellow-50 text-yellow-700 ring-yellow-300 dark:bg-yellow-900/20 dark:text-yellow-400 dark:ring-yellow-600' 
                        : 'text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400'
                    ]"
                  >
                    {{ showRawData ? 'Raw' : 'HTML' }}
                  </button>
                </div>
                
                <!-- Right side: Close button -->
                <button 
                  class="px-3 py-1.5 text-xs rounded-md ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 transition-colors" 
                  @click="$emit('close')"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 