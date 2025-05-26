<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

// Icons
import {
  BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon,
  Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon,
  UserIcon, TableCellsIcon, ServerStackIcon, TagIcon, ClipboardDocumentCheckIcon,
  CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon
} from '@heroicons/vue/24/outline'

// Base Sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue'
import FooterSection from "@/sections/FooterSection.vue"
import FooterSlideSection from "@/sections/FooterSlideSection.vue"
import DeveloperSection from "@/sections/DeveloperSection.vue"

// Slide-Right Sections
import SideRightSection from "@/sections/SideRightSection.vue"
import RightProjectSection from "@/sections/RightProjectSection.vue"
import TeamSection from "@/sections/TeamSection.vue"
import RightUserSection from "@/sections/RightUserSection.vue"

// Props
const props = defineProps({
  title: String,
  set: Object,
  baseSections: Object,
  contract: Object,
  page: Object
})

// State
const _extended = ref(true)
const myProjectRight = ref(null)
const myTeamRight = ref(null)
const myUserRight = ref(null)
const myFooterSlide = ref(null)

// Generic toggle utility
const toggleRef = (refVar) => { refVar.value.open = !refVar.value.open }
const _toggleFooter = () => toggleRef(myFooterSlide)
const _toggleProject = () => toggleRef(myProjectRight)
const _toggleTeam = () => toggleRef(myTeamRight)
const _toggleUser = () => toggleRef(myUserRight)
const _toggleExtended = () => { _extended.value = !_extended.value }

// Dummy handler
const whatever = () => console.log('whatever')

// Toolbar Items
const _toolbarItems = [
  { name: 'Extended', icon: BarsArrowUpIcon, href: _toggleExtended, active: true },
  { name: 'Project', icon: ClipboardDocumentCheckIcon, href: _toggleProject, active: true },
  { name: 'Team', icon: UsersIcon, href: _toggleTeam, active: true },
  { name: 'Documents', icon: ClipboardDocumentCheckIcon, href: _toggleTeam, active: false },
  { name: 'Signals', icon: SignalIcon, href: _toggleTeam, active: false },
  { name: 'Play', icon: PlayIcon, href: _toggleTeam, active: false },
  { name: 'Launch', icon: RocketLaunchIcon, href: _toggleTeam, active: false },
  { name: 'Bell', icon: BellIcon, href: _toggleTeam, active: false },
  { name: 'Swatch', icon: SwatchIcon, href: _toggleTeam, active: false },
  { name: 'Users', icon: UsersIcon, href: _toggleTeam, active: false },
  { name: 'Wallet', icon: WalletIcon, href: _toggleTeam, active: false },
  { name: 'Tables', icon: TableCellsIcon, href: _toggleTeam, active: false },
  { name: 'Manuals', icon: QuestionMarkCircleIcon, href: _toggleTeam, active: false },
  { name: 'Wrench', icon: WrenchIcon, href: _toggleTeam, active: false },
  { name: 'Reports', icon: TableCellsIcon, href: whatever, active: false },
  { name: 'Developer', icon: WrenchIcon, href: _toggleFooter, active: true },
  { name: 'User', icon: UserIcon, href: _toggleUser, active: true },
]

// CSS Utility Classes
const _indigo = "text-gray-600 dark:text-gray-200 hover:text-black dark:hover:text-yellow-400"
const _sideSpacing = "mx-3"
</script>

<template>
  <html class="overscroll-none" :class="set.dark ? 'dark bg-black' : 'light bg-indigo-50'">
    <div class="bg-slate-50 text-black/50 dark:bg-black dark:text-white/50 h-screen w-screen font-roboto">
      <Head :title="title" />

      <!-- Header Section -->
      <template v-if="$slots.header">
        <div :class="_sideSpacing">
          <Banner />
          <slot name="header" />
          <div v-for="section in baseSections" :key="section.file">
                <HeaderSection
                  v-if="section.file === 'HeaderSection' && set.layout.header"
                  :page="page"
                  :set="set"
                  :contract="contract"
                  :section="section"
                />
                <SubHeaderSection
                  v-if="section.file === 'SubHeaderSection' && set.layout.subHeader"
                  :page="page"
                  :set="set"
                  :contract="contract"
                  :section="section"
                />
          </div>

          <!-- Right-side slideout panels -->
          <RightProjectSection ref="myProjectRight" :set="set" />
          <TeamSection ref="myTeamRight" :set="set" />
          <RightUserSection ref="myUserRight" :user="$page.props.auth.user" />
          <FooterSlideSection ref="myFooterSlide" :set="set" />
        </div>
      </template>

      <!-- Intro Slot -->
      <template v-if="$slots.intro">
        <slot name="intro" />
      </template>

      <!-- Main Content -->
      <template v-if="$slots.default">
        <div class="bg-gray-100 dark:bg-black flex">
          <!-- Sidebar -->
          <div
            v-if="set.layout.sidebar"
            class="flex bg-gray-100 dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800"
          >
            <nav class="flex flex-1 flex-col mx-3" aria-label="Sidebar">
              <ul :class="['space-y-1 mt-1', _extended ? 'w-32' : 'w-12']" role="list">
                <!-- Optional SubHeader Toggle -->
                <H2Icon
                  v-if="!set.layout.subHeader"
                  @click="set.layout.subHeader = true"
                  class="w-10 px-3 pb-3 mx-2"
                  :class="_indigo"
                />
                <!-- Toolbar Items -->
                <li
                  v-for="item in _toolbarItems"
                  :key="item.name"
                  class="-ml-1"
                >
                  <span
                    v-if="item.active"
                    @click="item.href"
                    class="flex pt-3 cursor-pointer"
                    :class="_indigo"
                  >
                    <component :is="item.icon" class="w-10 px-3 mx-1" />
                    <span
                      v-if="_extended"
                      class="-ml-1 text-xs text-gray-800 dark:text-gray-300"
                    >
                      {{ item.name }}
                    </span>
                  </span>
                </li>
              </ul>
            </nav>
          </div>

          <!-- Main Slot Content -->
          <div :class="_sideSpacing">
            <slot name="default" />
          </div>
        </div>
      </template>

      <!-- Footer -->
      <template v-if="$slots.footer">
        <slot name="footer" />
        <FooterSection
          v-if="set.layout.footer"
          :set="set"
          :contract="contract"
        />
        <DeveloperSection
          v-if="set.layout.developer"
          @click="set.layout.developer = false"
          :set="set"
          :contract="contract"
        />
        <div
          v-else
          @click="set.layout.developer = true"
          class="text-green-500 center cursor-pointer"
        >
          DEVELOPER-SECTION
        </div>

        <!-- Show toggle for subHeader if nothing else is active -->
        <H2Icon
          v-if="!set.layout.subHeader && !set.layout.header && !set.layout.sidebar"
          @click="set.layout.subHeader = true"
          class="w-10 px-2.5 pb-3 mx-2"
          :class="_indigo"
        />
      </template>
    </div>
  </html>
</template>
