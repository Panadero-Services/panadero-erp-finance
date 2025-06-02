<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from "@inertiajs/vue3";
import PostSearch from '@/components/PostSearch.vue';
import { validateField, parseRules } from '@/utils/validators';
import { Switch } from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import TheButton from "@/panadero/components/TheButton.vue";

// Props
const props = defineProps({
  lng: String,
  record: Object,
  module: String,
  table: String,
  superSelfAdmin: Boolean,
  db: Object,
});

const emit = defineEmits(['close', 'changeRecord']);

// State & Refs
const table = ref(props.table || 'posts');
const titleColumn = ref(props.record.titleColumn || 'title');
const idColumn = ref(props.record.idColumn || 'id');

const fields = ref<string[]>([]);
const readOnlyFields = ref(['id', 'created_at', 'updated_at']);
const boolFields = ref([
  'is_active', 'animate', 'sidebar', 'header', 'footer', 'public',
  'max_width', 'featured', 'blog', 'smart', 'published', 'locked', 'self'
]);

// Form setup
interface FormFields {
  id?: number;
  links?: string;
  is_active: boolean;
  is_archived?: boolean;
  [key: string]: any;
}

const form = useForm<FormFields>({
  ...props.record,
  is_active: props.record.is_active ?? true,
  is_archived: props.record.is_archived ?? false,
  links: props.record.links || '[]',
});

if (!form.links) {
  form.links = JSON.stringify([]);
}

// Computed: validation
const hasJsonLinkError = computed(() => {
  try {
    const links = JSON.parse(form.links);
    return !Array.isArray(links) || links.some(link => typeof link !== 'object' || !link.link_id || !link.type);
  } catch {
    return true;
  }
});

const hasJsonError = computed(() => (field: string) => {
  if (!form[field]) return false;
  try {
    JSON.parse(form[field]);
    return false;
  } catch {
    return true;
  }
});

const links = computed(() => {
  try {
    return JSON.parse(form.links);
  } catch {
    return [];
  }
});

const link = ref({ link_id: '', type: 'relates_to', link_title: '' });

// Methods
const addLink = () => {
  const newLinks = [...links.value, link.value];
  form.links = JSON.stringify(newLinks);
};

const removeLink = (index: number) => {
  const newLinks = [...links.value];
  newLinks.splice(index, 1);
  form.links = JSON.stringify(newLinks);
};

onMounted(() => {
  const allFields = Object.keys(props.record).filter(
    key => !['validation_rules', 'form_fields', 'links_table'].includes(key)
  );

  fields.value = allFields.sort((a, b) => {
    const seqA = props.record.form_fields?.[a]?.sequence || 0;
    const seqB = props.record.form_fields?.[b]?.sequence || 0;
    return seqA - seqB;
  });
});

const getRulesForField = (key: string) => {
  return props.record.validation_rules?.[key]
    ? parseRules(props.record.validation_rules[key])
    : null;
};

const isInvalid = (key: string) => {
  const rules = getRulesForField(key);
  return !validateField(form[key], rules);
};

const getRuleText = (key: string) => props.record.validation_rules?.[key] || '';

const isFormValid = computed(() =>
  fields.value.every(field => {
    const rules = getRulesForField(field);
    return !rules || validateField(form[field], rules);
  })
);

