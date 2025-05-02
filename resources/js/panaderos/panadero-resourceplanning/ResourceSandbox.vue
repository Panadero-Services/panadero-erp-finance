<script setup>
import {computed, onMounted, onUnmounted, ref, inject, watch} from 'vue';
import { moduleName, moduleVersion, panaderoResourcePlanning, _resourceData, _ppl, _links } from "panadero-resourceplanning";

import { getData } from "@/panaderos/panadero-resourceplanning/constants/planning-data.js";
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

const {resources, resourceData, links, colors, defaultColor, taskScaleColor, weekendColor, darkWeekendColor } =getData();

// define emits
const emit = defineEmits(['kill', 'wrench']);

// Globals
//const APP_NAME = process.env.APP_NAME;
//console.log(`moduleName: ${moduleName}`);

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

let planning = new panaderoResourcePlanning("resource_id");

onMounted(async ()=> {
    await planning.config();
    await planning.configColumns();
    var _resource_columns = await planning.configResourceColumns();
    await planning.cssMarker();
    await planning.drawHourCircles();
    await planning.lightBox();
    await planning.configLayout(_resource_columns);

    await props.set.setProjectType('resourcePlanning');

    await planning.fullScreenPlugin();
    await planning.init(props.set.dark);

    //await planning.load(_resourceData, _ppl, _links);
})


const _load = async () => {
  if(props.set.project.id > 0) {
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    const _type = props.set.projectType;
    const _projectId = props.set.project.id;
    //console.log(_path);
    let _resourcePlanning = await props.db.getState(_type, _path, _projectId);
    let _resPlan = await JSON.parse(_resourcePlanning);
    //console.log(_resPlan.data, _resPlan.links);

    const _teamPath = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment;
    const _team = await props.db.getState("team", _teamPath, props.set.project.id);
    console.log('_team');
    console.log(_team);

    const _resources = _team==null ? resources : JSON.parse(_team);

    await planning.parseResourcesStore(_resources);
    await planning.parse(_resPlan.data, _resPlan.links);
    }
}

const _save = async () => {
}


// button Reset
const _reset = async () => {
    await planning.reset(props.set.dark);

}

// button Shuffle
const _shuffle = async () => {
    //shuffleArray(resourceData);
    await planning.config();
    await planning.configColumns();
    await planning.init(props.set.dark);
}

const shuffleArray = async (array) => {
    for (let i = array.length - 1; i >= 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

// button FullScreen
const _fullScreen = async () => {
    await planning.fullscreen();
}

// button Color
const color = ref(defaultColor);
const _changeColor = async () => {
    _colorCount.value++;
    if (_colorCount.value >= colors.length) _colorCount.value=0;
    color.value = colors[_colorCount.value];
}


defineExpose({
  _load, _save
});

const show = async () => {
    console.log('show');

    dhx.alert({
        header: "i3 Alert Box",
        text: "With the help of i3 components you are able to create versatile user interfaces for web apps of any kind. Modern technologies and design make our library a great tool for developers working on the most complex projects. And here i3Message represents a small but indispensable toolkit of helpers for initializing all types of messages: notifications, alert and confirmation boxes, and tooltips. Try out the abilities of dhtmlxMessage in our interactive demos and samples.",
        buttonsAlignment: "center",
        buttons: ["ok"],
    });
}

// pulseObject .. reload... 
const _pulse = inject('pulse')
const _pulseCount = ref(0);

watch(_pulse, (_new, _old) => {
    if(_pulseCount.value++ == 120) {
        _doRefresh();
    };
});

const _doRefresh = async () => {
    _pulseCount.value=0;
    console.log("_doRefresh");
    //await _reset();
    await _load();
}

const _button = "my-1 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

</script>

<template>
<div class="m-0">   
    <div class="grid grid-cols-4">     
        <div class="flex pl-2 col-span-3 mb-1 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="_reset" type="button" :class="_button">Reset</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_shuffle" type="button" :class="_button">Shuffle</button>
            <button @click="_changeColor" type="button" :class="_button">Color</button>
            <button @click="_fullScreen" type="button" :class="_button">FullScreen</button>
            <button @click="_year" type="button" :class="_button">Year</button>
            <button @click="_quarter" type="button" :class="_button">quarter</button>
            <button @click="_month" type="button" :class="_button">Month</button>
            <button @click="_week" type="button" :class="_button">Week</button>
            <button @click="_day" type="button" :class="_button">Day</button>
        </div>    
        <div class="items-end text-right mr-6" id="toolbar">
            <div  class="col-span-1 text-xs mt-4 ">
                <span>{{moduleName}}</span>
                <span class="ml-2" :class="_pulse? 'text-green-500' : ''">{{moduleVersion}}</span>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2" :class="set.layout.sidebar ? 'pr-48' : 'pr-4'">     
        <div id="resource_id" class="col-span-2 h-screen"></div>
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
