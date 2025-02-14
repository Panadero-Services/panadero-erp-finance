<script setup>
// vue
import {computed, onMounted, onUnmounted, ref} from 'vue';

// layout
import AppLayout from '@/layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import FooterSection from "@/sections/FooterSection.vue"

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


const props = defineProps({
    page: Object,
    baseSections: Object
});


const _page = ref([]);

// webhooks
//onMounted(async ()=> {
//   await _set.initMM();
//   await _set.initialize();
//})

const pulse = ref(false);

</script>

<template>
  <html>
    <AppLayout title="Bento" :bgDark="_set.dark" >
        
        <template #header>
            <Banner />
            <!--   0 Basic Header Sections .... -->
            <div v-for="section in baseSections"  >
                <HeaderSection v-if="section.file =='HeaderSection' && _set.layout.header" :page="page" :set="_set" :contract="_contract" :section="section"  />
                <SubHeaderSection v-if="section.file =='SubHeaderSection' && _set.layout.subHeader" :page="page" :set="_set" :contract="_contract" :section="section"  />
            </div>
        </template>
      
        <template #default>
          <MainSection :set="_set" v-model:pulse="pulse"/>
        </template>
    
    </AppLayout>
    </html>
</template>
