<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { useDbStore } from '@/stores/db';
import { useSettingsStore } from '@/stores/settings';

const props = defineProps({
    module: String,
    table: String,
    provider: {
        type: Object,
        required: true
    },
    db: {
        type: Object,
        required: true
    },
    set: {
        type: Object,
        required: true
    },
    isCompact: {
        type: Boolean,
        default: false
    }
});

// variables
const _iconChangedField = ref('iconChanged');
const _iconChangedId = ref(0);
const _iconChangedValue = ref(false);
const _true = true;
const _false = false;
const isTesting = ref(false);
const testResult = ref(null);
const testError = ref(null);

const processConfig = (config) => {
  if (typeof config === 'string') {
    try {
      return JSON.stringify(JSON.parse(config), null, 2);
    } catch (e) {
      return config;
    }
  }
  return JSON.stringify(config, null, 2);
}

const _config = processConfig(props.provider.config);

// css
const _container = {
  base: "p-0 h-[150px] opacity-90 from-gray-700/50 via-transparent rounded-xs shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500",
  light: "bg-white text-gray-600",
  dark: "dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300"
};

const _card = {
  base: "flex flex-col h-full min-h-24 p-2 sm:p-3  tracking-tight leading-4 ",
  body: "hover:text-xs transition-all duration-250",
  flex: _config.length > 800 ? "text-xxs pr-6" : "text-xs"
};

const _title = {
  base: "text-lg/5 tracking-tight max-lg:text-center0 ",
  noaccess: "dark:text-gray-50 text-gray-800",
  access: "dark:text-indigo-400 text-indigo-600 hover:text-black hover:dark:text-yellow-400"
};

const _subtitle = {
  base: "text-xxs font-medium tracking-tight text-gray-500 max-lg:text-center dark:text-green-400",
}

const _footer = {
  base: "mt-auto",
};

const emit = defineEmits(['edit', 'delete', 'balance', 'test']);

const handleTest = async () => {
  isTesting.value = true;
  testResult.value = null;
  testError.value = null;
  emit('test', props.provider);
};

// Watch for test results from parent
const updateTestResult = (result, error) => {
  isTesting.value = false;
  testResult.value = result;
  testError.value = error;
};

defineExpose({ updateTestResult });

// Add this computed property after other computed properties
const formattedTestResult = computed(() => {
  if (!testResult.value) return null;
  
  // If it's a simple object with key-value pairs
  if (typeof testResult.value === 'object' && !Array.isArray(testResult.value)) {
    return Object.entries(testResult.value).map(([key, value]) => ({
      key,
      value: typeof value === 'object' ? JSON.stringify(value) : value
    }));
  }
  
  // If it's an array or other format, return as is
  return testResult.value;
});

</script>

<template>
  <div class="relative block w-full">
    <div :class="[_container.base, _container.light, _container.dark]">
      <div :class="[_card.base, isCompact ? 'max-h-[200px]' : '']">
        <!-- Full Mode -->
        <div v-if="!isCompact" class="flex flex-col flex-grow overflow-scroll">
          <div id="headers" class="space-y-1">
            <p @click="$emit('edit', provider)" :class="[_title.base, _title.access]">{{provider.name}}</p>
            <p :class="_subtitle.base">Type: {{provider.type}}</p>
          </div>

          <!-- Config Section (Full Height) -->
          <div id="config" class="mt-1 font-mono flex-grow overflow-auto" :class="[_card.flex, _card.body]">
            <pre class="text-xs">{{_config}}</pre>
          </div>
        </div>

        <!-- Compact Mode -->
        <div v-else class=" relative h-full">
          <!-- Title and Type -->
          <div class="">
            <div class="flex items-center space-x-0.5">
              <p @click="$emit('edit', provider)" class="text-[11px] font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer truncate">{{provider.name}}</p>
              <span class="text-[9px] text-gray-400 dark:text-gray-500">•</span>
              <p class="text-[9px] text-gray-500 dark:text-gray-400 truncate">{{provider.type}}</p>
            </div>
          </div>

          <!-- Result/Config Space -->
          <div class="">
            <div v-if="isTesting" class="text-[9px] text-indigo-400 dark:text-indigo-300">
              Testing...
            </div>
            <div v-else-if="testError" class="text-[9px] text-red-400 dark:text-red-300">
              {{ testError }}
            </div>
            <div v-else-if="formattedTestResult" class="text-[9px]">
              <div v-for="(item, index) in formattedTestResult" :key="index" class="flex items-center space-x-0.5">
                <span class="text-indigo-400 dark:text-indigo-300">{{ item.key }}:</span>
                <span class="text-emerald-400 dark:text-emerald-300">{{ item.value }}</span>
              </div>
            </div>
            <div v-else class="text-[9px] text-gray-400 dark:text-gray-500 truncate">
              {{ _config }}
            </div>
          </div>

          <!-- Buttons -->
          <div class="absolute bottom-0 left-0 right-0 grid grid-cols-4 gap-0.5">
            <button @click="handleTest" class="w-full text-[10px] bg-green-500 text-white hover:bg-green-600" title="Test">
              Test
            </button>
            <button @click="$emit('edit', provider)" class="w-full text-[10px] bg-indigo-500 text-white hover:bg-indigo-600" title="Edit">
              Edit
            </button>
            <button @click="$emit('balance', provider)" class="w-full text-[10px] bg-blue-500 text-white hover:bg-blue-600" title="Balance">
              Balance
            </button>
            <button @click="$emit('delete', provider.id)" class="w-full text-[10px] bg-red-500 text-white hover:bg-red-600" title="Delete">
              Delete
            </button>
          </div>
        </div>

        <!-- Test Result Section (Full Mode Only) -->
        <div v-if="!isCompact" class="mt-2">
          <div v-if="isTesting" class="text-xs text-indigo-400 dark:text-indigo-300">
            Testing...
          </div>
          <div v-if="testError" class="text-xs text-red-400 dark:text-red-300">
            {{ testError }}
          </div>
          <div v-if="formattedTestResult" class="text-xs">
            <div v-for="(item, index) in formattedTestResult" :key="index" class="flex justify-between">
              <span class="font-medium text-indigo-400 dark:text-indigo-300">{{ item.key }}:</span>
              <span class="text-emerald-400 dark:text-emerald-300">{{ item.value }}</span>
            </div>
          </div>
        </div>

        <!-- Footer (Full Mode Only) -->
        <div v-if="!isCompact" :class="_footer.base">
          <div class="grid grid-cols-2 border-t-2 dark:border-gray-700 my-2">
            <div class="flex flex-1 justify-start">
              <div class="flex flex-wrap items-center justify-between sm:flex-nowrap">
                <div class="ml-0 mt-2">
                  <div class="flex items-center">
                    <div class="ml-2">
                      <p class="text-sm text-gray-900 dark:text-indigo-400">ID: {{provider.id}}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-2 mt-2 justify-end">
              <button @click="$emit('edit', provider)" class="px-2 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600">
                Edit
              </button>
              <button @click="handleTest" class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                Test
              </button>
              <button @click="$emit('balance', provider)" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                Balance
              </button>
              <button @click="$emit('delete', provider.id)" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 