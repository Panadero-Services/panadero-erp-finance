<script setup>
import DynamicTable from '@/pages/DynamicTable.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object
});
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