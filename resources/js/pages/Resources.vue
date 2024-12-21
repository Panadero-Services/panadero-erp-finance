<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

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
import Banner from '@/Components/Banner.vue';


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

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>

            <div id="whatever" class="h-screen max-w-9xl bg-black">
                <ResourcePlanning :contract="_contract" :set="_set" :pulse="pulse"/>
            </div>

        </template>
  
    </AppLayout>
</template>
