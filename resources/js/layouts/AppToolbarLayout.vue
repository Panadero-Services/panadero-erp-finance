<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'

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

// Reactive variable to hold the remaining time until token expiration
const remainingTime = ref(null);

// Store the initial expiration time
const expirationTime = ref(Date.now() + (2 * 60 * 60 * 1000)); // 2 hours from now

// Add computed property for progress percentage
const progressPercentage = computed(() => {
  if (remainingTime.value === null) return 100;
  return (remainingTime.value / (2 * 60 * 60)) * 100; // 2 hours in seconds
});

// Add computed property for progress bar color
const progressBarColor = computed(() => {
  if (progressPercentage.value > 60) return 'bg-green-500 dark:bg-green-600';
  if (progressPercentage.value > 30) return 'bg-yellow-500 dark:bg-yellow-500';
  return 'bg-red-500 dark:bg-red-600';
});

const progressTextColor = computed(() => {
  if (progressPercentage.value > 60) return 'text-green-500 dark:text-green-600';
  if (progressPercentage.value > 30) return 'text-yellow-500 dark:text-yellow-500';
  return 'text-red-500 dark:text-red-600';
});

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

const page = usePage();

// Check token expiration every minute
const checkTokenExpiration = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  
  if (token) {
    try {
      const currentTime = Date.now();
      const remainingSeconds = Math.max(0, Math.floor((expirationTime.value - currentTime) / 1000));
      remainingTime.value = remainingSeconds;
      
      if (currentTime >= expirationTime.value || (expirationTime.value - currentTime) < 60000) {
        // Clear any stored data
        sessionStorage.clear();
        localStorage.clear();
        
        // Force redirect to login
        window.location.replace('/login');
      }
    } catch (error) {
      console.error('Error checking token expiration:', error);
      remainingTime.value = null;
    }
  } else {
    remainingTime.value = null;
  }
};

// Set up intervals
let tokenCheckInterval;
let countdownInterval;

onMounted(() => {
  console.log('Component mounted');
  if (!page.props.auth.user) {
    const currentPath = window.location.pathname;
    sessionStorage.setItem('intendedDestination', currentPath);
    window.location.href = '/login';
  } else {
    const intendedDestination = sessionStorage.getItem('intendedDestination');
    if (intendedDestination) {
      sessionStorage.removeItem('intendedDestination');
      window.location.href = intendedDestination;
    }
  }

  // Set initial time to 2 hours (7200 seconds)
  remainingTime.value = 2 * 60 * 60;
  
  // Check token immediately on mount
  checkTokenExpiration();
  
  // Update countdown every second
  countdownInterval = setInterval(() => {
    if (remainingTime.value > 0) {
      remainingTime.value--;
    }
  }, 1000);
  
  // Check token validity every minute
  tokenCheckInterval = setInterval(checkTokenExpiration, 60000);
});

onUnmounted(() => {
  // Clean up intervals when component is unmounted
  if (tokenCheckInterval) {
    clearInterval(tokenCheckInterval);
  }
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});


const _tooltip = computed(() => {
  //return "Asdf";
return "Auto log off: "+ Math.floor(remainingTime.value / 60) +"m " +remainingTime.value % 60 +"s";
});


</script>

<template>
  <html class="overscroll-none" :class="set.dark ? 'dark bg-black' : 'light bg-indigo-100'">
    <div class="bg-slate-100 text-black/50 dark:bg-slate-950 dark:text-white/50 h-screen w-screen font-roboto">
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
        
          <!-- Optionally display remaining time -->
          <div v-if="remainingTime !== null" class="justify-=bg-gray-100 dark:bg-black flex items-center gap-2 text-xs text-gray-500">
            <div class="ml-6 -mt-1 w-16 h-0.5 bg-gray-200 rounded-full overflow-hidden">
              <div :title="_tooltip"
                :class="[progressBarColor, 'h-full transition-all duration-500 ease-out']"
                :style="{ width: `${progressPercentage}%`, minWidth: '2px' }"
              ></div>
            </div><span v-if="remainingTime<4000" class="-mt-1 text-xxs font-bold"  :class="['h-full transition-all duration-500 ease-out', progressTextColor]">{{_tooltip}}</span>
          </div>

      </template>

      <!-- Main Content -->
      <template v-if="$slots.default">

        <div class="bg-gray-50 dark:bg-black flex">

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
      
        <div>
        
          <!-- Intro Slot Content - Independent -->
          <div class="w-full">
              <slot name="intro" />
          </div>

          <!-- Main Slot Content - Independent -->
          <div class="w-full">
              <slot name="default" />
          </div>

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
