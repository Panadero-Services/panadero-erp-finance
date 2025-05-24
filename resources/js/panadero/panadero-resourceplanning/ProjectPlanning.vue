<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
//import { moduleName, moduleVersion, panaderoResourcePlanning } from "panadero-resourceplanning";

import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';
import "@/panadero/panadero-resourceplanning/dhtmlxgantt.css";

import { gantt } from "dhtmlx-gantt";
// define emits
const emit = defineEmits(['kill', 'wrench']);

// Globals
//const APP_NAME = process.env.APP_NAME;
console.log('test panaderoResourcePlanning');
//console.log(`moduleName: ${moduleName}`);
//console.log(`moduleVersion: ${moduleVersion}`);
//console.log(`moduleGit: ${moduleGit}`);

// call parameters
const props = defineProps({
    contract: Object,
    set: Object,
    db: Object,
    pulse: Boolean
});

// globals
const _title="ProjectPlanning";

const _counter=ref(0);

var _resourceData = [
      {id: 1, text: "Infodatek Introductie2", type: "project", progress: 0.2, open: true, start_date: "30-10-2024 00:00", duration: 16, end_date: "10-11-2024 00:00", parent: 0},
      {id: 2, text: "Presentatie", type: "project", progress: 0.2, open: true, start_date: "30-10-2024 00:00", duration: 3, end_date: "03-11-2024 00:00", parent: 1},
      {id: 3, text: "IntroA", start_date: "30-10-2024 00:00", duration: 2, parent: 2, progress: 0.2, open: true, end_date: "31-10-2024 00:00", "owner": [{ "resource_id": "5", "value": 2 }] },
      {id: 4, text: "Decide ", start_date: "31-10-2024 00:00", type: "milestone", parent: 2, progress: 0, open: true, end_date: "31-10-2024 00:00", duration: 2},
      {id: 5, text: "IntroB", start_date: "01-11-2024 00:00", duration: 1, parent: 2, progress: 0, open: true, end_date: "02-11-2024 00:00"},
      {id: 6, text: "CodeBase Study", type: "project", start_date: "08-11-2024 00:00", duration: 8, parent: 1, progress: 0, open: true, end_date: "19-11-2024 00:00"},
      {id: 7, text: "DB Scheme", start_date: "04-11-2024 00:00", duration: 2, progress: 0, parent: 6, open: true, end_date: "06-11-2024 00:00"},
      {id: 8, text: "Models", start_date: "07-11-2024 00:00", duration: 2, parent: 6, progress: 0, open: true, end_date: "09-11-2024 00:00", "owner": [{ "resource_id": "5", "value": 2 }]},
      {id: 9, text: "App lLayout", start_date: "11-11-2024 00:00", duration: 2, parent: 6, progress: 0.1, open: true, end_date: "13-11-2024 00:00", "owner": [{ "resource_id": 4, "value": 4 }]},
      {id: 10, text: "App Logic", start_date: "14-11-2024 00:00", duration: 2, parent: 6, progress: 0, open: true, end_date: "16-11-2024 00:00", "owner": [{ "resource_id": 4, "value": 4 }]},
      {id: 11, text: "Frontend", start_date: "18-11-2024 00:00", duration: 2, parent: 6, progress: 0.1, open: true, end_date: "20-11-2024 00:00", "owner": [{ "resource_id": 4, "value": 4 }]},
      {id: 12, text: "Backend", start_date: "21-11-2024 00:00", duration: 2, parent: 6, progress: 0, open: true, end_date: "23-11-2024 00:00", "owner": [{ "resource_id": 4, "value": 4 }]},
      {id: 13, text: "Correctie Programn", type: "project", start_date: "01-11-2024 00:00", duration: 30, parent: 0, progress: 0.1, open: false, end_date: "30-11-2024 00:00"},
      {id: 14, text: "Item #6 Beta Release",  start_date: "28-10-2024 00:00", duration: 5, parent: 13, progress: 0.5, open: true, end_date: "02-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 15, text: "Item #7 Integrate System",  start_date: "04-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "09-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 16, text: "Item #8 Interface setup",  start_date: "11-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "16-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 17, text: "Item #9 Structure redesign",  start_date: "18-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "23-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 18, text: "Item #10 Structure Modules",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 19, text: "Item #11 Structure Components",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 1, "value": 8 }]},
      {id: 20, text: "Deployment 1.5", start_date: "27-11-2024 00:00", type: "milestone", parent: 13, progress: 0, open: true, end_date: "27-11-2024 00:00", duration: 1},
      {id: 21, text: "Item #12 Deployments",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 2, "value": 8 }]},
      {id: 22, text: "Item #13 Rebalance datasources",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 8 }]},
      {id: 23, text: "Item #14 Training",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 4, "value": 8 }]},
      {id: 24, text: "Item #15 Documentation",  start_date: "25-11-2024 00:00", duration: 5, parent: 13, progress: 0, open: true, end_date: "30-11-2024 00:00", "owner": [{ "resource_id": 5, "value": 8 }]},
      {id: 25, text: "Final Deployment 2.0", start_date: "27-11-2024 00:00", type: "milestone", parent: 13, progress: 0, open: true, end_date: "27-11-2024 00:00", duration: 1},
      {id: 31, text: "Custom Features", type: "project", start_date: "03-11-2024 00:00", duration: 5, parent: 0, progress: 0.2, open: false, end_date: "08-11-2024 00:00"},
      {id: 32, text: "KlantA database", start_date: "01-11-2024 00:00", duration: 7, parent: 31, progress: 0, open: true, end_date: "08-11-2024 00:00"},
      {id: 33, text: "KlantB Userbase", start_date: "04-11-2024 00:00", duration: 7, parent: 31, progress: 0, open: true, end_date: "11-11-2024 00:00"},
      {id: 34, text: "Interface setup", start_date: "08-11-2024 00:00", duration: 7, parent: 31, progress: 0, open: true, end_date: "15-11-2024 00:00"},
      {id: 35, text: "Documentation creation", start_date: "10-11-2024 00:00", duration: 7, parent: 31, progress: 0, open: true, end_date: "17-11-2024 00:00"},
      {id: 36, text: "Release v1.0", start_date: "19-11-2024 00:00", type: "milestone", parent: 35, progress: 0, open: true, end_date: "19-11-2024 00:00", duration: 1}
    ];

