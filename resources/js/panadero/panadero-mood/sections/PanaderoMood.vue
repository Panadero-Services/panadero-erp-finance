<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';
import { moduleName, moduleVersion, moduleGit, panaderoMood } from "panadero-mood";

// kanban
// moved package back to root folder
// now exists in two folder 1.root 2./panadero-mood/vendor
import { Kanban, Toolbar, defaultEditorShape } from "@dhx/kanban";
import "@dhx/kanban/dist/kanban.css";

//import { getData } from "@dhx/trial-kanban/demos/common/data.js";
import { getData, notFoundMood, defaultMood, defaultMood1, defaultMood2, emptyMoodSet } from "@/panadero/panadero-mood/constants/mood-data.js";

import "@dhx/kanban/assets/demos.css";

// define emits
const emit = defineEmits(['kill', 'wrench']);

// Globals
console.log('test panaderoMood');
console.log(`moduleName: ${moduleName}`);
console.log(`moduleVersion: ${moduleVersion}`);
console.log(`moduleGit: ${moduleGit}`);
// call parameters

const props = defineProps({
    //ref: Object, ref is a reserved property
    contract: Object,
    set: Object,
    db: Object
});

// globals
const _title="Mood";

let board, bar;


const _add = () => {}
const _delete = () => {}

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;

    const _board =  await board.serialize();
    const _payload = {  "model": "StateDataset",
                      "type": props.set.projectType,
                      "path": _path,
                      "projectId": props.set.project.id,
                      "json" : JSON.stringify(_board),
                      "isActive": 1
                     };
    await props.db.setState(_payload);
  }
}

/// move this section also to store!!!
const _load = async () => {
    if(props.set.project.id > 0) {
        const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
        const _type = props.set.projectType;
        const _projectId = props.set.project.id;

        const _board = await props.db.getState(_type, _path, _projectId);

        if (_board){
            const _jsonBoard = await JSON.parse(_board);
            console.log(_jsonBoard);
            board.parse(_jsonBoard);
        } else {
            console.log("notFound");
            board.parse(notFoundMood); 
        }
    }
}

const _clear = async () => {  board.parse(emptyMoodSet); }
const _sample1 = async () => {  board.parse(defaultMood); }
const _sample2 = async () => {  board.parse(defaultMood1); }
const _sample3 = async () => {  board.parse(defaultMood2); }


const _loadPlan = async () => {
  if(props.set.project.id > 0){

    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    //const _type = props.set.projectType;
    const _type = "resourcePlanning";
    const _projectId = props.set.project.id;
    const _board = await props.db.getState(_type, _path, _projectId);
//    console.log(_board);
    const _jsonBoard = await JSON.parse(_board.replace('"data"','"cards"'));
    _jsonBoard.cards.forEach(v => {

        v.progress = v.progress*100;
        if (v.progress<1) v.column = "idle";
            else if (v.progress<10) v.column = "pending";
                else if (v.progress<99) v.column = "started";
                    else v.column = "done";
        
        //v.color = "#33B0B4";
        v.color = "";
        if (v.type=='project') v.color = "#58C3FE";
        if (v.type=='milestone') v.color = "#F1B941";


    console.log(v.start_date);

        v.start_date = v.start_date.substring(3,6)+v.start_date.substring(0,3)+v.start_date.substring(6);// + '-' +v.start_date.substring(0,2)+'-'+ v.start_date.substring(10,4);
        v.end_date = v.end_date.substring(3,6)+v.end_date.substring(0,3)+v.end_date.substring(6);// + '-' +v.start_date.substring(0,2)+'-'+ v.start_date.substring(10,4);

        v.start_date = new Date(v.start_date);
        v.end_date = new Date(v.end_date);
        //return gantt.date.str_to_date("%d-%m-%Y %H:%i")(date);
    console.log(v.start_date);


//        v.start_date = v.start_date.substring(3,6)+v.start_date.substring(0,3)+v.start_date.substring(6);// + '-' +v.start_date.substring(0,2)+'-'+ v.start_date.substring(10,4);
//        v.end_date = v.end_date.substring(3,6)+v.end_date.substring(0,3)+v.end_date.substring(6);// + '-' +v.start_date.substring(0,2)+'-'+ v.start_date.substring(10,4);
//console.log(v.start_date);

        v['label'] = v['text'];
        v.users = [];
        if('owner' in v) v.owner.forEach(x => {v.users.push(parseInt(x.resource_id))});
    });
    console.log(_jsonBoard);
    board.parse(_jsonBoard);
  }
}

 const editorShape = [
        {
            key: "label",
            type: "text",
            label: "Labelz",
            modalSection: "left", // places the control in the left column of the modal editor
        },
        {
            key: "description",
            type: "textarea",
            label: "Description",
            modalSection: "left",
        },
        {
            key: "start_date",
            type: "date",
            label: "Start date",
            format:"%d-%m-%Y %H:%i",
            modalSection: "right", // places the control in the right column of the modal editor
        },
        {
            key: "end_date",
            type: "date",
            label: "End date",
            format:"%d-%m-%Y %H:%i",
            modalSection: "right",
        },
        {
            key: "priority",
            type: "combo",
            label: "Priority",
            modalSection: "right",
        },
        {
            key: "users",
            type: "multiselect",
            label: "Users",
            modalSection: "right",
        },
        {
            key: "color",
            type: "color",
            label: "Color",
            modalSection: "right",
        },
        {
            key: "progress",
            type: "progress",
            label: "Progress",
            modalSection: "right",
        },
        {
            key: "links",
            type: "links",
            label: "Links",
            modalSection: "left",
        },
        {
            key: "comments",
            type: "comments",
            label: "Comments",
            config: {
                placement: "editor",
            },
            modalSection: "left",
        },
    ];



// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
  await props.set.initMM();
  await props.set.initialize();
  await props.set.setProjectType('mood');

  var { columns, cards, cardShape, users } = getData();   

    // override users from team settings
    const _teamPath = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment;
    console.log(_teamPath);
    const _team = await props.db.getState("team", _teamPath, props.set.project.id);
    if(!_team===undefined) users = JSON.parse(_team.replaceAll("text","label"));
    cardShape.users.values = users;

  board = new Kanban("#root", 
    { columns, cards, cardShape, 
      theme: { name: props.set.dark ? "willow-dark" : "willow" }, 
      editorShape,
      editor: {
        placement: "modal", // "sidebar" (default) | "modal"
        autoSave: false 
    },


    });
  bar = new Toolbar("#toolbar", { api: board.api, theme: props.set.dark ? "willow-dark" : "willow", });
  
  await loadEvents();
  // board.selectCard({ id: 3, groupMode: true });
});

const loadEvents = (_theme) => {
  //board.api.intercept("move-card", (_obj) => {console.log('move-card', _obj);});
  //board.api.intercept("add-card", (_obj) => {console.log('add-card', _obj);});
  // forbid moving cards to the column with the "done" ID
  // board.api.intercept("move-card", ({ id, columnId }) => { console.log(id, columnId); if(columnId !== "done" ){ return false; } });
}

onUnmounted(async ()=> {
  //await set.setProjectType('none');
  board.destructor();
  bar.destructor();
});

const changeTheme = (_theme) => {
  board.setTheme({ name: _theme});
  bar.setConfig({ theme: _theme });
}


// Theme Buttons
const _setWillow = () => { props.set.dark=false; changeTheme('willow'); }
const _setMaterial = () => { props.set.dark=false; changeTheme('material');}
const _setWillowDark = () => { props.set.dark=true; changeTheme('willow-dark');}
const _setPanaderos = () => { props.set.dark=false; changeTheme('panaderos');}

const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

const pulse = inject("pulse");

const refreshPage = () => { window.location.reload(); };

</script>
<template>

<div class="m-0">   
    <div class="grid grid-cols-2">     
        <div class="pl-2 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="refreshPage" type="button" :class="_button">Refresh</button>
            <button @click="_setWillow" type="button" :class="_button">Light</button>
            <button @click="_setWillowDark" type="button" :class="_button">Dark</button>
            <button @click="_setMaterial" type="button" :class="_button">Material</button>
            <button @click="_setPanaderos" type="button" :class="_button">Panaderos</button>
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_loadPlan" type="button" :class="_button">loadPlan</button>
            <button @click="_clear" type="button" :class="_button">Clear</button>
            <button @click="_sample1" type="button" :class="_button">F1</button>
            <button @click="_sample2" type="button" :class="_button">F2</button>
            <button @click="_sample3" type="button" :class="_button">F3</button>

        </div>      
        <div class="" id="toolbar"></div>
    </div>
    <div class="grid grid-cols-2">     
        <div id="root" class="col-span-2"></div>
        <div id="whatever" class=""></div>
    </div>
</div>

</template>

<!-- custom styles -->
<style>

    .wx-willow-theme {
        --wx-background: rgb(243 244 246);
        --wx-input-background: #fff;
        --wx-input-border: #111;
    }

    .wx-panaderos-theme {
    --wx-field-width: 100%;
    --wx-theme-name: panaderos;
        --wx-kanban-toolbar-height: 56px;
        --wx-color-font: rgba(5, 5, 5, 0.9);
        --wx-color-secondary-font: rgba(255, 255, 255, 0.5);
        --wx-icon-color: rgba(255, 255, 255, 0.7);
        --wx-icon-color-hover: rgba(255, 255, 255, 0.2);
        --wx-kanban-background: #eaeefe;
        --wx-background: #f9fbe7;
        --wx-background-alt: #666;
        --wx-kanban-content-background: #d8e0ff;
        --wx-border: 0.5px solid #818080;
        --wx-border-medium: 1px solid #818080;
        --wx-kanban-card-border: 1px solid #818080;
        --wx-kanban-column-width: 150px;
        --wx-input-background: #9e9e9e;
        --wx-color-font-disabled: #878585;
    }
</style>