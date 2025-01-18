<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();


// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import FooterSection from "@/sections/FooterSection.vue"
import SideRightSection from "@/sections/SideRightSection.vue"
import RightTeamSection from "@/sections/RightTeamSection.vue"
import PanaderoMood from "@/panaderos/panadero-mood/PanaderoMood.vue";
import DeveloperSection from "@/sections/DeveloperSection.vue"

import { PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

// reactive components
const pulse = ref(false);
const myChild = ref(null);
const mySideRight = ref(null);
const myTeamRight = ref(null);
const _header=ref(true);
const _subHeader=ref(true);
const _sideBar = ref(false);

const openSide = ref(false)
const _toggleProject = async () => { mySideRight.value.open = !mySideRight.value.open; }
const _toggleTeam = async () => { myTeamRight.value.open = !myTeamRight.value.open; }
const whatever = async () => { console.log('whatever')}

const navigation = [
//  { name: 'Dashboard', icon: TableCellsIcon, href: 'whatever', current: true },
//  { name: 'Team', icon: TableCellsIcon, href: 'whatever', current: false },
//  { name: 'Projects', icon: TableCellsIcon, href: 'whatever', current: false },
//  { name: 'Calendar', icon: TableCellsIcon, href: '#', current: false },
//  { name: 'Documents', icon: TableCellsIcon, href: '#', current: false },
  { name: 'Team', icon: UsersIcon, href: _toggleTeam, current: false },
  { name: 'Reports', icon: TableCellsIcon, href: whatever, current: false },
  { name: 'Project', icon: ClipboardDocumentCheckIcon, href: _toggleProject, current: false },
];


// Theme Buttons
const _setWillow = () => { _set.dark=false; myChild.value.changeTheme('willow'); }
const _setMaterial = () => { _set.dark=false; myChild.value.changeTheme('material');}
const _setWillowDark = () => { _set.dark=true; myChild.value.changeTheme('willow-dark');}
const _setPanaderos = () => { _set.dark=false; myChild.value.changeTheme('panaderos');}

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
//const _button ="rounded px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 ";
const _hover = "hover:bg-indigo-400 dark:hover:bg-indigo-600";
const _bg = "bg-white dark:bg-black";
const _bgSelected = "bg-indigo-200 dark:bg-indigo-800";
const _hoverAdd = "hover:bg-green-400 dark:hover:bg-green-600";
const _hoverDelete = "hover:bg-red-400 dark:hover:bg-red-600";
const _indigo = " text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-yellow-400 ";
const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-indigo-400 dark:hover:ring-indigo-600";


</script>
<template>
    <AppLayout title="Indigo3 Mood" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection v-if="_set.layout.header" :set="_set" :contract="_contract"/>

            <SubHeaderSection v-if="_set.layout.subHeader" :set="_set"/>
            <SideRightSection ref="mySideRight" :set="_set"/>
            <RightTeamSection ref="myTeamRight" :set="_set"/>
            
            <!-- -->
            <!-- buttons subHeader -->
            <!--             
            <div v-if="true" class="absolute space-x-2 z-40" :class="[_header ? 'top-16' : 'top-1', _subHeader ? 'left-32' : 'left-80']" >
              <button v-if="_subHeader" @click="myChild._save" :disabled="_set.project.id==0" type="button" class="rounded bg-white dark:bg-slate-950 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:ring-green-600" :class="_set.project.id==0 ? 'opacity-35' : '' ">Save</button>
              <button v-if="_subHeader" @click="myChild._load" :disabled="_set.project.id==0" type="button" class="rounded bg-white dark:bg-slate-950 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:ring-green-600" :class="_set.project.id==0 ? 'opacity-35' : '' ">Load</button>
              <button v-if="_subHeader" @click="myChild._add" type="button" class="rounded bg-white dark:bg-slate-950 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:ring-green-600">Add</button>
              <button v-if="_subHeader"  @click="myChild._delete" type="button" class="rounded bg-white dark:bg-slate-950 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-red-400 dark:hover:ring-red-600">Delete</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('willow')" type="button" class="rounded bg-white dark:bg-slate-950 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:ring-indigo-600">Light</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('willow-dark')" type="button" class="rounded bg-white dark:bg-slate-950 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:ring-indigo-600">Dark</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('material')" type="button" class="rounded bg-white dark:bg-slate-950 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:ring-indigo-600">Material</button>
              <button v-if="_subHeader"  @click="_header=!_header" type="button" class="rounded bg-white dark:bg-slate-950 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:ring-indigo-600">Header</button>
              <button @click="_subHeader=!_subHeader" type="button" class="rounded bg-white dark:bg-slate-950 px-1 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:ring-indigo-600">subHeader</button>
            </div>
            -->

        </template>

        <template #default>
            <div class="flex">
                
                <div v-if="_set.layout.sidebar" class="flex bg-slate-100 dark:bg-slate-950 text-center">
                  <nav class="flex flex-1 flex-col" aria-label="Sidebar">
                    <ul role="list" class="-mx-2 space-y-1 mt-4">
                        <h2-icon v-if="!_set.layout.subHeader" @click="_set.layout.subHeader = true" class="w-10 px-3 pb-3 mx-2" :class="_indigo" />
                        <home-icon class="w-10 px-3 pt-0 mx-2" :class="_indigo" />
                        <clipboard-document-check-icon @click="mySideRight.open = !mySideRight.open" class="w-10 px-3 pt-3 mx-2 " :class="_indigo" />
                        <signal-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <circle-stack-icon @click="mySideRight.open = !mySideRight.open" class="w-10 px-3 pt-3 mx-2 " :class="_indigo" />
                        <play-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <rocket-launch-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <bell-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <swatch-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <users-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <wallet-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <table-cells-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <server-stack-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <wrench-icon @click="mySideRight.open = !mySideRight.open" class="w-10 px-3 pt-3 mx-2 " :class="_indigo" />
                        <question-mark-circle-icon class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        <li v-for="item in navigation" :key="item.name">
                            <component :is="item.icon" @click="item.href" class="w-10 px-3 pt-3 mx-2" :class="_indigo" />
                        </li>
                    </ul>
                  </nav>
                </div>

                <div id="whatever" class="w-full ... min-h-4 max-w-9xl bg-white">
                    <div class="grid grid-cols-2 ">     
                        
                        <div class="pl-2" :class="_set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
                            
                            <button @click="_setWillow" type="button" :class="_button">Light</button>
                            <button @click="_setWillowDark" type="button" :class="_button">Dark</button>
                            <button @click="_setMaterial" type="button" :class="_button">Material</button>
                            <button @click="_setPanaderos" type="button" :class="_button">Panaderos</button>
                            <button @click="myChild._save" type="button" :class="_button">Save</button>
                            <button @click="myChild._load" type="button" :class="_button">Load</button>
                        
                        </div>      

                        <div class="" id="toolbar"></div>
                    </div>
                        <PanaderoMood ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse"/>
                </div>
            </div>
        </template>
  
        <template #footer>
            <FooterSection v-if="_set.layout.footer" :set="_set" :contract="_contract"/>
            <DeveloperSection @click="_set.layout.developer=false" v-if="_set.layout.developer" :set="_set" :contract="_contract"/>

            <div v-else @click="_set.layout.developer=true" class="text-green-500 center">DEVELOPER-SECTION</div>

            <h2-icon v-if="!_set.layout.subHeader && !_set.layout.header && !_set.layout.sidebar" @click="_set.layout.subHeader = true" class="w-10 px-2.5 pb-3 mx-2" :class="_indigo" />
        </template>
        
    </AppLayout>
</template>

<!-- custom styles -->
<style>
</style>