var _ppl = [
 {id: 1, text: "R&D", parent:null},
 {id: 2, text: "Development", parent:null},
 {id: 3, text: "Mike", parent:1, unit: "hours/day" },
 {id: 4, text: "Lieuwe", parent:2, unit: "hours/day" },
 {id: 5, text: "Anna", parent:2, unit: "hours/day" },
 {id: 6, text: "Floe", parent:2, unit: "hours/day" },
 {id: 6, text: "Stuart", parent:2, unit: "hours/day" },
 {id: 6, text: "Phill", parent:2, unit: "hours/day" },
 {id: 7, text: "Unassigned", parent:2},
 ];

var _links = [
   {id: "1", source: "1", target: "2", type: "1"},
   {id: "2", source: "2", target: "3", type: "0"},
   {id: "3", source: "3", target: "4", type: "0"},
   {id: "4", source: "4", target: "5", type: "0"},
   {id: "5", source: "2", target: "6", type: "0"},
   {id: "6", source: "5", target: "7", type: "0"},
   {id: "7", source: "7", target: "8", type: "0"},
   {id: "8", source: "8", target: "9", type: "0"},
   {id: "9", source: "9", target: "10", type: "0"},
   {id: "10", source: "10", target: "11", type: "0"},
   {id: "11", source: "11", target: "12", type: "0"},
   {id: "12", source: "14", target: "15", type: "0"},
   {id: "13", source: "15", target: "16", type: "0"},
   {id: "14", source: "16", target: "17", type: "0"},
   {id: "15", source: "17", target: "18", type: "0"},
   {id: "16", source: "18", target: "19", type: "0"},
   {id: "17", source: "19", target: "20", type: "0"},
   {id: "18", source: "20", target: "21", type: "0"},
   {id: "19", source: "21", target: "22", type: "0"},
   {id: "20", source: "22", target: "23", type: "0"},
   {id: "20", source: "23", target: "24", type: "0"},
   {id: "20", source: "24", target: "25", type: "0"},
   {id: "21", source: "32", target: "33", type: "0"},
   {id: "22", source: "33", target: "34", type: "0"},
   {id: "23", source: "34", target: "35", type: "0"},
   {id: "24", source: "35", target: "36", type: "0"},
   ];

const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-1 md:p-3 lg:p-4 shadow-sm hover:border-gray-400";
//const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-2 md:p-3 lg:p-4 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400";

