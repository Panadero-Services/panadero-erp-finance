<script setup>
import { ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import DiagramLayout from '@/panadero/panadero-diagram/layouts/DiagramLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import PertSection from "@/panadero/panadero-diagram/sections/PanaderoPert.vue";
import MindSection from "@/panadero/panadero-diagram/sections/PanaderoMind.vue";
import LaneSection from "@/panadero/panadero-diagram/sections/PanaderoLane.vue";

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

_set.domainFunction = "design";

const props = defineProps({
    page: Object,
    baseSections: Object, 
    item: String
});
const myChild = ref(null);
const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);

// dashboard specifics
_set.layout.footer=false;
</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>
         <div id="" class="">
            <div class="">
               <PertSection v-if="item=='pert'" ref="myChild" :set="_set" :db="_db" />
               <MindSection v-if="item=='mind'" ref="myChild" :set="_set" :db="_db" />
               <LaneSection v-if="item=='lane'" ref="myChild" :set="_set" :db="_db" />
            </div>
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
