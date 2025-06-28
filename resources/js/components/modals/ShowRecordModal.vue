<script setup lang="ts">
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { RadioGroup, RadioGroupOption, RadioGroupLabel } from '@headlessui/vue';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';

// Props
const props = defineProps({
  lng: String,
  record: Object,
  module: String,
  table: String,
  superSelfAdmin: Boolean,
  db: Object,
  // Settings preservation
  preservedModalSize: String,
  preservedFontSize: String,
  preservedActiveTab: String,
});

const emit = defineEmits(['close', 'changeRecord']);

// Active tab state
const activeTab = ref(props.preservedActiveTab || 'content');

// Modal size and display options
const modalSize = ref(props.preservedModalSize || 'standard');
const showRawData = ref(false);
const fontSize = ref(props.preservedFontSize || 'sm');

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
  width: sizeConfig[modalSize.value].width,
  height: sizeConfig[modalSize.value].height
}));

// Computed for font size classes
const fontSizeClass = computed(() => fontSizeConfig[fontSize.value]);

// Computed for formatted body
const formattedBody = computed(() => {
  if (!props.record.body) return 'No content';
  if (showRawData.value) {
    return props.record.body;
  }
  return props.record.body.replaceAll('.','.<br>');
});

// Computed for initials (First and Last name initials)
const initials = computed(() => {
  if (!props.record.title) return 'R';
  const words = props.record.title.trim().split(' ');
  if (words.length >= 2) {
    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
  }
  return words[0].charAt(0).toUpperCase();
});

// Computed for status flags
const statusFlags = computed(() => [
  { key: 'is_active', label: 'Active', active: props.record.is_active == 1 },
  { key: 'is_archived', label: 'Archived', active: props.record.is_archived == 1 },
  { key: 'is_featured', label: 'Featured', active: props.record.is_featured == 1 },
  { key: 'is_published', label: 'Published', active: props.record.is_published == 1 },
  { key: 'is_public', label: 'Public', active: props.record.is_public == 1 },
  { key: 'is_locked', label: 'Locked', active: props.record.is_locked == 1 }
]);

// Get all displayable fields (excluding internal fields)
const displayFields = computed(() => {
  if (!props.record) return [];
  return Object.keys(props.record).filter(key => 
    !['validation_rules', 'form_fields', 'links_table', 'titleColumn', 'idColumn'].includes(key)
  );
});

// Computed for related posts/links
const links = computed(() => {
  try {
    if (!props.record.links) return [];
    const parsedLinks = JSON.parse(props.record.links);
    
    // Ensure each link has the necessary properties, handle both post_id/post_title and link_id/link_title formats
    return parsedLinks.map(link => ({
      link_id: link.link_id || link.post_id || link.id || 'Unknown',
      link_title: link.link_title || link.post_title || link.title || link.name || `Record ${link.link_id || link.post_id || link.id}`,
      type: link.type || 'relates_to',
      ...link // spread original properties
    }));
  } catch {
    return [];
  }
});

// Computed for content sections (split by periods for indexing)
const contentSections = computed(() => {
  if (!props.record.body) return [];
  
  // Split by periods, but be more intelligent about it
  const sentences = props.record.body
    .split(/\.(?=\s|$)/) // Split on periods followed by space or end of string
    .filter(sentence => sentence.trim().length > 10) // Only include meaningful sentences
    .map(sentence => sentence.trim());
  
  return sentences.map((sentence, index) => ({
    id: `section-${index}`,
    title: sentence.substring(0, 50) + (sentence.length > 50 ? '...' : ''),
    content: sentence,
    index: index + 1
  }));
});

// Computed to check if we should show content index (large or full mode)
const showContentIndex = computed(() => {
  const isLargeOrFull = modalSize.value === 'large' || modalSize.value === 'full';
  const hasMultipleSections = contentSections.value.length > 1;
  return isLargeOrFull && hasMultipleSections;
});

// Method to navigate to related record
const navigateToRelated = (link) => {
  console.log('Navigating to related record:', link);
  
  // Ensure we have the necessary data, handle both post_id and link_id formats
  const linkId = link.link_id || link.post_id || link.id;
  const linkType = link.type || 'relates_to';
  
  if (!linkId) {
    console.error('Cannot navigate: missing link ID', link);
    return;
  }
  
  emit('changeRecord', { 
    type: linkType, 
    id: linkId,
    title: link.link_title || link.post_title || link.title || link.name,
    // Preserve current modal settings
    preservedModalSize: modalSize.value,
    preservedFontSize: fontSize.value,
    preservedActiveTab: activeTab.value
  });
};

// Method to scroll to content section
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

