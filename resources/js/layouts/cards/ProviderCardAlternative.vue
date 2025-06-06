<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  parameters: {
    type: String,
    default: '{}'
  },
  initials: {
    type: String,
    default: ''
  },
  provider: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['edit', 'delete', 'test']);

const activeTab = ref('result');
const isTesting = ref(false);
const testResult = ref(null);
const testError = ref(null);

function emitEdit() {
  emit('edit', props.provider || { name: props.name, type: props.type, config: props.description });
}
function emitDelete() {
  emit('delete', (props.provider && props.provider.id) || null);
}

const handleTest = async () => {
  isTesting.value = true;
  testResult.value = null;
  testError.value = null;
  try {
    emit('test', props.provider || { name: props.name, type: props.type, config: props.description });
  } catch (e) {
    testError.value = e.message || 'Test failed.';
  } finally {
    isTesting.value = false;
  }
};

const formattedConfig = computed(() => {
  try {
    return JSON.stringify(JSON.parse(props.description), null, 2);
  } catch {
    return props.description;
  }
});

const formattedParameters = computed(() => {
  try {
    return JSON.stringify(JSON.parse(props.parameters), null, 2);
  } catch {
    return props.parameters;
  }
});

const formattedTestResult = computed(() => {
  if (!testResult.value) return [];
  if (typeof testResult.value === 'object' && !Array.isArray(testResult.value)) {
    return Object.entries(testResult.value).map(([key, value]) => ({
      key,
      value: typeof value === 'object' ? JSON.stringify(value) : value
    }));
  }
  return [{ key: 'Result', value: testResult.value }];
});

// Expose for parent update
function updateTestResult(result, error) {
  isTesting.value = false;
  testResult.value = result;
  testError.value = error;
}
defineExpose({ updateTestResult });
</script> 
<template>
  <div class="rounded-lg shadow-md px-4 py-3 bg-white dark:bg-slate-900 flex flex-col items-start h-[400px] w-full transition-all duration-200 hover:shadow-lg">
    <!-- Header section -->
    <div class="flex items-center mb-1 w-full">
      <div class="h-12 w-12 bg-green-100 dark:bg-green-900 flex items-center justify-center rounded-full -ml-2 mr-2 -mt-12">
        <span class="text-green-600 dark:text-green-300 font-bold text-base">{{ initials }}</span>
      </div>
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ name }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-500">{{ type }}</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex mb-2 w-full border-b border-gray-200 dark:border-gray-700">
      <button 
        @click="activeTab = 'result'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'result' 
            ? 'border-b-2 border-green-600 text-green-600 dark:text-green-400 dark:border-green-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Result
      </button>
      <button 
        @click="activeTab = 'config'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'config' 
            ? 'border-b-2 border-green-600 text-green-600 dark:text-green-400 dark:border-green-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Config
      </button>
      <button 
        @click="activeTab = 'parameters'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'parameters' 
            ? 'border-b-2 border-green-600 text-green-600 dark:text-green-400 dark:border-green-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Parameters
      </button>
    </div>

    <!-- Content area -->
    <div class="flex-1 w-full overflow-auto">
      <div v-if="activeTab === 'result'" class="space-y-2">
        <div v-if="isTesting" class="text-green-600 dark:text-green-400 text-sm flex items-center">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Testing...
        </div>
        <div v-else-if="testError" class="text-red-500 dark:text-red-400 text-sm p-3 bg-red-50 dark:bg-red-900/20 rounded border border-red-200 dark:border-red-800">
          <div class="font-semibold mb-1">Error:</div>
          {{ testError }}
        </div>
        <div v-else-if="formattedTestResult && formattedTestResult.length" class="space-y-2">
          <div v-for="(item, idx) in formattedTestResult" 
               :key="idx" 
               class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-600">
            <span class="font-medium text-green-700 dark:text-green-400 text-xs">{{ item.key }}{{JSON.stringify(item.value).length }}:</span>
            <span v-if="JSON.stringify(item.value).length>25" class="text-gray-700 dark:text-gray-300 ml-2 text-xs">{{ JSON.stringify(item.value) }}</span>
            <span v-else class="text-gray-700 dark:text-gray-300 ml-2 text-xs">{{ item.value }}</span>
          </div>
        </div>
        <div v-else class="text-gray-400 dark:text-gray-500 text-sm italic p-3 bg-gray-50 dark:bg-gray-700/30 rounded border border-gray-200 dark:border-gray-600">
          No result yet. Click Test to begin.
        </div>
      </div>

      <div v-else-if="activeTab === 'config'" class="font-mono">
        <pre class=" text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-600 overflow-auto text-xxs">{{ formattedConfig }}</pre>
      </div>

      <div v-else-if="activeTab === 'parameters'" class="font-mono">
        <pre class=" text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-600 overflow-auto text-xxs">{{ formattedParameters }}</pre>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="flex space-x-1 w-full justify-end mt-3 pt-0 border-t border-gray-200 dark:border-gray-700">
      <button class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="emitEdit">Edit</button>
      <button class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="handleTest">Test</button>
      <button class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="emitDelete">Delete</button>
    </div>
  </div>
</template>
