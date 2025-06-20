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

// Relations list for dynamic select fields
const relations = reactive({});

// Links table based on current table
const linksTable = computed(() => {
  return [table.value, `${table.value}_related`, `${table.value}_dependencies`];
});

// Initialize form with default values
const loadForm = async () => {
  try {
    const initialForm = {};
    Object.keys(props.record.form_fields || {}).forEach(field => {
      // For select fields, use the specific relation ID from the record
      if (props.record.form_fields[field].type === 'select') {
        // Get the relation ID from the record (e.g., user_id, project_id)
        const relationId = props.record[field];
        
        // If we have a relation object (e.g., user, project), use its ID
        if (props.record[field.replace('_id', '')]) {
          const relation = props.record[field.replace('_id', '')];
          initialForm[field] = relation.id;
        } else {
          initialForm[field] = relationId;
        }
        
        // Load the relations data
        const _table = (field).replace("_id", "s");
        loadRelations(_table, field);
      } else {
        initialForm[field] = props.record[field] || null;
      }
    });
    
    Object.assign(form, initialForm);
  } catch (error) {
    console.error('Error initializing form:', error);
  }
};

// Load relations for select fields
const loadRelations = async (_table, _field) => {
  try {
    // Use a non-blocking approach to prevent page refresh
    // For now, we'll skip loading relations on modal open to prevent refresh
    // Relations can be loaded when user actually interacts with select fields
    relations[_field] = [];
    
    // Alternative: Use a timeout to delay the API call
    setTimeout(async () => {
      try {
        const response = await props.db.fetchApiRecords(_table);
        relations[_field] = response;
      } catch (error) {
        console.error('Error fetching relations:', error);
        relations[_field] = [];
      }
    }, 100);
  } catch (error) {
    console.error('Error in loadRelations:', error);
    relations[_field] = [];
  }
};

// Validation methods
const getRulesForField = (key) => {
  return props.record.validation_rules?.[key]
    ? parseRules(props.record.validation_rules[key])
    : null;
};

const isInvalid = (key) => {
  const rules = getRulesForField(key);
  return !validateField(form[key], rules);
};

const getRuleText = (key) => props.record.validation_rules?.[key] || '';

const isFormValid = computed(() =>
  Object.keys(props.record.form_fields || {}).every(field => {
    const rules = getRulesForField(field);
    return !rules || validateField(form[field], rules);
  })
);

// Form submission
const submit = async () => {
  if (!isFormValid.value) return;

  // Format the data before submission
  const formData = {
    ...form,
    // Keep json as a string if it's already a string, otherwise stringify it
    json: typeof form.json === 'string' ? form.json : JSON.stringify(form.json),
    // Keep links as a string if it's already a string, otherwise stringify it
    links: typeof form.links === 'string' ? form.links : JSON.stringify(form.links)
  };

  router.put(`/api/${props.table}/${form.id}`, formData, {
    preserveScroll: true,
    onSuccess: () => {
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
    },
    onError: (errors) => {
      console.error('Form submission error:', errors);
    },
    onFinish: () => true,
  });
};

