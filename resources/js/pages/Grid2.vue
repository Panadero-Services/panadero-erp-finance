<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

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
import Banner from '@/Components/Banner.vue';

//import ResourcePlanning from "@/panaderos/panadero-resourceplanning/ResourcePlanning.vue";
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

//import "dhx-gridpack-package/codebase/gridpack.js";
//import "dhtmlx-gridpack/codebase/gridpack.css";

//console.log(dhx);
//import"panadero-resourceplanning/gridpack_trial_9_0/codebase/gridpack.css";
//import * as dhx from "../assets/dhxSuite/suite";
//import "../assets/dhxSuite/suite.css";

const data = [
  { project: "Real Estate", owner: "Lieuwe Bakker", start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: 92, cost: 3588, budget: 11768, balance: 8180, paid: true, access: "1, 2, 4", renewals: "1-2 times", project_id: 9969 },
  { project: "HR Application", owner: "Inge Morcus", start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: 340, cost: 15980, budget: 18856, balance: 2876, paid: true, access: "4, 5", renewals: "1 time", project_id: 1342 },
  { project: "HR Application", owner: "Marco Kroesbergen", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: 484, cost: 21296, budget: 14907, balance: -6389, paid: false, access: "3, 1",renewals: "1 time",project_id: 1110 },
  { project: "Inventory", owner: "Damien Rice", start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: 345, cost: 14835, budget: 70911, balance: 56076, paid: false, access: "2, 4, 5",renewals: "1-2 times",project_id: 6789 },
  { project: "Inventory",owner: "Bart Nijssen", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: 57, cost: 2052, budget: 5068, balance: 3016, paid: true, access: "1, 2, 4, 5",renewals: "1-2 times",project_id: 4332 },
  { project: "RentMagic", owner: "Eric Verhelst", start_date: "Fri Jun 02 2024 00:00:00", end_date: "Mon Jan 06 2024 00:00:00", status: "Done", hours: 211, cost: 8229, budget: 16540, balance: 8311, paid: false, access: "3, 5", renewals: "more than 5 times",project_id: 3562 },
  { project: "RentMagic", owner: "Mark Timmer", start_date: "Thu Jun 05 2025 00:00:00", end_date: "Mon Mar 07 2025 00:00:00", status: "In Progress",  hours: 3, cost: 144, budget: 122, balance: -22, paid: true, access: "3, 2", renewals: "1 time", project_id: 3421 },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Feb 04 2025 00:00:00", end_date: "Tue Mar 08 2025 00:00:00", status: "In Progress", hours: 76, cost: 3496, budget: 12515, balance: 9019, paid: true, access: "1, 5", renewals: "more than 5 times", project_id: 2567 },
  { project: "RentMagic", owner: generateName(), start_date: "Thu Jun 05 2025 00:00:00", end_date: "Mon Mar 07 2025 00:00:00", status: "In Progress", hours: 3, cost: 144, budget: 122, balance: -22, paid: true, access: "3, 2", renewals: "1 time", project_id: 3421 },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Feb 04 2025 00:00:00", end_date: "Tue Mar 08 2025 00:00:00", status: "In Progress", hours: 76, cost: 3496, budget: 12515, balance: 9019, paid: true, access: "1, 5", renewals: "more than 5 times", project_id: 2567 },
  { project: "Real Estate", owner: generateName(), start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: 92, cost: 3588, budget: 11768, balance: 8180, paid: true, access: "1, 2, 4", renewals: "1-2 times", project_id: 9969 },
  { project: "HR Application", owner: generateName(), start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: 340, cost: 15980, budget: 18856, balance: 2876, paid: true, access: "4, 5", renewals: "1 time", project_id: 1342 },
  { project: "HR Application", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: 484, cost: 21296, budget: 14907, balance: -6389, paid: false, access: "3, 1", renewals: "1 time", project_id: 1110 },
  { project: "Inventory", owner: generateName(), owner: "Damien Rice", start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: 345, cost: 14835, budget: 70911, balance: 56076, paid: false, access: "2, 4, 5", renewals: "1-2 times", project_id: 6789 },
  { project: "Inventory", owner: "Bart Nijssen", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: 57, cost: 2052, budget: 5068, balance: 3016, paid: true, access: "1, 2, 4, 5", renewals: "1-2 times", project_id: 4332 },
  { project: "RentMagic", owner: generateName(), start_date: "Fri Jun 02 2024 00:00:00", end_date: "Mon Jan 06 2024 00:00:00", status: "Done", hours: 211, cost: 8229, budget: 16540, balance: 8311, paid: false, access: "3, 5", renewals: "more than 5 times", project_id: 3562 },
  { project: "Real Estate", owner: generateName(), start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "HR Application", owner: generateName(), start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "HR Application", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "Inventory", owner: generateName(), start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "Inventory", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
];

const users = [
  { id: "0", value: "Mick", ava: "01.jpg" },
  { id: "1", value: "Lieuwe", ava: "01.jpg" },
  { id: "2", value: "Peter", ava: "02.jpg" },
  { id: "3", value: "Mark", ava: "03.jpg" },
  { id: "4", value: "Bart", ava: "04.jpg" },
  { id: "5", value: "Inge", color: "#61C874" }
];

const srcPhoto = "https://snippet.dhtmlx.com/codebase/data/common/img/02/avatar_";
const columns = [
  {
    id: "paid",
    header: [
      {
        text: `
        <label class="dhx_checkbox dhx_cell-editor__checkbox ">
          <input type="checkbox" class="dhx_checkbox__input dhx_checkbox--check-all">
          <span class="dhx_checkbox__visual-input "></span>
        </label>
      `,
        rowspan: 2,
        htmlEnable: true
      }
    ],
    type: "boolean",
    sortable: false
  },
  {
    id: "project",
    header: [
      { text: "Project" },
      { content: "comboFilter", tooltipTemplate: () => "Choose a project" }
    ],
    footer: [{ text: "Total" }],
    resizable: true
  },
  {
    id: "access",
    header: [
      { text: "Access" },
      { content: "comboFilter", filterConfig: { multiselection: true } }
    ],
    editorType: "multiselect",
    options: users,
    template: getAccessTemplate,
    htmlEnable: true
  },
  {
    id: "status",
    header: [{ text: "Status" }, { content: "selectFilter" }],
    editorType: "combobox",
    options: ["Done", "In Progress", "Not Started"],
    editorConfig: {
      template: ({ value }) => getPriorityTemplate(value)
    },
    template: getPriorityTemplate,
    htmlEnable: true
  },
  {
    id: "owner",
    header: [{ text: "Owner" }, { content: "inputFilter" }]
  },
  {
    id: "balance",
    header: [{ text: "Balance" }, { content: "inputFilter" }],
    footer: [{ text: ({ sum }) => sum }],
    template: (value, { balance }) => balance > 0
      ? `<div style='color:green'>⬆ ${value}</div>`
      : `<div style='color:red'>⬇ ${value}</div>`,
    htmlEnable: true,
    summary: "sum",
    numberMask: { prefix: "€ " }
  },
  {
    id: "hours",
    header: [{ text: "Hours" }, { content: "inputFilter" }],
    footer: [{ text: ({ sum }) => sum }],
    summary: "sum",
    numberMask: { maxDecLength: 0 }
  },
  {
    id: "renewals",
    header: [{ text: "Renewals" }, { content: "comboFilter" }],
    type: "string",
    editorType: "combobox",
    options: ["1 time", "1-2 times", "more than 5 times"]
  },
  {
    id: "start_date",
    header: [{ text: "Start date", align: "center" }],
    align: "center",
    type: "date",
    dateFormat: "%d %M %Y",
    editorConfig: { asDateObject: true }
  },
  {
    id: "end_date",
    header: [{ text: "End date", align: "center" }],
    type: "date",
    align: "center",
    dateFormat: "%d %M %Y",
    editorConfig: { asDateObject: true }
  },
  {
    id: "cost",
    header: [{ text: "Cost" }, { content: "inputFilter" }],
    footer: [{ text: ({ sum }) => sum }],
    summary: "sum",
    numberMask: { prefix: "€ ", maxDecLength: 0 }
  },
  {
    id: "budget",
    header: [{ text: "Budget" }, { content: "inputFilter" }],
    footer: [{ text: ({ sum }) => sum }],
    summary: "sum",
    numberMask: { prefix: "€ " }
  },
  {
    id: "project_id",
    header: [{ text: "Project ID", align: "center" }, { content: "inputFilter" }],
    align: "center",
    patternMask: "ISS-000.0",
  }
];

function generateName(){
  let f = ["Kai", "Louis", "Bob", "Robin", "Luka", "Eliana", "Jaden", "Ezra", "Luca", "Rowan", "Nova", "Amara", "Aaliyah", "Finn", "Lieuwe", "Mark", "Eric", "Eric", "Vince", "Inge", "Laura", "Linda", "Peter", "Mick", "Sofia", "Fleur", "Tessa", "Ray", "Raymond", "Toffie", "Eppie", "Mickey", "Piet", "Jan", "Mick", "Gopher", "Don", "Karel", "Albert", "Pieter"];
  let l= ["Smith", "Johnson", "Williams", "Brown", "Jones", "Garcia", "Miller", "Davis", "Martinez", "Wilson", "Bakker", "Kroesbergen", "Wilson", "Nijssen", "Swanders", "Jansen", "Janssen", "Bond", "Garner", "van Basten", "Bergkamp", "Ali", "Hemyatahar", "Reagan", "van Kinsbergen", "van Brakel", "Alyeson"];
  let rf = Math.floor(Math.random()*f.length);
  let rl = Math.floor(Math.random()*l.length);
  return f[rf]+" "+l[rl];
}

const _add = () => {
  grid.data.add(
        {
            project: "Real Estate",
            owner: generateName(),
            start_date: "Mon Dec 30 2024 00:00:00",
            end_date: "Tue Dec 31 2024 00:00:00",
            status: "Not Started",
            hours: Math.floor(Math.random() * 150),
            cost: Math.floor(Math.random() * 5000),
            budget: Math.floor(Math.random() * 100000),
            balance: Math.floor(Math.random() * 10000),
            paid: true,
            access: Math.ceil(Math.random() * 4),
            renewals: "1-2 times",
            project_id: Math.floor(Math.random() * 5000),
        }
  );
}
 
const _delete = () => {
  const cell = grid.selection.getCell();
  grid.data.remove(cell.row.id);
}

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
  let status = "dhx-demo_grid-status--not-started";
  if (value === "Done") status = "dhx-demo_grid-status--done";
  if (value === "In Progress") status = "dhx-demo_grid-status--in-progress";
  return `
    <div class='dhx-demo_grid-template'>
      <span>${value}</span>
    </div>`
}

let grid;
// webhooks
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

const pulse = ref(false);

onUnmounted(async ()=> {
  grid.destructor();
});

// css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";



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
            <div  class="" id="grid"></div>
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


