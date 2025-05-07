<script setup>
import {computed, onMounted, onUnmounted, ref, inject, watch} from 'vue';
import { moduleName, moduleVersion, panaderoResourcePlanning, _resourceData, _ppl, _links } from "panadero-resourceplanning";

import { getData } from "@/panaderos/panadero-resourceplanning/constants/planning-data.js";
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'



const {resources, resourceData, links, colors, defaultColor, taskScaleColor, weekendColor, darkWeekendColor } =getData();

// define emits
const emit = defineEmits(['kill', 'wrench']);

// call parameters
const props = defineProps({
    contract: Object,
    set: Object,
    db: Object,
    pulse: Boolean
});

// globals
const _title="ResourcePlanningSandbox";

const _counter=ref(0);

let planning = new panaderoResourcePlanning("resource_id");

onMounted(async ()=> {
    await planning.config();
    //await planning.configColumns();
    var _resource_columns = await planning.configResourceColumns();
    await planning.cssMarker();
    await planning.drawHourCircles();
    await planning.lightBox();
    await planning.configLayout(_resource_columns);

    await props.set.setProjectType('resourcePlanning');

    // plugins
    await planning.fullScreenPlugin();
    await planning.markerPlugin();
    await planning.groupingTasksPlugin();

    await planning.init(props.set.dark);

    await _calendarWrappers();

})

// button FullScreen
const _fullScreen = async () => {
    await planning.fullscreen();
}

const show = async () => {
    console.log('show');

    dhx.alert({
        header: "i3 Alert Box",
        text: "With the help of i3 components you are able to create versatile user interfaces for web apps of any kind. Modern technologies and design make our library a great tool for developers working on the most complex projects. And here i3Message represents a small but indispensable toolkit of helpers for initializing all types of messages: notifications, alert and confirmation boxes, and tooltips. Try out the abilities of dhtmlxMessage in our interactive demos and samples.",
        buttonsAlignment: "center",
        buttons: ["ok"],
    });
}

// pulseObject .. reload... 
const _pulse = inject('pulse')
const _pulseCount = ref(0);

watch(_pulse, (_new, _old) => {
    if(_pulseCount.value++ == 120) {
        _doRefresh();
    };
});

const _doRefresh = async () => {
    _pulseCount.value=0;
    console.log("_doRefresh");
    //await _reset();
    await _load();
}

// themes
const _themes = ['meadow', 'dark', 'skyblue', 'broadway', 'material', 'contrast-white', 'contrast-black', 'contrast-rm'];
const _darkThemes = ['dark','contrast-black', 'contrast-rm'];
const _theme = ref('meadow');

// timeFrames
const _timeFrame = ref('week');
const _timeFrames = ['day', 'week', 'month', 'quarter', 'year'];

// routines
const color = ref(defaultColor);
const _ChangeColor = async () => {
// button Color
    _colorCount.value++;
    if (_colorCount.value >= colors.length) _colorCount.value=0;
    color.value = colors[_colorCount.value];
}

const _Shuffle = async () => {
    //shuffleArray(resourceData);
    await planning.config();
    await planning.configColumns();
    await planning.init(props.set.dark);
}

