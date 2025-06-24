<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useForm, usePage } from "@inertiajs/vue3";
import PostSearch from '@/components/PostSearch.vue';
import { validateField, parseRules } from '@/utils/validators';
import { Switch } from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import TheButton from "@/panadero/components/TheButton.vue";
//import axios from 'axios';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Badges from '@/components/colors/Badges.vue';

const page = usePage();

// Types

// Props
const props = defineProps({
  table: {
    type: String,
    required: true
  },
  module: {
    type: String,
    required: true
  },
  record: {
    type: Object,
    required: true
  },
  db: {
    type: Object,
    required: true
  },
  meta: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close']);

// State & Refs
const table = computed(() => props.record.table || '');
const titleColumn = ref(props.record.titleColumn || 'title');
const idColumn = ref(props.record.idColumn || 'id');

// Form setup
const form = useForm({
  id: props.record.id,
  ...props.record
});

// Relations list for dynamic select fields - now using metadata
const projects = reactive({});

// Links table based on current table
const linksTable = computed(() => {
  return props.meta.links_table || [table.value, `${table.value}_related`, `${table.value}_dependencies`];
});

// Initialize form with default values
const loadForm = () => {
  try {
    const initialForm = {};
    
    Object.keys(props.meta.form_fields || {}).forEach(field => {
      const fieldConfig = props.meta.form_fields[field];
      
      // For select fields, handle them based on their purpose
      if (fieldConfig.type === 'select') {
        let fieldValue = null;
        
        // Check if this is a foreign key field (ends with _id)
        if (field.endsWith('_id')) {
          // Check if the record has the direct field (e.g., user_id)
          if (props.record[field] !== undefined) {
            fieldValue = parseInt(props.record[field]);
          }
          // Check if the record has a relation object (e.g., user.id)
          else {
            const relationName = field.replace('_id', ''); // user_id -> user
            if (props.record[relationName] && props.record[relationName].id) {
              fieldValue = parseInt(props.record[relationName].id);
            }
          }
        } else {
          // For non-foreign key select fields (like color, status), keep as string
          fieldValue = props.record[field] || fieldConfig.default || null;
        }
        
        initialForm[field] = fieldValue;
        
        // Convert options object to array format for consistency
        if (fieldConfig.options) {
          projects[field] = Object.entries(fieldConfig.options).map(([id, label]) => ({
            id: field.endsWith('_id') ? parseInt(id) : id, // Keep strings for non-ID fields
            title: label,
            name: label // Add name for compatibility
          }));
        }
        
        // Special handling for status field - use status mapping from model
        if (field === 'status' && props.meta.status_mapping) {
          projects[field] = Object.entries(props.meta.status_mapping).map(([statusKey, statusClasses]) => ({
            id: statusKey,
            title: statusKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()), // Convert 'in_progress' to 'In Progress'
            name: statusKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
            classes: statusClasses // Store the CSS classes for styling if needed
          }));
        }
      } else {
        // Simple assignment for non-select fields
        initialForm[field] = props.record[field] || null;
      }
    });
    
    Object.assign(form, initialForm);
    
    // Convert arrays to JSON strings for textarea display
    if (Array.isArray(form.json)) {
      form.json = JSON.stringify(form.json, null, 2);
    }
    
    if (Array.isArray(form.links)) {
      form.links = JSON.stringify(form.links, null, 2);
    }
    
    // Add options field handling
    if (Array.isArray(form.options)) {
      form.options = JSON.stringify(form.options, null, 2);
    }
  } catch (error) {
    // Silent error handling
  }
};

// Add a computed property to check if options are loaded
const isOptionsLoaded = computed(() => {
  return Object.keys(props.meta.form_fields || {}).every(field => {
    if (props.meta.form_fields[field].type === 'select') {
      return projects[field] && projects[field].length > 0;
  }
    return true;
  });
});

// Validation methods
const getRulesForField = (key) => {
  return props.meta.validation_rules?.[key]
    ? parseRules(props.meta.validation_rules[key])
    : null;
};

const isInvalid = (key) => {
  const rules = getRulesForField(key);
  return !validateField(form[key], rules);
};

const getRuleText = (key) => props.meta.validation_rules?.[key] || '';

const isFormValid = computed(() =>
  Object.keys(props.meta.form_fields || {}).every(field => {
    const rules = getRulesForField(field);
    return !rules || validateField(form[field], rules);
  })
);

// Form submission
const submit = async () => {
  console.log('Form submission started');
  console.log('Form data:', form);
  console.log('Table:', props.table);
  console.log('Form ID:', form.id);
  
  if (!isFormValid.value) {
    console.log('Form validation failed');
    return;
  }

  // Filter data to only include record fields (no metadata)
  const filteredData = {};
  
  // Only include fields that are defined in form_fields (actual record fields)
  Object.keys(props.meta.form_fields || {}).forEach(fieldName => {
    if (form.hasOwnProperty(fieldName)) {
      const fieldConfig = props.meta.form_fields[fieldName];
      let value = form[fieldName];
      
      // Convert switch fields to proper boolean values
      if (fieldConfig.type === 'switch') {
        // Handle null, undefined, and falsy values
        if (value === null || value === undefined || value === '') {
          value = false;
        } else if (typeof value === 'string') {
          value = value === 'true' || value === '1';
        } else {
          value = Boolean(value);
        }
      }
      
      // Handle JSON fields - convert strings back to arrays/objects
      if (fieldName === 'json' || fieldName === 'links' || fieldName === 'options') {
        if (typeof value === 'string') {
          try {
            value = JSON.parse(value);
          } catch (e) {
            // If parsing fails, keep as string or set to empty array
            value = fieldName === 'links' ? [] : (fieldName === 'options' ? [] : {});
          }
        }
        // If it's already an array/object, keep it as is
      }
      
      filteredData[fieldName] = value;
    }
  });
  
  // Always include the ID for the update
  filteredData.id = form.id;

  console.log('Filtered data:', filteredData);

  // Use axios directly with proper CSRF handling for API routes
  try {
    console.log('Making API call to:', `/api/${props.table}/${form.id}`);
    const response = await axios.put(`/api/${props.table}/${form.id}`, filteredData, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      withCredentials: true
    });

    console.log('API response:', response);

    // Success handling
      const page = usePage();
      const user = page.props.auth.user;

      props.db.logAction({
        action: `${props.table}.update`,
        user_id: user.id || 'no_uid',
        module: props.module,
        node: 'none',
        team: user.current_team.name || 'no_team',
        project: 'none',
        content: form.title || 'none',
        json: JSON.stringify(form),
        tags: 'content, posts',
      });

      emit('close');
    
    // Show success message
    router.visit(window.location.pathname, {
      data: { success: `${props.table} updated successfully` },
      preserveScroll: true,
      replace: true
    });

  } catch (error) {
    console.error('API call failed:', error);
    console.error('Error response:', error.response);
    
    // Handle validation errors silently
    if (error.response?.status === 422) {
      console.log('Validation failed:', error.response.data);
      // Validation failed - could add user notification here if needed
    }
  }
};

