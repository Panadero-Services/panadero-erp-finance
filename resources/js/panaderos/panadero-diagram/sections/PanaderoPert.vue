<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';
//import DiagramLayout from './DiagramLayout.vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";
//import '../codebase/diagramWithEditor.js?v=6.0.10';

//import "@dhx/diagram_6/codebase/diagramWithEditor.js?v=6.0.10";
//import "@dhx/diagram_6/codebase/diagram.css?v=6.0.10";
//import "@dhx/diagram_6/codebase/diagramWithEditor.css?v=6.0.10";




import { notFoundPert, defaultPert, productData, networkData } from '../codebase/data.js';

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
const _title="Pert";

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
        editor.diagram.data.parse(notFoundPert);
    }
  }
}

const _clear = async () => { editor.diagram.data.removeAll(); }

const _sample1 = async () => { editor.diagram.data.parse(productData); }
const _sample2 = async () => { editor.diagram.data.parse(networkData); }
const _sample3 = async () => { editor.diagram.data.parse(defaultPert); }

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


    function dateToString(date) {
        if (!(date instanceof Date)) return;
        return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()}`
    }

    function stringToDate(string) {
        if (typeof string !== "string") return;
        const [day, month, year] = string.split(".");
        return new Date(year, month - 1, day);
    }

    function getDiffDays(firstDate, secondDate) {
        const DAY = 1000 * 60 * 60 * 24;
        const firstDateObj = stringToDate(firstDate);
        const secondDateObj = stringToDate(secondDate);
        const diffTime = secondDateObj.getTime() - firstDateObj.getTime();
        return Math.floor(diffTime / DAY);
    }




// webhooks
onMounted(async ()=> {

//    console.log(navigator.language);
console.log(dhx);

    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('pert');
    // editor.parse(mindmapData);
    editor = new dhx.DiagramEditor("editor", {
        type: "default",
        defaults: {
            line: { stroke: "#4d4d4d" }
        },
        view: {
            shapebar: {
                sections: {
                    "Pert shapes": [
                        { type: "pert", stage: "analysis", task: "Analysis", status: "T1." },
                        { type: "pert", stage: "design", task: "Design", status: "T2." },
                        { type: "pert", stage: "development", task: "Development", status: "T3." },
                        { type: "pert", stage: "testing", task: "Testing", status: "T4." },
                        { type: "pert", stage: "launch", task: "Launch", status: "T5." },
                    ],
                    "Flow shapes": [{ flowShapes: true }],
                },
            },
            editbar: {
                properties: {
                    pert: [
                        { type: "arrange", $properties: { angle: { hidden: true } }},
                        { type: "details"},
                    ],
                    $swimlane: [
                        { type: "gridStep" },
                    ]
                },
                controls: {
                    details: {
                        type: "fieldset",
                        label: "Task details",
                        rows: [
                            { type: "checkbox", text: "Critical", key: "critical" },
                            { type: "input", key: "status", label: "Status" },
                            { type: "textarea", key: "task", label: "Description" },
                            {
                                align: "between",
                                cols: [
                                    { type: "datepicker", key: "start_date", dateFormat:"%d.%m.%Y", label: "Date start", width: "48%" },
                                    { type: "datepicker", key: "end_date", dateFormat:"%d.%m.%Y", label: "Date end", width: "48%" },
                                ],
                            },
                            {
                                type: "combo",
                                key: "stage",
                                label: "Stage",
                                placeholder: "Select stage",
                                options: [
                                    { id: "analysis", value: "Analysis" },
                                    { id: "design", value: "Design" },
                                    { id: "development", value: "Development" },
                                    { id: "testing", value: "Testing" },
                                    { id: "launch", value: "Launch" },
                                ]
                            },
                        ],
                    },
                }
            }
        },
    });
    editor.diagram.addShape("pert", { 
        defaults: {
            width: 200,
            height: 97,
            responsible: "Responsible",
            start_date: dateToString(new Date),
            end_date: dateToString(new Date(Date.now() + 100000000)),
            preview: { scale: 0.55 },
        },
        template: ({ start_date, end_date, status, task, stage, critical }) => {
            const duration = getDiffDays(start_date, end_date);
            const criticalCss = critical && "dhx_diagram_template_c--critical" || "";
            return `
                <div class="dhx_diagram_template_c dhx_diagram_template_c__stage--${stage} ${criticalCss}" >
                    <div class="dhx_diagram_template_c__header">${task}</div>
                    <div class="dhx_diagram_template_c__content">
                        <div class="dhx_diagram_template_c__row">
                            <div class="dhx_diagram_template_c__text">${status}</div>
                            <div class="dhx_diagram_template_c__text">${duration} days</div>
                        </div>
                        <div class="dhx_diagram_template_c__row">
                            <div class="dhx_diagram_template_c__text">${start_date}</div>
                            <div class="dhx_diagram_template_c__text">${end_date}</div>
                        </div>
                    </div>
                </div>
            `;
        },
    });

    editor.parse(defaultPert);

});

onUnmounted(async ()=> {
  //await set.setProjectType('none');
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
    
        <div class="h-screen w-screen" id="editor"></div>
</div>

</template>

<!-- custom styles -->
<style>

    .dhx_diagram_template_c {
        --dhx-template-c-header-background: var(--dhx-background-primary);
        --dhx-template-c-header-color: var(--dhx-font-color-contrast);

        font-family: var(--dhx-font-family);
        font-size: var(--dhx-font-size-normal);
        font-weight: var(--dhx-font-weight-regular);
        line-height: var(--dhx-line-height-large);
        color: var(--dhx-font-color-primary);

        height: 100%;
        width: 100%;

        background-color: var(--dhx-background-primary);
        outline: var(--dhx-border);
        outline-offset: -1px;
        border-radius: 4px;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        overflow: hidden;
    }
    .dhx_diagram_template_c--critical {
        outline: 1px solid var(--dhx-color-danger);
    }
    .dhx_diagram_template_c__stage--analysis {
        --dhx-template-c-header-background: #3CC97A;
    }
    .dhx_diagram_template_c__stage--analysis:hover {
        box-shadow: 0px 2px 3px 0px #3CC97A26;
    }
    .dhx_diagram_template_c__stage--design {
        --dhx-template-c-header-background: #9B60F8;
    }
    .dhx_diagram_template_c__stage--design:hover {
        box-shadow: 0px 2px 3px 0px #9B60F826;
    }  
    .dhx_diagram_template_c__stage--development {
        --dhx-template-c-header-background: #FFAE12;
    }
    .dhx_diagram_template_c__stage--development:hover {
        box-shadow: 0px 2px 3px 0px #FFAE1226;
    }
    .dhx_diagram_template_c__stage--testing {
        --dhx-template-c-header-background: #098ae8;
    }
    .dhx_diagram_template_c__stage--testing:hover {
        box-shadow: 0px 2px 3px 0px #098ae826;
    }
    .dhx_diagram_template_c__stage--launch {
        --dhx-template-c-header-background: #ff4b3b;
    }
    .dhx_diagram_template_c__stage--launch:hover {
        box-shadow: 0px 2px 3px 0px #ff4b3b26;
    }
    .dhx_diagram_template_c__header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        width: 100%;
        min-height: 32px;
        height: 100%;
        border-radius: 4px;
        border: var(--dhx-border);
        font-size: 16px;        
        font-weight: var(--dhx-font-weight-bold);
        color: var(--dhx-template-c-header-color);
        background-color: var(--dhx-template-c-header-background);
    }
    .dhx_diagram_template_c__content {
        width: 100%;
    }
    .dhx_diagram_template_c__row {
        display: flex;
        width: 100%;
        min-height: 32px;
    }
    .dhx_diagram_template_c__row:not(:first-child) {
        border-top: var(--dhx-border);
        min-height: 33px;
    }
    .dhx_diagram_template_c__text {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dhx_diagram_template_c__text:not(:first-child) {
        border-left: var(--dhx-border);
    }
</style>