<script setup>
import {computed, onMounted, onUnmounted, ref, provide} from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

import pricingPlans from '@/constants/plans.js'
import PricingPlans from '@/panadero/PricingPlans.vue'

const props = defineProps({
    page: Object,
    baseSections: Object
});

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);


const frequencies = [
  { value: 'monthly', label: 'Monthly', priceSuffix: '/month' },
  { value: 'annually', label: 'Annually', priceSuffix: '/year' },
]

const frequency = ref(frequencies[0])

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4 min-w-full ">




          <div class="py-6 sm:py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
              <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-400">Pricing</h2>


                <p class="mt-2 text-xl md:text-2xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Your Innovativon Journey
                <span class="mt-2 text-xl md:text-2xl lg:text-6xl font-bold tracking-tight text-indigo-600 dark:text-white sm:text-4xl">Starts here!</span></p>
              </div>
              <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-800 dark:text-gray-300">Choose an affordable plan that’s packed with the best features for engaging your audience, creating customer loyalty, and driving sales.</p>
              <div class="mt-16 flex justify-center">
                <fieldset aria-label="Payment frequency">
                  <RadioGroup v-model="frequency" class="grid grid-cols-2 gap-x-1 rounded-full bg-white/5 p-1 text-center text-xs font-semibold leading-5 text-white">
                    <RadioGroupOption as="template" v-for="option in frequencies" :key="option.value" :value="option" v-slot="{ checked }">
                      <div :class="[checked ? 'bg-indigo-500' : '', 'cursor-pointer rounded-full px-2.5 py-1']">{{ option.label }}</div>
                    </RadioGroupOption>
                  </RadioGroup>
                </fieldset>
              </div>


        <PricingPlans :plans="pricingPlans" />


            </div>
          </div>

         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
