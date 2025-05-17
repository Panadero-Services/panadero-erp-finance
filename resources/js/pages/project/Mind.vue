<script setup>
import { ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import MainSection from "@/panaderos/panadero-diagram/sections/PanaderoMind.vue";

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

_set.domainFunction = "project";


const props = defineProps({
    page: Object,
    baseSections: Object
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
               <MainSection ref="myChild" :set="_set" :db="_db" />
            </div>
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
