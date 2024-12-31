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

import PanaderoMood from "@/panaderos/panadero-mood/PanaderoMood.vue";

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

const pulse = ref(false);

// css

const myChild = ref(null);
const _header=ref(true);
const _subHeader=ref(true);


// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
const _button ="rounded px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 ";
const _hover = "hover:bg-indigo-400 dark:hover:bg-indigo-600";
const _bg = "bg-white dark:bg-black";
const _bgSelected = "bg-indigo-200 dark:bg-indigo-800";
const _hoverAdd = "hover:bg-green-400 dark:hover:bg-green-600";
const _hoverDelete = "hover:bg-red-400 dark:hover:bg-red-600";

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection v-if="_header" :set="_set" :contract="_contract"/>
            <SubHeaderSection v-if="_subHeader" :set="_set"/>

            <div class="" id="toolbar"></div>

            <div v-if="true" class="absolute space-x-2 z-40" :class="[_header ? 'top-16' : 'top-1', _subHeader ? 'left-32' : 'left-80']" >

              <button v-if="_subHeader" @click="myChild._save" :disabled="_set.project.id==0" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600" :class="_set.project.id==0 ? 'opacity-35' : '' ">Save</button>
              <button v-if="_subHeader" @click="myChild._load" :disabled="_set.project.id==0" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600" :class="_set.project.id==0 ? 'opacity-35' : '' ">Load</button>
              <button v-if="_subHeader" @click="myChild._add" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600">Add</button>
              <button v-if="_subHeader"  @click="myChild._delete" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-red-400 dark:hover:bg-red-600">Delete</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('willow')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Light</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('willow-dark')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Dark</button>
              <button v-if="_subHeader"  @click="myChild.changeTheme('material')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Material</button>

              <button v-if="_subHeader"  @click="_header=!_header" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Header</button>
              <button @click="_subHeader=!_subHeader" type="button" class="rounded bg-white dark:bg-gray-900 px-1 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">subHeader</button>

            </div>

        </template>

        <template #default>
            <div  class="" id="root"></div>

            <div id="whatever" class="min-h-4 max-w-9xl dark:bg-black">
                <PanaderoMood ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse"/>
            </div>

        </template>
  
        <template #footer>

            <FooterSection  :set="_set" :contract="_contract"/>

        </template>

        
    </AppLayout>
</template>

<!-- custom styles -->
<style>
</style>