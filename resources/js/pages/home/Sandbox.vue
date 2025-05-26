<script setup>
// ——— Core Imports
import { ref, computed, onMounted, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';

// ——— Layout & Sections
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import DashboardSection from '@/sections/shared/DashboardSection.vue';

// ——— Stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

// ——— Components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import UserCard from "@/layouts/cards/UserCard.vue";
import BusinessFunctionCard from "@/pages/home/BusinessFunctionCard.vue";
import ModuleCard from "@/pages/home/ModuleCard.vue";

// ——— External Modules
import { moduleName, moduleGit } from 'panadero-self';

// ——— Setup
const _page = usePage();
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();
_set.domainFunction = "home";

// ——— Props
const props = defineProps({
  page: Object,
  baseSections: Object,
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String
});

// ——— Lifecycle
onMounted(async () => {
  _set.domainFunction = 'home';
  await _set.initMM();
  await _set.initialize();
});

// ——— Computed Properties
const validMM = computed(() => _set.isMetaMask);
const validWallet = computed(() => _set.wallet.length > 10);
const validNames = computed(() => _contract.resolvedNames.length > 2);

const selfResolve = ref("SELF");
const validNick = computed(() => _contract.resolvedNames.includes(selfResolve.value));

// ——— Helpers
const usdPrice = 0.01;
const _pulse = ref(false);
provide('pulse', _pulse);

// ——— UI Functions
const disableScroll = () => document.body.classList.add("stop-scrolling");
const enableScroll = () => document.body.classList.remove("stop-scrolling");

const _startGame = async () => {
  await _set.game3Toggle();
  _set.game3On ? disableScroll() : enableScroll();
};

const keyUpResolve = async (_selfResolve) => {
  selfResolve.value = _selfResolve;
};

const refreshPage = () => window.location.reload();

// ——— Static UI Configs
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

const filter = ref("");
const selfVersion = "0.2.1";

const url = {
  welcome: "https://selfcrypto.io",
  register: "https://dashboard.selfcrypto.io/",
  stakepool: "https://self-inky.vercel.app/staking",
  game: "https://test-dashboard.selfcrypto.io",
  auction: "https://self-auction.netlify.app/"
};
</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>

        <div class="grid grid-cols-2 bg-white"> 
            <div class="pl-2 " >
            </div>      
            <div class="" id="toolbar">
            </div>
        </div>

         <div id="whatever" class="bg-white">

            <dashboard-section :set="_set" :db="_db" :contract="_contract"/>

         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>

</template>

<style>

</style>

