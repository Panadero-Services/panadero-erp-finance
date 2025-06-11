<script setup>
//home/dashboard
import {computed, onMounted, onUnmounted, ref, provide} from 'vue';
import axios from 'axios';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();
import {moduleName, moduleGit} from 'panadero-self';
// sections

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

_set.domainFunction = "home";

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

// cards
import UserCard from "@/layouts/cards/UserCard.vue";
import BusinessFunctionCardSmall from "@/pages/home/BusinessFunctionCardSmall.vue";
import ModuleCard from "@/pages/home/ModuleCard.vue";

import ApplicationLogo from '@/components/logoSelf.vue';
import EditRecordModal from '@/components/modals/EditRecordModal.vue';

const selfVersion ="0.2.1";

const props =defineProps({
    page: Object,
    baseSections: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String
});

// variables
const _module = "home";
const _table = 'dashboard';
const _model = 'Dashboard';
const keyIndex = ref(0);
const editRecordMode = ref(false);
const _activeRecord = ref({});
const _superSelfAdmin = computed(() => _usePage.props.auth.user?.super_self_admin || 0);

// Store preserved modal settings
const _preservedModalSettings = ref({
  modalSize: null,
  fontSize: null,
  activeTab: null
});

const _close = async (_nr) => {
    editRecordMode.value = false;
}

const handleRecordChange = (recordData) => {
  // Store preserved settings if provided
  if (recordData.preservedModalSize) {
    _preservedModalSettings.value.modalSize = recordData.preservedModalSize;
  }
  if (recordData.preservedFontSize) {
    _preservedModalSettings.value.fontSize = recordData.preservedFontSize;
  }
  if (recordData.preservedActiveTab) {
    _preservedModalSettings.value.activeTab = recordData.preservedActiveTab;
  }
}

// webhooks
onMounted(async ()=> {
    _set.domainFunction='home';
   await _set.initMM();
   await _set.initialize();
})

const url = {
    welcome: "https://selfcrypto.io",
    register: "https://dashboard.selfcrypto.io/",
    stakepool: "https://self-inky.vercel.app/staking",
    game: "https://test-dashboard.selfcrypto.io",
    auction: "https://self-auction.netlify.app/"
};

let validMM = computed(() => {
    return _set.isMetaMask;
});

let validWallet = computed(() => {
    return _set.wallet.length > 10;
});

let validNames = computed(() => {
    return _contract.resolvedNames.length > 2;
});

let validNick = computed(() => {
    return _contract.resolvedNames.includes(selfResolve.value);
});

var usdPrice=0.01;

let selfResolve=ref("SELF");

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);


// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

function disableScroll() {
    document.body.classList.
    add("stop-scrolling");
}

function enableScroll() {
    document.body.classList
    .remove("stop-scrolling");
}

const _startGame = async () => {
    await _set.game3Toggle();
    if (_set.game3On) disableScroll();
    else enableScroll();
}

const keyUpResolve = async (_selfResolve) => {
    selfResolve.value = _selfResolve;
}



const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

const services = ref([]);
const features = ref([]);
const filter = ref('');
const showEditModal = ref(false);
const selectedRecord = ref(null);

const handleEdit = (record) => {
  selectedRecord.value = record;
  showEditModal.value = true;
};

const handleSave = async (updatedRecord) => {
  try {
    await axios.put(`/features/${updatedRecord.id}`, updatedRecord);
    // Update the features list with the edited record
    const index = features.value.findIndex(f => f.id === updatedRecord.id);
    if (index !== -1) {
      features.value[index] = updatedRecord;
    }
    showEditModal.value = false;
  } catch (error) {
    console.error('Error updating record:', error);
  }
};

const refreshPage = () => { window.location.reload(); };

onMounted(async () => {
  try {
    const [servicesResponse, featuresResponse] = await Promise.all([
      axios.get('/business-services'),
      axios.get('/features')
    ]);
    services.value = servicesResponse.data;
    features.value = featuresResponse.data;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

</script>




<template>
  <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
    <template #header>
      <pulse v-model="_pulse" :animation="_set.animate" />


    </template>

    <template #intro />

    <template #default>

      <div class="text-center" id="toolbar">
        <input v-model="filter" name="filter" type="filter" autocomplete="filter" required="" class="mt-2 min-w-0 flex-auto rounded-sm bg-gray-50 dark:bg-slate-950 px-3.5 py-0.5 text-base text-gray-600 sm:text-sm/6 focus:ring-0" placeholder="Search" />
      </div>


      <div class="grid grid-cols-2 m-6 text-3xl font-semibold text-black dark:text-white sm:ml-24">
        <p>Business Services</p>
      </div>

      <div id="whatever" class="ml-6 min-h-[280px]">
        <div class="">
          <div class="flex flex-wrap gap-2">
            <template v-for="service in services">
              <div v-if="service.title.toLowerCase().includes(filter)" class="flex-shrink-0">
                <business-function-card-small :set="_set" :f="service" :progress="service.progress" />
              </div>
            </template>
          </div>
        </div>
      </div>

      <div class=" m-6 mt-16 text-3xl font-semibold text-black dark:text-white text-right sm:mr-32">
        <p>Features</p>
      </div>

      <div id="features" class="ml-6  min-h-[280px]">
        <div class="">
          <div class="flex flex-wrap gap-2 justify-end">
            <template v-for="feature in features">
              <div v-if="feature.title.toLowerCase().includes(filter)" class="flex-shrink-0">
                <business-function-card-small 
                  :set="_set" 
                  :f="feature" 
                  :progress="feature.progress"
                  @edit="handleEdit"
                />
              </div>
            </template>
          </div>
        </div>
      </div>

    </template>

    <template #footer>

    </template>
  </AppToolbarLayout>


<div v-if="showEditModal" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8 dark:bg-gray-800">

  <EditRecordModal
    v-if="showEditModal"
    :show="showEditModal"
    :record="selectedRecord"
    @close="showEditModal = false"
    @save="handleSave"
  />
</div>



</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.scrollable-place {
    height: 1000px;
}

.stop-scrolling {
    height: 100%;
    overflow: hidden;
}




</style>
