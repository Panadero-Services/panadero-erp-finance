<script setup>
import { ref, computed, onMounted } from 'vue';
import { formatDistance } from 'date-fns';
import { XMarkIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
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
  db: Object
});

const emit = defineEmits(['close', 'edit', 'delete']);

// State
const modelConfig = ref(null);
const loading = ref(true);
const error = ref(null);

// Computed for formatted body
const formattedBody = computed(() => {
  if (!props.record.body) return 'No content';
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

// Fetch model configuration
const fetchModelConfig = async () => {
  try {
    loading.value = true;
    const response = await props.db.getModelConfig(props.module, props.table);
    modelConfig.value = response;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

// Handle edit
const handleEdit = () => {
  emit('edit', props.record.id);
};

// Handle delete
const handleDelete = () => {
  if (confirm('Are you sure you want to delete this record?')) {
    emit('delete', props.record.id);
  }
};

// Close modal
const closeModal = () => {
  emit('close');
};

onMounted(() => {
  fetchModelConfig();
});
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-2/3 h-1/3 max-w-4xl max-h-96 overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-3">
          <!-- Avatar -->
          <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
            {{ initials }}
          </div>
          
          <!-- Title and ID -->
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ record.title || 'Untitled' }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              ID: {{ record.id }}
            </p>
          </div>
        </div>
        
        <!-- Close button -->
        <button
          @click="closeModal"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
        >
          <XMarkIcon class="h-6 w-6" />
        </button>
      </div>

      <!-- Content -->
      <div class="flex h-full">
        <!-- Main content area -->
        <div class="flex-1 p-4 overflow-y-auto">
          <!-- Description -->
          <div class="mb-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h3>
            <div class="text-sm text-gray-700 dark:text-gray-300" v-html="formattedBody"></div>
          </div>

          <!-- Status flags -->
          <div v-if="statusFlags.length > 0" class="mb-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Status</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="flag in statusFlags"
                :key="flag.key"
                :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  flag.active
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                ]"
              >
                {{ flag.label }}
              </span>
            </div>
          </div>

          <!-- Key fields -->
          <div v-if="displayFields.length > 0" class="mb-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Details</h3>
            <div class="grid grid-cols-2 gap-4">
              <div
                v-for="field in displayFields.slice(0, 6)"
                :key="field"
                class="text-sm"
              >
                <span class="font-medium text-gray-700 dark:text-gray-300">
                  {{ field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}:
                </span>
                <span class="text-gray-600 dark:text-gray-400 ml-1">
                  {{ record[field] || 'N/A' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Action buttons sidebar -->
        <div class="w-24 bg-gray-50 dark:bg-gray-700 p-4 flex flex-col gap-2">
          <button
            @click="handleEdit"
            class="flex items-center justify-center p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
            title="Edit"
          >
            <PencilIcon class="h-4 w-4" />
          </button>
          
          <button
            @click="handleDelete"
            class="flex items-center justify-center p-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
            title="Delete"
          >
            <TrashIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template> 