//let planning = new panaderoResourcePlanning("whateverz");


onMounted(async ()=> {




    gantt.plugins({
            quick_info: true
        });

        gantt.init("whateverz");

        gantt.parse({
            tasks: [
                { id: 11, text: "Project #1", type: "project", progress: 0.6, open: true },
                { id: 12, text: "ProjectTask #1", start_date: "03-04-2023", duration: 5, parent: 11, progress: 1, open: true },
                { id: 13, text: "Task #2", start_date: "03-04-2023", type: "project", parent: 11, progress: 0.5, open: true },
                { id: 14, text: "Task #3", start_date: "02-04-2023", duration: 6, parent: 11, progress: 0.8, open: true },
                { id: 15, text: "Task #4", type: "project", parent: 11, progress: 0.2, open: true },
                { id: 16, text: "Final milestone", start_date: "15-04-2023", type: "milestone", parent: 11, progress: 0, open: true },

                { id: 17, text: "Task #2.1", start_date: "03-04-2023", duration: 2, parent: 13, progress: 1, open: true },
                { id: 18, text: "Task #2.2", start_date: "06-04-2023", duration: 3, parent: 13, progress: 0.8, open: true },
                { id: 19, text: "Task #2.3", start_date: "10-04-2023", duration: 4, parent: 13, progress: 0.2, open: true },
                { id: 20, text: "Task #2.4", start_date: "10-04-2023", duration: 4, parent: 13, progress: 0, open: true },
                { id: 21, text: "Task #4.1", start_date: "03-04-2023", duration: 4, parent: 15, progress: 0.5, open: true },
                { id: 22, text: "Task #4.2", start_date: "03-04-2023", duration: 4, parent: 15, progress: 0.1, open: true },
                { id: 23, text: "Mediate milestone", start_date: "14-04-2023", type: "milestone", parent: 15, progress: 0, open: true }
            ],
            links: [
                { id: 10, source: 11, target: 12, type: 1 },
                { id: 11, source: 11, target: 13, type: 1 },
                { id: 12, source: 11, target: 14, type: 1 },
                { id: 13, source: 11, target: 15, type: 1 },
                { id: 14, source: 23, target: 16, type: 0 },
                { id: 15, source: 13, target: 17, type: 1 },
                { id: 16, source: 17, target: 18, type: 0 },
                { id: 17, source: 18, target: 19, type: 0 },
                { id: 18, source: 19, target: 20, type: 0 },
                { id: 19, source: 15, target: 21, type: 2 },
                { id: 20, source: 15, target: 22, type: 2 },
                { id: 21, source: 15, target: 23, type: 0 }
            ]
        });

;
})

onUnmounted(async ()=> {
    gantt.clearAll(); 

    //gantt.destructor();
})




function shuffleArray(array) {
    for (let i = array.length - 1; i >= 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

const _shuffle = async () => {
   shuffleArray(_resourceData);
}

const _init = async () => {
   await planning.init();
}

const _load = async () => {
   await planning.load(_resourceData, _links, _ppl);
}

const _parse = async () => {
   await planning.parse();
}


const _changeColor = async () => {
    _counter.value++;
    if (_counter.value >= colors.length) _counter.value=0;
    color.value = colors[_counter.value];
}



const colors = [['#00695c','#26a69a','#330099','#6633FF', '#ede'],
['#F9C241','#FCDF9C','#3CB2C9','#A7E1EC', '#121'],
['#37AFE1','#4CC9FE','#FFB22C','#FFDE4D', '#121'],
['#9B7EBD','#D4BEE4','#A5B68D','#C1CFA1', '#121'],
['#0D92F4','#F95454','#0A6847','#F3CA52', '#000'],
['#344C64','#577B8D','#3C3D37','#697565', '#ede']];

//let color = ref(['#00695c','#26a69a','#330099','#6633FF', '#ede']);
//let color = ref(['#F9C241','#FCDF9C','#3CB2C9','#A7E1EC', '#121']);
let color = ref(['#37AFE1','#4CC9FE','#FFB22C','#FFDE4D', '#121']);
//let color = ref(['#9B7EBD','#D4BEE4','#A5B68D','#C1CFA1', '#121']);
//let color = ref(['#344C64','#577B8D','#3C3D37','#697565', '#ede']);

let _task_scale="#FCFCFC";
let _weekend="#F2F2F2";
//let _dark_weekend="#00001A";
let _dark_weekend="#000000";

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    let _planning =  await planning.serialize();
    let _payload = {  "model": "StateDataset",
                      "type": props.set.projectType,
                      "path": props.set.project.environment+"."+props.set.project.category,
                      "projectId": props.set.project.id,
                      "json" : JSON.stringify(_planning),
                      "isActive": 1
                     };
    await props.db.setState(_payload);
  }
}

