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
import { dataset, short_dataset } from '@dhx/spreadsheet/samples/common/data.js?v=5.2.0';

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
        const _jsonData=[];
        console.log("notFound");
        spreadsheet.parse(_jsonData);
    }
  }
}

const _clear = async () => {
    spreadsheet.clear();
}

const _menu = async () => {
  spreadsheet.menu = true;
}

const _basic = async () => {
  const _jsonBasicData = [{"sheets":[{"name":"Report","id":"u1747520260840","data":[{"value":"Average","css":"text_end bold","format":"common","cell":"B1"},{"value":"Product's Share","css":"text_end bold","format":"common","cell":"C1"},{"value":"Min","css":"text_end bold","format":"common","cell":"D1"},{"value":"Max","css":"text_end bold","format":"common","cell":"E1"},{"value":"NET","css":"text_end bold","format":"common","cell":"F1"},{"value":"Drone","css":"bold","format":"common","cell":"A2"},{"value":"=AVERAGE(Income!B2:M2)","css":"text_end","format":"currency","cell":"B2"},{"value":"=Income!B2/Income!B12","format":"percent","css":"text_end","cell":"C2"},{"value":"=MIN(Income!B2:M2)","format":"currency","css":"text_end","cell":"D2"},{"value":"=MAX(Income!B2:M2)","format":"currency","css":"text_end","cell":"E2"},{"value":"=Income!N2-Expenses!E2","format":"currency","css":"text_end","cell":"F2"},{"value":"VR Glasses","format":"common","css":"bold","cell":"A3"},{"value":"=AVERAGE(Income!B3:M3)","format":"currency","css":"text_end","cell":"B3"},{"value":"=Income!B3/Income!B12","format":"percent","css":"text_end","cell":"C3"},{"value":"=MIN(Income!B3:M3)","format":"currency","css":"text_end","cell":"D3"},{"value":"=MAX(Income!B3:M3)","format":"currency","css":"text_end","cell":"E3"},{"value":"=Income!N3-Expenses!E3","format":"currency","css":"text_end","cell":"F3"},{"value":"Voice Remote Control","format":"common","css":"bold","cell":"A4"},{"value":"=AVERAGE(Income!B4:M4)","format":"currency","css":"text_end","cell":"B4"},{"value":"=Income!B4/Income!B12","format":"percent","css":"text_end","cell":"C4"},{"value":"=MIN(Income!B4:M4)","format":"currency","css":"text_end","cell":"D4"},{"value":"=MAX(Income!B4:M4)","format":"currency","css":"text_end","cell":"E4"},{"value":"=Income!N4-Expenses!E4","format":"currency","css":"text_end","cell":"F4"},{"value":"Touch Projector","format":"common","css":"bold","cell":"A5"},{"value":"=AVERAGE(Income!B5:M5)","format":"currency","css":"text_end","cell":"B5"},{"value":"=Income!B5/Income!B12","format":"percent","css":"text_end","cell":"C5"},{"value":"=MIN(Income!B5:M5)","format":"currency","css":"text_end","cell":"D5"},{"value":"=MAX(Income!B5:M5)","format":"currency","css":"text_end","cell":"E5"},{"value":"=Income!N5-Expenses!E5","format":"currency","css":"red_text text_end","cell":"F5"},{"value":"Smart Band","format":"common","css":"bold","cell":"A6"},{"value":"=AVERAGE(Income!B6:M6)","format":"currency","css":"text_end","cell":"B6"},{"value":"=Income!B6/Income!B12","format":"percent","css":"text_end","cell":"C6"},{"value":"=MIN(Income!B6:M6)","format":"currency","css":"text_end","cell":"D6"},{"value":"=MAX(Income!B6:M6)","format":"currency","css":"text_end","cell":"E6"},{"value":"=Income!N6-Expenses!E6","format":"currency","css":"text_end","cell":"F6"},{"value":"Video Doorbell","format":"common","css":"bold","cell":"A7"},{"value":"=AVERAGE(Income!B7:M7)","format":"currency","css":"text_end","cell":"B7"},{"value":"=Income!B7/Income!B12","format":"percent","css":"text_end","cell":"C7"},{"value":"=MIN(Income!B7:M7)","format":"currency","css":"text_end","cell":"D7"},{"value":"=MAX(Income!B7:M7)","format":"currency","css":"text_end","cell":"E7"},{"value":"=Income!N7-Expenses!E7","format":"currency","css":"text_end","cell":"F7"},{"value":"Smart TV","format":"common","css":"bold","cell":"A8"},{"value":"=AVERAGE(Income!B8:M8)","css":"text_end","format":"currency","cell":"B8"},{"value":"=Income!B8/Income!B12","format":"percent","css":"text_end","cell":"C8"},{"value":"=MIN(Income!B8:M8)","format":"currency","css":"text_end","cell":"D8"},{"value":"=MAX(Income!B8:M8)","format":"currency","css":"text_end","cell":"E8"},{"value":"=Income!N8-Expenses!E8","format":"currency","css":"text_end","cell":"F8"},{"value":"Robot Vacuum","format":"common","css":"bold","cell":"A9"},{"value":"=AVERAGE(Income!B9:M9)","format":"currency","css":"text_end","cell":"B9"},{"value":"=Income!B9/Income!B12","format":"percent","css":"text_end","cell":"C9"},{"value":"=MIN(Income!B9:M9)","format":"currency","css":"text_end","cell":"D9"},{"value":"=MAX(Income!B9:M9)","format":"currency","css":"text_end","cell":"E9"},{"value":"=Income!N9-Expenses!E9","format":"currency","css":"text_end","cell":"F9"},{"value":"Air Purifier","format":"common","css":"bold","cell":"A10"},{"value":"=AVERAGE(Income!B10:M10)","format":"currency","css":"text_end","cell":"B10"},{"value":"=Income!B10/Income!B12","format":"percent","css":"text_end","cell":"C10"},{"value":"=MIN(Income!B10:M10)","format":"currency","css":"text_end","cell":"D10"},{"value":"=MAX(Income!B10:M10)","format":"currency","css":"text_end","cell":"E10"},{"value":"=Income!N10-Expenses!E10","format":"currency","css":"text_end","cell":"F10"},{"value":"Baby Monitor","format":"common","css":"bold","cell":"A11"},{"value":"=AVERAGE(Income!B11:M11)","format":"currency","css":"text_end","cell":"B11"},{"value":"=Income!B11/Income!B12","format":"percent","css":"text_end","cell":"C11"},{"value":"=MIN(Income!B11:M11)","format":"currency","css":"text_end","cell":"D11"},{"value":"=MAX(Income!B11:M11)","format":"currency","css":"text_end","cell":"E11"},{"value":"=Income!N11-Expenses!E11","format":"currency","css":"text_end","cell":"F11"},{"value":"Total","format":"common","css":"bold blue_background","cell":"A12"},{"css":"blue blue_background","format":"common","cell":"B12"},{"css":"bold blue_background","format":"common","cell":"C12"},{"css":"bold blue_background","format":"common","cell":"D12"},{"css":"bold blue_background","format":"common","cell":"E12"},{"value":"=Income!N12-Expenses!E12","css":"text_end bold blue_background","format":"currency","cell":"F12"}],"cols":[{"width":120},{"width":120},{"width":120},{"width":120},{"width":120},{"width":120}],"rows":[{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32}],"merged":[]},{"name":"Income","id":"u1747520260841","data":[{"value":"Jan","css":"text_end bold","format":"common","cell":"B1"},{"value":"Feb","css":"text_end bold","format":"common","cell":"C1"},{"value":"Mar","css":"text_end bold","format":"common","cell":"D1"},{"value":"Apr","css":"text_end bold","format":"common","cell":"E1"},{"value":"May","css":"text_end bold","format":"common","cell":"F1"},{"value":"Jun","css":"text_end bold","format":"common","cell":"G1"},{"value":"Jul","css":"text_end bold","format":"common","cell":"H1"},{"value":"Aug","css":"text_end bold","format":"common","cell":"I1"},{"value":"Sep","css":"text_end bold","format":"common","cell":"J1"},{"value":"Oct","css":"text_end bold","format":"common","cell":"K1"},{"value":"Nov","css":"text_end bold","format":"common","cell":"L1"},{"value":"Dec","css":"text_end bold","format":"common","cell":"M1"},{"value":"Total","css":"text_end bold","format":"common","cell":"N1"},{"value":"Drone","format":"common","css":"bold","cell":"A2"},{"value":500000,"css":"text_end","format":"currency","cell":"B2"},{"value":200000,"css":"text_end","format":"currency","cell":"C2"},{"value":700000,"css":"text_end","format":"currency","cell":"D2"},{"value":750000,"css":"text_end","format":"currency","cell":"E2"},{"value":600000,"css":"text_end","format":"currency","cell":"F2"},{"value":800000,"css":"text_end","format":"currency","cell":"G2"},{"value":500000,"css":"text_end","format":"currency","cell":"H2"},{"value":100000,"css":"text_end","format":"currency","cell":"I2"},{"value":750000,"css":"text_end","format":"currency","cell":"J2"},{"value":900000,"css":"text_end","format":"currency","cell":"K2"},{"value":200000,"css":"text_end","format":"currency","cell":"L2"},{"value":950000,"css":"text_end","format":"currency","cell":"M2"},{"value":"=SUM(B2:M2)","css":"text_end","format":"currency","cell":"N2"},{"value":"VR Glasses","css":"bold","format":"common","cell":"A3"},{"value":80000,"css":"text_end","format":"currency","cell":"B3"},{"value":75000,"css":"text_end","format":"currency","cell":"C3"},{"value":90000,"css":"text_end","format":"currency","cell":"D3"},{"value":100000,"css":"text_end","format":"currency","cell":"E3"},{"value":95000,"css":"text_end","format":"currency","cell":"F3"},{"value":105000,"css":"text_end","format":"currency","cell":"G3"},{"value":102500,"css":"text_end","format":"currency","cell":"H3"},{"value":75000,"css":"text_end","format":"currency","cell":"I3"},{"value":85000,"css":"text_end","format":"currency","cell":"J3"},{"value":90000,"css":"text_end","format":"currency","cell":"K3"},{"value":120000,"css":"text_end","format":"currency","cell":"L3"},{"value":112500,"css":"text_end","format":"currency","cell":"M3"},{"value":"=SUM(B3:M3)","css":"text_end","format":"currency","cell":"N3"},{"value":"Voice Remote Control","css":"bold","format":"common","cell":"A4"},{"value":20000,"css":"text_end","format":"currency","cell":"B4"},{"value":25000,"css":"text_end","format":"currency","cell":"C4"},{"value":26500,"css":"text_end","format":"currency","cell":"D4"},{"value":25750,"css":"text_end","format":"currency","cell":"E4"},{"value":23500,"css":"text_end","format":"currency","cell":"F4"},{"value":21000,"css":"text_end","format":"currency","cell":"G4"},{"value":21500,"css":"text_end","format":"currency","cell":"H4"},{"value":19800,"css":"text_end","format":"currency","cell":"I4"},{"value":21500,"css":"text_end","format":"currency","cell":"J4"},{"value":25000,"css":"text_end","format":"currency","cell":"K4"},{"value":27500,"css":"text_end","format":"currency","cell":"L4"},{"value":26000,"css":"text_end","format":"currency","cell":"M4"},{"value":"=SUM(B4:M4)","css":"text_end","format":"currency","cell":"N4"},{"value":"Touch Projector","css":"bold","format":"common","cell":"A5"},{"value":"=7000","css":"text_end","format":"currency","cell":"B5"},{"value":9250,"css":"text_end","format":"currency","cell":"C5"},{"value":8000,"css":"text_end","format":"currency","cell":"D5"},{"value":9000,"css":"text_end","format":"currency","cell":"E5"},{"value":10000,"css":"text_end","format":"currency","cell":"F5"},{"value":8500,"css":"text_end","format":"currency","cell":"G5"},{"value":9000,"css":"text_end","format":"currency","cell":"H5"},{"value":7200,"css":"text_end","format":"currency","cell":"I5"},{"value":9500,"css":"text_end","format":"currency","cell":"J5"},{"value":10000,"css":"text_end","format":"currency","cell":"K5"},{"value":11000,"css":"text_end","format":"currency","cell":"L5"},{"value":9000,"css":"text_end","format":"currency","cell":"M5"},{"value":"=SUM(B5:M5)","css":"text_end","format":"currency","cell":"N5"},{"value":"Smart Band","css":"bold","format":"common","cell":"A6"},{"value":50000,"css":"text_end","format":"currency","cell":"B6"},{"value":52000,"css":"text_end","format":"currency","cell":"C6"},{"value":53000,"css":"text_end","format":"currency","cell":"D6"},{"value":53500,"css":"text_end","format":"currency","cell":"E6"},{"value":52500,"css":"text_end","format":"currency","cell":"F6"},{"value":51000,"css":"text_end","format":"currency","cell":"G6"},{"value":50000,"css":"text_end","format":"currency","cell":"H6"},{"value":49000,"css":"text_end","format":"currency","cell":"I6"},{"value":51000,"css":"text_end","format":"currency","cell":"J6"},{"value":52500,"css":"text_end","format":"currency","cell":"K6"},{"value":56000,"css":"text_end","format":"currency","cell":"L6"},{"value":53000,"css":"text_end","format":"currency","cell":"M6"},{"value":"=SUM(B6:M6)","css":"text_end","format":"currency","cell":"N6"},{"value":"Video Doorbell","css":"bold","format":"common","cell":"A7"},{"value":18000,"css":"text_end","format":"currency","cell":"B7"},{"value":19500,"css":"text_end","format":"currency","cell":"C7"},{"value":20500,"css":"text_end","format":"currency","cell":"D7"},{"value":20250,"css":"text_end","format":"currency","cell":"E7"},{"value":20500,"css":"text_end","format":"currency","cell":"F7"},{"value":19000,"css":"text_end","format":"currency","cell":"G7"},{"value":18000,"css":"text_end","format":"currency","cell":"H7"},{"value":16500,"css":"text_end","format":"currency","cell":"I7"},{"value":17000,"css":"text_end","format":"currency","cell":"J7"},{"value":18500,"css":"text_end","format":"currency","cell":"K7"},{"value":19000,"css":"text_end","format":"currency","cell":"L7"},{"value":18500,"css":"text_end","format":"currency","cell":"M7"},{"value":"=SUM(B7:M7)","css":"text_end","format":"currency","cell":"N7"},{"value":"Smart TV","css":"bold","format":"common","cell":"A8"},{"value":310000,"css":"text_end","format":"currency","cell":"B8"},{"value":295000,"css":"text_end","format":"currency","cell":"C8"},{"value":315000,"css":"text_end","format":"currency","cell":"D8"},{"value":320000,"css":"text_end","format":"currency","cell":"E8"},{"value":295000,"css":"text_end","format":"currency","cell":"F8"},{"value":280000,"css":"text_end","format":"currency","cell":"G8"},{"value":290000,"css":"text_end","format":"currency","cell":"H8"},{"value":285000,"css":"text_end","format":"currency","cell":"I8"},{"value":300000,"css":"text_end","format":"currency","cell":"J8"},{"value":315000,"css":"text_end","format":"currency","cell":"K8"},{"value":340000,"css":"text_end","format":"currency","cell":"L8"},{"value":335000,"css":"text_end","format":"currency","cell":"M8"},{"value":"=SUM(B8:M8)","css":"text_end","format":"currency","cell":"N8"},{"value":"Robot Vacuum","css":"bold","format":"common","cell":"A9"},{"value":50000,"css":"text_end","format":"currency","cell":"B9"},{"value":55000,"css":"text_end","format":"currency","cell":"C9"},{"value":57500,"css":"text_end","format":"currency","cell":"D9"},{"value":60000,"css":"text_end","format":"currency","cell":"E9"},{"value":62500,"css":"text_end","format":"currency","cell":"F9"},{"value":57500,"css":"text_end","format":"currency","cell":"G9"},{"value":55000,"css":"text_end","format":"currency","cell":"H9"},{"value":50000,"css":"text_end","format":"currency","cell":"I9"},{"value":57500,"css":"text_end","format":"currency","cell":"J9"},{"value":60000,"css":"text_end","format":"currency","cell":"K9"},{"value":62500,"css":"text_end","format":"currency","cell":"L9"},{"value":65000,"css":"text_end","format":"currency","cell":"M9"},{"value":"=SUM(B9:M9)","css":"text_end","format":"currency","cell":"N9"},{"value":"Air Purifier","css":"bold","format":"common","cell":"A10"},{"value":15000,"css":"text_end","format":"currency","cell":"B10"},{"value":20000,"css":"text_end","format":"currency","cell":"C10"},{"value":25000,"css":"text_end","format":"currency","cell":"D10"},{"value":30000,"css":"text_end","format":"currency","cell":"E10"},{"value":25000,"css":"text_end","format":"currency","cell":"F10"},{"value":27500,"css":"text_end","format":"currency","cell":"G10"},{"value":25000,"css":"text_end","format":"currency","cell":"H10"},{"value":20000,"css":"text_end","format":"currency","cell":"I10"},{"value":25000,"css":"text_end","format":"currency","cell":"J10"},{"value":30000,"css":"text_end","format":"currency","cell":"K10"},{"value":27500,"css":"text_end","format":"currency","cell":"L10"},{"value":30000,"css":"text_end","format":"currency","cell":"M10"},{"value":"=SUM(B10:M10)","css":"text_end","format":"currency","cell":"N10"},{"value":"Baby Monitor","css":"bold","format":"common","cell":"A11"},{"value":25000,"css":"text_end","format":"currency","cell":"B11"},{"value":27000,"css":"text_end","format":"currency","cell":"C11"},{"value":27500,"css":"text_end","format":"currency","cell":"D11"},{"value":26500,"css":"text_end","format":"currency","cell":"E11"},{"value":28000,"css":"text_end","format":"currency","cell":"F11"},{"value":29000,"css":"text_end","format":"currency","cell":"G11"},{"value":29500,"css":"text_end","format":"currency","cell":"H11"},{"value":25000,"css":"text_end","format":"currency","cell":"I11"},{"value":26000,"css":"text_end","format":"currency","cell":"J11"},{"value":27000,"css":"text_end","format":"currency","cell":"K11"},{"value":27500,"css":"text_end","format":"currency","cell":"L11"},{"value":25000,"css":"text_end","format":"currency","cell":"M11"},{"value":"=SUM(B11:M11)","css":"text_end","format":"currency","cell":"N11"},{"value":"Total","format":"common","css":"bold","cell":"A12"},{"value":"=SUM(B2:B11)","css":"text_end bold","format":"currency","cell":"B12"},{"value":"=SUM(C2:C11)","css":"text_end bold","format":"currency","cell":"C12"},{"value":"=SUM(D2:D11)","css":"text_end bold","format":"currency","cell":"D12"},{"value":"=SUM(E2:E11)","css":"text_end bold","format":"currency","cell":"E12"},{"value":"=SUM(F2:F11)","css":"text_end bold","format":"currency","cell":"F12"},{"value":"=SUM(G2:G11)","css":"text_end bold","format":"currency","cell":"G12"},{"value":"=SUM(H2:H11)","css":"text_end bold","format":"currency","cell":"H12"},{"value":"=SUM(I2:I11)","css":"text_end bold","format":"currency","cell":"I12"},{"value":"=SUM(J2:J11)","css":"text_end bold","format":"currency","cell":"J12"},{"value":"=SUM(K2:K11)","css":"text_end bold","format":"currency","cell":"K12"},{"value":"=SUM(L2:L11)","css":"text_end bold","format":"currency","cell":"L12"},{"value":"=SUM(M2:M11)","css":"text_end bold","format":"currency","cell":"M12"},{"value":"=SUM(N2:N11)","css":"text_end bold","format":"currency","length":167,"cell":"N12"}],"cols":[{"width":120},{"width":120},{"width":120},{"width":120},{"width":120},{"width":120}],"rows":[{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32}],"merged":[]},{"name":"Expenses","id":"u1747520260842","data":[{"value":"PPC Campaigns","css":"text_end bold","format":"common","cell":"B1"},{"value":"Sponsored Articles","css":"text_end bold","format":"common","cell":"C1"},{"value":"SMM","css":"text_end bold","format":"common","cell":"D1"},{"value":"Total","format":"common","css":"bold text_end","cell":"E1"},{"value":"Drone","css":"bold","format":"common","cell":"A2"},{"value":150000,"css":"text_end","format":"currency","cell":"B2"},{"value":25000,"css":"text_end","format":"currency","cell":"C2"},{"value":150000,"css":"text_end","format":"currency","cell":"D2"},{"value":"=SUM(B2:D2)","css":"text_end","format":"currency","cell":"E2"},{"value":"VR Glasses","css":"bold","format":"common","cell":"A3"},{"value":40000,"css":"text_end","format":"currency","cell":"B3"},{"value":0,"css":"text_end","format":"currency","cell":"C3"},{"value":15000,"css":"text_end","format":"currency","cell":"D3"},{"value":"=SUM(B3:D3)","css":"text_end","format":"currency","cell":"E3"},{"value":"Voice Remote, Control","format":"common","css":"bold","cell":"A4"},{"value":25000,"css":"text_end","format":"currency","cell":"B4"},{"value":5000,"css":"text_end","format":"currency","cell":"C4"},{"value":5000,"css":"text_end","format":"currency","cell":"D4"},{"value":"=SUM(B4:D4)","css":"text_end","format":"currency","cell":"E4"},{"value":"Touch Projector","format":"common","css":"bold","cell":"A5"},{"value":105500,"css":"text_end","format":"currency","cell":"B5"},{"value":3500,"css":"text_end","format":"currency","cell":"C5"},{"value":3000,"css":"text_end","format":"currency","cell":"D5"},{"value":"=SUM(B5:D5)","css":"text_end","format":"currency","cell":"E5"},{"value":"Smart Band","format":"common","css":"bold","cell":"A6"},{"value":50000,"css":"text_end","format":"currency","cell":"B6"},{"value":0,"css":"text_end","format":"currency","cell":"C6"},{"value":5000,"css":"text_end","format":"currency","cell":"D6"},{"value":"=SUM(B6:D6)","css":"text_end","format":"currency","cell":"E6"},{"value":"Video Doorbell","format":"common","css":"bold","cell":"A7"},{"value":5000,"css":"text_end","format":"currency","cell":"B7"},{"value":5000,"css":"text_end","format":"currency","cell":"C7"},{"value":500,"css":"text_end","format":"currency","cell":"D7"},{"value":"=SUM(B7:D7)","css":"text_end","format":"currency","cell":"E7"},{"value":"Smart TV","format":"common","css":"bold","cell":"A8"},{"value":50000,"css":"text_end","format":"currency","cell":"B8"},{"value":30000,"css":"text_end","format":"currency","cell":"C8"},{"value":10000,"css":"text_end","format":"currency","cell":"D8"},{"value":"=SUM(B8:D8)","css":"text_end","format":"currency","cell":"E8"},{"value":"Robot Vacuum","format":"common","css":"bold","cell":"A9"},{"value":50000,"css":"text_end","format":"currency","cell":"B9"},{"value":0,"css":"text_end","format":"currency","cell":"C9"},{"value":5000,"css":"text_end","format":"currency","cell":"D9"},{"value":"=SUM(B9:D9)","css":"text_end","format":"currency","cell":"E9"},{"value":"Air Purifier","format":"common","css":"bold","cell":"A10"},{"value":30000,"css":"text_end","format":"currency","cell":"B10"},{"value":10000,"css":"text_end","format":"currency","cell":"C10"},{"value":50000,"css":"text_end","format":"currency","cell":"D10"},{"value":"=SUM(B10:D10)","css":"text_end","format":"currency","cell":"E10"},{"value":"Baby Monitor","format":"common","css":"bold","cell":"A11"},{"value":80000,"css":"text_end","format":"currency","cell":"B11"},{"value":5000,"css":"text_end","format":"currency","cell":"C11"},{"value":2500,"css":"text_end","format":"currency","cell":"D11"},{"value":"=SUM(B11:D11)","css":"text_end","format":"currency","cell":"E11"},{"value":"Total","format":"common","css":"bold","cell":"A12"},{"value":"=SUM(B2:B11)","css":"text_end bold","format":"currency","cell":"B12"},{"value":"=SUM(C2:C11)","css":"text_end bold","format":"currency","cell":"C12"},{"value":"=SUM(D2:D11)","css":"text_end bold","format":"currency","cell":"D12"},{"value":"=SUM(E2:E11)","css":"bold text_end","format":"currency","cell":"E12"}],"cols":[{"width":120},{"width":120},{"width":120},{"width":120},{"width":120},{"width":120}],"rows":[{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32},{"height":32}],"merged":[]}],"styles":{"text_end":{"white-space":"nowrap","justify-content":"flex-end","text-align":"right"},"bold":{"white-space":"nowrap","font-weight":"bold"},"red_text":{"white-space":"nowrap","color":"#E94633"},"blue_background":{"white-space":"nowrap","background":"#B3E5FC"}},"formats":[{"name":"Common","id":"common","mask":"#,##0.","example":"15.0031"},{"name":"Number","id":"number","mask":"#,##0.00","example":"15.0031"},{"name":"Percent","id":"percent","mask":"#,##0.00%","example":"15.0031"},{"name":"U.S. Dollar","id":"currency","mask":"$#,##0.00"},{"name":"Date","id":"date","mask":"dd/mm/yyyy","example":"44490.5625"},{"name":"Time","id":"time","mask":"h:mm am/pm","example":"44490.5625","timeFormat":12},{"name":"Text","id":"text","mask":"@","example":"some text"},{"name":"Euro","id":"euro","mask":"[$€]#,##0.00"},{"name":"Swiss franc","id":"franc","mask":"[$CHF]#,##0.00"}]}];
  spreadsheet.parse(_jsonBasicData);
}


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