// Initialize form on mount
onMounted(() => {
  if (!props.record.id) return;
  if (usePage().props.auth && usePage().props.auth.user) {
    loadForm();
  } else {
    // Optionally, show a message or redirect
    console.warn('User not authenticated, not loading form.');
  }
});
// Display states
const advancedMode = ref(false);
let _switchSection = ".";

// Style constants
const _button = {
  active: "w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400",
  inactive: "w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800",
};

const _input = {
  base: "w-full pl-3 pr-10 py-2 text-xs rounded-md focus:outline-none focus:ring-1",
  light: "bg-white border-gray-300 text-gray-700 focus:ring-indigo-500",
  dark: "dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-600",
  label: "block text-sm text-gray-700 dark:text-gray-200 font-bold",
  info: "text-xxs text-gray-400 ml-1",
  readOnly: "text-xxs text-gray-500 dark:text-gray-300",
};

const _container = {
  base: "fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75",
};

const _window = {
  base: "z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[720px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500",
  padding: "p-6 pt-10",
  motion: "motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2",
  light: "bg-gray-100 text-gray-600 from-gray-200/50 via-transparent",
  dark: "dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600",
};

const _footer = {
  base: "fixed left-1/2 transform -translate-x-1/2 w-full max-w-4xl p-4 bg-gray-100 dark:bg-gray-900 rounded-b-xl dark:border-gray-600",
  switches: "fixed bottom-12 bg-gray-100 dark:bg-gray-900  border-t dark:border-gray-600",
  buttons: "flex bottom-0 justify-end space-x-4 border-b",
};

// Links handling - always work with arrays
const link = ref({ link_id: "0", type: 'relates_to', link_title: '' });

const availableLinkTypes = computed(() => {
  return props.meta.links_table || [];
});

const hasJsonLinkError = computed(() => {
  try {
    let links;
    if (typeof form.links === 'string') {
      links = JSON.parse(form.links || '[]');
    } else if (Array.isArray(form.links)) {
      links = form.links;
    } else {
      links = [];
    }
    return !Array.isArray(links) || links.some(link => typeof link !== 'object' || !link.link_id || !link.type);
  } catch {
    return true;
  }
});

