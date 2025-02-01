<script setup>
// vue
import {computed, onMounted, onUnmounted, ref} from 'vue';
// layout
import DashboardLayout from '@/layouts/DashboardLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import HeaderSection from "@/sections/HeaderSection.vue";
import SubHeaderSection from "@/sections/SubHeaderSection.vue";
import Banner from '@/components/Banner.vue';
// custom sections
import MainSection from '@/panaderos/bento/sections/BentoMainSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

//const props = defineProps({
//    page: Object
//});

const _page = ref([]);

// webhooks
onMounted(async ()=> {
   await _set.initMM();
   await _set.initialize();
   _page.value = await _db.getPage('bento');
})

const pulse = ref(false);

</script>

<template>
  <html>
    <DashboardLayout title="Bento" :set="_set">

        <template #header>
          <Banner />

            <div v-for="section in baseSections"  >
               <!--   0 Basic Public Header .... -->
                <div v-if="page.header">
                    <HeaderSection v-if="section.file =='HeaderSection2.vue'" :set="_set" :contract="_contract" :page="page" :section="section"  />
                </div>
            </div>

          
          <SubHeaderSection :set="_set" v-model:pulse="pulse"/>
        </template>
      
        <template #default>
          <MainSection :set="_set" v-model:pulse="pulse"/>
        </template>
    
    </DashboardLayout>
    </html>
</template>
