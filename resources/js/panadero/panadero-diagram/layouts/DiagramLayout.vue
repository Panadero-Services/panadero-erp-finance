<script setup>
import "@/panadero/panadero-diagram/codebase/diagramWithEditor.js?v=6.0.10";
import "@/panadero/panadero-diagram/codebase/diagram.css?v=6.0.10";
import "@/panadero/panadero-diagram/codebase/diagramWithEditor.css?v=6.0.10";

// layouts
//import Footer from '@/layouts/Footer.vue';
//import Header from '@/layouts/Header.vue';

const props = defineProps({
    item: String
});


</script>

<template>
    <!--:class="_mainLayout" -->
    <div >
        <slot />
    </div>
</template>

<style>

</style>