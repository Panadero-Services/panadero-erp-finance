<script setup>
import { computed, onMounted, onUnmounted, ref, inject, watch } from 'vue';
import { moduleName, moduleVersion, panaderoResourcePlanning } from "panadero-resourceplanning";

import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';

import { gantt } from "dhtmlx-gantt";

//import { resourceData, ppl, ppl_vince, links, colors, defaultColor, taskScaleColor, weekendColor, darkWeekendColor } from  "./constants/rm_planning.js";
import { getData } from "@/panaderos/panadero-resourceplanning/constants/planning-data.js";
import "@/panaderos/panadero-resourceplanning/dhtmlxgantt.css";

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
});

// globals
const _title="ResourcePlanning";
const _colorCount=ref(0);
const {resources, resourceData, ppl, links, colors, defaultColor, taskScaleColor, weekendColor, darkWeekendColor } =getData();

const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-1 md:p-3 lg:p-4 shadow-sm hover:border-gray-400";
//const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-2 md:p-3 lg:p-4 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400";
//let planning = new panaderoResourcePlanning("resource_id");

const _init = async () => {
    gantt.init("resource_id");
    gantt.setSkin(props.set.dark ? "dark" : 'meadow');
    gantt.message({ text: "Panadero Resource Planning Module Initialize", expire: 0 });
    gantt.message({ text: "Issue resources from module RentMagic", expire: 0 });
}

const _config = async () => {
    gantt.config.scale_height = 80;
    //gantt.i18n.setLocale("nl");
    gantt.config.date_format = "%d-%m-%Y %H:%i";
   // gantt.config.date_format = "%Y-%m-%d %H:%i";
    gantt.config.scales = [
        { unit: "week", step: 1, format: " Week %w" },
        { unit: "day", step: 1, format: "%d.%m" },
        { unit: "day", step: 1, format: "%D" },
         // {unit: "hour", step: 2, format: "%H"},
    ];

    gantt.config.row_height = 32;
    gantt.config.bar_height = 24;
    gantt.config.min_column_width = 40;
    gantt.config.resource_store = "resource";
    gantt.config.resource_property = "owner";
    gantt.locale.labels.section_resources = "Owners";
    gantt.config.order_branch = true;
    gantt.config.open_tree_initially = true;
}

const _configColumns = async () => {
    gantt.config.columns = [
        { name: "text", tree: true, width: 250, resize: true },
        { name: "start_date", align: "center", width: 100, resize: true },
        //{ name: "end_date", align: "center", width: 100, resize: true},
        {   name: "owner", 
            align: "center", 
            width: 100, 
            label: "Owner", 
            template: function (task) {
                if (task.type == gantt.config.types.project) return ""; 
                var store = gantt.getDatastore(gantt.config.resource_store);    // resource
                var owners = task[gantt.config.resource_property];              // owner 
                if (!owners || !owners.length) return "- -"; 
                if(owners.length == 1) return store.getItem(owners[0].resource_id).text; 
                var result = "";
                owners.forEach(function(owner) {
                    var owner = store.getItem(owner.resource_id);
                    if (!owner) return;
                    result += "<div class='owner-label' title='" + owner.text + "'>" + owner.text.substr(0, 2) + "</div>";
                });
                return result;
            }, 
            resize: true
        },
        { name: "duration", width: 60, align: "center", resize: true },
        { name: "add", width: 44 }
    ];
}

// get number of hours for 
const _getAssignments = (resourceId) => {
    var assignments;
    var store = gantt.getDatastore(gantt.config.resource_store);    //resource
    var resource = store.getItem(resourceId);

    if (resource.$level === 0) {
        assignments = [];
        store.getChildren(resourceId).forEach(function(childId) {
            assignments = assignments.concat(gantt.getResourceAssignments(childId));
        });
   } else if (resource.$level === 1) {
      assignments = gantt.getResourceAssignments(resourceId);
   } else{
      assignments = gantt.getResourceAssignments(resource.$resource_id, resource.$task_id);
   }
//   console.log(assignments);
   return assignments;
}

