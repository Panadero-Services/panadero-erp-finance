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


import ResourcePlanning from "@/panaderos/panadero-resourceplanning/ResourcePlanning.vue";
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

// webhooks
onMounted(async ()=> {
  await _set.initMM();
  await _set.initialize();
});

const pulse = ref(false);

onUnmounted(async ()=> {
    //gantt.destructor();
    //cont.innerHTML = "";
});
// css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

const _header=ref(true);
const _subHeader=ref(true);

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>
            <Banner />

        <HeaderSection v-if="_header" :set="_set" :contract="_contract"/>
            <SubHeaderSection v-if="_subHeader" :set="_set"/>

         
      <div v-if="true" class="absolute space-x-2 z-40" :class="[_header ? 'top-16' : 'top-1', _subHeader ? 'left-32' : 'left-80']" >

              <button v-if="_subHeader"  @click="_set.projectTitle='default'" type="button" class=" rounded bg-white dark:bg-indigo-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">d1</button>
              <button v-if="_subHeader"  @click="_set.projectTitle='segundo'" type="button" class="rounded bg-white dark:bg-indigo-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">s2</button>
              <button v-if="_subHeader"  @click="_set.projectTitle='tercera'" type="button" class="rounded bg-white dark:bg-indigo-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">t3</button>

              <button v-if="_subHeader" @click="_save" :disabled="_set.projectId==0" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600" :class="_set.projectId==0 ? 'opacity-35' : '' ">Save</button>
              <button v-if="_subHeader" @click="_load" :disabled="_set.projectId==0" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600" :class="_set.projectId==0 ? 'opacity-35' : '' ">Load</button>
              <button v-if="_subHeader" @click="_add" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600">Add</button>
              <button v-if="_subHeader"  @click="_delete" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-red-400 dark:hover:bg-red-600">Delete</button>
              <button v-if="_subHeader"  @click="changeTheme('willow')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Light</button>
              <button v-if="_subHeader"  @click="changeTheme('willow-dark')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Dark</button>
              <button v-if="_subHeader"  @click="changeTheme('material')" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Material</button>
              <button v-if="_subHeader"  @click="_header=!_header" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">Header</button>
              <button @click="_subHeader=!_subHeader" type="button" class="rounded bg-white dark:bg-gray-900 px-1 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-600">subHeader</button>

            </div>

        </template>

        <template #default>

            <div id="whatever" class="h-screen max-w-9xl bg-black">
                <ResourcePlanning :contract="_contract" :set="_set" :pulse="pulse"/>
            </div>

        </template>
  
    </AppLayout>
</template>
