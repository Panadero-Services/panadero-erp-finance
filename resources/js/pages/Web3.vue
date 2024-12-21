<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';

import DashboardLayout from '@/layouts/DashboardLayout.vue';

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

import Web3Section from "@/sections/Web3Section.vue";
//import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
//import { CheckIcon } from '@heroicons/vue/20/solid'


// webhooks
onMounted(async ()=> {
})

</script>
<template>
    <DashboardLayout title="Tiers" :set="_set">
        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>
            <Web3Section />
        </template>

    </DashboardLayout>
</template>

<style>

</style>
