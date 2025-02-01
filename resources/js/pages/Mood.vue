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
import FooterSlideSection from "@/sections/FooterSlideSection.vue"
import SideRightSection from "@/sections/SideRightSection.vue"
import RightTeamSection from "@/sections/RightTeamSection.vue"
import RightUserSection from "@/sections/RightUserSection.vue"
import PanaderoMood from "@/panaderos/panadero-mood/PanaderoMood.vue";
import DeveloperSection from "@/sections/DeveloperSection.vue"

import { BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, UserIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

// props
const props = defineProps({
    page: Object,
    baseSections: Object
});

// reactive components
const pulse = ref(false);
const myChild = ref(null);
const mySideRight = ref(null);
const myTeamRight = ref(null);
const myUserRight = ref(null);
const myFooterSlide = ref(null);
const _header=ref(true);
const _subHeader=ref(true);
const _sideBar = ref(false);

const openSide = ref(false)
const _toggleFooter = async () => { myFooterSlide.value.open = !myFooterSlide.value.open; }
const _toggleProject = async () => { mySideRight.value.open = !mySideRight.value.open; }
const _toggleTeam = async () => { myTeamRight.value.open = !myTeamRight.value.open; }
const _toggleExtended = async () => { _extended.value = !_extended.value; }
const _toggleUser = async () => { myUserRight.value.open = !myUserRight.value.open; }
const whatever = async () => { console.log('whatever')}

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
const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400 ";
const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-indigo-400 dark:hover:ring-indigo-600";

const navigation = [
  { name: 'Extended', icon: BarsArrowUpIcon, href: _toggleExtended, current: false },
  { name: 'Documents', icon: ClipboardDocumentCheckIcon, href: _toggleTeam, current: false },
  { name: 'Signals', icon: SignalIcon, href: _toggleTeam, current: false },
  { name: 'Basetables', icon: CircleStackIcon, href: _toggleTeam, current: false },
  { name: 'Play', icon: PlayIcon, href: _toggleTeam, current: false },
  { name: 'Launch', icon: RocketLaunchIcon, href: _toggleTeam, current: false },
  { name: 'Bell', icon: BellIcon, href: _toggleTeam, current: false },
  { name: 'Swatch', icon: SwatchIcon, href: _toggleTeam, current: false },
  { name: 'Users', icon: UsersIcon, href: _toggleTeam, current: false },
  { name: 'Wallet', icon: WalletIcon, href: _toggleTeam, current: false },
  { name: 'Tables', icon: TableCellsIcon, href: _toggleTeam, current: false },
  { name: 'Serverstacks', icon: ServerStackIcon, href: _toggleTeam, current: false },
  { name: 'Manuals', icon: QuestionMarkCircleIcon, href: _toggleTeam, current: false },
  { name: 'Wrench', icon: WrenchIcon, href: _toggleTeam, current: false },
  { name: 'Team', icon: UsersIcon, href: _toggleTeam, current: false },
  { name: 'Reports', icon: TableCellsIcon, href: whatever, current: false },
  { name: 'Project', icon: ClipboardDocumentCheckIcon, href: _toggleProject, current: false },
  { name: 'Developer', icon: WrenchIcon, href: _toggleFooter, current: false },
  { name: 'User', icon: UserIcon, href: _toggleUser, current: false },
];

let _extended=ref(false);
</script>
<template>
    <AppLayout :title="page.title">

        <template #header>
            <Banner />
            {{page.title}}

            <div v-for="section in baseSections"  >
               <!--   0 Basic Public Header .... -->
                <div v-if="page.header">
                    <HeaderSection v-if="section.file =='HeaderSection2.vue'" :set="_set" :contract="_contract" :page="page" :section="section"  />
                </div>
            </div>
            <!-- 
            <HeaderSection v-if="_set.layout.header" :set="_set" :contract="_contract"/>
            -->
            <SubHeaderSection v-if="_set.layout.subHeader" :set="_set"/>
            <SideRightSection ref="mySideRight" :set="_set"/>
            <RightTeamSection ref="myTeamRight" :set="_set"/>
            <RightUserSection ref="myUserRight" :user="$page.props.auth.user"/>
            <FooterSlideSection ref="myFooterSlide" :set="_set"/>

        </template>

        <template #default>
            <div class="flex">
                
                <div v-if="_set.layout.sidebar" class="flex bg-slate-100 dark:bg-slate-950 ">
                  <nav class="flex flex-1 flex-col" aria-label="Sidebar">
                    <ul role="list" class=" space-y-1 mt-1" :class="_extended?'w-32':'w-12'">
                        <h2-icon v-if="!_set.layout.subHeader" @click="_set.layout.subHeader=true" class="w-10 px-3 pb-3 mx-2" :class="_indigo" />
                        <li v-for="item in navigation" :key="item.name" class="-ml-1">
                             <span @click="item.href" class="flex pt-3" :class="_indigo"><component :is="item.icon" :set="_set" class="w-10 px-3 mx-1 " /><span v-if="_extended" class="-ml-1 text-sm">{{item.name}}</span></span>
                        </li>
                    </ul>
                  </nav>
                </div>

                <div id="whatever" class="w-full ... min-h-4 min-w-full bg-white">
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
                    </div class="-pl-4">
                        <PanaderoMood ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse" />
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