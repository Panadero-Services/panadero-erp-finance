<script setup>
import { computed, ref, onMounted } from 'vue';
import { useForm, usePage } from "@inertiajs/vue3";
import { Switch } from '@headlessui/vue';
import TheButton from "@/panadero/components/TheButton.vue";
import { validateField, parseRules } from '@/utils/validators';

const props = defineProps({
  lng: String,
  record: Object,
  module: String,
  table: String,
  superSelfAdmin: Boolean,
  db: Object
});

const emit = defineEmits(['close']);

const fields = ref([]);
const readOnlyFields = ref(['id', 'created_at', 'updated_at']);
const boolFields = ref([
  'is_active', 'animate', 'sidebar', 'header', 'footer', 'public',
  'max_width', 'featured', 'blog', 'smart', 'published', 'locked', 'self'
]);

const form = useForm({ ...props.record });

onMounted(() => {
  // Get all fields except validation_rules and form_fields
  const allFields = Object.keys(props.record).filter(key => (key !== 'validation_rules' && key !== 'form_fields'));
  
  // Sort fields by sequence number from form_fields
  fields.value = allFields.sort((a, b) => {
    const seqA = props.record.form_fields?.[a]?.sequence || 0;
    const seqB = props.record.form_fields?.[b]?.sequence || 0;
    return seqA - seqB;
  });
});

const getRulesForField = (key) => {
  return props.record.validation_rules[key] ? parseRules(props.record.validation_rules[key]) : null;
};

const isInvalid = (key) => {
  const rules = getRulesForField(key);
  return !validateField(form[key], rules);
};

const getRuleText = (key) => props.record.validation_rules?.[key] || '';

const submit = async () => {
  if (!isFormValid.value) {
    alert("Some fields are invalid.");
    return;
  }

  const _response = await form.put(route(`${props.table}.update`, props.record.id));

  const _logData = {
    action: `${props.table}.update`,
    user_id: usePage().props.auth.user.id || 'no_uid',
    module: props.module,
    node: 'none',
    team: usePage().props.auth.user.current_team.name || 'no_team',
    project: 'none',
    content: form.title || 'none',
    json: JSON.stringify(form),
    tags: 'content, posts',
  };

  await props.db.logAction(_logData);
  emit('close');
};

const isFormValid = computed(() => {
  return fields.value.every(field => {
    const rules = getRulesForField(field);
    return !rules || validateField(form[field], rules);
  });
});
</script>

<template>
  <div>
    <div class="fixed inset-0 z-20 bg-black opacity-50 dark:opacity-75" @click="$emit('close')"></div>

    <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl px-4 lg:px-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl transform -translate-x-1/2 -translate-y-1/2">
      <div class="pb-4 text-left">
        <h3 class="text-xl font-semibold dark:text-white mb-4">
          Edit Record <span class="text-indigo-500">{{ table }}</span>
        </h3>
      </div>

      <form @submit.prevent="submit" class="grid grid-cols-6 gap-4">
        <div
          v-for="f in fields"
          :key="f"
          :class="{
            'col-span-6': props.record.form_fields?.[f]?.col_span === 6,
            'col-span-4': props.record.form_fields?.[f]?.col_span === 4,
            'col-span-3': props.record.form_fields?.[f]?.col_span === 3,
            'col-span-2': props.record.form_fields?.[f]?.col_span === 2,
            'col-span-1': props.record.form_fields?.[f]?.col_span === 1 || !props.record.form_fields?.[f]?.col_span
          }"
        >
          <label :for="f" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            {{ f }}
            <span v-if="getRuleText(f)" class="text-xs text-gray-400 ml-1">({{ getRuleText(f) }})</span>
          </label>

          <!-- Boolean Switch -->
          <div v-if="boolFields.includes(f) && superSelfAdmin">
            <Switch v-model="form[f]"
              :class="[
                form[f] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-600',
                'relative inline-flex h-6 w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
              ]"
            >
              <span class="sr-only">{{ f }}</span>
              <span
                aria-hidden="true"
                :class="[
                  form[f] ? 'translate-x-5' : 'translate-x-0',
                  'inline-block h-5 w-5 transform rounded-full bg-white dark:bg-gray-200 shadow transition'
                ]"
              />
            </Switch>
          </div>

          <!-- Editable Input -->
          <div v-else-if="!readOnlyFields.includes(f) && superSelfAdmin">
            <!-- Textarea based on formFields config -->
            <textarea
              v-if="props.record.form_fields?.[f]?.type === 'textarea'"
              :rows="props.record.form_fields?.[f]?.rows || 3"
              v-model="form[f]"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
                isInvalid(f)
                  ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-400'
                  : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
              ]"
              :placeholder="props.record.form_fields?.[f]?.placeholder"
            ></textarea>

            <!-- String input -->
            <input
              v-else-if="typeof form[f] === 'string' || form[f] === null"
              type="text"
              v-model="form[f]"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
                isInvalid(f)
                  ? 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-400'
                  : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700',
                'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400'
              ]"
              :placeholder="props.record.form_fields?.[f]?.placeholder"
            />

            <!-- Number input -->
            <input
              v-else-if="typeof form[f] === 'number'"
              type="number"
              v-model="form[f]"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
                isInvalid(f)
                  ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                  : 'border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-700 focus:ring-indigo-500 dark:focus:ring-indigo-700'
              ]"
            />

            <!-- Validation message and character count -->
            <div v-if="isInvalid(f)" class="text-xs text-red-600 mt-1">
              Invalid value for {{ f }}.
              <div v-if="typeof form[f] === 'string' && getRulesForField(f)">
                <span v-for="rule in getRulesForField(f)" :key="rule.name">
                  <template v-if="['min', 'max'].includes(rule.name)">
                    Length: {{ form[f]?.length || 0 }} /
                    <span v-if="rule.name === 'min'">min {{ rule.param }}</span>
                    <span v-if="rule.name === 'max'">max {{ rule.param }}</span>
                  </template>
                </span>
              </div>
            </div>
          </div>

          <!-- Read-only Field -->
          <div v-else class="mt-1 text-sm text-gray-500 dark:text-gray-300">
            {{ form[f] }}
          </div>
        </div>

        <!-- Buttons -->
        <div class="col-span-6 pt-4 border-t mt-4 flex justify-end space-x-2">
          <button
            type="button"
            @click="$emit('close')"
            class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600"
          >
            Cancel
          </button>
          <button
            type="submit"
            v-if="superSelfAdmin"
            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 dark:bg-indigo-700 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 dark:hover:bg-indigo-800"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
