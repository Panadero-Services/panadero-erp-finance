<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';
import DemoTable from "@/panaderos/panadero-grid/DemoTable.vue";
import {computed, onMounted, onUnmounted, ref} from 'vue';

// gridpack
// moved package back to root folder
// now exists in two folder 1.root 2./panadero-grid/vendor
import "dhx-gridpack-package/codebase/gridpack.js";
import "dhtmlx-gridpack/codebase/gridpack.css";

const dataset =[{}];

const props = defineProps({
	table : String,
    set : Object
});

let grid;

onMounted(async ()=> {
    grid = new dhx.Grid("whatever", {
        columns: [
            { width: 100, id: "a", header: [{ text: "#" }] },
            { width: 100, id: "b", header: [{ text: "Title" }] },
            { width: 200, id: "c", header: [{ text: "Name" }] },
            { width: 200, id: "d", header: [{ text: "Address" }] }
        ],
        data:[{a: 1, b:'boek', c:"het hof", d:"sstraat 10"},
            {a: 2, b:'album', c:"day at the race", d:"sstraat 11"}],
        headerRowHeight: 40,
        leftSplit: 1,
        editable: true,
        keyNavigation: true,
        width: 600,
        height: "auto",
        dragItem: "both",
        selection: "row",
        //data: dataset
    });
});
</script>

<template>
    <!-- SELF Welcome 1 Card -->
    <div class="col-span-1 md:col-span-1 " >

        <div class="scale-100 p-6 h-80 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
            <div class="h-14 w-14 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full">
                <ApplicationLogo />
            </div>
            
            <div class="ml-4">
                <h2 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">table <span class="text-purple-500">{{table}}</span></h2>
                    <div> text area </div>
            </div>

            <div id="whatever"> </div>



        </div>


    </div>





</template>

<style>
     .grid {
        min-height: 400px;
        max-height: 600px;
    }
</style>





