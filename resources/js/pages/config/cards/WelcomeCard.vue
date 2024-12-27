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
    set : Object, 
    id :  Number 
});

let grid;

let _ref = props.table + '-gridPlaceholder-'+props.id;
let _pag = props.table + '-gridPlaceholder-'+props.id;



onMounted(async ()=> {
    grid = new dhx.Grid(_ref, {
        columns: [
            { width: 35, id: "a", header: [{ text: "#" }] },
            { width: 75, id: "b", header: [{ text: "Title" }] },
            { width: 120, id: "c", header: [{ text: "Name" }] },
            { width: 170, id: "d", header: [{ text: "Address" }] }
        ],
        data:[{a: 1, b:'boek', c:"het hof", d:"sstraat 10"},
            {a: 2, b:'album', c:"day at the race", d:"sstraat 11"},
            {a: 3, b:'closet', c:"never to do ", d:"sstraat 11"},
            {a: 4, b:'door', c:"smth with clay", d:"sstraat 11"},
            {a: 5, b:'window', c:"airbrush as a service", d:"sstraat 11"},
            {a: 6, b:'room', c:"cars without wheelsß", d:"sstraat 11"}
            ],
        headerRowHeight: 36,
        leftSplit: 1,
        editable: true,
        keyNavigation: true,
        rowHeight: 24,
        width: 600,
        height: "auto",
        dragItem: "both",
        selection: "row",
        css: props.set.dark ? "i3-dark" : "i3"

        //data: dataset
    });



const pagination = new dhx.Pagination(_pag, {
    css: props.set.dark ? "pagination-dark" : "pagination",
    data: grid.data,
    height: 12,
    pageSize: 50
});



});
</script>

<template>
    <!-- SELF Welcome 1 Card -->
    <div class="dhx-grid-template items-start justify-start dark:border-2 dark:border-gray-800 grid grid-cols-2 h-80 overflow-scroll w-96 scale-100 p-2 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none  motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500" >

        <div class="text-gray-900 dark:text-green-400 m-1 text-xs">
SYS TABLE

         </div>            
        <div class="h-4">
            <h2 class="mt-1 text-xs text-right font-thin uppercase text-gray-900 dark:text-white">table <span class="ml-2 text-purple-500">{{table}}</span></h2>
        </div>            
    
        <div class="text-xs col-span-2 items-start justify-start " :id="_ref"> </div>
        <div class="text-xs col-span-2" :id="_pag"> </div>


    </div>

</template>


<!-- custom styles -->
<style>
  .dhx-demo_grid-user {
    display: flex;
  }

    .pagination {
        --dhx-background-primary: #fff;
        --dhx-background-secondary: #C7A3F9;
        --dhx-color-primary: #118d8d;
        --dhx-font-color-primary: #fff;

        --dhx-font-size-normal: 11px;
        --dhx-icon-size-normal: 14px;
        height: 32px;
    }

    .pagination-dark {
        --dhx-background-primary: #000;
        --dhx-background-secondary: #000;
        --dhx-color-primary: #118d8d;
        --dhx-font-color-primary: #fff;
        --dhx-font-size-normal: 11px;
        --dhx-icon-size-normal: 14px;
        height: 32px;
    }

    .dhx-grid-template {
        height: 100%;
        align-items: center;
        justify-content: flex-start;
    }



    .i3 {
        justify-content: flex-start;
        --dhx-font-size-normal: 11px;
        --dhx-font-color-primary: #333;
        --dhx-background-primary: #fff;
        --dhx-s-grid-header-background: #C7A3F9;
        --dhx-border: var(--dhx-border-width) solid #E4D1FF;
    }


    .i3-dark {
        justify-content: flex-start;
        --dhx-font-size-normal: 11px;
        --dhx-font-color-primary: #ccc;
        --dhx-background-primary: #530090;
        --dhx-s-grid-header-background: #000;
        --dhx-border-color: rgb(30 41 59 / var(--tw-bg-opacity, 1));
        --dhx-border: var(--dhx-border-width) solid var(--dhx-border-color);
    }

    .custom {
        --dhx-font-color-primary: #222;
        --dhx-background-primary: #fff;
        --dhx-s-grid-header-background: #f5f8fa;
        --dhx-border-color: #eee;
        --dhx-border: var(--dhx-border-width) solid var(--dhx-border-color);
    }

    .custom-black {
        --dhx-font-color-primary: #bbb;
        --dhx-background-primary: #212;
        --dhx-s-grid-header-background: #b86c02;
        --dhx-border-color: #4A555E;
        --dhx-border: var(--dhx-border-width) solid var(--dhx-border-color);
    }

  .dhx-demo_grid-user-photo {
        border-radius: 50%;
        width: 24px;
        height: 24px;
        background: #61C874;
        text-align: center;
        line-height: 23px;
        border: solid 1px #FFF;
        color: white;
        font-weight: 500;
        margin-right: -3px;
  }
</style>








