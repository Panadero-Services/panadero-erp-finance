<script setup>
import DynamicTable from '@/pages/DynamicTable.vue';
import { useSettingsStore } from '@/stores/settings';

const _set = useSettingsStore();

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object
});

_set.domainFunction = "home";

</script>

<template>
    <DynamicTable
        :page="page"
        :baseSections="baseSections"
        :records="records"
        module="home"
        table="futures"
        model="Future"
    />
</template>