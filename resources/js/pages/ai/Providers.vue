<script setup>
import { ref, computed, onMounted, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
//import MainSection from '@/sections/BotsSection.vue';
//import BotsSection from "@/sections/BotsSection.vue"

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import ProviderCard from '@/layouts/cards/ProviderCard.vue';

_set.domainFunction = "ai";

const props = defineProps({
    page: Object,
    baseSections: Object
});

// webhooks
onMounted(fetchProviders);

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);

//import { ref, onMounted } from 'vue';
//import axios from 'axios'; // Ensure axios is installed and configured

const providers = ref([]);
const showModal = ref(false);
const editingProvider = ref(null); // Holds the provider object being edited
const currentProvider = ref({
  name: '',
  type: '',
  config: '{}', // Store config as string for textarea, parse/stringify on save/load
});
const configError = ref('');

// Add view mode state
const isCompactView = ref(false);

// Fetch providers from API
async function fetchProviders() {
  try {
    const response = await axios.get('/api/providers');
    providers.value = response.data.map(p => ({
      ...p,
      config: typeof p.config === 'string' ? p.config : JSON.stringify(p.config, null, 2) // Ensure config is string for display
    }));
  } catch (error) {
    console.error('Error fetching providers:', error);
    // Handle error (e.g., show a notification)
  }
}

function openModal(provider = null) {
  configError.value = '';
  if (provider) {
    editingProvider.value = { ...provider }; // Clone to avoid direct mutation
    currentProvider.value = {
      ...provider,
      config: typeof provider.config === 'object' ? JSON.stringify(provider.config, null, 2) : provider.config,
     };
  } else {
    editingProvider.value = null;
    currentProvider.value = { name: '', type: '', config: '{}' };
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingProvider.value = null;
}

function isValidJson(str) {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }
  return true;
}

// In provider.vue
async function saveProvider() {
  if (!isValidJson(currentProvider.value.config)) {
    configError.value = 'Configuration must be valid JSON.';
    return;
  }
  configError.value = '';

  let parsedConfig;
  try {
    parsedConfig = JSON.parse(currentProvider.value.config);
  } catch (e) {
    configError.value = 'Configuration is not valid JSON. Please correct it.';
    return; // Should not happen if isValidJson passed, but good for safety
  }

  const payload = {
    name: currentProvider.value.name,
    type: currentProvider.value.type,
    config: parsedConfig, // Send the PARSED OBJECT
  is_active: 1,

};

  try {
    if (editingProvider.value) {
      // Update existing provider
      await axios.put(`/api/providers/${editingProvider.value.id}`, payload);
    } else {
      // Create new provider
      //await axios.post('/api/providers', payload);

      const response = await axios.post('/api/providers', payload, {
          headers: {
              'withCredentials': true,
              'content-type': 'application/json',
              'Sign': 'postSignature'
          }
      });
      
      console.log(response.data);
      //return response.data;

    }
    closeModal();
    fetchProviders(); // Refresh the list
  } catch (error) {
    console.error('Error saving provider:', error);
    // Handle error (e.g., show validation errors from API)
     if (error.response && error.response.data && error.response.data.errors) {
        // Display validation errors from Laravel
        // This is a simple example; you might want a more robust error display system
        alert(JSON.stringify(error.response.data.errors));
    } else {
        alert('An error occurred while saving the provider.');
    }
  }
}

async function deleteProvider(id) {
  if (!confirm('Are you sure you want to delete this provider?')) {
    return;
  }
  try {
    await axios.delete(`/api/providers/${id}`);
    fetchProviders(); // Refresh the list
  } catch (error) {
    console.error('Error deleting provider:', error);
    // Handle error
  }
}




// New refs for Balance Modal
const showBalanceModal = ref(false);
const isFetchingBalance = ref(false);
const balanceData = ref(null);
const balanceError = ref('');
const selectedProviderForBalance = ref(null);

// ... existing methods: fetchProviders, openModal, closeModal, saveProvider, deleteProvider, testProvider, openTestResultModal, closeTestResultModal

