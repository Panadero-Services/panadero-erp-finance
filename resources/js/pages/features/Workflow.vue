<!--
  Features Workflow Page
  @version 1.0.1
  @date 19-Aug-2025
  @description Framewwork page component for Features.Workflow
-->
<script setup>
import { computed, onMounted, onUnmounted, ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import { WorkflowWrapper } from 'panadero-workflow';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'
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
        <div class="wrapper ... min-h-[640px] mr-4">
          <WorkflowWrapper />
        </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>

<style scoped>
.wrapper {
  width: 100%;
  height: 100%;
}
</style>