const submit = async () => {
  if (!isFormValid.value) return;

  form.put(route(`${props.table}.update`, props.record.id), {
    preserveScroll: true,
    onSuccess: () => {
      const user = usePage().props.auth.user;

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
    onFinish: () => false,
  });
};

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
</script>


<template>
  <div>
    <div :class="_container.base" @click="$emit('close')">
    </div>
    <div :class="[_window.base, _window.padding, _window.motion, _window.light, _window.dark]">
      <div class="h-full flex flex-col">
        <!-- Content area -->
        <div class="flex-1 overflow-y-auto">
          <!-- Editable Input 
          <div>{{links}}</div>
          <div>link: {{link}}</div>
          -->

          <!-- Edit Post title
          <div class="pb-2 text-left">
          <h3 class="text-base  dark:text-white mb-2">
              Edit Record <span class="text-indigo-500">{{ table }}</span>
          </h3>
          </div>
          -->

          <form @submit.prevent="submit" class="grid grid-cols-8 gap-4">
            <div
              v-for="f in fields"
              :key="f"
              :class="{
                'col-span-8': record.form_fields?.[f]?.col_span === 8,
                'col-span-6': record.form_fields?.[f]?.col_span === 6,
                'col-span-5': record.form_fields?.[f]?.col_span === 5,
                'col-span-4': record.form_fields?.[f]?.col_span === 4,
                'col-span-3': record.form_fields?.[f]?.col_span === 3,
                'col-span-2': record.form_fields?.[f]?.col_span === 2,
                'col-span-1': record.form_fields?.[f]?.col_span === 1 || !record.form_fields?.[f]?.col_span
              }"
            >
<!-- label --> <!-- Label left.. -->
              <label :for="f" class="block text-xs font-medium text-gray-700 dark:text-gray-200 ">
                  <span v-if="!(getRuleText(f) == 'boolean')">
                    {{ getRuleText(f) == 'boolean' ? f.replace('is_','') : f }}
                  </span>
                <span v-if="getRuleText(f) && (getRuleText(f)!='boolean')" :class="isInvalid(f) ? 'text-red-500' : 'text-gray-500'">({{ getRuleText(f) }})</span>

                  <!-- Validation message and character count -->
                  <span v-if="isInvalid(f)" class="text-xxs text-red-600 ml-4 pt-4">
                    <div v-if="typeof form[f] === 'string' && getRulesForField(f)">
                      <span v-for="rule in getRulesForField(f)" :key="rule.name">
                        <template v-if="['min', 'max'].includes(rule.name)">
                          Length: {{ form[f]?.length || 0 }} /
                          <span v-if="rule.name === 'min'">min {{ rule.param }}</span>
                          <span v-if="rule.name === 'max'">max {{ rule.param }}</span>
                        </template>
                      </span>
                    </div>
                  </span>

              </label>

              <!-- Boolean Switch  moved to footer-->
              <div v-if="getRuleText(f) == 'boolean' && superSelfAdmin" class="scale-75 text-center">
                 <!-- 
                <Switch v-model="form[f]"
                  :class="[
                    form[f] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-600',
                    'relative inline-flex h-6 w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
                  ]"
                >
                  <span aria-hidden="true"
                    :class="[
                      form[f] ? 'translate-x-5' : 'translate-x-0',
                      'inline-block h-5 w-5 transform rounded-full bg-white dark:bg-gray-200 shadow transition'
                    ]"
                  />
                </Switch>
                -->
              </div>

              <!-- Editable Input -->
              <div v-else-if="!readOnlyFields.includes(f) && superSelfAdmin">
