<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';
import { EnvelopeIcon, ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon, NewspaperIcon, RectangleGroupIcon, PencilSquareIcon, BarsArrowUpIcon, BuildingOfficeIcon, ClipboardDocumentCheckIcon, CubeTransparentIcon, CreditCardIcon, RocketLaunchIcon, LanguageIcon, DevicePhoneMobileIcon, TruckIcon, PuzzlePieceIcon } from '@heroicons/vue/24/outline';

import { usePage } from '@inertiajs/vue3';
import NavLink from '@/components/NavLink.vue';


const props = defineProps({
    set: Object, 
    f: Object, 
    menu: Object, 
    featured: Boolean, 
    progress: Number
});

const _navigate = () =>  {
    router.push(props.f.url); 
}


// css
const _basic = " text-xxs lg:text-xs font-normal leading-tight ";
const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400";
const _menu = " flex items-center  "+ _indigo + _basic;
const _icon = " w-4 h-4 mr-2 ";

const _feature = "h-5 w-5 m-2 text-gray-500 dark:text-white";



</script>



<template>
    <!-- SELF Stakepool 2 Card -->
    <div  class="">
        <div class="scale-100 py-4 px-3 h-[400px] bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-xs shadow-md shadow-gray-400 dark:shadow-none motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">

            <div class="ml-2 mt-2 ">
            
                <div>
                    <div class="-mt-12 absolute inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-purple-500 rounded-full dark:bg-purple-700 divide-y ">
                        <span class="font-bold text-sm text-gray-50 dark:text-gray-300">{{f.item}}</span>
                    </div>

                    <div @click="_navigate" class="flex mt-4 pt-3 text-black dark:text-white text-xl text-center"> 
                          <RectangleGroupIcon v-if="'I3FrameworkIcon'==f.icon" :class="_feature" aria-hidden="true" />
                          <PencilSquareIcon v-if="'ContentManagementIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <BarsArrowUpIcon v-if="'ResourcePlanningIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <BuildingOfficeIcon v-if="'LogisticsIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <ClipboardDocumentCheckIcon v-if="'ProjectIcon'==f.icon" :class="_feature" aria-hidden="true" />
                          <CubeTransparentIcon v-if="'DesignIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <CreditCardIcon v-if="'EcommerceIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <RocketLaunchIcon v-if="'Web3Icon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <LanguageIcon v-if="'AiIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <DevicePhoneMobileIcon v-if="'SocialMediaIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <PuzzlePieceIcon v-if="'PuzzlePieceIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <TruckIcon v-if="'TruckIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <CloudArrowUpIcon v-if="'CloudArrowUpIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                          <ArrowPathIcon v-if="'ArrowPathIcon'==f.icon" :class="_feature" aria-hidden="true" />
                          <FingerPrintIcon v-if="'FingerPrintIcon'==f.icon" :class="_feature" aria-hidden="true" />
                          <Bars3Icon v-if="'Bars3Icon'==f.icon" :class="_feature" aria-hidden="true" />                     
                          <LockClosedIcon v-if="'LockClosedIcon'==f.icon" :class="_feature" aria-hidden="true" />
                          <WalletIcon v-if="'WalletIcon'==f.icon" :class="_feature" aria-hidden="true" />
                            {{f.title}}
                    </div>

                    <div class="text-gray-600 dark:text-gray-400 text-xxs text-center"> Business Service </div>

                    <!-- Navigation Links -->
                    <template v-for="item in f.options" :key="item.name">
                        <NavLink v-if="item.when ? item.when() : true" :href="item.url" :active="route().current(item.route)" :class="_menu">
                            {{item.name}}
                        </NavLink>
                    </template>
                </div>

            <div class="mt-4">
                <div class="text-xs mt-4 ml-1 text-indigo-600 dark:text-indigo-400">
                    <p class="line-clamp-1">{{f.description}}</p>
                    <p>{{f.version || 'v0.0.00'}}</p>

                    <h2 class="mt-2 text-black dark:text-white text-xs">{{set.settings}}<span>Business Requirement dbStruct</span> </h2>
                    <h2 class="text-black dark:text-white text-xs">{{set.settings}}<span>Process</span> </h2>
                    <h2 class="text-black dark:text-white text-xs">{{set.settings}}<span>Logic</span> </h2>
                </div>                     
            </div>


                  <div>
                    <div class="mt-4" aria-hidden="true">
                        <div class="text-right text-xs" :class="progress>90 ? 'text-green-600 dark:text-green-400' : 'text-indigo-600 dark:text-indigo-400'">{{progress}} %</div>

                      <div class="overflow-hidden rounded-full bg-gray-200">
                        <div class="h-1 rounded-full " :class="progress>90 ? 'bg-green-600 dark:bg-green-400' : 'bg-indigo-600 dark:bg-indigo-400'" :style="'width:'+ progress +'%'" />
                      </div>
                      <div class="mt-3 hidden grid-cols-4 text-xs font-medium text-gray-600 sm:grid">
                        <div :class="progress>20 ? 'text-indigo-600  dark:text-indigo-400' : ''">Make it Work</div>
                        <div class="text-center" :class="progress>50 ? 'text-indigo-600 dark:text-indigo-400' : ''">Make it Good</div>
                        <div class="text-center" :class="progress>75 ? 'text-indigo-600 dark:text-indigo-400' : ''">Modular</div>
                        <div class="text-right" :class="progress>90 ? 'text-indigo-600 dark:text-indigo-400' : ''">Deployed</div>
                      </div>
                    </div>
                  </div>

            <div class="mt-6">
                <div>
                    <div>
                        <span class="text-black dark:text-white text-xs flex mt-6">
                            <EnvelopeIcon class="mt-1 mr-2 w-4 h-4 text-blue-700"/>
                            {{$page.props.auth.user.email}}
                            <span v-if="f.status=='featured'" class="ml-4 -mt-1 inline-flex items-center rounded-md bg-green-500/10 px-2 py-1 text-xs font-medium text-green-800 dark:text-green-400 ring-1 ring-inset ring-green-500/20">Featured</span>
                            <span v-if="f.status=='upgrading'" class="ml-4 -mt-1 inline-flex items-center rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-800 dark:text-blue-400 ring-1 ring-inset ring-blue-500/20">Upgrading</span>
                            <span v-if="f.status=='scheduled'" class="ml-4 -mt-1 inline-flex items-center rounded-md bg-gray-500/10 px-2 py-1 text-xs font-medium text-gray-800 dark:text-gray-400 ring-1 ring-inset ring-gray-500/20">Scheduled</span>
                            <span v-if="f.status=='deprecated'" class="ml-4 -mt-1 inline-flex items-center rounded-md bg-red-500/10 px-2 py-1 text-xs font-medium text-red-800 dark:text-red-400 ring-1 ring-inset ring-red-500/20">Deprecated</span>
                        </span>


                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>