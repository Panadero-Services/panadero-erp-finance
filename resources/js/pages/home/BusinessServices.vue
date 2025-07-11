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
        module="home"
        table="business_services"
        model="BusinessService"
    />
</template>