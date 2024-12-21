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
import Banner from '@/Components/Banner.vue';
// custom sections
import Pagination from "@/layouts/Pagination.vue"
import BotsSection from "@/sections/BotsSection.vue"

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
   _page.value = await _db.getPage('bots');
})

const pulse = ref(false);

</script>

<template>
  <html>
    <AppLayout title="Bots" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>
            <bots-section  :set="_set" :contract="_contract" :db="_db"/>
        </template>

    </AppLayout>
    </html>
</template>
