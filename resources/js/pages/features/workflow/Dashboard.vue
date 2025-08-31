<script setup>
import { ref, onMounted, provide, computed } from 'vue';
import { WorkflowWrapper } from '/packages/panadero-workflow'

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
//import BotsSection from "@/sections/BotsSection.vue"

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

_set.domainFunction = "ai";

const props = defineProps({
    page: Object,
    baseSections: Object
});

// webhooks
onMounted(async ()=> {
   //await _set.initMM();
   //await _set.initialize();
   //_page.value = await _db.getPage('bots');
})

// Reactive settings to pass to workflow module
const workflowSettings = computed(() => ({
  fontSize: [],
  fontSizes:[],
  scalingStyles: []
}))

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
         <div id="whatever" class="w-full ... min-h-4 min-w-full ">
            <WorkflowWrapper  />
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
