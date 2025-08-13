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

// Contact form state
const contactForm = ref({
  name: '',
  email: '',
  company: '',
  phone: '',
  plan: '',
  users: '',
  message: '',
  requestType: 'demo'
});

const isSubmitting = ref(false);
const showSuccess = ref(false);

// Handle form submission
const handleSubmit = async () => {
  isSubmitting.value = true;
  
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  showSuccess.value = true;
  isSubmitting.value = false;
  
  // Reset form
  contactForm.value = {
    name: '',
    email: '',
    company: '',
    phone: '',
    plan: '',
    users: '',
    message: '',
    requestType: 'demo'
  };
  
  // Hide success message after 5 seconds
  setTimeout(() => {
    showSuccess.value = false;
  }, 5000);
};

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
         <div id="mainDiv" class="w-full min-h-[640px] min-w-full">

          <!-- Hero Section -->
          <div class="bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-900 text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
              <h1 class="text-5xl md:text-6xl font-bold mb-6">
                Enterprise ERP Solutions
              </h1>
              <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Transform your business with AI-powered financial management, inventory control, and predictive analytics
              </p>
            </div>
          </div>

          <!-- Pricing Section -->
          <div id="pricing" class="py-2 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

              
              <PricingPlans :plans="pricingPlans" />
              

            </div>
          </div>

        

          <!-- CTA Section -->
          <div class="bg-indigo-900 text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
              <h2 class="text-4xl font-bold mb-6">
                Ready to Transform Your Business?
              </h2>
              <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join hundreds of companies already using our ERP solutions to streamline operations and boost profitability
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contact-form" class="bg-white text-indigo-900 hover:bg-gray-100 px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                  Get Started
                </a>
                <a href="tel:+31-631-3000-31" class="bg-transparent border-2 border-white hover:bg-white hover:text-indigo-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                  Call Now: +31-631-3000-31
                </a>
              </div>
            </div>
          </div>

         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>

<style scoped>
/* Custom styles for the pricing page */
</style>