const _configResourceColumns = async () => {
    var _resource_columns = {
        columns: [
            {
                name: "name", label: "Name", tree:true, template: function (resource) {
                    return resource.text;
                }
            },
            {
                name: "workload", label: "Workload", template: function (resource) {
                    var totalDuration = 0;
                    if (resource.$level == 2) {
                        var assignment = gantt.getResourceAssignments(resource.$resource_id, resource.$task_id)[0];
                        totalDuration = resource.duration * assignment.value;
                    } else {
                        var assignments = _getAssignments(resource.id);
                        assignments.forEach(function (assignment) {
                        var task = gantt.getTask(assignment.task_id);
                        totalDuration += Number(assignment.value) * task.duration;
                    });
                    }
                    return (totalDuration || 0) + "h";
                }
            }
        ]
    };
    return _resource_columns;
}

const _cssMarker = async () => {
    gantt.templates.resource_cell_class = function(start_date, end_date, resource, tasks) {
        var css = [];
        css.push("resource_marker");
        tasks.length <= 1 ? css.push("workday_ok") : css.push("workday_over") ;
        return css.join(" ");
    };
}

const _drawHourCircles = async () => {
    gantt.templates.resource_cell_value = function(start_date, end_date, resource, tasks) {
        var result = 0;
        tasks.forEach(function(item) {
            var assignments = gantt.getResourceAssignments(resource.id, item.id);
            assignments.forEach(function(assignment){
                var task = gantt.getTask(assignment.task_id);
                result += assignment.value * 1;
            });
        });
        if(result % 1){
            result = Math.round(result * 10)/10;
        }
        if (result > 0)
        return "<div>" + result + "</div>";
    };
}

const _whateverRoutine = async () => {
    return '_whateverRoutine?';
}

const _lightBox = async () => {
    gantt.config.lightbox.sections = [
        { name: "description", height: 38, map_to: "text", type: "textarea", focus: true },
        { name: "type", map_to: "type", type: "select", 
            options: [
                { key: "task", label: "task" },
                { key: "project", label: "project" },
                { key: "milestone", label: "milestone" }
            ]},
        { name: "resources", type: "resources", map_to: "owner", options: gantt.serverList("people"), default_value: 4 },
        { name: "time", type: "duration", map_to: "auto"},
        { name: "parent", type: "parent", allow_root: "true", root_label: "No parent", filter: function (id, task) {
                /*  if(task.$level > 1){
                        return false;
                    }else{
                        return true;
                    }*/
                return true;
            }
        }
    ];
}

const _configLayout = async (_resource_columns) => {
    gantt.config.layout = {
        css: "gantt_container",
        rows: [
            {
                cols: [
                    { view: "grid", group:"grids", scrollY: "scrollVer" },
                    { resizer: true, width: 1 },
                    { view: "timeline", scrollX: "scrollHor", scrollY: "scrollVer" },
                    { view: "scrollbar", id: "scrollVer", group:"vertical" }
                ],
                gravity:2
            },
            { resizer: true, width: 1 },
            {
                config: _resource_columns,
                cols: [
                    { view: "resourceGrid", group:"grids", width: 435, scrollY: "resourceVScroll" },
                    { resizer: true, width: 1 },
                    { view: "resourceTimeline", scrollX: "scrollHor", scrollY: "resourceVScroll" },
                    { view: "scrollbar", id: "resourceVScroll", group:"vertical" }
                ],
                gravity: 1
            },
            { view: "scrollbar", id: "scrollHor"}
        ]
    };

    gantt.templates.scale_cell_class = function(date) {
        if(date.getDay()==0||date.getDay()==6) {
           return "weekend";
       }
    };
    gantt.templates.timeline_cell_class = function(task,date) {
        if(date.getDay()==0||date.getDay()==6) { 
            return "weekend" ;
        }
    };
}

onMounted(async ()=> {
    await _config();
    await _configColumns();
    var _resource_columns = await _configResourceColumns();
    await _cssMarker();
    await _drawHourCircles();
    await _lightBox();
    await _configLayout(_resource_columns);

    await props.set.setProjectType('resourcePlanning');


gantt.plugins({
    fullscreen: true
});


    await _init();
})

const _parseResourcesStore = async () => {
    var resourcesStore = gantt.createDatastore({
        name: gantt.config.resource_store,
        type: "treeDatastore",
        initItem: function (item) {
            item.parent = item.parent || gantt.config.root_id;
            item[gantt.config.resource_property] = item.parent;
            item.open = true;
            return item;
        }
    });

    resourcesStore.attachEvent("onParse", function() {
        var people = [];
        resourcesStore.eachItem(function(res) {
            if(!resourcesStore.hasChild(res.id)) {
                var copy = gantt.copy(res);
                copy.key = res.id;
                copy.label = res.text;
                people.push(copy);
            }
        });
        gantt.updateCollection("people", people);
    });

    if (props.set.project.title=="vince") resourcesStore.parse(resources);
    else resourcesStore.parse(ppl);
}



