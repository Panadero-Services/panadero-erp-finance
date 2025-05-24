<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";

import '@dhx/spreadsheet/samples/common/index.css?v=5.2.0';
import '@dhx/spreadsheet/codebase/spreadsheet.css?v=5.2.0';
import '@dhx/spreadsheet/codebase/spreadsheet.js?v=5.2.0';
import { dataset, short_dataset, notFoundSheet, defaultProject } from '@/panadero/panadero-spreadsheet/constants/data.js';


//defaultSpreadsheet1

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
const _title="Fund";

const _add = () => {}
const _delete = () => {}

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;

    _state = spreadsheet.serialize();

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

    const _fund = await props.db.getState(_type, _path, _projectId);

    if (_fund){
        const _jsonData = await JSON.parse(_fund);
        console.log(_jsonData);
      spreadsheet.parse(_jsonData);
    } else {
        console.log("notFound");
        spreadsheet.parse(notFoundSheet);
    }
  }
}

const _clear = async () => {
    spreadsheet.clear();
}

const _menu = async () => {
  spreadsheet.menu = true;
}

const _sample1 = async () => { spreadsheet.parse(dataset); }
const _sample2 = async () => { spreadsheet.parse(short_dataset); }
const _sample3 = async () => { spreadsheet.parse(defaultProject); }

const _loadPlan = async () => {
  if(props.set.project.id > 0){

    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    //const _type = props.set.projectType;
    const _type = "fund";
    const _projectId = props.set.project.id;
  }
}

let spreadsheet, _state;
 
// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('fund');
    // editor.parse(mindmapData);

       spreadsheet = new dhx.Spreadsheet("spreadsheet", {
        menu: true,
        formats: [
          { name: "U.S. Dollar", id: "currency", mask: "$#,##0.00" },
          { name: "Euro", id: "euro", mask: "[$€]#,##0.00" },
          { name: "Swiss franc", id: "franc", mask: "[$CHF]#,##0.00" }
        ]
      });
      spreadsheet.parse([]);

});


onUnmounted(async ()=> {
  //await set.setProjectType('none');
  //await spreadsheet.destructor();
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
            <!-- custom buttons
              <button @click="_menu" type="button" :class="_button">Menu</button>
              <button @click="_loadPlan" type="button" :class="_button">loadPlan</button>
             -->
        </div>      
    </div>
    
    <div class="h-screen w-screen">
      <div class="h-4/5 w-full" id="spreadsheet"></div>
    </div>
</div>

</template>

<!-- custom styles -->
<style>
    .dhx_sample-container__widget {
        max-width: 100%;
        height: calc(100% - 61px);
      }
</style>