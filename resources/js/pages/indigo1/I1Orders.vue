<script setup>
import DynamicTable from '@/pages/DynamicTable.vue';
import { useSettingsStore } from '@/stores/settings';

const _set = useSettingsStore();

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object
});

_set.domainFunction = "indigo1";

</script>

<template>
    <DynamicTable
        :page="page"
        :baseSections="baseSections"
        :records="records"
        module="indigo1"
        table="i1_orders"
        model="I1Order"
    />
</template>