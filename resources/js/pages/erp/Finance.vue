<script setup>
import {computed, onMounted, onUnmounted, ref, provide} from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import Finance from '../../../../packages/panadero-erp-finance/src/components/Finance.vue';

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


const props = defineProps({
    page: Object,
    baseSections: Object
});

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);

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
         <div class="w-screen"></div>
        <div class="finance-wrapper ... min-h-[640px] mr-4">
          <Finance />
        </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>

<style scoped>
.finance-wrapper {
  width: 100%;
  height: 100%;
}
</style>






