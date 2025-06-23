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
      } else {
        // Simple assignment for non-select fields
        initialForm[field] = props.record[field] || null;
      }
    });
    
    Object.assign(form, initialForm);
    
    // Convert arrays to JSON strings for textarea display
    if (Array.isArray(form.json)) {
      form.json = JSON.stringify(form.json);
    }
    
    if (Array.isArray(form.links)) {
      form.links = JSON.stringify(form.links);
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
  if (!isFormValid.value) return;

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
      
      filteredData[fieldName] = value;
    }
  });
  
  // Always include the ID for the update
  filteredData.id = form.id;

  // Format json and links fields
  if (filteredData.json !== undefined) {
    filteredData.json = typeof filteredData.json === 'string' ? filteredData.json : JSON.stringify(filteredData.json);
  }
  if (filteredData.links !== undefined) {
    filteredData.links = typeof filteredData.links === 'string' ? filteredData.links : JSON.stringify(filteredData.links);
  }

  // Use axios directly with proper CSRF handling for API routes
  try {
    const response = await axios.put(`/api/${props.table}/${form.id}`, filteredData, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      withCredentials: true
    });

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
    // Handle validation errors silently
    if (error.response?.status === 422) {
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
</script>

<template>
  <div>
    <div :class="_container.base" @click="$emit('close')">
    </div>
    <div :class="[_window.base, _window.padding, _window.motion, _window.light, _window.dark]">
      <div class="h-full flex flex-col">
        <!-- Content area -->
        <div class="flex-1 overflow-y-auto">
          <form @submit.prevent="submit" class="grid grid-cols-8 gap-4">
            <template v-for="(fieldConfig, fieldName) in meta.form_fields" :key="String(fieldName)">
              <!-- Skip switch fields in main form - they're handled in footer -->
              <div
                v-if="fieldConfig.type !== 'switch'"
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

                <!-- JSON Field -->
                <div v-else-if="fieldName === 'json'" class="mt-1">
                  <textarea
                    v-if="meta.form_fields?.[fieldName]?.type === 'textarea'"
                    :rows="meta.form_fields?.[fieldName]?.rows || 2"
                    v-model="form[String(fieldName)]"
                    :readonly="fieldConfig.readonly"
                    :class="[
                      'block w-full rounded-md shadow-sm sm:text-xs ',
                      isInvalid(String(fieldName))
                        ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                      'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400',
                      fieldConfig.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                    ]"
                    :placeholder="meta.form_fields?.[fieldName]?.placeholder"
                  ></textarea>
                </div>

                <!-- Links Field -->
                <div v-else-if="fieldName === 'links'" class="mt-1">
                  <div class="space-y-2 overflow-scroll z-30">
                    <div class="flex items-center justify-between p-1.5 rounded-sm">
                      <div class="flex items-center space-x-2 block w-full mr-2">
                        <select v-model="link.type" class="text-xs rounded-md" :class="[_input.light, _input.dark]">
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
                      <button @click.prevent="addLink" :class="[_button.active]" class="ml-2">Add_Link</button>
                    </div>

                    <!-- Existing links display -->
                    <div class="grid grid-cols-8">
                      <!-- Show col-span-5 links items -->
                      <!-- JSON Error Message -->
                      <div v-if="hasJsonLinkError" class="bg-red-100 dark:bg-red-800 text-black text-center p-2 rounded-sm col-span-5 h-12">
                        JSON Error
                      </div>

                      <div v-if="!hasJsonLinkError && links" class="col-span-5">
                        <div v-for="(link, index) in links" 
                            :key="index" 
                            v-if="index !== (links.length - 1)"
                            class="bg-gray-100 dark:bg-gray-700 rounded-sm flex">
                          <button @click.prevent="removeLink(index)" class="text-indigo-500 hover:text-red-700 w-6 ml-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                          </button>
                          <div class="flex items-center justify-between">
                            <div class="flex items-center">
                              <span class="text-xs text-gray-600 dark:text-gray-300">{{ link.type }}</span>
                              <span class="text-xs text-gray-800 dark:text-gray-200 ml-2">{{ link.link_title }}</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-span-3">
                        <textarea
                          v-if="meta.form_fields?.[fieldName]?.type === 'textarea'"
                          :rows="meta.form_fields?.[fieldName]?.rows || 2"
                          v-model="form[String(fieldName)]"
                          :readonly="fieldConfig.readonly"
                          :class="[
                            'block w-full rounded-md shadow-sm sm:text-xs ',
                            isInvalid(String(fieldName))
                              ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                              : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                            'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400',
                            fieldConfig.readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : ''
                          ]"
                          :placeholder="meta.form_fields?.[fieldName]?.placeholder"
                        ></textarea>
                      </div>
                    </div>
                  </div>
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
