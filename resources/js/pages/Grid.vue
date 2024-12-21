<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';

//import ResourcePlanning from "@/panaderos/panadero-resourceplanning/ResourcePlanning.vue";
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

//import { moduleName, moduleVersion } from "panadero-resourceplanning";
import { moduleName, moduleVersion, panaderoGrid} from "panadero-grid";

// gridpack
import "dhx-gridpack-package/codebase/gridpack.js";
import "dhtmlx-gridpack/codebase/gridpack.css";

// grid define
const pGrid= new panaderoGrid();
const data =  pGrid.initialData();
const users =  pGrid.initialUsers();
const srcPhoto = "https://snippet.dhtmlx.com/codebase/data/common/img/02/avatar_";

// grid columns
const columns = [
  { id: "paid", header: [ { text: `
        <label class="dhx_checkbox dhx_cell-editor__checkbox ">
          <input type="checkbox" class="dhx_checkbox__input dhx_checkbox--check-all">
          <span class="dhx_checkbox__visual-input "></span>
        </label> `, rowspan: 2, htmlEnable: true }], type: "boolean", sortable: false },
  { id: "project", header: [{ text: "Project" }, { content: "comboFilter", tooltipTemplate: () => "Choose a project" } ], footer: [{ text: "Total" }], resizable: true },
  { id: "access", header: [ { text: "Access" }, { content: "comboFilter", filterConfig: { multiselection: true } } ],
    editorType: "multiselect", options: users, template: getAccessTemplate, htmlEnable: true },
  { id: "status", header: [{ text: "Status" }, { content: "selectFilter" }], editorType: "combobox", options: ["Shutdown", "Idle", "Running", "Finished", "Pause"],
    editorConfig: { template: ({ value }) => getPriorityTemplate(value) }, template: getPriorityTemplate, htmlEnable: true },
  { id: "owner", header: [{ text: "Owner" }, { content: "inputFilter" }] },
  { id: "balance", header: [{ text: "Balance" }, { content: "inputFilter" }], footer: [{ text: ({ sum }) => sum }],
    template: (value, { balance }) => balance > 0
      ? `<div style='color:green'>⬆ ${value}</div>`
      : `<div style='color:red'>⬇ ${value}</div>`,
    htmlEnable: true, summary: "sum", numberMask: { prefix: "€ " } },
  { id: "hours", header: [{ text: "Hours" }, { content: "inputFilter" }], footer: [{ text: ({ sum }) => sum }], summary: "sum", numberMask: { maxDecLength: 0 } },
  { id: "renewals", header: [{ text: "Renewals" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: ["1 time", "1-2 times", "more than 5 times"] },
  { id: "start_date", header: [{ text: "Start date", align: "center" }], align: "center", type: "date", dateFormat: "%d %M %Y", editorConfig: { asDateObject: true } },
  { id: "end_date", header: [{ text: "End date", align: "center" }], type: "date", align: "center", dateFormat: "%d %M %Y", editorConfig: { asDateObject: true } },
  { id: "cost", header: [{ text: "Cost" }, { content: "inputFilter" }], footer: [{ text: ({ sum }) => sum }], summary: "sum", numberMask: { prefix: "€ ", maxDecLength: 0 } },
  { id: "budget", header: [{ text: "Budget" }, { content: "inputFilter" }], footer: [{ text: ({ sum }) => sum }], summary: "sum", numberMask: { prefix: "€ " } },
  { id: "project_id", header: [{ text: "Project ID", align: "center" }, { content: "inputFilter" }], align: "center", patternMask: "ISS-000.0", }
];

// grid columns routines
columns.forEach(column => column.minWidth = 150);

function getAccessTemplate(values) {
  if (!values.length) return "";
  const photos = values.split(", ").reduce((total, value) => {
    const user = users.find(i => i.value === value);
    return total + (user.ava
      ? `<img src=${srcPhoto + user.ava} class="dhx-demo_grid-user-photo"/>`
      : `<div class="dhx-demo_grid-user-photo" style="background:${user.color};">${user.value[0]}</div>`)
  }, "");
  return `<div class="dhx-demo_grid-user">${photos}</div>`;
}

function getPriorityTemplate(value) {
  if (!value) return;
  let status = "";
  if (value === "Shutdown") status = "text-red-600 dark:text-red-400";
  if (value === "Idle") status = "text-gray-600 dark:text-gray-400";
  if (value === "Running") status = "text-green-600 dark:text-green-400";
  if (value === "Finished") status = "text-blue-600 dark:text-blue-400";
  if (value === "Pause") status = "text-gray-600 dark:text-gray-400";
  return `
    <div class="${status}">
      <span>${value}</span>
    </div>`
}

const _add = () => {
  grid.data.add( pGrid.generateRow() );
}
 
const _delete = () => {
  const cell = grid.selection.getCell();
  grid.data.remove(cell.row.id);
}

let grid;
onMounted(async ()=> {
    await _set.initMM();
    await _set.initialize();
    //await grid.data.load("https://snippet.dhtmlx.com/codebase/data/grid/03/dataset.json");
    grid = new dhx.Grid("grid", {
    columns,
    data,
    leftSplit: 1,
    editable: true,
    keyNavigation: true,
    autoWidth: true,
    height: "auto",
    dragItem: "both",
    selection: "row",
    css: _set.dark ? "custom-black" : "custom",
    eventHandlers: {
      onclick: {
        "dhx_checkbox--check-all": function (event, data) {
          grid.data.forEach(row => {
            grid.data.update(row.id, {
              [data.col.id]: event.target.checked,
            });
          });
        }
      },
    },
  });
});

onUnmounted(async ()=> {
  grid.destructor();
});

const pulse = ref(false);

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>

            <div v-if="true" class="absolute top-16 left-24 space-x-2 z-40">
              <button @click="_add" type="button" class="rounded bg-white dark:bg-gray-900 px-4 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-green-400 dark:hover:bg-green-600">Add</button>
              <button @click="_delete" type="button" class="rounded bg-white dark:bg-gray-900 px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-red-400 dark:hover:bg-red-600">Delete</button>
            </div>

        </template>

        <template #default>
            <div  class="" id="grid">
            </div>
            <div id="whatever" class="max-w-9xl">
            </div>
        </template>
  
        <template #footer>

        </template>
    </AppLayout>
</template>

<!-- custom styles -->

<style>
  .dhx-demo_grid-user {
    display: flex;
  }
    .custom {
        --dhx-font-color-primary: #222;
        --dhx-background-primary: #fff;
        --dhx-s-grid-header-background: #f5f8fa;
        --dhx-border-color: #4A555E;
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


