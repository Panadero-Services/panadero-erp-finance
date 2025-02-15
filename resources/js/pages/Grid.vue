<script setup>

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import MainSection from "@/panaderos/panadero-grid/PanaderoGrid.vue";

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

const props = defineProps({
    page: Object,
    baseSections: Object
});

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" >

      <template #header />
      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4 min-w-full ">
            <MainSection ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse" />
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