const links = computed(() => {
  // Add debugging
  console.log('links computed - form.links:', form.links, 'type:', typeof form.links);
  
  // Always return an array
  if (Array.isArray(form.links)) {
    return form.links;
  } else if (typeof form.links === 'string') {
    try {
      const parsed = JSON.parse(form.links || '[]');
      return Array.isArray(parsed) ? parsed : [];
  } catch {
      return [];
    }
  } else if (form.links === null || form.links === undefined) {
    return [];
  } else {
    // If it's an object but not an array, try to convert it
    try {
      return [form.links];
    } catch {
      return [];
    }
  }
});

const addLink = () => {
  if (link.value.link_id && link.value.link_title) {
    const newLinks = [...links.value, { ...link.value }];
    form.links = newLinks; // Store as array, not string
    link.value = { link_id: "0", type: 'relates_to', link_title: '' };
  }
};

const removeLink = (i) => {
  const newLinks = [...links.value];
  newLinks.splice(i, 1);
  form.links = newLinks;
};

const linksJsonString = computed({
  get() {
    if (typeof form.links === 'string') {
      return form.links;
    } else if (Array.isArray(form.links)) {
      return JSON.stringify(form.links, null, 2);
    } else {
      return '[]';
    }
  },
  set(value) {
    try {
      form.links = JSON.parse(value);
    } catch {
      form.links = [];
    }
  }
});

const updateLinksFromJson = (event) => {
  try {
    const parsed = JSON.parse(event.target.value);
    if (Array.isArray(parsed)) {
      form.links = parsed;
    }
  } catch {
    // Invalid JSON, keep the current value
  }
};

// Options handling - always work with arrays
const newOption = ref({ name: '', url: '', route: '' });
const options = computed(() => {
  // Add debugging
  console.log('options computed - form.options:', form.options, 'type:', typeof form.options);
  
  // Always return an array
  if (Array.isArray(form.options)) {
    return form.options;
  } else if (typeof form.options === 'string') {
    try {
      const parsed = JSON.parse(form.options || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch {
      return [];
    }
  } else if (form.options === null || form.options === undefined) {
    return [];
  } else {
    // If it's an object but not an array, try to convert it
    try {
      return [form.options];
    } catch {
      return [];
    }
  }
});

const addOption = () => {
  if (newOption.value.name && newOption.value.url) {
    const newOptions = [...options.value, { ...newOption.value }];
    form.options = newOptions; // Store as array, not string
    newOption.value = { name: '', url: '', route: '' };
  }
};

const removeOption = (i) => {
  const newOptions = [...options.value];
  newOptions.splice(i, 1);
  form.options = newOptions;
};

const optionsJsonString = computed({
  get() {
    if (typeof form.options === 'string') {
      return form.options;
    } else if (Array.isArray(form.options)) {
      return JSON.stringify(form.options, null, 2);
    } else {
      return '[]';
    }
  },
  set(value) {
    try {
      form.options = JSON.parse(value);
    } catch {
      form.options = [];
    }
  }
});

// Improved JSON Field
const jsonString = computed({
  get() {
    if (typeof form.json === 'string') {
      return form.json;
    } else if (typeof form.json === 'object') {
      return JSON.stringify(form.json, null, 2);
    } else {
      return '';
    }
  },
  set(value) {
    try {
      form.json = JSON.parse(value);
    } catch {
      form.json = value; // Keep as string if parsing fails
    }
  }
});

const hasJsonError = computed(() => {
  if (!form.json || typeof form.json !== 'string') return false;
  try {
    JSON.parse(form.json);
    return false;
  } catch {
    return true;
  }
});

const jsonValue = computed(() => {
  if (hasJsonError.value) return null;
  try {
    return JSON.parse(form.json);
  } catch {
    return null;
  }
});

// Add tab management
const activeTab = ref('main'); // 'main', 'json', 'links', 'options'

// Color variables for status mapping
const statusColors = {
  green: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
  red: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/20 dark:ring-red-400/20',
  blue: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-blue-600/20 dark:ring-blue-400/20',
  yellow: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20',
  purple: 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 ring-purple-600/20 dark:ring-purple-400/20',
  indigo: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 ring-indigo-600/20 dark:ring-indigo-400/20',
  pink: 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 ring-pink-600/20 dark:ring-pink-400/20',
  gray: 'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-400/20',
  orange: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 ring-orange-600/20 dark:ring-orange-400/20',
  teal: 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 ring-teal-600/20 dark:ring-teal-400/20',
  cyan: 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 ring-cyan-600/20 dark:ring-cyan-400/20',
  emerald: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20 dark:ring-emerald-400/20',
  lime: 'bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-300 ring-lime-600/20 dark:ring-lime-400/20',
  amber: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 ring-amber-600/20 dark:ring-amber-400/20',
  rose: 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 ring-rose-600/20 dark:ring-rose-400/20',
  violet: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 ring-violet-600/20 dark:ring-violet-400/20',
  fuchsia: 'bg-fuchsia-50 dark:bg-fuchsia-900/20 text-fuchsia-700 dark:text-fuchsia-300 ring-fuchsia-600/20 dark:ring-fuchsia-400/20',
  slate: 'bg-slate-50 dark:bg-slate-900/20 text-slate-700 dark:text-slate-300 ring-slate-600/20 dark:ring-slate-400/20',
  zinc: 'bg-zinc-50 dark:bg-zinc-900/20 text-zinc-700 dark:text-zinc-300 ring-zinc-600/20 dark:ring-zinc-400/20',
  neutral: 'bg-neutral-50 dark:bg-neutral-900/20 text-neutral-700 dark:text-neutral-300 ring-neutral-600/20 dark:ring-neutral-400/20',
  stone: 'bg-stone-50 dark:bg-stone-900/20 text-stone-700 dark:text-stone-300 ring-stone-600/20 dark:ring-stone-400/20'
};

// Helper function to get status color from model mapping
const getStatusColor = (status) => {
  // Get the status mapping from the model metadata
  const statusMapping = props.meta?.status_mapping || {};
  const colorKey = statusMapping[status?.toLowerCase()];
  
  // Return the color classes or fallback to gray
  return statusColors[colorKey] || statusColors.gray;
};

// You can use the exposed function
const { getStatusColor: BadgesStatusColor } = Badges;

// Add to state & refs section
const showColorDropdown = ref(false);

// Add click outside handler to close dropdown
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.color-dropdown')) {
      showColorDropdown.value = false;
    }
  });
});

