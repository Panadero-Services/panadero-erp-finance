
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
  <div class="rounded-lg shadow-md px-6 py-4 bg-white dark:bg-gray-800 flex flex-col items-start h-[400px] w-[450px]">
    <div class="flex items-center mb-4 w-full">
      <div class="h-12 w-12 bg-green-100 flex items-center justify-center rounded-full mr-4">
        <span class="text-green-600 font-bold text-xl">{{ initials }}</span>
      </div>
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ name }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ type }}</p>
      </div>
    </div>
    <!-- Tabs -->
    <div class="flex mb-2 w-full border-b border-gray-200 dark:border-gray-700">
      <button @click="activeTab = 'result'" :class="['px-4 py-2 text-sm font-medium focus:outline-none', activeTab === 'result' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500']">Result</button>
      <button @click="activeTab = 'config'" :class="['px-4 py-2 text-sm font-medium focus:outline-none', activeTab === 'config' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500']">Config</button>
    </div>
    <!-- Tab Content -->
    <div class="flex-1 w-full overflow-auto">
      <div v-if="activeTab === 'result'">
        <div v-if="isTesting" class="text-green-600 text-sm">Testing...</div>
        <div v-else-if="testError" class="text-red-500 text-sm">{{ testError }}</div>
        <div v-else-if="formattedTestResult && formattedTestResult.length" class="text-sm">
          <div v-for="(item, idx) in formattedTestResult" :key="idx" class="flex justify-between">
            <span class="font-medium text-green-700">{{ item.key }}:</span>
            <span class="text-gray-700">{{ item.value }}</span>
          </div>
        </div>
        <div v-else class="text-gray-400 text-sm">No result yet. Click Test.</div>
      </div>
      <div v-else-if="activeTab === 'config'">
        <pre class="text-xxs text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ formattedConfig }}</pre>
      </div>
    </div>
    <!-- Buttons -->
    <div class="flex space-x-2 w-full justify-end mb-0">
      <button class="mt-2.5 mx-1 rounded p-2 w-20 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="emitEdit">Edit</button>
      <button class="mt-2.5 mx-1 rounded p-2 w-20 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="handleTest">Test</button>
      <button class="mt-2.5 mx-1 rounded p-2 w-20 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="emitDelete">Delete</button>
    </div>
  </div>
</template>
