<script setup>
// vue
import {computed, onMounted, onUnmounted, ref} from 'vue';

// layout
import AppLayout from '@/layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

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

// custom sections
import MainSection from '@/panaderos/bento/sections/BentoMainSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// icons
import { BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, UserIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'


// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

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
   
   <AppLayout title="Bento" :bgDark="_set.dark" >

      <template #header>
         <Banner />
         <!--   0 Basic Header Sections .... -->
         <div v-for="section in baseSections">
            <HeaderSection v-if="section.file =='HeaderSection' && _set.layout.header" :page="page" :set="_set" :contract="_contract" :section="section"  />
            <SubHeaderSection v-if="section.file =='SubHeaderSection' && _set.layout.subHeader" :page="page" :set="_set" :contract="_contract" :section="section"  />
         </div>

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

                <div id="whatever" class="w-full ... min-h-4 min-w-full ">
         <MainSection :set="_set" v-model:pulse="pulse"/>
                </div>
            </div>
        </template>




   </AppLayout>
</template>