// Add to state & refs section
const activeColorDropdown = ref(null);

// Add color options
const colorOptions = [
  { value: 'red', label: 'Red' },
  { value: 'blue', label: 'Blue' },
  { value: 'green', label: 'Green' },
  { value: 'yellow', label: 'Yellow' },
  { value: 'purple', label: 'Purple' },
  { value: 'indigo', label: 'Indigo' },
  { value: 'pink', label: 'Pink' },
  { value: 'gray', label: 'Gray' },
  { value: 'orange', label: 'Orange' },
  { value: 'teal', label: 'Teal' },
  { value: 'cyan', label: 'Cyan' },
  { value: 'emerald', label: 'Emerald' },
  { value: 'lime', label: 'Lime' },
  { value: 'amber', label: 'Amber' },
  { value: 'rose', label: 'Rose' },
  { value: 'violet', label: 'Violet' },
  { value: 'fuchsia', label: 'Fuchsia' },
  { value: 'slate', label: 'Slate' },
  { value: 'zinc', label: 'Zinc' },
  { value: 'neutral', label: 'Neutral' },
  { value: 'stone', label: 'Stone' },
  { value: 'turquoise', label: 'Turquoise' }
];

const toggleColorDropdown = (fieldName) => {
  activeColorDropdown.value = activeColorDropdown.value === fieldName ? null : fieldName;
};

const selectColor = (fieldName, color) => {
  form[fieldName] = color;
  activeColorDropdown.value = null;
};

// Update onMounted
onMounted(() => {
  if (!props.record.id) return;
  if (usePage().props.auth && usePage().props.auth.user) {
    loadForm();
    
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.color-dropdown')) {
        activeColorDropdown.value = null;
      }
    });
  }
});
</script>

