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
  meta: Object,
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
const fontSize = ref(props.preservedFontSize || 'xs');

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

// Status flags computed property
const statusFlags = computed(() => {
  const formFields = props.meta?.form_fields || props.meta?.formFields;
  if (!formFields) return [];
  
  return Object.entries(formFields)
    .filter(([_, config]) => config.type === 'switch')
    .map(([field, config]) => ({
      key: field,
      label: config.label || field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
      active: props.record[field] == 1 || props.record[field] === true,
      config
    }));
});

// Content fields computed property
const contentFields = computed(() => {
  return props.meta?.content_fields || ['description', 'body'];
});

const formattedContent = computed(() => {
  const contentSections = [];
  
  for (const field of contentFields.value) {
    const value = props.record[field];
    const fieldLabel = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    
    if (value !== null && value !== undefined) {
      let content = value;
      
      if (showRawData.value) {
        contentSections.push({
          field: field,
          label: fieldLabel,
          content: content,
          type: 'raw',
          isEmpty: !content || content.toString().trim() === ''
        });
      } else {
        let formatted = content;
        
        if (field === 'color') {
          formatted = `
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600" 
                   style="background-color: ${content || '#3b82f6'}"></div>
              <span>${content || 'No color set'}</span>
            </div>
          `;
        } else if (field === 'icon') {
          formatted = `
            <div class="flex items-center gap-2">
              <span class="text-lg">${content || '📄'}</span>
              <span>${content || 'No icon set'}</span>
            </div>
          `;
        } else if (field === 'version') {
          formatted = `
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
              ${content || 'No version'}
            </span>
          `;
        } else {
          if (content && content.toString().trim() !== '') {
            formatted = content.toString()
              .replace(/\n/g, '<br>')
              .replace(/\.(?=\s|$)/g, '.<br>')
              .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
              .replace(/\*(.*?)\*/g, '<em>$1</em>');
          } else {
            formatted = '<em class="text-gray-500">No content</em>';
          }
        }
        
        contentSections.push({
          field: field,
          label: fieldLabel,
          content: formatted,
          type: 'formatted',
          isEmpty: !content || content.toString().trim() === ''
        });
      }
    }
  }
  
  return contentSections.length > 0 ? contentSections : null;
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
    console.log('Record links field:', props.record.links);
    console.log('Record links type:', typeof props.record.links);
    
    if (!props.record.links) {
      console.log('No links field found');
      return [];
    }
    
    // Handle both string and array formats
    let parsedLinks;
    if (typeof props.record.links === 'string') {
      parsedLinks = JSON.parse(props.record.links);
    } else if (Array.isArray(props.record.links)) {
      parsedLinks = props.record.links;
    } else {
      console.log('Links is not a string or array:', props.record.links);
      return [];
    }
    
    console.log('Parsed links:', parsedLinks);
    
    const result = parsedLinks.map(link => ({
      link_id: link.link_id || link.post_id || link.id || 'Unknown',
      link_title: link.link_title || link.post_title || link.title || link.name || `Record ${link.link_id || link.post_id || link.id}`,
      type: link.type || 'relates_to',
      ...link
    }));
    
    console.log('Processed links result:', result);
    return result;
  } catch (error) {
    console.error('Error parsing links:', error);
    console.log('Raw links value:', props.record.links);
    return [];
  }
});

// Computed for content sections (split by periods for indexing)
const contentSections = computed(() => {
  if (!props.record.body) return [];
  
  const sentences = props.record.body
    .split(/\.(?=\s|$)/)
    .filter(sentence => sentence.trim().length > 10)
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
    
    if (!activeTab.value || !['content', 'status', 'details', 'relations'].includes(activeTab.value)) {
      // Check if there are relations/links first
      if (links.value && links.value.length > 0) {
        activeTab.value = 'relations';
      } else if (modelConfig.value?.flags?.length) {
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

// Add the getTabContent function
function getTabContent(tab) {
  switch (tab) {
    case 'record':
      return 'record';
    case 'content':
      return 'content';
    case 'status':
      return 'status';
    case 'details':
      return 'details';
    case 'relations':
      return 'relations';
    case 'metadata':
      return 'metadata';
    default:
      return null;
  }
}

onMounted(() => {
  fetchModelConfig();
});
</script>

<template>
  <ModalLayout
    :record="record" 
    :title="record.title || record.name || 'Record Details'"
    :tabs="['content', 'status', 'details', 'relations', 'metadata', 'config', 'record']"
    :time-ago="formatDistance(new Date(record.updated_at || Date.now()), new Date())"
    :initials="initials"
    :id="record.id"
    :active-tab="activeTab"
    :modal-size="modalSize"
    :font-size="fontSize"
    :show-raw-data="showRawData"
    :status-flags="statusFlags"
    :related-links="links"
    :meta="meta"
    @update:active-tab="activeTab = $event"
    @update:modal-size="modalSize = $event"
    @update:font-size="fontSize = $event"
    @update:show-raw-data="showRawData = $event"
    @navigate="navigateToRelated"
    @close="$emit('close')"
  >
    <!-- Content Tab -->
    <template v-if="getTabContent(activeTab) === 'content'">
      <div :class="['space-y-4', fontSizeClass]">
        <div v-if="!formattedContent" class="text-gray-400 text-center py-8">
          No content fields available.
        </div>
        
        <div v-else class="grid gap-4" :class="{
          'grid-cols-1': modalSize === 'standard',
          'grid-cols-2': modalSize === 'large', 
          'grid-cols-3': modalSize === 'full'
        }">
          <div v-for="section in formattedContent" :key="section.field" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <!-- Field Header -->
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
              <h3 class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                <span class="mr-2">
                  {{ section.field === 'description' ? '📝' : 
                     section.field === 'color' ? '🎨' : 
                     section.field === 'version' ? '🏷️' : 
                     section.field === 'icon' ? '🔖' : '📄' }}
                </span>
                {{ section.label }}
                <span class="ml-2 text-gray-500 dark:text-gray-500 opacity-75">({{ section.field }})</span>
                <span v-if="section.isEmpty" class="ml-2 text-orange-500 opacity-75">(empty)</span>
              </h3>
            </div>
            
            <!-- Content Body -->
            <div v-if="section.type === 'raw'" :class="[
              'text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-4 overflow-auto font-mono min-h-[100px]',
              fontSizeClass
            ]">
              {{ section.content || '(empty)' }}
            </div>
            
            <div v-else :class="[
              'text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-4 overflow-auto min-h-[100px]',
              fontSizeClass
            ]" v-html="section.content">
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Details Tab -->
    <template v-else-if="getTabContent(activeTab) === 'details'">
      <div :class="['grid grid-cols-1 gap-2', fontSizeClass]">
        <!-- Your existing details content -->
      </div>
    </template>
  </ModalLayout>
</template>
