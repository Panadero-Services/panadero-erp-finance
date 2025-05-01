<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import { moduleName, moduleVersion, panaderoResourcePlanning, _resourceData, _ppl, _links } from "panadero-resourceplanning";

import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

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
const _title="ResourcePlanning";

const _counter=ref(0);

const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-1 md:p-3 lg:p-4 shadow-sm hover:border-gray-400";
//const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-2 md:p-3 lg:p-4 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400";

let planning = new panaderoResourcePlanning("whateverz");


onMounted(async ()=> {
    await planning.init();
    await planning.setSkin(props.set.dark ? "dark" :  "meadow");
    await planning.load(_resourceData, _links, _ppl);
    await planning.parse();
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
       {{moduleName}}
       {{moduleVersion}}
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
