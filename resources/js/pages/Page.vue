<script setup>

// vue
import {computed, onMounted, onUnmounted, ref} from 'vue';
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// layout
import AppLayout from '@/layouts/AppLayout.vue';

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';
import PanaderoMood from "@/panaderos/panadero-mood/PanaderoMood.vue";

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// props
const props = defineProps({
    page: Object,
    baseSections: Object
});

// reactive components
const pulse = ref(false);
const myChild = ref(null);

// css

</script>

<template>
    <AppLayout :set="_set" :page="page" :baseSections="baseSections" :contract="_contract">

        <template #header>HEADER</template>
        <template #intro>INTRO</template>

        <template #default>
            CONTENT
                <PanaderoMood ref="myChild" :contract="_contract" :set="_set" :db="_db" :pulse="pulse" />
        </template>

  
        <template #footer>FOOTER</template>
        
    </AppLayout>
</template>

<!-- custom styles -->
<style>
</style>