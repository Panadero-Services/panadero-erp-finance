<!--
  ERP Inventory Page
  @version 1.0.0
  @date 31-Aug-2025
  @description Framework page component for ERP.Inventory
-->
<script setup>
import { computed, onMounted, onUnmounted, ref, provide, inject } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import InventoryWrapper from 'panadero-erp-inventory/src/InventoryWrapper.vue';

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



// Get settings store from parent - this IS the SSOT
const settingsStore = inject('settingsStore');

const darkModeClasses = computed(() => {
  const isDark = settingsStore.dark === true
  return {
    container: isDark ? 'bg-gray-800 text-white' : 'bg-white text-gray-900',
    card: isDark ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-200',
    // ... rest of classes
  }
})




const props = defineProps({
    page: Object,
    baseSections: Object
});

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);

// css
const _title = "text-blue-600 dark:text-blue-300";
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
          <InventoryWrapper :settings="_set" />
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