<script setup>
import { ref, computed, onMounted } from 'vue';
import { formatDistance } from 'date-fns';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { RadioGroup, RadioGroupOption, RadioGroupLabel } from '@headlessui/vue';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import ModalLayout from '@/components/layouts/ModalLayout.vue';
import axios from 'axios';

// Props
const props = defineProps({
  lng: String,
  record: {
    type: Object,
    required: true
  },
  module: String,
  table: String,
  superSelfAdmin: Boolean,
  db: Object,
  preservedModalSize: String,
  preservedFontSize: String,
  preservedActiveTab: String
});

const emit = defineEmits(['close', 'changeRecord']);

// State
const modelConfig = ref(null);
const loading = ref(true);
const error = ref(null);

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
const statusFlags = computed(() => {
  if (!modelConfig.value?.flags) return [];
  return modelConfig.value.flags.map(flag => ({
    key: flag,
    label: flag.replace('is_', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
    active: props.record[flag] == 1
  }));
});

// Get all displayable fields (excluding internal fields)
const displayFields = computed(() => {
  if (!modelConfig.value?.formFields) return [];
  return Object.keys(modelConfig.value.formFields).filter(key => 
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

// Fetch model configuration
const fetchModelConfig = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/model-config/${props.module}/${props.table}`);
    modelConfig.value = response.data;

    console.log(response);
    
    // Set initial active tab based on available data
    if (!activeTab.value || !['content', 'status', 'details', 'relations'].includes(activeTab.value)) {
      if (modelConfig.value?.flags?.length) {
        activeTab.value = 'status';
      } else if (modelConfig.value?.formFields) {
        activeTab.value = 'details';
      } else {
        activeTab.value = 'content';
      }
    }
  } catch (err) {
    console.error('Error fetching model config:', err);
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchModelConfig();
});
</script>

<template>
  <ModalLayout
    :record = "record" 


    :title="record.title || record.name || 'Record Details'"
    :tabs="['content', 'status', 'details', 'relations', 'config', 'record']"


    :time-ago="formatDistance(new Date(record.updated_at || Date.now()), new Date())"
    :initials="initials"
    :id="record.id"
    :active-tab="activeTab"
    :modal-size="modalSize"
    :font-size="fontSize"
    :show-raw-data="showRawData"
    :status-flags="statusFlags"
    :related-links="links"
    @update:active-tab="activeTab = $event"
    @update:modal-size="modalSize = $event"
    @update:font-size="fontSize = $event"
    @update:show-raw-data="showRawData = $event"
    @navigate="navigateToRelated"
    @close="$emit('close')"
  >
    <!-- Content Tab -->
    <div v-if="activeTab === 'content'" class="flex">
      


      <!-- Content Index Sidebar (only in large/full mode) -->
      <div v-if="showContentIndex" class="w-64 pr-4 border-r border-gray-200 dark:border-gray-700">
        <div class="sticky top-0">
          <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Content Index</h3>
          <div class="space-y-1">
            <button
              v-for="section in contentSections"
              :key="section.id"
              @click="scrollToSection(section.id)"
              class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 dark:hover:bg-gray-700/50 text-gray-600 dark:text-gray-300"
            >
              {{ section.index }}. {{ section.title }}
            </button>
          </div>
        </div>
      </div>



      <!-- Main Content -->
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


    </div>

    <!-- Status Tab -->


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

  </ModalLayout>
</template>
