<script setup>
import {computed, onMounted, onUnmounted, ref, provide} from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// sections
import ProductOverview from '@/pages/ecommerce/ProductOverview.vue';
import CategoryOverview from '@/pages/ecommerce/CategoryOverview.vue';
import StoreNavigation from '@/pages/ecommerce/StoreNavigation.vue';
import PromoSection from '@/pages/ecommerce/PromoSection.vue';
import ReviewSection from '@/pages/ecommerce/ReviewSection.vue';
import IncentiveSection from '@/pages/ecommerce/IncentiveSection.vue';

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

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

const props = defineProps({
    page: Object,
    baseSections: Object
});

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4 min-w-full">

            <store-navigation />
            <product-overview />
            <incentive-section />
            <category-overview />
            <review-section />

         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>

</template>