// Initialize form on mount
onMounted(() => {
  if (!props.record.id) return;
  if ( usePage().props.auth &&  usePage().props.auth.user) {
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
  return props.record.links_table || [];
});

const hasJsonLinkError = computed(() => {
  try {
    const links = JSON.parse(form.links || '[]');
    return !Array.isArray(links) || links.some(link => typeof link !== 'object' || !link.link_id || !link.type);
  } catch {
    return true;
  }
});

const links = computed(() => {
  try {
    return JSON.parse(form.links || '[]');
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
            <template v-for="(fieldConfig, fieldName) in record.form_fields" :key="String(fieldName)">
              <div
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


                <div v-if="fieldConfig.readonly" class="mt-3" :class="_input.readOnly">
                    {{ form[fieldName] }}
                </div>

<!-- Select Field -->
                <div v-else-if="fieldConfig.type === 'select'" class="mt-1">
                  <select
                    v-model="form[String(fieldName)]"
                    :id="String(fieldName)"
                    class="block w-full pl-3 pr-10 py-2 text-xs rounded-md border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                    :required="record.validation_rules?.[String(fieldName)]?.includes('required')"
                  >
                    <option v-if="!form[String(fieldName)]" value="">Select {{ fieldConfig.label }}</option>
                    <!-- Show relations if they exist, otherwise show options -->
                    <template v-if="relations[String(fieldName)]?.length">
                      <option v-for="relation in relations[String(fieldName)]" :key="String(relation.id)" :value="String(relation.id)" :selected="String(relation.id) === String(form[String(fieldName)])">
                       {{ relation.id || relation.name}} {{ relation.title || relation.name}}
                      </option>
                    </template>
                    <template v-else>
                      <option v-for="(label, id) in fieldConfig.options" :key="String(id)" :value="String(id)" :selected="String(id) === String(form[String(fieldName)])">
                        {{ label }}
                      </option>
                    </template>
                  </select>
                  <p v-if="fieldConfig.help" class="mt-1 text-xs text-gray-500">{{ fieldConfig.help }}</p>
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
                              <span class="text-xs text-gray-600 dark:text-gray-300 w-24">
                                {{ link?.type?.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase()) || 'N/A' }}
                              </span>
                              <span class="text-xs text-blue-600 dark:text-gray-300 w-6">{{ link?.link_id || 'N/A' }}</span>
                              <span class="text-xs text-blue-600 dark:text-gray-300 w-max hover:text-black cursor-pointer" 
                                    @click="emit('changeRecord', { type: link.type, id: link.link_id })">
                                {{ link?.link_title || 'N/B' }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-span-3">
                        <!-- Show col-span-3 links inputbox -->
                        <textarea
                          v-if="record.form_fields?.[fieldName]?.type === 'textarea'"
                          :rows="record.form_fields?.[fieldName]?.rows || 2"
                          v-model="form[String(fieldName)]"
                          :class="[
                            'block w-full rounded-md shadow-sm sm:text-xs ',
                            isInvalid(String(fieldName))
                              ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                              : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                            'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
                          ]"
                          :placeholder="record.form_fields?.[fieldName]?.placeholder"
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
                      @click="form[String(fieldName)] = true"
                      :class="[
                        _button.base,
                        form[String(fieldName)] ? _button.active : _button.inactive
                      ]"
                    >
                      Yes
                    </button>
                    <button
                      type="button"
                      @click="form[String(fieldName)] = false"
                      :class="[
                        _button.base,
                        !form[String(fieldName)] ? _button.active : _button.inactive
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
                    :class="[
                      _input.base,
                      _input.light,
                      _input.dark,
                      isInvalid(String(fieldName)) ? 'border-red-500' : ''
                    ]"
                    :required="record.validation_rules?.[String(fieldName)]?.includes('required')"
                  />
                  <textarea
                    v-else
                    :id="String(fieldName)"
                    :rows="record.form_fields?.[fieldName]?.rows || 2"
                    v-model="form[String(fieldName)]"
                    :class="[
                      _input.base,
                      _input.light,
                      _input.dark,
                      isInvalid(String(fieldName)) ? 'border-red-500' : ''
                    ]"
                    :required="record.validation_rules?.[String(fieldName)]?.includes('required')"
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
              v-for="(fieldConfig, fieldName) in record.form_fields"
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
              <label :for="fieldName" class="ml-4 text-center text-xs" v-if="getRuleText(fieldName) == 'boolean'">
                <span >
                {{ fieldName.replace('is_','') }}
              </span>
              </label>

              <!-- Boolean Switch -->
              <div v-if="getRuleText(fieldName) == 'boolean'" class="scale-75 ml-1">
                <Switch v-model="form[fieldName]"
                  :class="[
                    form[fieldName] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-600',
                    'relative inline-flex h-6 w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
                  ]"
                >
                  <span aria-hidden="true"
                    :class="[
                      form[fieldName] ? 'translate-x-5' : 'translate-x-0',
                      'inline-block h-5 w-5 transform rounded-full bg-white dark:bg-gray-200 shadow transition'
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
