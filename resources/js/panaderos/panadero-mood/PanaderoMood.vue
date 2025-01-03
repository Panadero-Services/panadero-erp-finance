<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import { moduleName, moduleVersion, moduleGit, panaderoMood} from "panadero-mood";

// kanban
// moved package back to root folder
// now exists in two folder 1.root 2./panadero-mood/vendor
import { Kanban, Toolbar, defaultEditorShape } from "@dhx/trial-kanban";
import "@dhx/trial-kanban/dist/kanban.css";

//import { getData } from "@dhx/trial-kanban/demos/common/data.js";
import { getData } from "@/constants/mood-data.js";
import "@dhx/trial-kanban/assets/demos.css";

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
    db: Object,
    pulse: Boolean
});

// globals
const _title="Moodz";

// mood define
const data = [
  { project: "Real Estate", owner: "Sep Bakker", start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: 92, cost: 3588, budget: 11768, balance: 8180, paid: true, access: "1, 2, 4", renewals: "1-2 times", project_id: 9969 },
  { project: "HR Application", owner: "Anja Marcus", start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: 340, cost: 15980, budget: 18856, balance: 2876, paid: true, access: "4, 5", renewals: "1 time", project_id: 1342 },
  { project: "HR Application", owner: "Sir Kroesdalen", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: 484, cost: 21296, budget: 14907, balance: -6389, paid: false, access: "3, 1",renewals: "1 time",project_id: 1110 },
  { project: "Inventory", owner: "Damien Rice", start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: 345, cost: 14835, budget: 70911, balance: 56076, paid: false, access: "2, 4, 5",renewals: "1-2 times",project_id: 6789 },
  { project: "Inventory",owner: "Bart Spronkel", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: 57, cost: 2052, budget: 5068, balance: 3016, paid: true, access: "1, 2, 4, 5",renewals: "1-2 times",project_id: 4332 },
  { project: "RentMagic", owner: "Eric Vermouth", start_date: "Fri Jun 02 2024 00:00:00", end_date: "Mon Jan 06 2024 00:00:00", status: "Done", hours: 211, cost: 8229, budget: 16540, balance: 8311, paid: false, access: "3, 5", renewals: "more than 5 times",project_id: 3562 },
  { project: "RentMagic", owner: "Mark Timmer", start_date: "Thu Jun 05 2025 00:00:00", end_date: "Mon Mar 07 2025 00:00:00", status: "In Progress",  hours: 3, cost: 144, budget: 122, balance: -22, paid: true, access: "3, 2", renewals: "1 time", project_id: 3421 },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Feb 04 2025 00:00:00", end_date: "Tue Mar 08 2025 00:00:00", status: "In Progress", hours: 76, cost: 3496, budget: 12515, balance: 9019, paid: true, access: "1, 5", renewals: "more than 5 times", project_id: 2567 },
  { project: "RentMagic", owner: generateName(), start_date: "Thu Jun 05 2025 00:00:00", end_date: "Mon Mar 07 2025 00:00:00", status: "In Progress", hours: 3, cost: 144, budget: 122, balance: -22, paid: true, access: "3, 2", renewals: "1 time", project_id: 3421 },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Feb 04 2025 00:00:00", end_date: "Tue Mar 08 2025 00:00:00", status: "In Progress", hours: 76, cost: 3496, budget: 12515, balance: 9019, paid: true, access: "1, 5", renewals: "more than 5 times", project_id: 2567 },
  { project: "Real Estate", owner: generateName(), start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: 92, cost: 3588, budget: 11768, balance: 8180, paid: true, access: "1, 2, 4", renewals: "1-2 times", project_id: 9969 },
  { project: "HR Application", owner: generateName(), start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: 340, cost: 15980, budget: 18856, balance: 2876, paid: true, access: "4, 5", renewals: "1 time", project_id: 1342 },
  { project: "HR Application", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: 484, cost: 21296, budget: 14907, balance: -6389, paid: false, access: "3, 1", renewals: "1 time", project_id: 1110 },
  { project: "Inventory", owner: generateName(), owner: "Damien Rice", start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: 345, cost: 14835, budget: 70911, balance: 56076, paid: false, access: "2, 4, 5", renewals: "1-2 times", project_id: 6789 },
  { project: "Inventory", owner: "Dick Nimm", start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: 57, cost: 2052, budget: 5068, balance: 3016, paid: true, access: "1, 2, 4, 5", renewals: "1-2 times", project_id: 4332 },
  { project: "RentMagic", owner: generateName(), start_date: "Fri Jun 02 2024 00:00:00", end_date: "Mon Jan 06 2024 00:00:00", status: "Done", hours: 211, cost: 8229, budget: 16540, balance: 8311, paid: false, access: "3, 5", renewals: "more than 5 times", project_id: 3562 },
  { project: "Real Estate", owner: generateName(), start_date: "Fri Feb 02 2024 00:00:00", end_date: "Wed Jun 05 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "HR Application", owner: generateName(), start_date: "Sun Mar 03 2024 00:00:00", end_date: "Sat Feb 07 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "HR Application", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Wed Jan 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "Inventory", owner: generateName(), start_date: "Tue Jan 01 2024 00:00:00", end_date: "Sun Jun 11 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "Inventory", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
  { project: "RentMagic", owner: generateName(), start_date: "Mon Jan 01 2024 00:00:00", end_date: "Fri Feb 09 2024 00:00:00", status: "Done", hours: Math.floor(Math.random() * 150), cost: Math.floor(Math.random() * 5000), budget: Math.floor(Math.random() * 100000), balance: Math.floor(Math.random() * 10000), paid: true, access: Math.ceil(Math.random() * 4), renewals: "1-2 times", project_id: Math.floor(Math.random() * 5000) },
];

const users = [
  { id: "0", value: "Mick", ava: "01.jpg" },
  { id: "1", value: "Lara", ava: "01.jpg" },
  { id: "2", value: "Peter", ava: "02.jpg" },
  { id: "3", value: "Minnie", ava: "03.jpg" },
  { id: "4", value: "Dirk", ava: "04.jpg" },
  { id: "5", value: "Henk", color: "#61C874" }
];

const srcPhoto = "https://snippet.dhtmlx.com/codebase/data/common/img/02/avatar_";

function generateName(){
  let f = ["Kai", "Louis", "Bob", "Robin", "Luka", "Eliana", "Jaden", "Ezra", "Luca", "Rowan", "Nova", "Amara", "Aaliyah", "Finn", "Lieuwe", "Mark", "Eric", "Eric", "Vince", "Inge", "Laura", "Linda", "Peter", "Mick", "Sofia", "Fleur", "Tessa", "Ray", "Raymond", "Toffie", "Eppie", "Mickey", "Piet", "Jan", "Mick", "Gopher", "Don", "Karel", "Albert", "Pieter"];
  let l= ["Smith", "Johnson", "Williams", "Brown", "Jones", "Garcia", "Miller", "Davis", "Martinez", "Wilson", "Bakker", "Kroesbergen", "Wilson", "Nijssen", "Swanders", "Jansen", "Janssen", "Bond", "Garner", "van Basten", "Bergkamp", "Ali", "Hemyatahar", "Reagan", "van Kinsbergen", "van Brakel", "Alyeson"];
  let rf = Math.floor(Math.random()*f.length);
  let rl = Math.floor(Math.random()*l.length);
  return f[rf]+" "+l[rl];
}

const _add = () => {}
const _delete = () => {}

/// remove this section to store!!!
const _save = async () => {
  if(props.set.project.id > 0){
    let _board =  await board.serialize();
    let _payload = {  "model": "StateDataset",
                      "type": props.set.projectType,
                      "path": props.set.project.environment+"."+props.set.project.category,
                      "projectId": props.set.project.id,
                      "json" : JSON.stringify(_board),
                      "isActive": 1
                     };
    await props.db.setState(_payload);
  }
}

/// move this section also to store!!!
const _load = async () => {
  if(props.set.project.id > 0){

    let _type = props.set.projectType;
    let _path = props.set.project.environment+"."+props.set.project.category;
    let _projectId = props.set.project.id;

    let _board = await props.db.getState(_type, _path, _projectId);
    let _jsonBoard = await JSON.parse(_board);
    console.log(_jsonBoard);

    board.parse(_jsonBoard);
  }
}

let board, bar;

// webhooks
onMounted(async ()=> {
  await props.set.initMM();
  await props.set.initialize();
  await props.set.setProjectType('mood');

  const { columns, cards, users, cardShape } = getData();   

  board = new Kanban("#root", { columns, cards, cardShape, theme: { name: props.set.dark ? "willow-dark" : "willow" }, });
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

const pulse = ref(false);

defineExpose({
  _load, _save, changeTheme, _add, _delete
});

</script>
<template>

  <div class="m-0">   
    <div  class="" id="root"></div>
    <div id="whatever" class="max-w-9xl"></div>
  </div>

</template>

<!-- custom styles -->
<style>
</style>