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
import BusinessFunctionCard from "@/pages/home/BusinessFunctionCard.vue";
import ModuleCard from "@/pages/home/ModuleCard.vue";

import ApplicationLogo from '@/components/logoSelf.vue';

const selfVersion ="0.2.1";

const props =defineProps({
    page: Object,
    baseSections: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String
});

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


const _modules = [
    { "item":"Grid", "title":"Grid feature", "description": "This module is all about relative content browsing", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' },
                { name: 'Grid sample', url: route('grid'),    route: 'grid' }
            ]
            , "status":"featured"
    }, 
    { "item":"Event", "title":"Scheduler feature", "description": "This module is all about eventHandlers", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            , "status":"featured"
    }, 
    { "item":"Mood", "title":"Mood feature", "description": "This module is all about task assigments and todo browsing", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            , "status":"featured"
    }, 
    { "item":"Calc", "title":"Spreadsheet feature", "description": "This moduls is all about calculation requirements", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            , "status":"featured"
    }, 
    { "item":"Proj", "title":"Project feature", "description": "This modules is all about project handling", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            , "status":"featured"
    }, 
    { "item":"Contract", "title":"Contract feature", "description": "This modules is all web3 Contract browsing", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            , "status":"featured"
    }, 
    { "item":"Gantt", "title":"Gantt feature ", "description": "This module is all about planning projects", "options" : 
            [   
                { name: 'Demo', url: route('home/dashboard'),    route: 'home/dashboard' }
            ]
            ,"status":"featured"
    }
];


const _functions = ref([]);
const filter = ref('');

onMounted(async () => {
  const response = await axios.get('/business_services');
  _functions.value = response.data;
});

const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

const refreshPage = () => { window.location.reload(); };

const services = ref([]);

onMounted(async () => {
  const response = await axios.get('/business_services');
  services.value = response.data;
});

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>

        <div class="grid grid-cols-2"> 
            <div class="pl-2 " >
                <button @click="_set.dark=false" type="button" :class="_button">Light</button>
                <button @click="_set.dark=true" type="button" :class="_button">Dark</button>
                <button @click="refreshPage" type="button" :class="_button">Refresh</button>
            </div>      
            <div class="" id="toolbar">
                <input v-model="filter" name="filter" type="filter" autocomplete="filter" required="" 
                class="mt-2 min-w-0 flex-auto rounded-sm bg-gray-50 dark:bg-slate-950 px-3.5 py-0.5 text-base text-gray-600 sm:text-sm/6 focus:ring-0" placeholder="Search" />
            </div>
        </div>

         <div id="whatever" class="">{{filter}}
            <!--    <self-intro /> -->
            <div class="">
                <!--    <ApplicationLogo class="block w-80 h-80" /> -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 xl:grid-cols-3 pt-12 gap-4 ">
                    <template v-for="_f in _functions"><div v-if="_f.title.toLowerCase().includes(filter)" ><business-function-card :set="_set" :f="_f"  :progress="_f.progress" /></div></template>
                    <template v-for="_m in _modules"><div v-if="_m.title.toLowerCase().includes(filter)" ><module-card :set="_set" :f="_m"   /></div></template>
                </div>
            </div>

         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>

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
