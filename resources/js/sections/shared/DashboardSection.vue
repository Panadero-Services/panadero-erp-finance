<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue';
import { moduleName, moduleVersion, moduleGit } from "panadero-mood";

import { Kanban, template, Toolbar } from "@dhx/kanban";
import "@dhx/kanban/dist/kanban.css";
import "@dhx/kanban/assets/demos.css";

import {
  getData,
  notFoundMood,
  defaultMood,
  defaultMood1,
  defaultMood2,
  emptyMoodSet
} from "@/panadero/panadero-mood/constants/mood-data.js";

const emit = defineEmits(['kill', 'wrench']);

const props = defineProps({
  contract: Object,
  set: Object,
  db: Object
});

console.log("Panadero Mood Init", { moduleName, moduleVersion, moduleGit });

const _title = "Mood";
const pulse = inject("pulse");
const boardRef = ref(null);
const barRef = ref(null);

const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 dark:hover:ring-indigo-400";

// ---------------------
// Helpers
// ---------------------
const projectPath = computed(() =>
  `${props.set.domain}.${props.set.project.title}.${props.set.project.environment}.${props.set.project.category}`
);

const parseDates = (task) => {
  const reformat = (date) =>
    new Date(
      date.substring(3, 6) + date.substring(0, 3) + date.substring(6)
    );

  task.start_date = reformat(task.start_date);
  task.end_date = reformat(task.end_date);
  return task;
};

const transformCard = (card) => {
  card.progress *= 100;
  card.column =
    card.progress < 1
      ? "idle"
      : card.progress < 10
      ? "pending"
      : card.progress < 99
      ? "started"
      : "done";

  card.color = card.type === "project" ? "#58C3FE" : card.type === "milestone" ? "#F1B941" : "";

  card.label = card.text;
  card.users = (card.owner || []).map((x) => parseInt(x.resource_id));
  return parseDates(card);
};

// ---------------------
// Core Actions
// ---------------------

const _save = async () => {
  if (props.set.project.id > 0) {
    const _board = await boardRef.value.serialize();
    const payload = {
      model: "StateDataset",
      type: props.set.projectType,
      path: projectPath.value,
      projectId: props.set.project.id,
      json: JSON.stringify(_board),
      isActive: 1
    };
    await props.db.setState(payload);
  }
};

const _load = async () => {
  if (props.set.project.id > 0) {
    const data = await props.db.getState(
      props.set.projectType,
      projectPath.value,
      props.set.project.id
    );
    boardRef.value.parse(data ? JSON.parse(data) : notFoundMood);
  }
};

const _loadPlan = async () => {
  if (props.set.project.id > 0) {
    const data = await props.db.getState("resourcePlanning", projectPath.value, props.set.project.id);
    if (data) {
      const parsed = JSON.parse(data.replace('"data"', '"cards"'));
      parsed.cards = parsed.cards.map(transformCard);
      boardRef.value.parse(parsed);
    }
  }
};

const _clear = () => boardRef.value.parse(emptyMoodSet);
const _sample1 = () => boardRef.value.parse(defaultMood);
const _sample2 = () => boardRef.value.parse(defaultMood1);
const _sample3 = () => boardRef.value.parse(defaultMood2);

// ---------------------
// Theme Management
// ---------------------

const changeTheme = (theme) => {
  boardRef.value.setTheme({ name: theme });
  barRef.value.setConfig({ theme });
};

const _setTheme = (theme, dark) => {
  props.set.dark = dark;
  changeTheme(theme);
};

// ---------------------
// Init + Teardown
// ---------------------

onMounted(async () => {
  await props.set.initMM();
  await props.set.initialize();
  await props.set.setProjectType("mood");

  const { columns, cards, cardShape, users: defaultUsers } = getData();

  // Load team users
  const teamData = await props.db.getState("team", `${props.set.domain}.${props.set.project.title}.${props.set.project.environment}`, props.set.project.id);
  if (teamData) {
    cardShape.users.values = JSON.parse(teamData.replaceAll("text", "label"));
  }

  boardRef.value = new Kanban("#root", {
    columns,
    cards,
    cardShape,
    //cardTemplate: template(card => cardTemplate(card)),
    editorShape
    //,cardHeight: 200
  });

  barRef.value = new Toolbar("#toolbar", {
    api: boardRef.value.api,
    theme: props.set.dark ? "willow-dark" : "willow"
  });

  // Hook events
  loadEvents();
});

onUnmounted(() => {
  boardRef.value?.destructor();
  barRef.value?.destructor();
});

const refreshPage = () => window.location.reload();

const loadEvents = () => {
  // Example: intercept events
  // boardRef.value.api.intercept("move-card", ({ id, columnId }) => { return columnId !== "done"; });
};

// ---------------------
// UI Config
// ---------------------

const editorShape = [/* same as before */];
const cardTemplate = ({ cardFields, selected }) => {
  const { label, color, id } = cardFields;
  return `
    <div class="custom-card" style="padding: 20px">
      <div class="status-color" style="background: ${color}"></div>
      <div data-menu-id="${id}">
        <i class="wxi-dots-v"></i>
      </div>
      ${selected ? "Selected:" : ""}${label}
    </div>`;
};
</script>

<template>

<div class="m-0 bg-white">   
    <div class="grid grid-cols-2 bg-white">     
        <div class="pl-2 bg-white" :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
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
        <div id="root" class="col-span-2 mt-2"></div>
        <div id="whatever" class=""></div>
    </div>
</div>

</template>

<!-- custom styles -->
<style>


    .wx-willow-theme{
        --wx-kanban-content-background: #000;
    }


    .wx-panaderos-theme {
    --wx-field-width: 100%;
    --wx-theme-name: panaderos;
        --wx-input-background: #fff;
        --wx-kanban-column-width: 200px;
        --wx-background: #fff;
        --wx-kanban-background: #fff;
        --wx-kanban-card-border: 1px solid #ddd;
        --wx-kanban-content-background: #fff;

    }
</style>
