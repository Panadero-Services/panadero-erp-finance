<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";

import '../codebase/index.css?v=6.0.10';
import '../codebase/diagramWithEditor.css?v=6.0.10';
import '../codebase/diagramWithEditor.js?v=6.0.10';
import { swimlaneData } from '../codebase/data.js';

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
        const _jsonLane = await JSON.parse(_lane);
        console.log(_jsonLane);
        editor.diagram.data.parse(_jsonLane);
    } else {
        const _jsonDefaultLane='[{"type":"end","width":140,"height":90,"fontColor":"#4C4C4C","fill":"#EEF1F6","stroke":"#B8C6D6","text":"End","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","x":790,"y":110,"fixed":false,"editable":true,"id":"u1747404444790","strokeType":"line"},{"type":"start","width":140,"height":90,"fontColor":"#4C4C4C","fill":"#EEF1F6","stroke":"#B8C6D6","text":"Start","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","x":300,"y":110,"fixed":false,"editable":true,"id":"u1747404445104","strokeType":"line"},{"type":"rectangle","width":140,"height":90,"fontColor":"#33B579","fill":"#000000","stroke":"#3F4757","text":"NotFound","strokeWidth":1,"fontSize":20,"lineHeight":14,"strokeDash":"0","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","x":540,"y":110,"fixed":false,"editable":true,"id":"u1747404445427","strokeType":"line","fontWeight":"bold"}]';
        console.log("notFound");
        editor.diagram.data.parse(_jsonDefaultLane);
    }
  }
}

const _clear = async () => {
    editor.diagram.data.removeAll();
}