const _parse = async (_data, _links) => {
    gantt.parse({ data: _data, links: _links });
}

onUnmounted(async ()=> {
    gantt.clearAll(); 
    //gantt.destructor();
})

// button Reset
const _reset = async () => {
    await gantt.clearAll();
    gantt.setSkin(props.set.dark ? "dark" : 'meadow');
    await _config();
    await _configColumns();
    await _init();
}

// button 
const _load = async () => {
  if(props.set.project.id > 0) {
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    const _type = props.set.projectType;
    const _projectId = props.set.project.id;
    //console.log(_path);
    let _resourcePlanning = await props.db.getState(_type, _path, _projectId);
    let _resPlan = await JSON.parse(_resourcePlanning);
    //console.log(_resPlan.data, _resPlan.links);
    
    await _parseResourcesStore();
    await _parse(_resPlan.data, _resPlan.links);
//    await _parse(resourceData, links);
  }
}

// button Save
/// move this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;

    let _resourcePlanning =  await gantt.serialize();
    let _payload = {  "model": "StateDataset",
                      "type": props.set.projectType,
                      "path": _path,
                      "projectId": props.set.project.id,
                      "json" : JSON.stringify(_resourcePlanning),
                      "isActive": 1
                     };
    await props.db.setState(_payload);
    gantt.message({ text: "Saved Environment", expire: 0 });
  }
}

// button Shuffle
const _shuffle = async () => {
    //shuffleArray(resourceData);
    await _config();
    await _configColumns();
    await _init();
}

// button FullScreen
const _fullScreen = async () => {
   if (!gantt.getState().fullscreen) {
        // expanding the gantt to full screen
        await gantt.expand();
    }
    else {
        // collapsing the gantt to the normal mode
        await gantt.collapse();
    }
}

const shuffleArray = async (array) => {
    for (let i = array.length - 1; i >= 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

// button Color
const color = ref(defaultColor);
const _changeColor = async () => {
    _colorCount.value++;
    if (_colorCount.value >= colors.length) _colorCount.value=0;
    color.value = colors[_colorCount.value];
}

const updateInfo = async () => {
    var state = gantt.getState();
    var tasks = gantt.getTaskByTime(state.min_date, state.max_date);
    var types = gantt.config.types;
    var result = {};
    var html = "";
    var active = false;

    // get available types
    result[types.task] = 0;
    result[types.project] = 0;
    result[types.milestone] = 0;

    // sort tasks by type
    for (var i = 0, l = tasks.length; i < l; i++) {
        if (tasks[i].type && result[tasks[i].type] != "undefined")
            result[tasks[i].type] += 1;
        else
            result[types.task] += 1;
    }
    // render list items for each type
    for (var j in result) {
        if (j == types.task)
            active = true;
        else
            active = false;
        html += getListItemHTML(j, result[j], active);
    }

    document.getElementById("gantt_info").innerHTML = html;
};

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


// CSS
const _button = "my-1 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
//const pulse = inject("pulse");

</script>

<template>
<div class="m-0">   
    <div class="grid grid-cols-4">     
        <div class="flex pl-2 col-span-3 mb-1 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="_reset" type="button" :class="_button">Reset</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_parse" type="button" :class="_button">Parse</button>
            <button @click="_shuffle" type="button" :class="_button">Shuffle</button>
            <button @click="_changeColor" type="button" :class="_button">Color</button>
            <button @click="_fullScreen" type="button" :class="_button">FullScreen</button>
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
    --background-color: #666;
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
    background-color: v-bind(taskScaleColor);
    font-size: 10px;
    border-bottom: 1px solid #cecece;
}

.dark .gantt_task_scale {
    background-color: v-bind(darkWeekendColor);
}

.gantt_grid_scale .gantt_grid_head_cell {
    background-color: v-bind(taskScaleColor);
    font-size: 12px;
    border-bottom: 1px solid #cecece;
}

.dark .gantt_grid_scale .gantt_grid_head_cell {
    background-color: v-bind(darkWeekendColor);
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

  .weekend{ background: v-bind(weekendColor) !important;}
  .dark .weekend{ background: v-bind(darkWeekendColor) !important;}

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
      font-size: 10px;
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
