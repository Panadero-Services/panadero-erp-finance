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
  base: "z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500",
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

// Links handling
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
  try {
    if (typeof form.links === 'string') {
      return JSON.parse(form.links || '[]');
    } else if (Array.isArray(form.links)) {
      return form.links;
    } else {
      return [];
    }
  } catch {
    return [];
  }
});

const addLink = () => {
  const newLinks = [...links.value, link.value];
  form.links = JSON.stringify(newLinks);
};

const removeLink = (index) => {
  const newLinks = [...links.value];
  newLinks.splice(index, 1);
  form.links = JSON.stringify(newLinks);
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

// Options handling
const newOption = ref({ name: '', url: '', route: '' });
const options = computed(() => {
  try {
    if (typeof form.options === 'string') {
      return JSON.parse(form.options || '[]');
    } else if (Array.isArray(form.options)) {
      return form.options;
    } else {
      return [];
    }
  } catch {
    return [];
  }
});

const addOption = () => {
  if (newOption.value.name && newOption.value.url) {
    const newOptions = [...options.value, { ...newOption.value }];
    form.options = JSON.stringify(newOptions);
    newOption.value = { name: '', url: '', route: '' };
  }
};

const removeOption = (index) => {
  const newOptions = [...options.value];
  newOptions.splice(index, 1);
  form.options = JSON.stringify(newOptions);
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

// Tab management for JSON fields
const activeTab = ref('form'); // 'form', 'json', 'links', 'options'

// Check if record has JSON fields
const hasJsonFields = computed(() => {
  return form.value.json || form.value.links || form.value.options;
});

// JSON field handling (keep existing code)
const jsonString = computed({
  get() {
    if (typeof form.value.json === 'string') {
      return form.value.json;
    } else if (typeof form.value.json === 'object') {
      return JSON.stringify(form.value.json, null, 2);
    } else {
      return '';
    }
  },
  set(value) {
    try {
      form.value.json = JSON.parse(value);
    } catch {
      form.value.json = value; // Keep as string if parsing fails
    }
  }
});

const hasJsonError = computed(() => {
  if (!form.value.json || typeof form.value.json !== 'string') return false;
  try {
    JSON.parse(form.value.json);
    return false;
  } catch {
    return true;
  }
});

const jsonValue = computed(() => {
  if (hasJsonError.value) return null;
  try {
    return JSON.parse(form.value.json);
  } catch {
    return null;
  }
});

// Links field handling (keep existing code)
const link = ref({ type: 'relates_to', link_id: '', link_title: '' });
const availableLinkTypes = ['relates_to', 'depends_on', 'blocks', 'duplicates', 'is_duplicate_of'];

const links = computed(() => {
  try {
    if (typeof form.value.links === 'string') {
      return JSON.parse(form.value.links || '[]');
    } else if (Array.isArray(form.value.links)) {
      return form.value.links;
    } else {
      return [];
    }
  } catch {
    return [];
  }
});

const hasJsonLinkError = computed(() => {
  if (!form.value.links || typeof form.value.links !== 'string') return false;
  try {
    JSON.parse(form.value.links);
    return false;
  } catch {
    return true;
  }
});

const addLink = () => {
  if (link.value.link_id && link.value.link_title) {
    const newLinks = [...links.value, { ...link.value }];
    form.value.links = JSON.stringify(newLinks);
    link.value = { type: 'relates_to', link_id: '', link_title: '' };
  }
};

const removeLink = (index) => {
  const newLinks = [...links.value];
  newLinks.splice(index, 1);
  form.value.links = JSON.stringify(newLinks);
};

// Options field handling (keep existing code)
const newOption = ref({ name: '', url: '', route: '' });
const options = computed(() => {
  try {
    if (typeof form.value.options === 'string') {
      return JSON.parse(form.value.options || '[]');
    } else if (Array.isArray(form.value.options)) {
      return form.value.options;
    } else {
      return [];
    }
  } catch {
    return [];
  }
});

const addOption = () => {
  if (newOption.value.name && newOption.value.url) {
    const newOptions = [...options.value, { ...newOption.value }];
    form.value.options = JSON.stringify(newOptions);
    newOption.value = { name: '', url: '', route: '' };
  }
};

const removeOption = (index) => {
  const newOptions = [...options.value];
  newOptions.splice(index, 1);
  form.value.options = JSON.stringify(newOptions);
};

const optionsJsonString = computed({
  get() {
    if (typeof form.value.options === 'string') {
      return form.value.options;
    } else if (Array.isArray(form.value.options)) {
      return JSON.stringify(form.value.options, null, 2);
    } else {
      return '[]';
    }
  },
  set(value) {
    try {
      form.value.options = JSON.parse(value);
    } catch {
      form.value.options = [];
    }
  }
});

</script>

<template>
  <div>
    <div :class="_container.base" @click="$emit('close')"></div>
    <div :class="[_window.base, _window.padding, _window.motion, _window.light, _window.dark]">
      <div class="h-full flex flex-col">
        <!-- Header with tabs if JSON fields exist -->
        <div v-if="hasJsonFields" class="border-b border-gray-200 dark:border-gray-700">
          <nav class="flex space-x-8 px-4">
            <button
              @click="activeTab = 'form'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-xs',
                activeTab === 'form'
                  ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
              ]"
            >
              Form Fields
            </button>
            <button
              v-if="form.json"
              @click="activeTab = 'json'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-xs',
                activeTab === 'json'
                  ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
              ]"
            >
              JSON Data
            </button>
            <button
              v-if="form.links"
              @click="activeTab = 'links'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-xs',
                activeTab === 'links'
                  ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
              ]"
            >
              Links
            </button>
            <button
              v-if="form.options"
              @click="activeTab = 'options'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-xs',
                activeTab === 'options'
                  ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
              ]"
            >
              Options
            </button>
          </nav>
        </div>

        <!-- Content area -->
        <div class="flex-1 overflow-y-auto">
          <!-- Form Fields Tab -->
          <div v-if="activeTab === 'form' || !hasJsonFields">
            <form @submit.prevent="submit" class="grid grid-cols-8 gap-4">
              <template v-for="(fieldConfig, fieldName) in meta.form_fields" :key="String(fieldName)">
                <!-- Skip JSON fields in main form - they're handled in tabs -->
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

                  <!-- Select Field -->
                  <div v-if="fieldConfig.type === 'select'" class="mt-1">
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
                      >
                        {{ relation.title || relation.name || relation.id }}
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

          <!-- JSON Data Tab -->
          <div v-if="activeTab === 'json'" class="p-4">
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">JSON Data Editor</h3>
              
              <!-- JSON Editor with syntax highlighting -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                  JSON Content
                </label>
                <textarea
                  v-model="jsonString"
                  rows="15"
                  class="block w-full rounded-md shadow-sm text-sm font-mono border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400"
                  placeholder='{"key": "value", "array": [1, 2, 3]}'
                ></textarea>
                
                <!-- JSON validation indicator -->
                <div v-if="hasJsonError" class="absolute top-4 right-4">
                  <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                </div>
                <div v-else class="absolute top-4 right-4">
                  <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
              </div>
              
              <!-- JSON validation message -->
              <div v-if="hasJsonError" class="text-sm text-red-600 bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800">
                Invalid JSON format. Please check your syntax.
              </div>
              
              <!-- JSON preview -->
              <details v-if="!hasJsonError && jsonValue" class="mt-4">
                <summary class="text-sm text-gray-500 cursor-pointer font-medium">Preview JSON structure</summary>
                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded border">
                  <pre class="whitespace-pre-wrap text-sm">{{ JSON.stringify(jsonValue, null, 2) }}</pre>
                </div>
              </details>
            </div>
          </div>

          <!-- Links Tab -->
          <div v-if="activeTab === 'links'" class="p-4">
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Links Manager</h3>
              
              <!-- Add new link -->
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded border">
                <div class="flex items-center space-x-2 flex-1">
                  <select v-model="link.type" class="text-sm rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                    <option v-for="_link in availableLinkTypes" :value="_link">{{_link.replaceAll('_',' ')}}</option>
                  </select>
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
                <button @click.prevent="addLink" class="ml-3 px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                  Add Link
                </button>
              </div>

              <!-- Existing links -->
              <div v-if="links.length > 0" class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing Links</h4>
                <div v-for="(link, index) in links" :key="index" 
                     class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded border">
                  <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ link.type }}</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ link.link_title }}</span>
                  </div>
                  <button @click.prevent="removeLink(index)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Raw JSON Editor -->
              <details class="mt-4">
                <summary class="text-sm text-gray-500 cursor-pointer font-medium">Advanced: Edit JSON directly</summary>
                <textarea
                  v-model="form.links"
                  rows="8"
                  class="mt-2 block w-full rounded border text-sm font-mono border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700"
                  placeholder='[{"type": "relates_to", "link_id": "1", "link_title": "Related Item"}]'
                ></textarea>
              </details>
            </div>
          </div>

          <!-- Options Tab -->
          <div v-if="activeTab === 'options'" class="p-4">
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Options Manager</h3>
              
              <!-- Add new option -->
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded border">
                <div class="flex items-center space-x-2 flex-1">
                  <input
                    v-model="newOption.name"
                    placeholder="Option name"
                    class="text-sm rounded border px-3 py-2 flex-1 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700"
                  />
                  <input
                    v-model="newOption.url"
                    placeholder="URL"
                    class="text-sm rounded border px-3 py-2 flex-1 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700"
                  />
                  <input
                    v-model="newOption.route"
                    placeholder="Route"
                    class="text-sm rounded border px-3 py-2 flex-1 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700"
                  />
                </div>
                <button @click.prevent="addOption" class="ml-3 px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                  Add Option
                </button>
              </div>

              <!-- Existing options -->
              <div v-if="options.length > 0" class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing Options</h4>
                <div v-for="(option, index) in options" :key="index" 
                     class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded border">
                  <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ option.name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ option.url }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">{{ option.route }}</div>
                  </div>
                  <button @click.prevent="removeOption(index)" class="ml-3 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Raw JSON Editor -->
              <details class="mt-4">
                <summary class="text-sm text-gray-500 cursor-pointer font-medium">Advanced: Edit JSON directly</summary>
                <textarea
                  v-model="optionsJsonString"
                  rows="8"
                  class="mt-2 block w-full rounded border text-sm font-mono border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700"
                  placeholder='[{"name": "Dashboard", "url": "/dashboard", "route": "dashboard"}]'
                ></textarea>
              </details>
            </div>
          </div>
        </div>

        <!-- Footer with buttons -->
        <div :class="[_footer.base, _footer.buttons]">
          <button @click="$emit('close')" type="button" :class="[_button.active]">Cancel</button>
          <button @click="submit" type="button" :class="[_button.active]">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>