defineExpose({
  _load, _save
});

</script>

<template>
    <div class="mx-0.5">
   <div class="grid grid-cols-12 text-xxs sm:text-xs md-text-sm dark:text-gray-200">
      <div @click="emit('switch')" class="col-span-2">
     </div>
     <div @click="_changeColor">COLOR_{{_counter}}</div>
     <div @click="_init">INIT</div>
     <div @click="_load">LOAD</div>
     <div @click="_shuffle">SHUFFLE</div>
     <div @click="_parse">PARSE</div>
   </div>
   <div id="whateverz" class="h-screen max-w-9xl bg-black"></div>

    <div class=" mt-2 gap-2 lg:mt-3 lg:gap-3 grid grid-cols-12" :id="_counter"> 
    </div>

    </div>
</template>
<style>


:root {
    --background-color: #ffffff;
    --text-color: #000000;
    --primary-color: #d2e8de;
}
[data-theme="dark"] {
    --background-color: #090808;
    --text-color: #efefef;
    --primary-color: #374241;
}
[data-theme="grape"] {
    --background-color: #9919c4;
    --text-color: #8c64dc;
    --primary-color: #490e81;
}
[data-theme="lemon"] {
    --background-color: #be9523;
    --text-color: #e5df6c;
    --primary-color: #ea9e2c;
}

[data-theme="blueberry"] {
    --background-color: #0d4fde;
    --text-color: #0ec3e3;
    --primary-color: #0c408d;
}

.gantt_task_scale{
    background-color: v-bind(_task_scale);
    font-size: 10px;
    border-bottom: 1px solid #cecece;
}

.dark .gantt_task_scale {
    background-color: v-bind(_dark_weekend);
}

.gantt_grid_scale .gantt_grid_head_cell {
    background-color: v-bind(_task_scale);
    font-size: 12px;
    border-bottom: 1px solid #cecece;
}

.dark .gantt_grid_scale .gantt_grid_head_cell {
    background-color: v-bind(_dark_weekend);
}

.gantt_grid_data{
   margin: 2px;
    font-size: 11px;
    border-bottom: 1px solid #cecece;
}

.gantt_scale {
    color: #aaa;
    font-size: 12px;
    border-bottom: 1px solid #cecece;
}

  /* background color of task bars*/
  .gantt_task_line {
    background:  v-bind(color[3]);  ;
    border: 1px solid #2898b0;
  }

  /* text color */
  .gantt_task_line .gantt_task_content {
    color: v-bind(color[4]);
    font-size: 11px;
  }

  /* progress fill */
  .gantt_task_progress {
    background-color:  v-bind(color[2]);
  }

  /* project */
  /* background color of project bars*/
  .gantt_task_line.gantt_bar_project {
    background-color: v-bind(color[1]);
    border: 1px solid #3c9445;
    font-size: 11px;
  }

  /* progress of project bars */
  .gantt_task_line.gantt_bar_project .gantt_task_progress {
    background-color:  v-bind(color[0]);
  }

  .weekend{ background: v-bind(_weekend) !important;}
  .dark .weekend{ background: v-bind(_dark_weekend) !important;}

  .gantt_milestone {
    background: #FFCC33 }

    .resource_marker{
      text-align: center;
    }
    .resource_marker div{
      font-size: 10px;
      width: 20px;
      height: 20px;
      border-radius: 12px;
      color: #111;
      margin: 3px;
      display: inline-flex;
      justify-content: center;
      align-items: center;
    }
    .resource_marker.workday_ok div {
      background: var(--dhx-gantt-base-colors-success);
    }

    .resource_marker.workday_over div{
      background: var(--dhx-gantt-base-colors-error);
    }

    .owner-label {
      width: 20px;
      height: 20px;      
      font-size: 12px;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      border: 1px solid #cccccc;
      border-radius: 25px;
      background: #e6e6e6;
      color: #6f6f6f;
      margin: 0 3px;
      font-weight: bold;
    }

</style>
