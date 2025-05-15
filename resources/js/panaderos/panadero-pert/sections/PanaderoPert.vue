<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);


import { } from "@dhx/diagram";



// Globals
const props = defineProps({
    //ref: Object, ref is a reserved property
    contract: Object,
    set: Object,
    db: Object
});

// globals
const _title="Pert";

let editor;


 const productData = [
        {
            "type": "$swimlane",
            "height": 1130,
            "width": 1230,
            "layout": [
                ["analysis"],
                ["design"],
                ["development"],
                ["testing"],
                ["launch"]
            ],
            "subHeaderRows": {
                "headers": [
                    { "text": "Analysis", "fill": "#DAFFE6" },
                    { "text": "Design", "fill": "#E4CCFF" },
                    { "text": "Development", "fill": "#FCD19C" },
                    { "text": "Testing", "fill": "#BDE3FF" },
                    { "text": "Launch", "fill": "#FFC7C2" }
                ]
            },
        },
        {
            "type": "$sgroup",
            "id": "analysis",
            "style": { "fill": "#DAFFE633" },
            "groupChildren": [ "T1.1", "T1.2"],
        },
        {
            "type": "$sgroup",
            "id": "design",
            "style": { "fill": "#E4CCFF33" },
            "groupChildren": ["T2.1", "T2.2"],
        },
        {
            "type": "$sgroup",
            "id": "development",
            "style": { "fill": "#FCD19C33" },
            "groupChildren": ["T3.1", "T3.2"],
        },
        {
            "id": "testing",
            "type": "$sgroup",
            "style": { "fill": "#BDE3FF33" },
            "groupChildren": ["T4.1", "T4.2"],
        },
        {
            "id": "launch",
            "type": "$sgroup",
            "style": { "fill": "#FFC7C233" },
            "groupChildren": ["T5.1"],
        },
        {
            "type": "pert",
            "id": "T5.1",
            "status": "T5.1",
            "x": 1000,
            "y": 960,
            "task": "Go Live!",
            "start_date": "08.09.2024",
            "end_date": "08.09.2024",
            "stage": "launch",
            "responsible": "Henry Bennett"
        },
        {
            "type": "pert",
            "id": "T1.1",
            "status": "T1.1",
            "x": 80,
            "y": 50,
            "task": "Market Research",
            "start_date": "01.08.2024",
            "end_date": "03.08.2024",
            "stage": "analysis",
            "responsible": "Greg Mash"
        },
        {
            "type": "pert",
            "id": "T1.2",
            "status": "T1.2",
            "x": 310,
            "y": 50,
            "task": "Competitor Analysis",
            "start_date": "04.08.2024",
            "end_date": "07.08.2024",
            "stage": "analysis",
            "responsible": "Greg Mash"
        },
        {
            "type": "pert",
            "id": "T2.1",
            "status": "T2.1",
            "x": 310,
            "y": 280,
            "task": "Competitor Analysis",
            "start_date": "08.08.2024",
            "end_date": "14.08.2024",
            "stage": "design",
            "responsible": "Olga Dahixi"
        },
        {
            "type": "pert",
            "id": "T2.2",
            "status": "T2.2",
            "x": 540,
            "y": 280,
            "task": "Wireframing",
            "start_date": "08.08.2024",
            "end_date": "17.08.2024",
            "stage": "design",
            "responsible": "Olga Dahixi"
        },
        {
            "type": "pert",
            "id": "T3.1",
            "status": "T3.1",
            "x": 540,
            "y": 510,
            "task": "Front-end Coding",
            "start_date": "18.08.2023",
            "end_date": "27.08.2023",
            "stage": "development",
            "responsible": "Mike Freebel"
        },
        {
            "type": "pert",
            "id": "T3.2",
            "status": "T3.2",
            "x": 770,
            "y": 510,
            "task": "Back-end Coding",
            "start_date": "28.08.2023",
            "end_date": "31.08.2023",
            "stage": "development",
            "responsible": "Emma Lynch"
        },
        {
            "type": "pert",
            "id": "T4.1",
            "status": "T4.1",
            "x": 770,
            "y": 730,
            "task": "Unit Testing ",
            "start_date": "01.09.2023",
            "end_date": "03.09.2023",
            "stage": "testing",
            "responsible": "Mike Freebel"
        },
        {
            "type": "pert",
            "id": "T4.2",
            "status": "T4.2",
            "x": 1000,
            "y": 730,
            "task": "Integration Testing",
            "start_date": "04.09.2023",
            "end_date": "07.09.2023",
            "stage": "testing",
            "responsible": "Charles Little"
        },
        {
            "id": "u1712664501167",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T1.1",
            "to": "T1.2",
            "fromSide": "right",
            "toSide": "left"
        },
        {
            "id": "u1712664501613",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T1.2",
            "to": "T2.1",
            "fromSide": "bottom",
            "toSide": "top"
        },
        {
            "id": "u1712664502390",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T2.1",
            "to": "T2.2",
            "fromSide": "right",
            "toSide": "left"
        },
        {
            "id": "u1712664502802",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T2.2",
            "to": "T3.1",
            "fromSide": "bottom",
            "toSide": "top"
        },
        {
            "id": "u1712664503494",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T3.1",
            "to": "T3.2",
            "fromSide": "right",
            "toSide": "left"
        },
        {
            "id": "u1712664503951",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T3.2",
            "to": "T4.1",
            "fromSide": "bottom",
            "toSide": "top"
        },
        {
            "id": "u1712664504578",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T4.1",
            "to": "T4.2",
            "fromSide": "right",
            "toSide": "left"
        },
        {
            "id": "u1712664505005",
            "type": "line",
            "forwardArrow": "filled",
            "from": "T4.2",
            "to": "T5.1",
            "fromSide": "bottom",
            "toSide": "top"
        }
    ];

    function isDarkTheme() {
        const node = document.getElementById("editor").childNodes[0];
        const currentTheme = node.dataset.dhxTheme;
        return (currentTheme === "dark" || currentTheme === "contrast-dark");
    }

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

 
// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('mood');

    await loadEditor();
    await loadEvents();
  // board.selectCard({ id: 3, groupMode: true });
});