<template>
  <div>
    <div :class="_container.base" @click="$emit('close')">
    </div>
    <div :class="[_window.base, _window.padding, _window.motion, _window.light, _window.dark]">
      <div class="h-full flex flex-col">

        <!-- Modal Header with Tabs -->
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              {{ form.title || form.item || 'Edit Record' }}
            </h2>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              #{{ form.id }} &mdash; {{ props.table }}
            </div>
          </div>
          
          <!-- Tabs moved to the right -->
          <div class="flex space-x-1">
            <!-- Main Form Tab -->
            <button
              @click="activeTab = 'main'"
              :class="[
                'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                activeTab === 'main'
                  ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
              ]"
            >
              Main
            </button>
            
            <!-- JSON Tab -->
            <button
              v-if="meta.form_fields?.json"
              @click="activeTab = 'json'"
              :class="[
                'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                activeTab === 'json'
                  ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
              ]"
            >
              JSON
            </button>
            
            <!-- Links Tab -->
            <button
              v-if="meta.form_fields?.links"
              @click="activeTab = 'links'"
              :class="[
                'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                activeTab === 'links'
                  ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
              ]"
            >
              Links
            </button>
            
            <!-- Options Tab -->
            <button
              v-if="meta.form_fields?.options"
              @click="activeTab = 'options'"
              :class="[
                'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
                activeTab === 'options'
                  ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
              ]"
            >
              Options
            </button>
          </div>
          
          <button @click="$emit('close')" class="text-gray-400 hover:text-red-500 ml-4">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <!-- Content area -->
        <div class="flex-1 overflow-y-auto">
          
          <!-- Main Form Tab -->
          <div v-if="activeTab === 'main'" class="p-4">
          <form @submit.prevent="submit" class="grid grid-cols-8 gap-4">
            <template v-for="(fieldConfig, fieldName) in meta.form_fields" :key="String(fieldName)">
                <!-- Skip JSON fields and switch fields in main form -->
              <div
                  v-if="fieldConfig.type !== 'switch' && !['json', 'links', 'options'].includes(fieldName)"
                :class="{
                  'col-span-8': fieldConfig.col_span === 8,
                  'col-span-6': fieldConfig.col_span === 6,
                  'col-span-5': fieldConfig.col_span === 5,
                  'col-span-4': fieldConfig.col_span === 4,
                  'col-span-3': fieldConfig.col_span === 3,
                  'col-span-2': fieldConfig.col_span === 2,
                  'col-span-1': fieldConfig.col_span === 1 || !fieldConfig.col_span
                }"
              >
                <!-- Label -->
                <label :for="String(fieldName)" class="block text-xs font-medium text-gray-700 dark:text-gray-200">
                  <span v-if="!(getRuleText(String(fieldName)) == 'boolean')">
                    {{ getRuleText(String(fieldName)) == 'boolean' ? String(fieldName).replace('is_','') : fieldName }}
                  </span>       
                  <span v-if="getRuleText(String(fieldName)) && (getRuleText(String(fieldName))!='boolean')" :class="isInvalid(String(fieldName)) ? 'text-red-500' : 'text-gray-500'">({{ getRuleText(String(fieldName)) }})</span>

                  <!-- Validation message and character count -->
                  <span v-if="isInvalid(String(fieldName))" class="text-xxs text-red-600 ml-4 pt-4">
                    <div v-if="typeof form[String(fieldName)] === 'string' && getRulesForField(String(fieldName))">
                      <span v-for="rule in getRulesForField(String(fieldName))" :key="rule.name">
                        <template v-if="['min', 'max'].includes(rule.name)">
                          Length: {{ form[String(fieldName)]?.length || 0 }} /
                          <span v-if="rule.name === 'min'">min {{ rule.param }}</span>
                          <span v-if="rule.name === 'max'">max {{ rule.param }}</span>
                        </template>
                      </span>
                    </div>
                  </span>
                </label>

                  <!-- Color Select Field -->
                  <div v-if="fieldConfig.type === 'select' && fieldName === 'color'" class="mt-1 relative color-dropdown">
                    <button 
                      type="button"
                      @click="toggleColorDropdown(fieldName)"
                      class="block w-full pl-3 pr-10 py-2 text-xs rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer bg-white dark:bg-gray-700 flex items-center justify-between"
                    >
                      <div class="flex items-center">
                        <div 
                          class="w-2 h-2 rounded-full mr-1.5"
                          :style="{ backgroundColor: form[fieldName] || '#3b82f6' }"
                        ></div>
                        <span class="text-gray-900 dark:text-gray-300">
                          {{ form[fieldName] || 'Select color' }}
                        </span>
                      </div>
                      <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </button>
                    
                    <!-- Dropdown with reduced font size -->
                    <div 
                      v-show="activeColorDropdown === fieldName"
                      class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 rounded-md shadow-lg border border-gray-200 dark:border-gray-600 py-1 max-h-60 overflow-auto text-xs"
                    >
                      <button
                        type="button" 
                        v-for="color in colorOptions"
                        :key="color.value"
                        @click="selectColor(fieldName, color.value)"
                        class="w-full flex items-center px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                      >
                        <div 
                          class="w-2 h-2 rounded-full mr-1.5"
                          :style="{ backgroundColor: color.value }"
                        ></div>
                        <span class="text-gray-900 dark:text-gray-300">
                          {{ color.label }}
                        </span>
                      </button>
                    </div>
                </div>

                  <!-- Regular Select Field -->
                <div v-else-if="fieldConfig.type === 'select'" class="mt-1">
                  <select
                      v-model="form[fieldName]"
                      :id="fieldName"
                      :disabled="fieldConfig.readonly"
                      class="block w-full pl-3 pr-10 py-2 text-xs rounded-md border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 disabled:bg-gray-100 disabled:cursor-not-allowed"
                      :required="meta.validation_rules?.[fieldName]?.includes('required')"
                    >
                      <option value="">Select {{ fieldConfig.label }}</option>
                      <option 
                        v-for="relation in (projects[fieldName] || [])" 
                        :key="relation.id" 
                        :value="relation.id"
                        :class="fieldName === 'color' ? 'flex items-center' : ''"
                      >
                        <template v-if="fieldName === 'color'">
                          <span class="inline-flex items-center">
                            <span class="w-2 h-2 rounded-full mr-1.5" :style="{ backgroundColor: relation.id }"></span>
                            {{ relation.title || relation.name || relation.id }}
                          </span>
                    </template>
                    <template v-else>
                          {{ relation.title || relation.name || relation.id }}
                        </template>
                      </option>
                  </select>
                  <p v-if="fieldConfig.help" class="mt-1 text-xs text-gray-500">{{ fieldConfig.help }}</p>
                </div>

                  <!-- Readonly Field Display -->
                  <div v-else-if="fieldConfig.readonly" class="mt-1">
                    <div class="block w-full pl-3 pr-10 py-2 text-xs rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">
                      {{ form[String(fieldName)] || 'N/A' }}
                    </div>
                    <p v-if="fieldConfig.help" class="mt-1 text-xs text-gray-500">{{ fieldConfig.help }}</p>
                      </div>

                  <!-- Datetime Field -->
                  <div v-else-if="fieldConfig.type === 'datetime'" class="mt-1">
                    <input
                      type="datetime-local"
                      :id="String(fieldName)"
                          v-model="form[String(fieldName)]"
                      :readonly="fieldConfig.readonly"
                          :class="[
                        _input.base,
                        _input.light,
                        _input.dark,
                        isInvalid(String(fieldName)) ? 'border-red-500' : '',
                        fieldConfig.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                      ]"
                      :required="meta.validation_rules?.[String(fieldName)]?.includes('required')"
                    />
                    <p v-if="fieldConfig.help" class="mt-1 text-xs text-gray-500">{{ fieldConfig.help }}</p>
                </div>

                <!-- Boolean Field -->
                <div v-else-if="fieldConfig.type === 'boolean'" class="mt-1">
                  <div class="flex space-x-4">
                    <button
                      type="button"
                        :disabled="fieldConfig.readonly"
                      @click="form[String(fieldName)] = true"
                      :class="[
                        _button.base,
                          form[String(fieldName)] ? _button.active : _button.inactive,
                          fieldConfig.readonly ? 'opacity-50 cursor-not-allowed' : ''
                      ]"
                    >
                      Yes
                    </button>
                    <button
                      type="button"
                        :disabled="fieldConfig.readonly"
                      @click="form[String(fieldName)] = false"
                      :class="[
                        _button.base,
                          !form[String(fieldName)] ? _button.active : _button.inactive,
                          fieldConfig.readonly ? 'opacity-50 cursor-not-allowed' : ''
                      ]"
                    >
                      No
                    </button>
                  </div>
                </div>

                <!-- Default Input -->
                <div v-else class="mt-1">
                  <input
                    v-if="fieldConfig.type !== 'textarea'"
                    :type="fieldConfig.type || 'text'"
                    :id="String(fieldName)"
                    v-model="form[String(fieldName)]"
                      :readonly="fieldConfig.readonly"
                    :class="[
                      _input.base,
                      _input.light,
                      _input.dark,
                        isInvalid(String(fieldName)) ? 'border-red-500' : '',
                        fieldConfig.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                    ]"
                    :required="meta.validation_rules?.[String(fieldName)]?.includes('required')"
                  />
                  <textarea
                    v-else
                    :id="String(fieldName)"
                    :rows="meta.form_fields?.[fieldName]?.rows || 2"
                    v-model="form[String(fieldName)]"
                      :readonly="fieldConfig.readonly"
                    :class="[
                      _input.base,
                      _input.light,
                      _input.dark,
                        isInvalid(String(fieldName)) ? 'border-red-500' : '',
                        fieldConfig.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                    ]"
                    :required="meta.validation_rules?.[String(fieldName)]?.includes('required')"
                    rows="3"
                  ></textarea>
                </div>
              </div>
            </template>
          </form>
          </div>

          <!-- JSON Tab -->
          <div v-if="activeTab === 'json'" class="p-4">
            <div class="space-y-4">
              <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">JSON Data Editor</h3>
              
              <!-- JSON Field -->
              <div class="space-y-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-200">JSON Data</label>
                <div class="relative">
                  <textarea
                    v-model="jsonString"
                    :rows="8"
                    :readonly="meta.form_fields?.json?.readonly"
                    :class="[
                      'block w-full rounded-md shadow-sm sm:text-xs font-mono',
                      isInvalid('json')
                        ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                      'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400',
                      meta.form_fields?.json?.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                    ]"
                    placeholder="Enter valid JSON..."
                  ></textarea>
                  
                  <!-- JSON validation indicator -->
                  <div v-if="hasJsonError" class="absolute top-2 right-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                  <div v-else class="absolute top-2 right-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                </div>
                
                <!-- JSON validation message -->
                <div v-if="hasJsonError" class="text-xs text-red-600 bg-red-50 dark:bg-red-900/20 p-2 rounded border border-red-200 dark:border-red-800">
                  Invalid JSON format. Please check your syntax.
                </div>
                
                <!-- JSON preview -->
                <details v-if="!hasJsonError && jsonValue" class="mt-2">
                  <summary class="text-xs text-gray-500 cursor-pointer">Preview JSON structure</summary>
                  <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-800 rounded border text-xs">
                    <pre class="whitespace-pre-wrap">{{ JSON.stringify(jsonValue, null, 2) }}</pre>
                  </div>
                </details>

                <!-- Debug Panel (common style for all tabs) -->
                <div class="mt-4 mb-16 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800">
                  <div class="text-[10px] font-medium text-blue-800 dark:text-blue-200 mb-1">Debug content:</div>
                  <pre class="text-[9px] leading-[14px] text-blue-700 dark:text-blue-300 whitespace-pre-wrap">