// Close popups when clicking outside
const closePopups = () => {
  showSizePopup.value = false;
  showFontPopup.value = false;
};
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
          <div class="p-2 h-full opacity-90 from-gray-950/50 via-transparent rounded-xs shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500 bg-white text-gray-600 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300" @click="closePopups">
          <div class="flex flex-col h-full min-h-24 p-1 sm:px-6 sm:pt-3 tracking-tight leading-4">
            
            <!-- Header section with close button -->
            <div class="flex items-center mb-1 w-full">
              <div class="h-12 w-12 bg-blue-200 dark:bg-blue-900 flex items-center justify-center rounded-full mr-4 flex-shrink-0 absolute top-3 -mt-8 left-1">
                <div class="text-blue-600 dark:text-blue-300 font-bold text-base absolute top-2">{{ initials }}</div>
              </div>
              <div class="flex-1 text-center sm:text-lg">
                <h3 class="line-clamp-1 font-semibold text-gray-900 dark:text-white">
                  {{ record.title || record.name || 'Record Details' }}
                </h3>
                <p class="text-xxxs sm:text-xxs text-gray-500 dark:text-gray-500">
                  Updated {{ formatDistance(new Date(record.updated_at || Date.now()), new Date()) }} ago
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
            <div class="flex mb-2 w-full border-b border-gray-200 dark:border-gray-700 grid grid-cols-2">
              <div class="flex flex-wrap">
                <button 
                  @click="activeTab = 'content'"
                  :class="[
                    'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                    activeTab === 'content' 
                      ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
                  ]"
                >
                  Contentac
                </button>
                <button 
                  @click="activeTab = 'status'"
                  :class="[
                    'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                    activeTab === 'status' 
                      ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
                  ]"
                >
                  Status
                </button>
                <button 
                  @click="activeTab = 'details'"
                  :class="[
                    'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                    activeTab === 'details' 
                      ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
                  ]"
                >
                  Details
                </button>
                <button 
                  v-if="links.length > 0"
                  @click="activeTab = 'relations'"
                  :class="[
                    'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                    activeTab === 'relations' 
                      ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
                  ]"
                >
                  Relations ({{ links.length }})
                </button>
              </div>
              <div class="justify-end text-right">
                <div class="flex space-x-2 justify-end mt-3">
                  <CategorySectionIcon 
                    icon="FingerPrint" 
                    :activated="record.is_active == 1" 
                    :error="false" 
                    title="is active"
                  />
                  <CategorySectionIcon 
                    icon="LockClosed" 
                    :activated="record.is_locked == 1" 
                    :error="false" 
                    title="is locked"
                  />
                  <CategorySectionIcon 
                    icon="Fire" 
                    :activated="record.is_featured == 1" 
                    :error="false" 
                    title="is featured"
                  />
                  <CategorySectionIcon 
                    icon="Heart" 
                    :activated="record.is_published == 1" 
                    :error="false" 
                    title="is published"
                  />
                  <CategorySectionIcon 
                    icon="LockOpen" 
                    :activated="record.is_public == 1" 
                    :error="false" 
                    title="is public"
                  />
                  <CategorySectionIcon 
                    icon="Tv" 
                    :activated="record.is_archived == 1" 
                    :error="false" 
                    title="is archived"
                  />
                </div>
              </div>
            </div>

            <!-- Content area -->
            <div class="flex-1 w-full overflow-auto">
              <!-- Content Tab -->
              <div v-if="activeTab === 'content'" :class="[showContentIndex ? 'flex space-x-4' : 'space-y-2']">
                <!-- Debug info (remove in production) -->
                <div v-if="showRawData" class="mb-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 text-xs text-yellow-800 dark:text-yellow-200 rounded border">
                  Debug: modalSize={{ modalSize }}, contentSections={{ contentSections.length }}, showContentIndex={{ showContentIndex }}
                </div>
                
                <!-- Main content area -->
                <div :class="[showContentIndex ? 'flex-1' : 'w-full']">
                  <div v-if="showRawData" :class="[
                    'text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto font-mono',
                    fontSizeClass
                  ]">
                    {{ formattedBody }}
                  </div>
                  <div v-else>
                    <!-- Content with sections for indexing -->
                    <div v-if="showContentIndex" class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto">
                      <div v-for="section in contentSections" 
                           :key="section.id" 
                           :id="section.id"
                           :class="['mb-4 pb-2', fontSizeClass]">
                        <div class="text-gray-700 dark:text-gray-300">
                          {{ section.content }}<span v-if="section.index < contentSections.length">.</span>
                        </div>
                      </div>
                    </div>
                    <!-- Regular content for standard mode -->
                    <div v-else :class="[
                      'text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto',
                      fontSizeClass
                    ]" v-html="formattedBody">
                    </div>
                  </div>
                </div>
                
                <!-- Content Index Sidebar (only in large/full mode) -->
                <div v-if="showContentIndex" class="w-80 flex-shrink-0">

                  <div class=" p-1 sticky top-0">
                    <div class="space-y-1">
                      
                     
                     
                  <div v-for="(link, index) in links" 
                       :key="index" 
                       class="flex items-center justify-between p-3 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors cursor-pointer"
                       @click="navigateToRelated(link)">
                    <div class="flex items-center space-x-3">
                      <div class="min-w-0 flex-1">
                        <p :class="['font-medium text-blue-700 dark:text-blue-400 truncate text-xs', fontSizeClass]">
                          {{ link.link_title }}
                        </p>
                        <p :class="['text-gray-500 dark:text-gray-400 text-xxxs', fontSizeClass]">
                           {{ (link.type || 'unknown').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                        </p>
                        <!-- Debug info (remove in production) -->
                        <p v-if="showRawData" :class="['text-xs text-gray-400 font-mono mt-1']">
                          Raw: {{ JSON.stringify(link).substring(0, 60) }}...
                        </p>
                      </div>
                    </div>
                    <div :class="['text-gray-400 dark:text-gray-500 flex-shrink-0', fontSizeClass]">
                      →
                    </div>
                  </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Status Tab -->
              <div v-else-if="activeTab === 'status'" class="space-y-2">
                <div class="grid sm:grid-cols-2 gap-2">
                  <div v-for="(flag, idx) in statusFlags" 
                       :key="idx" 
                       class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                    <span :class="['font-medium text-blue-700 dark:text-blue-400', fontSizeClass]">{{ flag.label }}:</span>
                    <span :class="[
                      'px-2 py-1 rounded',
                      fontSizeClass,
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
              <div v-else-if="activeTab === 'details'" class="grid grid-cols-2 gap-2">
                <div v-for="field in displayFields" 
                     :key="field" 
                     class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                  <div class="flex-1">
                    <p :class="['font-medium text-blue-700 dark:text-blue-400 capitalize', fontSizeClass]">{{ field.replace('_', ' ') }}</p>
                    <p v-if="showRawData" :class="['text-gray-500 dark:text-gray-400 mt-1 break-words font-mono', fontSizeClass]">
                      {{ record[field] || 'N/A' }}
                    </p>
                    <p v-else :class="['text-gray-500 dark:text-gray-400 mt-1 break-words', fontSizeClass]">
                      {{ record[field] || 'N/A' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Relations Tab -->
              <div v-else-if="activeTab === 'relations'" class="space-y-2">
                <!-- Debug info (only when raw data is shown) -->
                <div v-if="showRawData && record.links" class="mb-2 p-2 bg-blue-50 dark:bg-blue-900/20 text-xs text-blue-800 dark:text-blue-200 rounded border">
                  Raw JSON: {{ record.links.substring(0, 200) }}{{ record.links.length > 200 ? '...' : '' }}
                  <br>Parsed links count: {{ links.length }}
                </div>
                
                <div v-if="links.length === 0" :class="['text-center text-gray-500 dark:text-gray-400 p-4', fontSizeClass]">
                  <div>No relations found</div>
                  <div v-if="record.links" class="mt-2 text-xs opacity-75">
                    Raw links data: {{ record.links.substring(0, 100) }}{{ record.links.length > 100 ? '...' : '' }}
                  </div>
                </div>
                <div v-else class="grid gap-2">
                  <div v-for="(link, index) in links" 
                       :key="index" 
                       class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors cursor-pointer"
                       @click="navigateToRelated(link)">
                    <div class="flex items-center space-x-3">
                      <div class="h-8 w-8 bg-green-200 dark:bg-green-900 flex items-center justify-center rounded-full flex-shrink-0">
                        <span :class="['text-green-600 dark:text-green-300 font-bold text-xs']">{{ link.link_id }}</span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <p :class="['font-medium text-blue-700 dark:text-blue-400 truncate', fontSizeClass]">
                          {{ link.link_title }}
                        </p>
                        <p :class="['text-gray-500 dark:text-gray-400', fontSizeClass]">
                          Type: {{ (link.type || 'unknown').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                        </p>
                        <!-- Debug info (remove in production) -->
                        <p v-if="showRawData" :class="['text-xs text-gray-400 font-mono mt-1']">
                          Raw: {{ JSON.stringify(link).substring(0, 60) }}...
                        </p>
                      </div>
                    </div>
                    <div :class="['text-gray-400 dark:text-gray-500 flex-shrink-0', fontSizeClass]">
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
                  ID: {{ record.id }}
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
                          @click="modalSize = option.id; showSizePopup = false"
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
                          @click="fontSize = option.id; showFontPopup = false"
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
                    @click="showRawData = !showRawData"
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