const shuffleArray = async (array) => {
    for (let i = array.length - 1; i >= 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}



const _path = () => { return props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category; }

const _Load = async () => {
  if(props.set.project.id > 0) {
    //const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
    const _type = props.set.projectType;
    const _projectId = props.set.project.id;
    //console.log(_path);
    let _resourcePlanning = await props.db.getState(_type, _path(), _projectId);
    let _resPlan = await JSON.parse(_resourcePlanning);
    //console.log(_resPlan.data, _resPlan.links);

    const _teamPath = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment;
    const _team = await props.db.getState("team", _teamPath, props.set.project.id);
    console.log('_team');
    console.log(_team);

    const _resources = _team==null ? resources : JSON.parse(_team);

    await planning.parseResourcesStore(_resources);
    await planning.parse(_resPlan.data, _resPlan.links);
    await planning.today();
    }
}

const _Save = async () => {
    if(props.set.project.id > 0) {
        //const _path = props.set.domain+"."+props.set.project.title+"."+props.set.project.environment+"."+props.set.project.category;
        let _resourcePlanning =  await planning.serialize();

        let _payload = {  "model": "StateDataset",
                          "type": props.set.projectType,
                          "path": _path(),
                          "projectId": props.set.project.id,
                          "json" : JSON.stringify(_resourcePlanning),
                          "isActive": 1
                         };
        await props.db.setState(_payload);
        await planning.message({ text: "Saved Environment", expire: 0 });
    }
}


const _ChangeTimeFrame = async () => {
    planning.setLevel(_timeFrame.value);
}

const _ChangeTheme = async () =>{
    planning.setSkin(_theme.value); 
    props.set.dark = _darkThemes.includes(_theme.value);
}

// actions
const _reset = async () => { await planning.reset(props.set.dark); }
const _refresh = async () => { await planning.refresh(); }
const _load = async () => { await _Load() }
const _save = async () => { await _Save() }
const _shuffle = async () => { await _Shuffle() }
const _changeColor = async () => { await _ChangeColor() }
const _changeTheme = async () => { await _ChangeTheme() }
const _changeTimeFrame = async () => { _ChangeTimeFrame(); }
const _addMarker = async () => { 

    console.log(_calendar.getValue(true));
    console.log(_timepicker.getValue());
    await planning.addMarker(_calendar.getValue(true)) 
}
const _popupShow = async () => {
console.log("show Popup")
 _popup.show("show"); 

}


// external access
defineExpose({ _load, _save });

// css
const _button = "my-1 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-1 md:p-3 lg:p-4 shadow-sm hover:border-gray-400";


// calendar 
var _popup, _calendar, _timepicker ;

const _calendarWrappers = async () =>{
    _calendar = new dhx.Calendar();
    _popup = new dhx.Popup({ css: "dhx_widget--bordered"});
    _timepicker = new dhx.Timepicker();

    _popup.attach(_timepicker);
    _popup.attach(_calendar);

}



</script>

<template>
<div class="m-0">   
    <div class="grid grid-cols-4">     
        <div class="flex pl-2 col-span-3 mb-1 " :class="set.dark ? 'wx-willow-dark-theme' : 'wx-willow-theme'">
            <button @click="_reset" type="button" :class="_button">Reset</button>
            <button @click="_load" type="button" :class="_button">Load</button>
            <button @click="_save" type="button" :class="_button">Save</button>
            <button @click="_fullScreen" type="button" :class="_button">FullScreen</button>


            <select v-model="_timeFrame" @change="_changeTimeFrame"  class="dark:bg-gray-900 bg-gray-100 border-0" :class="_button">
              <option disabled value="">select timeFrame</option>
              <option v-for="_t in _timeFrames">{{_t}}</option>
            </select>

            <select v-model="_theme" @change="_changeTheme"  class="dark:bg-gray-900 bg-gray-100 border-0" :class="_button">
              <option disabled value="">select theme</option>
              <option v-for="_t in _themes">{{_t}}</option>
            </select>
            <button @click="_popupShow" type="button" :class="_button">Date</button>
            <button @click="_addMarker" type="button" :class="_button">AddMeeting</button>
            <div id="show"></div>


        </div>    
        <div class="items-end text-right mr-6" id="toolbar">
            <div  class="col-span-1 text-xs mt-4 ">
                <span>{{moduleName}}</span>
                <span class="ml-2" :class="_pulse? 'text-green-500' : ''">{{moduleVersion}}</span>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2" :class="set.layout.sidebar ? 'pr-48' : 'pr-4'">     
        <div id="resource_id" class="col-span-2 h-screen"></div>
    </div>
</div>
</template>
<style scoped>
.calendars-wrapper {
  display: flex;
  height: fit-content;
  flex-direction: row;
  gap: 12px;
  justify-content: space-between;
}
</style>
