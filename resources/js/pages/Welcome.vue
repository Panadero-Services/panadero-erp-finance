<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Welcome from '@/components/Welcome.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';

const props =defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String
});

// webhooks
onMounted(async ()=> {
   await _set.initMM();
   await _set.initialize();
})

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

</script>
<template>

    <AppLayout title="Welcome" :set="_set" >

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>
            <div>
               <div class="col-span-2">
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-1 gap-2">
                        <div class="block max-w-9xl " >
                            <!-- <ApplicationLogo class="block w-80 h-80" /> -->
                            <img src="@/layouts/logos/original.png">
                        </div>

                        <div class="bg-white dark:bg-gray-950 overflow-hidden shadow-xl sm:rounded-lg max-w-7xl" :class="_shadow">
                            <Welcome />
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </AppLayout>

</template>
