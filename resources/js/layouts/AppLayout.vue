<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

import ApplicationMark from '@/components/ApplicationMark.vue';

// base-sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import FooterSection from "@/sections/FooterSection.vue"
import FooterSlideSection from "@/sections/FooterSlideSection.vue"
import SideRightSection from "@/sections/SideRightSection.vue"
import RightTeamSection from "@/sections/RightTeamSection.vue"
import RightUserSection from "@/sections/RightUserSection.vue"
import ToolbarSection from "@/sections/ToolbarSection.vue"
import DeveloperSection from "@/sections/DeveloperSection.vue"

// icons
import { BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, UserIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'

const props = defineProps({
    set: Object,
    page: Object,
    baseSections: Object,
    contract: Object,
});

const _header=ref(true);
const _subHeader=ref(true);
const _sideBar = ref(false);

// right windows activate switches
const mySideRight = ref(null);
const myTeamRight = ref(null);
const myUserRight = ref(null);
const myFooterSlide = ref(null);

const _activate = async (_subject) => { 
    if(_subject === "User") myUserRight.value.open = !myUserRight.value.open; 
    if(_subject === "Project") mySideRight.value.open = !mySideRight.value.open; 
    if(_subject === "Developer") myFooterSlide.value.open = !myFooterSlide.value.open; 
}

const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400 ";

</script>

<template>
  <html class="overscroll-none" :class="set.dark ? 'dark bg-black' : 'light bg-indigo-50'">
        <div class="bg-slate-50 text-black/50 dark:bg-slate-900 dark:text-white/50  font-roboto">
            <Head :title="page.title" />

            <!-- Header Content -->
            <div v-if="$slots.header">
                <div v-for="section in baseSections">
                    <Banner />

                   <!--   0 Basic Public Header .... -->
                    <div v-if="section.title=='Header' && page.header">
                        <HeaderSection  :set="set" :contract="contract" :page="page" :section="section"  />
                    </div>

                    <SubHeaderSection v-if="set.layout.subHeader" :set="set"/>
                    <SideRightSection ref="mySideRight" :set="set"/>
                    <RightTeamSection ref="myTeamRight" :set="set"/>
                    <RightUserSection ref="myUserRight" :user="$page.props.auth.user"/>
                    <FooterSlideSection ref="myFooterSlide" :set="set"/>

                </div>
                <slot name="header" />
            </div>


            <!-- Intro Content -->
            <div v-if="$slots.intro">
                <slot name="intro" />
            </div>

            <!-- Default Content -->
            <div class="bg-indigo-50 dark:bg-slate-900" v-if="$slots.default">
                <div class="flex">
                    <ToolbarSection  v-if="set.layout.sidebar" @activate="_activate" :set="set"/>
                    <slot name="default" />
                </div>
            </div>

            <!-- Footer Content -->
            <div v-if="$slots.footer" class="">
                <div class="flex">
                    <slot name="footer" />
                    <FooterSection v-if="set.layout.footer" :set="set" :contract="contract"/>
                    <DeveloperSection @click="set.layout.developer=false" v-if="set.layout.developer" :set="set" :contract="contract"/>
                    <div v-else @click="set.layout.developer=true" class="text-green-500 center">DEVELOPER-SECTION</div>
                    <h2-icon v-if="!set.layout.subHeader && !set.layout.header && !set.layout.sidebar" @click="set.layout.subHeader = true" class="w-10 px-2.5 pb-3 mx-2" :class="_indigo" />
                </div>

            </div>

        </div>
    </html>
</template>
