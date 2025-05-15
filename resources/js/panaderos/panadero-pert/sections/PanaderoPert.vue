<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";

//import '@dhx/diagram/codebase/diagramWithEditor.css?v=6.0.10';
//import '@dhx/diagram/samples/common/index.css?v=6.0.10';

//import '@dhx/diagram/codebase/diagramWithEditor.js?v=6.0.10';
//import '@dhx/diagram/samples/common/data.js?v=6.0.10';

//import '@dhx/diagram/codebase/diagram.css';

//import './codebase/diagram.js';

import { DiagramEditor }  from  '@dhx/diagram/codebase/diagram.js';

// Globals
const props = defineProps({
    //ref: Object, ref is a reserved property
    contract: Object,
    set: Object,
    db: Object
});

// globals
const _title="Pert";


const _add = () => {}
const _delete = () => {}

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;

  }
}

/// move this section also to store!!!
const _load = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    const _type = props.set.projectType;
    const _projectId = props.set.project.id;
  }
}

const _loadGantt = async () => {
  if(props.set.project.id > 0){

    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    //const _type = props.set.projectType;
    const _type = "resourcePlanning";
    const _projectId = props.set.project.id;
  }
}


//        const editor = new dhx.DiagramEditor("editor", {
//            type: "mindmap",
//        });

 
// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('mood');
    // editor.parse(mindmapData);
});

onUnmounted(async ()=> {
  //await set.setProjectType('none');
});

// Theme Buttons
const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
const pulse = inject("pulse");

</script>
<template>

<div class="m-0">   
    <div class="grid grid-cols-2">     
        <div class="pl-2 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_loadGantt" type="button" :class="_button">loadGantt</button>
            <button @click="_load" type="button" :class="_button">{{pulse}}</button>
        </div>      
        <div class="" id="toolbar"></div>
    </div>

<div id="diagram_container"></div>

</div>

</template>

<!-- custom styles -->
<style>
    .dhx_sample-widget {
        height: calc(100% - 61px);
    }
</style>