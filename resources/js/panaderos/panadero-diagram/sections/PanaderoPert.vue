<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';

// define emits
const emit = defineEmits(['kill', 'wrench']);

//import '@dhx/diagram/codebase/diagram.css';
//import { DiagramEditor } from "@dhx/diagram";
//import {  } from "@dhx/diagram";
//import '../codebase/diagramWithEditor.js?v=6.0.10';

//import "@dhx/diagram_6/codebase/diagramWithEditor.js?v=6.0.10";
//import "@dhx/diagram_6/codebase/diagram.css?v=6.0.10";
//import "@dhx/diagram_6/codebase/diagramWithEditor.css?v=6.0.10";

import "@/panaderos/panadero-diagram/codebase/diagramWithEditor.js?v=6.0.10";


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
        const _jsonMind = await JSON.parse(_lane);
        console.log(_jsonMind);
        editor.diagram.data.parse(_jsonMind);
    } else {
        
        console.log("notFound");
        const _jsonDefault = '[{"type":"$group","x":100,"y":-300,"width":1000,"height":220,"fixed":true,"style":{"strokeWidth":1,"stroke":"#CCC","strokeType":"line","borderStyle":"solid","fill":"#fff","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"id":"u1670338639002","open":true,"entryArea":{"catchArea":100,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"groupChildren":["u1670338639003","u1670338639056","u1670338640548","u1670338640436","u1670338639949","u1670338639004","u1670338667592","u1670338667708","u1670339716888","u1670339716926","u1670339720048","u1670339720049","u1670338640127","u1670339725841","u1670339730542","u1670339733975","u1670339734238","u1670339733743","u1670339733303","u1670339731843","u1747508819067"]},{"type":"text","width":46,"height":26,"text":"Slot::","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":128,"y":-260,"fixed":true,"editable":true,"id":"u1670338639056","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":103,"height":26,"text":"NOT FOUND ","lineHeight":"24","fontSize":"18","fontColor":"#CA3626","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":350,"y":-260,"fixed":true,"editable":true,"id":"u1670338639949","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":115,"height":33,"text":"Critical path:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":660,"y":-260,"fixed":true,"editable":true,"id":"u1670338640436","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":154,"height":26,"text":"Responsible team:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":900,"y":-260,"fixed":true,"editable":true,"id":"u1670338640548","angle":0,"fontWeight":"bold","hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-230,"fixed":true,"editable":true,"id":"u1670339716888","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-230,"fixed":true,"editable":true,"id":"u1670339716926","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-150,"fixed":true,"editable":true,"id":"u1670339720048","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-150,"fixed":true,"editable":true,"id":"u1670339720049","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":630,"y":-115,"fixed":true,"editable":true,"id":"u1670339725825","strokeType":"line","angle":0},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":800,"y":-115,"fixed":true,"editable":true,"id":"u1670339725841","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#9A60F8","stroke":"#9A60F8","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-220,"fixed":true,"editable":true,"id":"u1670339730542","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#3CC979","stroke":"#3CC979","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-180,"fixed":true,"editable":true,"id":"u1670339731843","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFAC12","stroke":"#FFAC12","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-140,"fixed":true,"editable":true,"id":"u1670339733303","strokeType":"line","angle":0,"hidden":false},{"type":"text","width":96,"height":16,"text":"- Product team","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-220,"fixed":true,"editable":true,"id":"u1670339733743","angle":0,"hidden":false},{"type":"text","width":40,"height":16,"text":"- Both","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-180,"fixed":true,"editable":true,"id":"u1670339733975","angle":0,"hidden":false},{"type":"text","width":93,"height":16,"text":"- Development","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-140,"fixed":true,"editable":true,"id":"u1670339734238","angle":0,"hidden":false}]';
        editor.diagram.data.parse(_jsonDefault);
    }
  }
}

const _clear = async () => {
    editor.diagram.data.removeAll();
}

