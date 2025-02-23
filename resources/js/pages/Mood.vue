<script setup>

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import MainSection from "@/panaderos/panadero-mood/sections/PanaderoMood.vue";

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
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract">

      <template #header />
      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4  max-w-11xl">
            <MainSection ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse" />
         </div>
      </template>

      <template #footer />

   </AppToolbarLayout>
</template>