<!-- Links  -->
                <!-- Links section for json field -->
                <template v-if="f === 'links'">

                  <div class="space-y-2 overflow-scroll z-30 ">
                    <div class="flex items-center justify-between p-1.5 rounded-sm">
                      <div class="flex items-center space-x-2 block w-full mr-2">
                        <select v-model="link.type" class="text-xs rounded-md" :class="[_input.light, _input.dark]">
                          <option v-for="_link in record.links_table" :value="_link">{{_link.replaceAll('_',' ')}}</option>
                        </select>
                        <PostSearch
                          :model-value="link.link_id==0"
                          :table="table"
                          :title-column="titleColumn"
                          :label="titleColumn"
                          :id-column="idColumn"
                          @update:model-value="(_value) => { link.link_id = _value; }"
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
                                {{ link?.type?.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) || 'N/A' }}
                              </span>
                              <span class="text-xs text-blue-600 dark:text-gray-300 w-6">
                                {{ link?.link_id || 'N/A' }}
                              </span>
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
                          v-if="record.form_fields?.[f]?.type === 'textarea' "
                          :rows="record.form_fields?.[f]?.rows || 3"
                          v-model="form[f]"
                          :class="[
                            'block w-full rounded-md shadow-sm sm:text-xs ',
                            isInvalid(f)
                              ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                              : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                            'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
                          ]"
                          :placeholder="record.form_fields?.[f]?.placeholder"
                        ></textarea>
                      </div>
                    </div>

                  </div>

                </template>

                <!-- Other fields -->
                <template v-if="record.form_fields?.[f]?.type === 'textarea' && !(f=='links')">

                   <!-- JSON Error Message -->
                    <div v-if="f=='json'"> 
                        <div v-if="hasJsonError(f)" class="bg-red-100 dark:bg-red-800 text-black text-center p-1 rounded-sm col-span-5 h-8">
                          JSON Error
                        </div>
                    </div>

                  <textarea
                    v-if="record.form_fields?.[f]?.type === 'textarea' "
                    :rows="record.form_fields?.[f]?.rows || 3"
                    v-model="form[f]"
                    :class="[
                      'mt-1 block w-full rounded-md shadow-sm sm:text-xs ',
                      isInvalid(f)
                        ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                      'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
                    ]"
                    :placeholder="record.form_fields?.[f]?.placeholder"
                  ></textarea>

                  <!-- String input -->
                  <input
                    v-else-if="typeof form[f] === 'string' || form[f] === null"
                    type="text"
                    v-model="form[f]"
                    :class="[
                      'mt-1 block w-full rounded-md shadow-sm sm:text-xs',
                      isInvalid(f)
                        ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                      'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
                    ]"
                    :placeholder="record.form_fields?.[f]?.placeholder"
                  />

                  <!-- Number input -->
                  <input
                    v-else-if="typeof form[f] === 'number'"
                    type="number"
                    v-model="form[f]"
                    :class="[
                      'mt-1 block w-full rounded-md shadow-sm sm:text-xs',
                      isInvalid(f)
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700'
                    ]"
                  />


                </template>
              </div>

              <!-- Read-only Field -->
          <div v-else :class="_input.readOnly">
                {{ form[f] }}
              </div>
            </div>
          </form>
        </div>

        <!-- Fixed line for switches above footer -->
        <div class="grid grid-cols-8 " :class="[_footer.base, _footer.switches]">
           <div
              v-for="f in fields"
              :key="f"
              :class="{
                'col-span-8': record.form_fields?.[f]?.col_span === 8,
                'col-span-6': record.form_fields?.[f]?.col_span === 6,
                'col-span-5': record.form_fields?.[f]?.col_span === 5,
                'col-span-4': record.form_fields?.[f]?.col_span === 4,
                'col-span-3': record.form_fields?.[f]?.col_span === 3,
                'col-span-2': record.form_fields?.[f]?.col_span === 2,
                'col-span-1': record.form_fields?.[f]?.col_span === 1 || !record.form_fields?.[f]?.col_span
              }"
            >
              <!-- Label left.. Boolean center -->
              <label :for="f" class="ml-4 text-center text-xs" v-if="getRuleText(f) == 'boolean'">
                <span >
                {{ f.replace('is_','') }}
              </span>
              </label>

              <!-- Boolean Switch -->
              <div v-if="getRuleText(f) == 'boolean' && superSelfAdmin" class="scale-75 ml-1">
                <Switch v-model="form[f]"
                  :class="[
                    form[f] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-600',
                    'relative inline-flex h-6 w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
                  ]"
                >
                  <span aria-hidden="true"
                    :class="[
                      form[f] ? 'translate-x-5' : 'translate-x-0',
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
            <button v-if="superSelfAdmin" @click="submit" type="button" :class="[_button.active]">Save</button>
        </div>


      </div>
    </div>
  </div>
</template>