const loadEvents = (_theme) => {
  //board.api.intercept("move-card", (_obj) => {console.log('move-card', _obj);});
  //board.api.intercept("add-card", (_obj) => {console.log('add-card', _obj);});
  // forbid moving cards to the column with the "done" ID
  // board.api.intercept("move-card", ({ id, columnId }) => { console.log(id, columnId); if(columnId !== "done" ){ return false; } });
}

const loadEditor = () => {
editor = new DiagramEditor("editor", {
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
                            key: "responsible",
                            label: "Responsible",
                            placeholder: "Select responsible",
                            options: ["Henry Bennett", "Greg Mash", "Olga Dahixi", "Mike Freebel", "Emma Lynch", "Charles Little"]
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
        width: 180,
        height: 124,
        responsible: "Responsible",
        start_date: dateToString(new Date),
        end_date: dateToString(new Date(Date.now() + 100000000)),
        preview: { scale: 0.65 },
    },
    template: ({ start_date, end_date, status, task, responsible, stage }) => {
        const duration = getDiffDays(start_date, end_date);
        return `
            <div class="dhx_diagram_template_c dhx_diagram_template_c__stage--${stage}" >
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
                <div class="dhx_diagram_template_c__footer">${responsible}</div>
            </div>
        `;
    },
});

editor.parse(productData);
}


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
    <div class="grid grid-cols-2">     
        {{_title}}
        <div id="root" class="col-span-2"></div>
        <div id="whatever" class=""></div>
    </div>
</div>

</template>

<!-- custom styles -->
<style>


 .dhx_diagram_template_c {
        --dhx-template-c-header-background: var(--dhx-background-primary);
        --dhx-template-c-header-color: var(--dhx-color-gray-700);

        font-family: var(--dhx-font-family);
        font-size: var(--dhx-font-size-normal);
        font-weight: var(--dhx-font-weight-regular);
        line-height: var(--dhx-line-height-small);
        color: var(--dhx-font-color-primary);

        height: 100%;
        width: 100%;

        background-color: var(--dhx-background-primary);
        border: var(--dhx-border);
        border-radius: 4px;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        overflow: hidden;
    }
    .dhx_diagram_template_c__stage--analysis {
        --dhx-template-c-header-background: #DAFFE6;
    }
    .dhx_diagram_template_c__stage--design {
        --dhx-template-c-header-background: #E4CCFF;
    } 
    .dhx_diagram_template_c__stage--development {
        --dhx-template-c-header-background: #FCD19C;
    }
    .dhx_diagram_template_c__stage--testing {
        --dhx-template-c-header-background: #BDE3FF;
    }
    .dhx_diagram_template_c__stage--launch {
        --dhx-template-c-header-background: #FFC7C2;
    }
    .dhx_diagram_template_c__header {
        padding: 8px;
        min-height: 32px;
        width: 100%;
        border-bottom: var(--dhx-border);
        font-weight: var(--dhx-font-weight-medium);
        text-align: center;
        color: var(--dhx-template-c-header-color);
        background-color: var(--dhx-template-c-header-background);
    }
    .dhx_diagram_template_c__footer {
        color: var(--dhx-font-color-secondary);

        display: flex;
        justify-content: center;
        align-items: center;

        height: 100%;
        width: 100%;
    }
    .dhx_diagram_template_c__content {
        width: 100%;
    }
    .dhx_diagram_template_c__row {
        display: flex;
        width: 100%;
        height: 32px;
        border-bottom: var(--dhx-border);
    }
    .dhx_diagram_template_c__text {
        padding: 8px;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dhx_diagram_template_c__text:not(:first-child) {
        border-left: var(--dhx-border);
    }

</style>