// Methods for Balance Modal
const openBalanceModal = async (provider) => {
  selectedProviderForBalance.value = provider;
  balanceData.value = null;
  balanceError.value = '';
  isFetchingBalance.value = true;
  showBalanceModal.value = true;

  try {
    // Ensure config is parsed if it's a string
    let config = provider.config;
    if (typeof config === 'string') {
        try {
            config = JSON.parse(config);
        } catch (e) {
            balanceError.value = 'Provider configuration is not valid JSON.';
            isFetchingBalance.value = false;
            return;
        }
    }

    if (!config.contract_address || !config.rpc_url) {
      balanceError.value = 'Provider configuration must include "contract_address" and "rpc_url" to fetch balance.';
      isFetchingBalance.value = false;
      return;
    }

    const response = await axios.get(`/api/providers/${provider.id}/balance`);
    if (response.data.success) {
      balanceData.value = response.data.data;
    } else {
      balanceError.value = response.data.message || 'Failed to fetch balance.';
    }
  } catch (error) {
    console.error('Error fetching balance:', error);
    if (error.response && error.response.data && error.response.data.message) {
      balanceError.value = error.response.data.message;
    } else if (error.message) {
      balanceError.value = error.message;
    } else {
      balanceError.value = 'An unknown error occurred while fetching balance.';
    }
  } finally {
    isFetchingBalance.value = false;
  }
};

const closeBalanceModal = () => {
  showBalanceModal.value = false;
  selectedProviderForBalance.value = null;
  balanceData.value = null;
  balanceError.value = '';
  isFetchingBalance.value = false;
};

// Ensure config is displayed nicely in the table (if not already handled)
// This computed property might be useful if your provider.config is stored as an object
// and you want to display its stringified version.
// If provider.config is already a string from the backend, this might not be needed
// or adjust `provider.config_display` logic.
const providersWithDisplayConfig = computed(() => {
  return providers.value.map(p => {
    let configDisplay = p.config;
    if (typeof p.config === 'object') {
      configDisplay = JSON.stringify(p.config, null, 2);
    }
    // Truncate for display in table if it's very long
    const maxLength = 50;
    if (configDisplay && configDisplay.length > maxLength) {
      configDisplay = configDisplay.substring(0, maxLength) + '...';
    }
    return { ...p, config_display: configDisplay };
  });
});
// In your table, you would then iterate over `providersWithDisplayConfig`
// or ensure `provider.config` is already a string and use `<pre class="truncate">{{ provider.config.substring(0,50) }}...</pre>`


// Add these refs after the existing refs
const showTestResultModal = ref(false);
const isTestingProvider = ref(false);
const testResultData = ref(null);
const testResultError = ref('');
const testingProviderName = ref('');

// Add these methods after the existing methods
async function testProvider(provider) {
  const cardRef = providerCards.value[provider.id];
  if (!cardRef) return;

  try {
    const response = await axios.post(`/api/providers/${provider.id}/test`);
    if (response.data.success) {
      cardRef.updateTestResult(response.data.data, null);
    } else {
      cardRef.updateTestResult(null, response.data.message || 'Test failed');
    }
  } catch (error) {
    console.error('Error testing provider:', error);
    cardRef.updateTestResult(null, error.response?.data?.message || error.message || 'An error occurred while testing the provider');
  }
}

function closeTestResultModal() {
  showTestResultModal.value = false;
  testingProviderName.value = '';
  testResultData.value = null;
  testResultError.value = '';
  isTestingProvider.value = false;
}

// Add this ref after other refs
const providerCards = ref({});

// <MainSection :set="_set" :contract="_contract" :db="_db" v-model:pulse="_pulse"/>
</script>