const _basic = async () => {
    const _jsonDefaultLane= '[{"id":"main","type":"$swimlane","height":680,"width":1060,"header":{"enable":true,"text":"Order Process Flow Phase 2","fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"500","fontColor":"#4C4C4C","position":"top","editable":true,"fill":"#F4D679","height":40,"iconColor":"#808080","closable":true},"layout":[[1,2,3],[4,5,6],[7,8,9]],"subHeaderRows":{"height":40,"enable":true,"position":"left","fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"500","fontColor":"#4C4C4C","iconColor":"#808080","editable":true,"fill":"#EEF1F6","headers":[{"text":"FrontDesk","id":"u1747404442293"},{"text":"Warehouse","id":"u1747404442297"},{"text":"Administration","id":"u1747404442298"}]},"subHeaderCols":{"height":40,"enable":true,"position":"top","fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"500","fontColor":"#4C4C4C","iconColor":"#808080","editable":true,"fill":"#EEF1F6","headers":[{"text":"Front Office team","id":"u1747404442294"},{"text":"Back Office team","id":"u1747404442295"},{"text":"Logistic team","id":"u1747404442296"}]},"x":-80,"y":20,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":100,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"groupChildren":[1,2,3,4,5,6,7,8,9]},{"id":1,"type":"$sgroup","groupChildren":["start","u1747404461842"],"x":-40,"y":100,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":2,"type":"$sgroup","groupChildren":["second-step"],"x":300,"y":100,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":5,"type":"$sgroup","groupChildren":["no","yes"],"x":300,"y":300,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":6,"type":"$sgroup","groupChildren":["third-step"],"x":640,"y":300,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":7,"type":"$sgroup","groupChildren":["first-step"],"x":-40,"y":500,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":8,"type":"$sgroup","groupChildren":["question"],"x":300,"y":500,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"id":9,"type":"$sgroup","groupChildren":["finish"],"x":640,"y":500,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"type":"rectangle","text":"First step","x":90,"y":560,"id":"first-step","strokeWidth":2,"width":140,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"type":"rectangle","text":"Rebalance Process","x":370,"y":150,"id":"second-step","strokeWidth":2,"width":140,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"type":"rectangle","text":"Yes","x":530,"y":360,"id":"yes","strokeWidth":2,"width":140,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"type":"circle","text":"Question","x":480,"y":560,"id":"question","strokeWidth":1,"width":90,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#90D2AF","stroke":"#33B579","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#000000","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"id":"u1628684824695","type":"line","points":[{"x":230,"y":605},{"x":525,"y":605}],"stroke":"#2196F3","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"width":295,"height":0,"x":230,"y":605,"from":"first-step","to":"question","fromSide":"right","toSide":"center","strokeType":"line","backArrow":"none","forwardArrow":"none"},{"type":"rectangle","text":"No","x":371.5,"y":360,"id":"no","strokeWidth":2,"width":137,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"type":"rectangle","text":"Third step","x":800,"y":360,"id":"third-step","strokeWidth":2,"width":140,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"type":"start","text":"Finish","x":800,"y":560,"id":"finish","strokeWidth":0,"width":140,"height":90,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FE9998","stroke":"#F35A4F","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#000000","fixed":false,"editable":true,"hidden":false,"strokeType":"line"},{"id":"u1628684834117","type":"line","points":[{"x":870,"y":560},{"x":870,"y":450}],"stroke":"#2196F3","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"width":0,"height":110,"x":870,"y":560,"from":"finish","to":"third-step","fromSide":"top","toSide":"bottom","strokeType":"line","backArrow":"none","forwardArrow":"none"},{"id":"u1628684835341","type":"line","points":[{"x":670,"y":405},{"x":800,"y":405}],"stroke":"#2196F3","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"width":130,"height":0,"x":670,"y":405,"customGap":0,"from":"yes","to":"third-step","fromSide":"right","toSide":"left","strokeType":"line","backArrow":"none","forwardArrow":"none"},{"id":"u1628684840321","type":"line","points":[{"x":525,"y":605},{"x":600,"y":450}],"stroke":"#2196F3","connectType":"straight","strokeWidth":2,"cornersRadius":0,"width":75,"height":155,"x":525,"y":605,"from":"question","to":"yes","fromSide":"center","toSide":"bottom","strokeType":"line","backArrow":"none","forwardArrow":"none"},{"id":"u1628684842190","type":"line","points":[{"x":440,"y":450},{"x":525,"y":605}],"stroke":"#2196F3","connectType":"straight","strokeWidth":2,"cornersRadius":0,"width":85,"height":155,"x":440,"y":450,"from":"no","to":"question","fromSide":"bottom","toSide":"center","strokeType":"line","backArrow":"none","forwardArrow":"none"},{"type":"$sgroup","id":3,"x":640,"y":100,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340,"groupChildren":["u1747404443384"]},{"type":"$sgroup","id":4,"x":-40,"y":300,"open":true,"style":{"strokeWidth":1,"stroke":"#B8C6D6","strokeType":"line","borderStyle":"solid","fill":"inherit","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"entryArea":{"catchArea":25,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"fixed":false,"hidden":false,"height":200,"width":340},{"type":"delay","width":140,"height":90,"fontColor":"#4C4C4C","fill":"#AEC1FF","stroke":"#B8C6D6","text":"Delay","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","x":670,"y":150,"fixed":false,"editable":true,"id":"u1747404443384","strokeType":"line","hidden":false},{"id":"u1747404448168","type":"line","points":[{"x":600,"y":360},{"x":600,"y":360}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"filled","width":0,"height":0,"x":600,"y":360,"from":"yes","to":"yes","fromSide":"top","toSide":"top"},{"type":"start","width":140,"height":90,"fontColor":"#4C4C4C","fill":"#90D2AF","stroke":"#33B579","text":"Start","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","x":90,"y":150,"fixed":false,"editable":true,"id":"u1747404461842","strokeType":"line","hidden":false},{"id":"u1747404464005","type":"line","points":[{"x":160,"y":240},{"x":160,"y":560}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"filled","width":0,"height":320,"x":160,"y":240,"from":"u1747404461842","to":"first-step","fromSide":"bottom","toSide":"top"},{"id":"u1747404466691","type":"line","points":[{"x":440,"y":360},{"x":440,"y":240}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"filled","width":0,"height":120,"x":440,"y":360,"from":"no","to":"second-step","fromSide":"top","toSide":"bottom"},{"id":"u1747404470145","type":"line","points":[{"x":370,"y":195},{"x":230,"y":195}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"filled","width":140,"height":0,"x":370,"y":195,"from":"second-step","to":"u1747404461842","fromSide":"left","toSide":"right"},{"id":"u1747406184781","type":"line","points":[{"x":736,"y":236},{"x":736,"y":236}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"none","width":0,"height":0,"x":736,"y":236},{"id":"u1747406186315","type":"dash","points":[{"x":670,"y":195},{"x":640,"y":195},{"x":600,"y":195},{"x":600,"y":330},{"x":600,"y":360}],"stroke":"#11B3A5","connectType":"elbow","strokeType":"dash","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"none","width":70,"height":165,"x":670,"y":195,"from":"u1747404443384","to":"yes","fromSide":"left","toSide":"top"},{"id":"u1747406194275","type":"line","points":[{"x":940,"y":605},{"x":940,"y":605}],"stroke":"#2196F3","connectType":"elbow","strokeType":"line","strokeWidth":2,"cornersRadius":0,"backArrow":"none","forwardArrow":"filled","width":0,"height":0,"x":940,"y":605,"from":"finish","to":"finish","fromSide":"right","toSide":"right"}]';
    editor.diagram.data.parse(_jsonDefaultLane);
}


const _loadGantt = async () => {
  if(props.set.project.id > 0){

    const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    //const _type = props.set.projectType;
    const _type = "resourcePlanning";
    const _projectId = props.set.project.id;
  }
}

let editor, _state;
 
// webhooks
onMounted(async ()=> {
    console.log(navigator.language);
    await props.set.initMM();
    await props.set.initialize();
    await props.set.setProjectType('lane');
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



editor = new dhx.DiagramEditor("swimlane", {
    type: "default",
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
            editor.parse(swimlaneData);

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
            <button @click="_clear" type="button" :class="_button">Clear</button>
            <button @click="_basic" type="button" :class="_button">Basic</button>
            <button @click="_loadGantt" type="button" :class="_button">loadGantt</button>
        </div>      
    </div>
    
        <div class="" id="editor"></div>
        <div class="bg-red-400 h-screen w-screen" id="swimlane"></div>
</div>

</template>

<!-- custom styles -->
<style>
    .dhx_sample-widget {
        height: calc(100% - 61px);
    }
</style>