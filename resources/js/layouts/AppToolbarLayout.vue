<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

//icons
import { BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, UserIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'

// baseSections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import FooterSection from "@/sections/FooterSection.vue"
import FooterSlideSection from "@/sections/FooterSlideSection.vue"
import DeveloperSection from "@/sections/DeveloperSection.vue"

// slideRightSections
import SideRightSection from "@/sections/SideRightSection.vue"
import RightTeamSection from "@/sections/RightTeamSection.vue"
import RightUserSection from "@/sections/RightUserSection.vue"

const props = defineProps({
    title: String,
    set: Object,
    baseSections: Object,
    contract: Object, 
    page: Object
});

const _extended=ref(true);
const mySideRight = ref(null);
const myTeamRight = ref(null);
const myUserRight = ref(null);
const myFooterSlide = ref(null);

const _toggleFooter = async () => { myFooterSlide.value.open = !myFooterSlide.value.open; }
const _toggleProject = async () => { mySideRight.value.open = !mySideRight.value.open; }
const _toggleTeam = async () => { myTeamRight.value.open = !myTeamRight.value.open; }
const _toggleExtended = async () => { _extended.value = !_extended.value; }
const _toggleUser = async () => { myUserRight.value.open = !myUserRight.value.open; }
const whatever = async () => { console.log('whatever')}

const _toolbarItems = [
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

// css
const _indigo = " text-gray-400 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400 ";
const _sideSpacing = "mx-3";

</script>

<template>
  <html class="overscroll-none" :class="set.dark ? 'dark bg-black' : 'light bg-indigo-50'">
        <div class="bg-slate-50 text-black/50 dark:bg-black dark:text-white/50 h-screen w-screen font-roboto">
            <Head :title="title"/>

            <!-- Header Content -->
            <div :class="_sideSpacing" v-if="$slots.header">
                <Banner />
                <slot name="header"/>
                 <!--   0 Basic Header Sections .... -->
                 <div v-for="section in baseSections">
                    <HeaderSection v-if="section.file =='HeaderSection' && set.layout.header" :page="page" :set="set" :contract="contract" :section="section"/>
                    <SubHeaderSection v-if="section.file =='SubHeaderSection' && set.layout.subHeader" :page="page" :set="set" :contract="contract" :section="section"/>
                 </div>
                 <SideRightSection ref="mySideRight" :set="set"/>
                 <RightTeamSection ref="myTeamRight" :set="set"/>
                 <RightUserSection ref="myUserRight" :user="$page.props.auth.user"/>
                 <FooterSlideSection ref="myFooterSlide" :set="set"/>
            </div>

            <!-- Intro Content -->
            <div v-if="$slots.intro">
                <slot name="intro" />
            </div>

            <!-- Main Content -->
            <div class="bg-gray-100 dark:bg-black" v-if="$slots.default">
                <div class="flex">
                    <div v-if="set.layout.sidebar" class="flex bg-gray-100 dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800">
                      <nav class="flex flex-1 flex-col mx-3 " aria-label="Sidebar">
                        <ul role="list" class=" space-y-1 mt-1" :class="_extended?'w-32':'w-12'">
                            <h2-icon v-if="!set.layout.subHeader" @click="set.layout.subHeader=true" class="w-10 px-3 pb-3 mx-2" :class="_indigo"/>
                            <li v-for="item in _toolbarItems" :key="item.name" class="-ml-1">
                                 <span @click="item.href" class="flex pt-3" :class="_indigo">
                                    <component :is="item.icon" :set="set" class="w-10 px-3 mx-1"/>
                                    <span v-if="_extended" class="-ml-1 text-xs text-gray-800 dark:text-gray-300">
                                        {{item.name}}
                                    </span>
                                </span>
                            </li>
                        </ul>
                      </nav>
                    </div>
                    <div :class="_sideSpacing">
                        <slot name="default" />
                    </div>
                </div>
            </div>

            <!-- Footer Content -->
            <div v-if="$slots.footer">
                <slot name="footer"/>
                <FooterSection v-if="set.layout.footer" :set="set" :contract="contract"/>
                <DeveloperSection @click="set.layout.developer=false" v-if="set.layout.developer" :set="set" :contract="contract"/>
                <div v-else @click="set.layout.developer=true" class="text-green-500 center">DEVELOPER-SECTION</div>
                <h2-icon v-if="!set.layout.subHeader && !set.layout.header && !set.layout.sidebar" @click="set.layout.subHeader = true" class="w-10 px-2.5 pb-3 mx-2" :class="_indigo"/>
            </div>
        </div>

    </html>
</template>
