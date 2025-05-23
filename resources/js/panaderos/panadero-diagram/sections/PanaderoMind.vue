<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";
//import '../codebase/index.css?v=6.0.10';
//import '../codebase/diagramWithEditor.css?v=6.0.10';
//import {  } from "@dhx/diagram_6/codebase/diagramWithEditor.js?v=6.0.10";

//export { workers, medicalWorkers, groupData, swimlaneData, mindmapData, networkData, radialData, productData, goalsData, defaultLane };
import { mindmapData, defaultMind, defaultMind2, nofFoundMind } from '../codebase/data.js';

//import '@dhx/diagram/codebase/diagramWithEditor.js?v=6.0.10';
//import '@dhx/diagram/codebase/diagram.css';
//import './codebase/diagram.js';

// Globals
const props = defineProps({
    //ref: Object, ref is a reserved property
    contract: Object,
    set: Object,
    db: Object
});

// globals
const _title="Mind";



const _add = () => {}
const _delete = () => {}

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;

    _state = editor.diagram.data.serialize();

    const _payload = {  "model": "StateDataset",
                      "type": props.set.projectType,
                      "path": _path,
                      "projectId": props.set.project.id,
                      "json" : JSON.stringify(_state),
                      "isActive": 1
                     };
    await props.db.setState(_payload);
  }
}

/// move this section also to store!!!
const _load = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    const _type = props.set.projectType;
    const _projectId = props.set.project.id;

    const _lane = await props.db.getState(_type, _path, _projectId);

    if (_lane){
        const _jsonMind = await JSON.parse(_lane);
        console.log(_jsonMind);
        editor.diagram.data.parse(_jsonMind);
    } else {
        
        console.log("notFound");
        editor.diagram.data.parse(defaultMind);
    }
  }
}

const _clear = async () => {
    editor.diagram.data.removeAll();
}

const _sample1 = async () => {
    editor.diagram.data.parse(mindmapData);
}
const _sample2 = async () => {
    editor.diagram.data.parse(defaultMind);
}
const _sample3 = async () => {
    editor.diagram.data.parse(defaultMind2);
}

const _loadPlan = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    //const _type = props.set.projectType;
    const _type = "resourcePlanning";
    const _projectId = props.set.project.id;
  }
}

let editor, _state;
 
const _data = [
        {
            "id": "1",
            "text": "Initial Start",
            "fill": "#0288D1",
            "fontColor": "#FFFFFF",
            "stroke": "#0288D1",
            "fontWeight": "bold"
        }];


// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('mind');
    // editor.parse(mindmapData);

            const defaults = {
                start: {
                    fill: "#F35A4F",
                    stroke: "#F35A4F",
                    fontColor: "#FFFFFF",
                    strokeWidth: 2,
                },
                circle: {
                    fill: "#F35A4F",
                    stroke: "#F35A4F",
                    fontColor: "#FFFFFF",
                    strokeWidth: 2,
                },
                rectangle: {
                    fill: "#FFFFFF",
                    stroke: "#F35A4F",
                    fontColor: "#4C4C4C",
                    strokeWidth: 2,
                },
            };

            editor = new dhx.DiagramEditor("mindmap", {
                type: "mindmap",
                lineConfig: {
                    lineGap: 30
                },
                defaults: {
                    line: {
                        stroke: "#4d4d4d",
                    }
                }
            });

            editor.diagram.config.defaults = defaults;
            editor.parse(_data);

});

onUnmounted(async ()=> {
  //await set.setProjectType('none');
    editor = null;
});

// Theme Buttons
const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
const _buttonDisabled = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-600 dark:ring-gray-600 ";
const pulse = inject("pulse");

</script>
<template>

<div class="m-0">   
    <div class="grid grid-cols-2">     
        <div class="pl-2 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_clear" type="button" :class="_button">Clear</button>
            <button @click="_sample1" type="button" :class="_button">F1</button>
            <button @click="_sample2" type="button" :class="_button">F2</button>
            <button @click="_sample3" type="button" :class="_button">F3</button>
            <button @click="_loadPlan" type="button" :class="_buttonDisabled">loadPlan</button>
            <button @click="_loadPlan" type="button" :class="_buttonDisabled">loadWork</button>
            <button @click="_loadPlan" type="button" :class="_buttonDisabled">loadBudget</button>
        </div>      
    </div>
    
    <div class="" id="editor"></div>
    <div class="h-screen w-screen" id="mindmap"></div>
</div>

</template>

<!-- custom styles -->
<style>
    .dhx_sample-widget {
        height: calc(100% - 61px);
    }
</style>