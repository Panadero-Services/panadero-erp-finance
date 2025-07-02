<script setup>
import { ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// sections
import MainSection from "@/pages/admin/RoleSection.vue";

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

_set.domainFunction = "admin";

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
        </template>
            <div class="p-4">
                <MainSection />
            </div>
    </AppToolbarLayout>
</template>