const _basic = async () => {
    editor.diagram.data.parse(_basic1);
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


const _basic0 = '[{"type":"$group","x":100,"y":-300,"width":1000,"height":220,"fixed":true,"style":{"strokeWidth":1,"stroke":"#CCC","strokeType":"line","borderStyle":"solid","fill":"#fff","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"id":"u1670338639002","open":true,"entryArea":{"catchArea":100,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"groupChildren":["u1670338639003","u1670338639056","u1670338640548","u1670338640436","u1670338639949","u1670338639004","u1670338667592","u1670338667708","u1670339716888","u1670339716926","u1670339720048","u1670339720049","u1670338640127","u1670339725841","u1670339730542","u1670339733975","u1670339734238","u1670339733743","u1670339733303","u1670339731843","u1747508819067"]},{"type":"pert","stage":"development","status":"number","critical":false,"task":"Task name","start_date":"01.06.2025","end_date":"01.07.2025","x":128,"y":-220,"id":"u1670338639003","fixed":true,"hidden":false,"width":200,"height":97,"responsible":"Responsible","preview":{"scale":0.55}},{"type":"text","width":129,"height":33,"text":"Task container:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":128,"y":-260,"fixed":true,"editable":true,"id":"u1670338639056","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":130,"height":31,"text":"Dependencies:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":350,"y":-260,"fixed":true,"editable":true,"id":"u1670338639949","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":279,"height":26,"text":"Dependencies without resources:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":350,"y":-190,"fixed":true,"editable":true,"id":"u1670338640127","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":115,"height":33,"text":"Critical path:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":660,"y":-260,"fixed":true,"editable":true,"id":"u1670338640436","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":154,"height":26,"text":"Responsible team:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":900,"y":-260,"fixed":true,"editable":true,"id":"u1670338640548","angle":0,"fontWeight":"bold","hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-230,"fixed":true,"editable":true,"id":"u1670339716888","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-230,"fixed":true,"editable":true,"id":"u1670339716926","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339716988","type":"line","points":[{"x":360,"y":-215},{"x":500,"y":-215}],"stroke":"#666666","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":360,"y":-215,"from":"u1670339716888","to":"u1670339716926","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none"},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-150,"fixed":true,"editable":true,"id":"u1670339720048","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-150,"fixed":true,"editable":true,"id":"u1670339720049","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339720091","type":"dash","points":[{"x":360,"y":-135},{"x":500,"y":-135}],"stroke":"#666666","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":360,"y":-135,"from":"u1670339720048","to":"u1670339720049","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"dash","backArrow":"none"},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":630,"y":-115,"fixed":true,"editable":true,"id":"u1670339725825","strokeType":"line","angle":0},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":800,"y":-115,"fixed":true,"editable":true,"id":"u1670339725841","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339725913","type":"line","points":[{"x":660,"y":-100},{"x":800,"y":-100}],"stroke":"#FF5252","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":660,"y":-100,"from":"u1670339725825","to":"u1670339725841","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none"},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#9A60F8","stroke":"#9A60F8","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-220,"fixed":true,"editable":true,"id":"u1670339730542","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#3CC979","stroke":"#3CC979","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-180,"fixed":true,"editable":true,"id":"u1670339731843","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFAC12","stroke":"#FFAC12","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-140,"fixed":true,"editable":true,"id":"u1670339733303","strokeType":"line","angle":0,"hidden":false},{"type":"text","width":96,"height":16,"text":"- Product team","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-220,"fixed":true,"editable":true,"id":"u1670339733743","angle":0,"hidden":false},{"type":"text","width":40,"height":16,"text":"- Both","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-180,"fixed":true,"editable":true,"id":"u1670339733975","angle":0,"hidden":false},{"type":"text","width":93,"height":16,"text":"- Development","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-140,"fixed":true,"editable":true,"id":"u1670339734238","angle":0,"hidden":false},{"type":"pert","stage":"launch","task":"Launch","status":"T5.","width":200,"height":97,"fontColor":"#4c4c4c","fill":"#eef1f6","stroke":"#b8c6d6","text":"Pert","responsible":"Responsible","start_date":"17.5.2025","end_date":"19.5.2025","preview":{"scale":0.55},"x":640,"y":-220,"fixed":false,"id":"u1747508819067","hidden":false}]';
const _basic1 = '[{"type":"pert","stage":"analysis","task":"Formalize specs","start_date":"28.11.2022","end_date":"10.12.2022","status":"01","critical":true,"id":"u1670326160173","x":0,"y":150,"responsible":"Henry Bennett","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"design","status":"02","critical":true,"task":"Design hardware","start_date":"10.12.2022","end_date":"24.12.2022","x":300,"y":0,"id":"u1670326160179","responsible":"Greg Mash","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"design","status":"03","critical":false,"task":"Design software","start_date":"10.12.2022","end_date":"05.01.2023","x":300,"y":150,"id":"u1670326160195","responsible":"Olga Dahixi","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"development","status":"04","critical":false,"task":"Layout manual","start_date":"10.12.2022","end_date":"31.12.2022","x":300,"y":300,"id":"u1670326160216","responsible":"Olga Dahixi","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"development","status":"05","critical":true,"task":"Breadboard hardware","start_date":"24.12.2022","end_date":"05.01.2023","x":560,"y":0,"id":"u1670326160523","responsible":"Mike Freebel","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"testing","status":"06","critical":false,"task":"Test hardware","start_date":"05.01.2023","end_date":"15.01.2023","x":560,"y":150,"id":"u1670326160529","responsible":"Emma Lynch","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"launch","status":"07","critical":false,"task":"Release manual","start_date":"31.12.2022","end_date":"15.01.2023","x":560,"y":300,"id":"u1670326160540","responsible":"Mike Freebel","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"launch","status":"08","critical":true,"task":"Release hardware","start_date":"15.01.2023","end_date":"30.01.2023","x":820,"y":0,"id":"u1670326160566","responsible":"Charles Little","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"launch","status":"09","critical":false,"task":"Release software","start_date":"15.01.2023","end_date":"30.01.2023","x":820,"y":150,"id":"u1670326160602","responsible":"Emma Lynch","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"development","status":"10","critical":true,"task":"Manufacture hardware","start_date":"30.01.2023","end_date":"11.02.2023","x":1080,"y":0,"id":"u1670326160644","responsible":"Greg Mash","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"type":"pert","stage":"launch","status":"11","critical":true,"task":"Deploy to production","start_date":"11.02.2023","end_date":"25.02.2023","x":1080,"y":150,"id":"u1670326160665","responsible":"Mike Freebel","width":200,"height":97,"preview":{"scale":0.55},"fixed":false},{"id":"u1670326163580","type":"line","stroke":"#FF5252","connectType":"elbow","x":100,"y":150,"from":"u1670326160173","to":"u1670326160179","fromSide":"top","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":100,"y":150},{"x":100,"y":140},{"x":100,"y":48.5},{"x":290,"y":48.5},{"x":300,"y":48.5}],"width":200,"height":101.5},{"type":"lineTitle","parent":"u1670326163580","text":"Brainstorm","distance":60,"id":"u1747508818317","editable":true,"fixed":false,"autoPosition":false,"fill":"#FFFFFF","lineHeight":14,"fontSize":14,"fontColor":"#4C4C4C","fontStyle":"normal","fontWeight":"normal","textAlign":"center","width":73,"height":16},{"id":"u1670326166477","type":"line","stroke":"#FF5252","connectType":"elbow","x":500,"y":48.5,"from":"u1670326160179","to":"u1670326160523","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":500,"y":48.5},{"x":560,"y":48.5}],"width":60,"height":0},{"id":"u1670326167316","stroke":"#FF5252","connectType":"elbow","x":660,"y":97,"from":"u1670326160523","to":"u1670326160529","fromSide":"bottom","toSide":"top","forwardArrow":"filled","strokeType":"line","backArrow":"none","type":"line","strokeWidth":2,"cornersRadius":0,"points":[{"x":660,"y":97},{"x":660,"y":150}],"width":0,"height":53},{"id":"u1670326173940","type":"line","stroke":"#FF5252","connectType":"elbow","x":660,"y":198.5,"from":"u1670326160529","to":"u1670326160566","fromSide":"center","toSide":"center","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":660,"y":198.5},{"x":920,"y":48.5}],"width":260,"height":150},{"id":"u1670326176076","type":"line","stroke":"#FF5252","connectType":"elbow","x":1020,"y":48.5,"from":"u1670326160566","to":"u1670326160644","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":1020,"y":48.5},{"x":1080,"y":48.5}],"width":60,"height":0},{"id":"u1670326177105","type":"line","stroke":"#FF5252","connectType":"elbow","x":1180,"y":97,"from":"u1670326160644","to":"u1670326160665","fromSide":"bottom","toSide":"top","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":1180,"y":97},{"x":1180,"y":150}],"width":0,"height":53},{"id":"u1670326177883","type":"line","stroke":"#666666","connectType":"elbow","x":200,"y":198.5,"from":"u1670326160173","to":"u1670326160195","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":200,"y":198.5},{"x":300,"y":198.5}],"width":100,"height":0},{"id":"u1670326179746","type":"line","stroke":"#666666","connectType":"elbow","x":500,"y":198.5,"from":"u1670326160195","to":"u1670326160529","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":500,"y":198.5},{"x":560,"y":198.5}],"width":60,"height":0},{"id":"u1670326180657","type":"line","stroke":"#666666","connectType":"elbow","x":760,"y":198.5,"from":"u1670326160529","to":"u1670326160602","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":760,"y":198.5},{"x":820,"y":198.5}],"width":60,"height":0},{"id":"u1670326181486","type":"line","stroke":"#666666","connectType":"elbow","x":920,"y":150,"from":"u1670326160602","to":"u1670326160566","fromSide":"top","toSide":"bottom","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":920,"y":150},{"x":920,"y":97}],"width":0,"height":53},{"id":"u1670326182311","type":"line","stroke":"#666666","connectType":"elbow","x":760,"y":348.5,"from":"u1670326160540","to":"u1670326160602","fromSide":"right","toSide":"bottom","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":760,"y":348.5},{"x":770,"y":348.5},{"x":920,"y":348.5},{"x":920,"y":257},{"x":920,"y":247}],"width":160,"height":101.5},{"id":"u1670326184758","type":"line","stroke":"#666666","connectType":"elbow","x":500,"y":348.5,"from":"u1670326160216","to":"u1670326160540","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":500,"y":348.5},{"x":560,"y":348.5}],"width":60,"height":0},{"id":"u1670326185712","type":"line","stroke":"#666666","connectType":"elbow","x":100,"y":247,"from":"u1670326160173","to":"u1670326160216","fromSide":"bottom","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none","strokeWidth":2,"cornersRadius":0,"points":[{"x":100,"y":247},{"x":100,"y":257},{"x":100,"y":348.5},{"x":290,"y":348.5},{"x":300,"y":348.5}],"width":200,"height":101.5},{"type":"$group","x":100,"y":-300,"width":1000,"height":220,"fixed":true,"style":{"strokeWidth":1,"stroke":"#CCC","strokeType":"line","borderStyle":"solid","fill":"#fff","overFill":"rgba(46, 204, 113, 0.1)","partiallyFill":"rgba(231, 76, 60, 0.1)"},"id":"u1670338639002","open":true,"entryArea":{"catchArea":100,"borderFlexible":false},"exitArea":{"groupBehavior":"unbound","padding":0},"groupChildren":["u1670338639003","u1670338639056","u1670338640548","u1670338640436","u1670338639949","u1670338639004","u1670338667592","u1670338667708","u1670339716888","u1670339716926","u1670339720048","u1670339720049","u1670338640127","u1670339725841","u1670339730542","u1670339733975","u1670339734238","u1670339733743","u1670339733303","u1670339731843"]},{"type":"pert","stage":"development","status":"number","critical":false,"task":"Task name","start_date":"10.12.2022","end_date":"10.12.2022","x":128,"y":-220,"id":"u1670338639003","fixed":true,"hidden":false,"width":200,"height":97,"responsible":"Responsible","preview":{"scale":0.55}},{"type":"pert","stage":"development","status":"number","critical":true,"task":"Task name","start_date":"10.12.2022","end_date":"10.12.2022","x":660,"y":-220,"id":"u1670338639004","fixed":true,"hidden":false,"width":200,"height":97,"responsible":"Responsible","preview":{"scale":0.55}},{"type":"text","width":129,"height":33,"text":"Task container:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":128,"y":-260,"fixed":true,"editable":true,"id":"u1670338639056","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":130,"height":31,"text":"Dependencies:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":350,"y":-260,"fixed":true,"editable":true,"id":"u1670338639949","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":279,"height":26,"text":"Dependencies without resources:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":350,"y":-190,"fixed":true,"editable":true,"id":"u1670338640127","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":115,"height":33,"text":"Critical path:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":660,"y":-260,"fixed":true,"editable":true,"id":"u1670338640436","angle":0,"fontWeight":"bold","hidden":false},{"type":"text","width":154,"height":26,"text":"Responsible team:","lineHeight":"24","fontSize":"18","fontColor":"rgba(0,0,0,0.70)","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":900,"y":-260,"fixed":true,"editable":true,"id":"u1670338640548","angle":0,"fontWeight":"bold","hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-230,"fixed":true,"editable":true,"id":"u1670339716888","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-230,"fixed":true,"editable":true,"id":"u1670339716926","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339716988","type":"line","points":[{"x":360,"y":-215},{"x":500,"y":-215}],"stroke":"#666666","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":360,"y":-215,"from":"u1670339716888","to":"u1670339716926","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none"},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":330,"y":-150,"fixed":true,"editable":true,"id":"u1670339720048","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":500,"y":-150,"fixed":true,"editable":true,"id":"u1670339720049","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339720091","type":"dash","points":[{"x":360,"y":-135},{"x":500,"y":-135}],"stroke":"#666666","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":360,"y":-135,"from":"u1670339720048","to":"u1670339720049","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"dash","backArrow":"none"},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":630,"y":-115,"fixed":true,"editable":true,"id":"u1670339725825","strokeType":"line","angle":0},{"type":"rectangle","width":30,"height":30,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFFFFF","stroke":"#FFFFFF","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":800,"y":-115,"fixed":true,"editable":true,"id":"u1670339725841","strokeType":"line","angle":0,"hidden":false},{"id":"u1670339725913","type":"line","points":[{"x":660,"y":-100},{"x":800,"y":-100}],"stroke":"#FF5252","connectType":"elbow","strokeWidth":2,"cornersRadius":0,"title":{"fontSize":14,"lineHeight":14,"textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontWeight":"normal","fontColor":"#4C4C4C","fill":"#FFF","draggable":true,"editable":true,"hidden":false},"width":140,"height":0,"x":660,"y":-100,"from":"u1670339725825","to":"u1670339725841","fromSide":"right","toSide":"left","forwardArrow":"filled","strokeType":"line","backArrow":"none"},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#9A60F8","stroke":"#9A60F8","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-220,"fixed":true,"editable":true,"id":"u1670339730542","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#3CC979","stroke":"#3CC979","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-180,"fixed":true,"editable":true,"id":"u1670339731843","strokeType":"line","angle":0,"hidden":false},{"type":"rectangle","width":40,"height":20,"text":"","extraLinesStroke":"#B8C6D6","strokeWidth":1,"fontSize":14,"lineHeight":14,"strokeDash":"0","fill":"#FFAC12","stroke":"#FFAC12","textAlign":"center","textVerticalAlign":"center","fontStyle":"normal","fontColor":"#4C4C4C","x":900,"y":-140,"fixed":true,"editable":true,"id":"u1670339733303","strokeType":"line","angle":0,"hidden":false},{"type":"text","width":96,"height":16,"text":"- Product team","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-220,"fixed":true,"editable":true,"id":"u1670339733743","angle":0,"hidden":false},{"type":"text","width":40,"height":16,"text":"- Both","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-180,"fixed":true,"editable":true,"id":"u1670339733975","angle":0,"hidden":false},{"type":"text","width":93,"height":16,"text":"- Development","lineHeight":14,"fontSize":14,"fontColor":"#666666","textAlign":"center","fontStyle":"normal","textVerticalAlign":"center","x":950,"y":-140,"fixed":true,"editable":true,"id":"u1670339734238","angle":0,"hidden":false}]';



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

    editor.parse(_basic0);

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
            <button @click="_loadPlan" type="button" :class="_button">loadPlan</button>
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