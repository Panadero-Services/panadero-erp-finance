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


// Reactive state for Test Provider Modal (ensure these are defined)
const showTestResultModal = ref(false);
const isTestingProvider = ref(false);
const testResultData = ref(null);
const testResultError = ref('');
const testingProviderName = ref(''); // To store the name of the provider being tested

// Method to open the test result modal
const openTestResultModal = () => {
  showTestResultModal.value = true;
};

// Method to close the test result modal
const closeTestResultModal = () => {
  showTestResultModal.value = false;
  testResultData.value = null;
  testResultError.value = '';
  testingProviderName.value = '';
};

// The testProvider method itself
const testProvider = async (provider) => {
  testingProviderName.value = provider.name;
  isTestingProvider.value = true;
  testResultData.value = null;
  testResultError.value = '';

  openTestResultModal(); // Open the modal immediately to show loading state

  try {
    const response = await axios.post(`/api/providers/${provider.id}/test`);
      console.log(response);
    if (response.data.success) {
      console.log(response.data);
      testResultData.value = response.data.data; // Assuming backend sends data in 'data' field
    } else {
      testResultError.value = response.data.message || 'Test failed with no specific error message.';
    }
  } catch (error) {
    console.error('Error testing provider:', error);
    if (error.response && error.response.data && error.response.data.message) {
      testResultError.value = error.response.data.message;
    } else if (error.message) {
      testResultError.value = error.message;
    } else {
      testResultError.value = 'An unknown error occurred during the test.';
    }
  } finally {
    isTestingProvider.value = false;
  }
};




// <MainSection :set="_set" :contract="_contract" :db="_db" v-model:pulse="_pulse"/>
</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4 min-w-full ">

           




            <div class="container mx-auto p-4">
               <h1 class="text-2xl font-bold mb-4">Manage Providers</h1>

                <!-- Button to open creation modal -->
                <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                  Add New Provider
                </button>

   

<!-- Providers Table -->
  <div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Config (First 50 chars)</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-if="providers.length === 0">
          <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No providers found.</td>
        </tr>
        <tr v-for="provider in providers" :key="provider.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ provider.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ provider.type }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 overflow-hidden overflow-ellipsis max-w-xs">
            <pre class="truncate">{{ provider.config_display || provider.config }}</pre>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
            <button @click="openModal(provider)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
            <button @click="deleteProvider(provider.id)" class="text-red-600 hover:text-red-900">Delete</button>
            <button @click="testProvider(provider)" class="text-green-600 hover:text-green-900">Test</button>
            <!-- New Balance Button -->
            <button 
              @click="openBalanceModal(provider)" 
              class="text-blue-600 hover:text-blue-900"
              :disabled="!provider.config || !JSON.parse(provider.config).contract_address || !JSON.parse(provider.config).rpc_url"
              title="View contract balance (requires contract_address and rpc_url in config)"
            >
              Balance
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>



  <!-- ... existing modals (add/edit, test result) ... -->
  <!-- ... move all modals here ... -->
  <!-- Balance Display Modal -->
  <div v-if="showBalanceModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <!-- Heroicon name: outline/currency-dollar -->
              <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Contract Balance: {{ selectedProviderForBalance?.name }}
              </h3>
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
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="closeBalanceModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>





  <!-- Test Result Modal -->
  <div v-if="showTestResultModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title-test" role="dialog" aria-modal="true">
    <!-- Modal content -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
      
      <!-- This span is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <!-- Icon (e.g., clipboard list for test results) -->
              <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-test">
                Test Result: {{ testingProviderName }}
              </h3>
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
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="closeTestResultModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

<!-- Add/Edit Provider Modal -->
<div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title-add-edit" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <form @submit.prevent="saveProvider">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-add-edit">
                {{ editingProvider ? 'Edit Provider' : 'Add New Provider' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label for="providerName" class="block text-sm font-medium text-gray-700">Name</label>
                  <input type="text" v-model="currentProvider.name" id="providerName" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                </div>
                <div>
                  <label for="providerType" class="block text-sm font-medium text-gray-700">Type</label>
                  <input type="text" v-model="currentProvider.type" id="providerType" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                </div>
                <div>
                  <label for="providerConfig" class="block text-sm font-medium text-gray-700">Config (JSON)</label>
                  <textarea v-model="currentProvider.config" id="providerConfig" rows="10" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 font-mono"></textarea>
                  <p v-if="configError" class="text-red-500 text-xs mt-1">{{ configError }}</p>
                </div>
                <div>
                  <label for="providerIsActive" class="flex items-center">
                    <input type="checkbox" v-model="currentProvider.is_active" id="providerIsActive" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Is Active</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
            Save
          </button>
          <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>











            </div>
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>


<style scoped>
/* Add any component-specific styles here, e.g., for modal transitions */
pre {
  white-space: pre-wrap; /* Ensure JSON wraps within the <pre> tag */
  word-wrap: break-word;
}
</style>
