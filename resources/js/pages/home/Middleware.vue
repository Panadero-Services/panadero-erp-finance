<script setup>
import { ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import MiddlewareArchitecture from '@/components/middleware/MiddlewareArchitecture.vue';
import MiddlewareLegend from '@/components/middleware/MiddlewareLegend.vue';

// sections
import MainSection from "@/components/middleware/MiddlewareArchitecture.vue";
import SampleSection from "@/components/middleware/MiddlewareSample.vue";

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _usePage = usePage();

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

_set.domainFunction = "erp";

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

        </template>

        <template #intro>
            <div class=" justify-between items-center mt-4 px-8">
                <h2 class="text-center font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Middleware Architecture
                </h2>
                <div class="flex items-center bg-white dark:bg-gray-700 px-4 py-2 rounded-lg shadow-sm">
                    <h4 class="px-4 mt-2 text-sm font-medium mr-4 text-gray-900 dark:text-white">Legend</h4>
                    <MiddlewareLegend />
                </div>
            </div>
        </template>


        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <SampleSection />
                    <MiddlewareArchitecture />
                </div>
            </div>
        </div>
    </AppToolbarLayout>
</template>
