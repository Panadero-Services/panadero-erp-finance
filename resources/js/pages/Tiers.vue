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

import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'


const frequencies = [
  { value: 'monthly', label: 'Monthly', priceSuffix: '/month' },
  { value: 'annually', label: 'Annually', priceSuffix: '/year' },
]
const tiers = [
  {
    name: 'Freelancer',
    id: 'tier-freelancer',
    href: '#',
    price: { monthly: '$39', annually: '$390' },
    description: 'The essential CMS to provide your best work for clients.',
    features: ['Dashboard', 'Articles', 'Posts', 'Up to 1,000 subscribers', 'Basic analytics', '48-hour support response time'],
    mostPopular: false,
  },
  {
    name: 'Startup',
    id: 'tier-startup',
    href: '#',
    price: { monthly: '$89', annually: '$890' },
    description: 'A plan that scales with your rapidly growing business.',
    features: [
      'Dashboards',
      'Articles',
      'Posts',
      'KPI dashboard',
      'Up to 10,000 subscribers',
      'Advanced analytics',
      '24-hour support response time',
      'Marketing automations',
    ],
    mostPopular: true,
  },
  {
    name: 'Enterprise',
    id: 'tier-enterprise',
    href: '#',
    price: { monthly: '$189', annually: '$1890' },
    description: 'Dedicated domain and infrastructure for your company.',
    features: [
      'Unlimited products',
      'Unlimited products',
      'Unlimited subscribers',
      'Advanced analytics',
      '1-hour, dedicated support response time',
      'Marketing automations',
      'Custom reporting tools',
    ],
    mostPopular: false,
  },
]

const frequency = ref(frequencies[0])


// webhooks
onMounted(async ()=> {
   await _set.initMM();
   await _set.initialize();

//    this.gantt = gantt;

})


onUnmounted(async ()=> {
    //gantt.destructor();
    //cont.innerHTML = "";
})



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




  <div class="py-6 sm:py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-4xl text-center">
        <h2 class="text-base font-semibold leading-7 text-indigo-400">Pricing</h2>
        <p class="mt-2 text-xl md:text-2xl lg:text-4xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Pricing plans for teams of&nbsp;all&nbsp;sizes</p>
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
      <div class=" isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
        <div v-for="tier in tiers" :key="tier.id" :class="[tier.mostPopular ? 'bg-gray-950 ring-2 ring-indigo-500' : 'ring-1 ring-white/10 bg-gray-900 hover:ring-yellow-400 hover:bg-gray-950', 'rounded-3xl p-8 xl:p-10']">
          <div class="flex items-center justify-between gap-x-4">
            <h3 :id="tier.id" class="text-lg font-semibold leading-8 text-black dark:text-white">{{ tier.name }}</h3>
            <p v-if="tier.mostPopular" class="rounded-full bg-indigo-500 px-2.5 py-1 text-xs font-semibold leading-5 text-white">Most popular</p>
          </div>
          <p class="mt-4 text-sm leading-6 text-gray-300">{{ tier.description }}</p>
          <p class="mt-6 flex items-baseline gap-x-1">
            <span class="text-4xl font-bold tracking-tight text-white">{{ tier.price[frequency.value] }}</span>
            <span class="text-sm font-semibold leading-6 text-gray-300">{{ frequency.priceSuffix }}</span>
          </p>
          <a :href="tier.href" :aria-describedby="tier.id" :class="[tier.mostPopular ? 'bg-indigo-500 text-white shadow-sm hover:bg-indigo-400 focus-visible:outline-indigo-500' : 'bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white', 'mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2']">Buy plan</a>
          <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-300 xl:mt-10">
            <li v-for="feature in tier.features" :key="feature" class="flex gap-x-3">
              <CheckIcon class="h-6 w-5 flex-none text-white" aria-hidden="true" />
              {{ feature }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

        </template>
  
    </AppLayout>
</template>