Debug: {{ activeTab }} count = {{ (activeTab === 'json' ? [form.json] : (activeTab === 'links' ? links : options)).length }}
{{ activeTab }} type = {{ typeof (activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
{{ activeTab }} value = {{ JSON.stringify(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options), null, 2) }}
activeTab = {{ activeTab }}
Condition check: {{ activeTab }} exists = {{ Boolean(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
                  </pre>
                </div>
              </div>
            </div>
          </div>

          <!-- Links Tab -->
          <div v-else-if="activeTab === 'links'" class="p-4">
            <div class="space-y-4">
              <!-- Title and manager on one line -->
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Links Manager</h3>
                
                <!-- Links Manager moved inline -->
                <div class="flex items-center space-x-2 flex-1 ml-4">
                  <select 
                    v-model="link.type" 
                    class="text-xs rounded px-2 py-1 w-1/4 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  >
                    <option v-for="_link in availableLinkTypes" :value="_link">{{_link.replaceAll('_',' ')}}</option>
                  </select>
                  <div class="flex-1">
                    <PostSearch
                      :model-value="link.link_id"
                      :model="props.table"
                      :table="props.table"
                      :title-column="titleColumn"
                      :label="titleColumn"
                      :id-column="idColumn"
                      @update:model-value="(_value) => { link.link_id = String(_value); }"
                      @update:link-title="(_title) => { link.link_title = _title }"
                    />
                  </div>
                  <button @click.prevent="addLink" :class="[_button.active]">Add</button>
                </div>
              </div>
              
              <!-- Content area with scrolling -->
              <div class="max-h-[calc(100vh-300px)] overflow-y-auto">
                <!-- Existing Links -->
                <div v-if="Array.isArray(links) && links.length > 0" class="space-y-4">
                  <div class="grid grid-cols-2 gap-2">
                    <div v-for="(linkItem, i) in links" :key="i" 
                         class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-700">
                      <div class="flex items-center min-w-0 flex-1">
                        <span class="text-xs text-gray-600 dark:text-gray-300 truncate">{{ linkItem?.type || 'unknown' }}</span>
                        <span class="text-xs text-gray-800 dark:text-gray-200 ml-2 truncate">{{ linkItem?.link_title || 'unknown' }}</span>
                      </div>
                      <button @click.prevent="removeLink(i)" class="ml-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Debug Panel (common style for all tabs) -->
                  <div class="mt-4 mb-16 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800">
                    <div class="text-[10px] font-medium text-blue-800 dark:text-blue-200 mb-1">Debug content:</div>
                    <pre class="text-[9px] leading-[14px] text-blue-700 dark:text-blue-300 whitespace-pre-wrap">
Debug: {{ activeTab }} count = {{ (activeTab === 'json' ? [form.json] : (activeTab === 'links' ? links : options)).length }}
{{ activeTab }} type = {{ typeof (activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
{{ activeTab }} value = {{ JSON.stringify(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options), null, 2) }}
activeTab = {{ activeTab }}
Condition check: {{ activeTab }} exists = {{ Boolean(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
                    </pre>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Options Tab -->
          <div v-else-if="activeTab === 'options'" class="p-4">
            <div class="space-y-4">
              <!-- Title and manager on one line -->
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Options</h3>
                
                <!-- Options Manager moved inline -->
                <div class="flex items-center space-x-2 flex-1 ml-4">
                  <input
                    v-model="newOption.name"
                    placeholder="Option name"
                    class="text-xs rounded px-2 py-1 w-1/3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  />
                  <input
                    v-model="newOption.url"
                    placeholder="URL"
                    class="text-xs rounded px-2 py-1 w-1/3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  />
                  <input
                    v-model="newOption.route"
                    placeholder="Route"
                    class="text-xs rounded px-2 py-1 w-1/3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  />
                  <button @click.prevent="addOption" :class="[_button.active]">
                    Add
                  </button>
                </div>
              </div>
              
              <!-- Content area with scrolling -->
              <div class="max-h-[calc(100vh-300px)] overflow-y-auto">
                <!-- Existing Options -->
                <div v-if="Array.isArray(options) && options.length > 0" class="space-y-4">
                  <div class="grid grid-cols-3 gap-2">
                    <div v-for="(optionItem, i) in options" :key="i" 
                         class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-700">
                      <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-gray-900 dark:text-gray-200 truncate">{{ optionItem?.name || 'unknown' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ optionItem?.url || 'unknown' }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ optionItem?.route || 'unknown' }}</div>
                      </div>
                      <button @click.prevent="removeOption(i)" class="ml-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Debug Panel (common style for all tabs) -->
                  <div class="mt-4 mb-16 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800">
                    <div class="text-[10px] font-medium text-blue-800 dark:text-blue-200 mb-1">Debug content:</div>
                    <pre class="text-[9px] leading-[14px] text-blue-700 dark:text-blue-300 whitespace-pre-wrap">
Debug: {{ activeTab }} count = {{ (activeTab === 'json' ? [form.json] : (activeTab === 'links' ? links : options)).length }}
{{ activeTab }} type = {{ typeof (activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
{{ activeTab }} value = {{ JSON.stringify(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options), null, 2) }}
activeTab = {{ activeTab }}
Condition check: {{ activeTab }} exists = {{ Boolean(activeTab === 'json' ? form.json : (activeTab === 'links' ? links : options)) }}
                    </pre>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Fixed line for switches above footer -->
        <div class="grid grid-cols-8 " :class="[_footer.base, _footer.switches]">
           <div
              v-for="(fieldConfig, fieldName) in meta.form_fields"
              :key="fieldName"
              :class="{
                  'col-span-8': fieldConfig.col_span === 8,
                  'col-span-6': fieldConfig.col_span === 6,
                  'col-span-5': fieldConfig.col_span === 5,
                  'col-span-4': fieldConfig.col_span === 4,
                  'col-span-3': fieldConfig.col_span === 3,
                  'col-span-2': fieldConfig.col_span === 2,
                  'col-span-1': fieldConfig.col_span === 1 || !fieldConfig.col_span
              }"
            >
              <!-- Label left.. Boolean center -->
              <label :for="fieldName" class="ml-4 text-center text-xs" v-if="fieldConfig.type === 'switch'">
                <span>{{ fieldName.replace('is_','') }}</span>
              </label>
              
              <!-- Switch Component for switch type fields -->
              <div v-if="fieldConfig.type === 'switch'" class="scale-75 ml-1">
                <Switch
                  v-model="form[String(fieldName)]"
                  :class="[
                    form[String(fieldName)] ? 'bg-indigo-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                  ]"
                >
                  <span
                    :class="[
                      form[String(fieldName)] ? 'translate-x-6' : 'translate-x-1',
                      'inline-block h-4 w-4 transform rounded-full bg-white transition-transform'
                    ]"
                  />
                </Switch>
              </div>
            </div>
        </div>

        <!-- Fixed footer with buttons -->
        <div :class="[_footer.base, _footer.buttons]">
          <button @click="$emit('close')" type="button" :class="[_button.active]">Cancel</button>
          <button @click="submit" type="button" :class="[_button.active]">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>