<template>
  <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
    <template #header>
      <pulse v-model="_pulse" :animation="_set.animate"/>
    </template>

    <template #intro>
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
          <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Add New Provider
          </button>
        </div>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">View Mode:</span>
          <button 
            @click="isCompactView = !isCompactView" 
            class="px-3 py-1 text-sm rounded-md transition-colors duration-200"
            :class="isCompactView ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
          >
            {{ isCompactView ? 'Compact' : 'Full' }}
          </button>
        </div>
      </div>
    </template>

    <template #default>
      <div :class="[
        'grid gap-2',
        isCompactView ? 'grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8' : 'md:grid-cols-2 xl:grid-cols-3'
      ]">
        <div v-for="provider in providers" :key="provider.id" class="text-sm dark:text-gray-200">
          <ProviderCard 
            :provider="provider"
            :module="'ai'"
            :table="'providers'"
            :db="_db"
            :set="_set"
            :is-compact="isCompactView"
            @edit="openModal"
            @delete="deleteProvider"
            @balance="openBalanceModal"
            @test="testProvider"
            :ref="el => { if (el) providerCards[provider.id] = el }"
          />
        </div>
      </div>
    </template>

    <template #footer>
      <!-- Modals -->
      <!-- Add/Edit Provider Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class=" rounded-lg p-6 max-w-lg w-full mx-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 ">
          <form @submit.prevent="saveProvider">
            <div class="mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ editingProvider ? 'Edit Provider' : 'Add New Provider' }}
              </h3>
            </div>
            <div class="space-y-4">
              <div>
                <label for="providerName" class="block text-sm font-medium ">Name</label>
                <input type="text" v-model="currentProvider.name" id="providerName" required 
                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 dark:bg-gray-600">
              </div>
              <div>
                <label for="providerType" class="block text-sm font-medium ">Type</label>
                <input type="text" v-model="currentProvider.type" id="providerType" required 
                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 dark:bg-gray-600">
              </div>
              <div>
                <label for="providerConfig" class="block text-sm font-medium">Config (JSON)</label>
                <textarea v-model="currentProvider.config" id="providerConfig" rows="10" required 
                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 font-mono dark:bg-gray-600"></textarea>
                <p v-if="configError" class="text-red-500 text-xs mt-1">{{ configError }}</p>
              </div>
              <div>
                <label for="providerIsActive" class="flex items-center">
                  <input type="checkbox" v-model="currentProvider.is_active" id="providerIsActive" 
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                  <span class="ml-2 text-sm ">Is Active</span>
                </label>
              </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
              <button type="submit" 
                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                Save
              </button>
              <button @click="closeModal" type="button" 
                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Balance Modal -->
      <div v-if="showBalanceModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
          <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              Contract Balance: {{ selectedProviderForBalance?.name }}
            </h3>
          </div>
          <div class="mt-2">
            <div v-if="isFetchingBalance" class="text-sm text-gray-500">
              Fetching balance...
            </div>
            <div v-if="balanceError" class="mt-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
              <p class="font-bold">Error:</p>
              <p>{{ balanceError }}</p>
            </div>
            <div v-if="balanceData && !isFetchingBalance && !balanceError" class="mt-3 text-sm text-gray-700">
              <p><strong>Contract Address:</strong> {{ balanceData.contract_address }}</p>
              <p><strong>Balance:</strong> {{ balanceData.balance_ether }} {{ balanceData.unit }}</p>
              <p class="text-xs text-gray-500">(Raw: {{ balanceData.balance_wei }} Wei)</p>
            </div>
            <div v-if="!isFetchingBalance && !balanceData && !balanceError" class="text-sm text-gray-500">
              No balance data to display. Ensure provider config has 'rpc_url' and 'contract_address'.
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button @click="closeBalanceModal" type="button" 
              class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Test Result Modal -->
      <div v-if="showTestResultModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
          <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              Test Result: {{ testingProviderName }}
            </h3>
          </div>
          <div class="mt-2">
            <div v-if="isTestingProvider" class="text-sm text-gray-500">
              Testing provider...
            </div>
            <div v-if="testResultError" class="mt-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
              <p class="font-bold">Error:</p>
              <pre class="whitespace-pre-wrap text-sm">{{ testResultError }}</pre>
            </div>
            <div v-if="testResultData && !isTestingProvider && !testResultError" class="mt-3">
              <p class="text-sm font-medium text-gray-700">Test Succeeded. Data:</p>
              <pre class="mt-1 p-2 bg-gray-100 rounded text-xs text-gray-600 whitespace-pre-wrap">{{ JSON.stringify(testResultData, null, 2) }}</pre>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button @click="closeTestResultModal" type="button" 
              class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
              Close
            </button>
          </div>
        </div>
      </div>
    </template>
  </AppToolbarLayout>
</template>


<style scoped>
/* Add any component-specific styles here, e.g., for modal transitions */
pre {
  white-space: pre-wrap; /* Ensure JSON wraps within the <pre> tag */
  word-wrap: break-word;
}
</style>
