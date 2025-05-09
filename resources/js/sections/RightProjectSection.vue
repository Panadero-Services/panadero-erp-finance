<script setup>
import { ref,  onMounted, computed } from 'vue'
import RightSectionLayout from '@/sections/layouts/RightSectionLayout.vue';
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline'
import UserModeSection from '@/sections/user-section/UserModeSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';
const _db = useDbStore();
const _set = useSettingsStore();

const props = defineProps({
    user: Object
});

const _projects = ref([]);
const _actualProject= ref(_set.project.title || "none");
const _actualEnvironment= ref(_set.project.environment || "none");
const newProjectName = ref('New');

//  'open' command
const open = ref(false)
defineExpose({ open });

const stats = computed(() => {
return [
  { label: 'Founded', value: '2025' },
  { label: 'Members', value: 0 },
  { label: 'Teams', value: '12' },
  { label: 'Projects', value: _projects.value.length },
]
});

onMounted(async ()=> {
   console.log('lets do it !');
   _projects.value = await _db.get('Project');
  //_actualProject.value = await _project.getProjectFromDb(1);
});

const _setEnvironment = (_env) => {
        _set.project.environment = _env;
        _actualEnvironment.value = _env;
}


const _setProject = (_title) => {
    if(!( usePage().props.auth.user == null)) {
        // toDo !! retrieve from db
        _set.project.id = _title=='none' ? 0 : 1;
        _set.project.title = _title;
        _set.project.validEnvironments = ["master", "alternative", "sandbox"];
        _set.project.environment = "master";
        _set.project.category = "primera";
         _actualProject.value=_title;
    }
}

const _insertProject = async () => {
   const _model = "Project";
   const _payload = {   "model": "Project",
                        "title" : newProjectName.value, 
                        "description" : "created by RightProjectSection.vue", 
                        "json" : '{"key":"value", "user":"JaW"}',
                        "user_id" : 12, 
                        "is_active" : 1
                  };
   const _result = await _db.insertProject(_model, _payload);
   _projects.value = await _db.get('Project');
}

const _cancel = async () => {
  open.value=false;
}



const _redRing = computed(()=>{
   return _set.mode.dev ? "hover:ring-slate-600 hover:bg-red-700 hover:text-white" : ""
});

const _validInputProject = computed(()=>{
   if (_projects.value.find(e => e.title.toLowerCase() === newProjectName.value.toLowerCase())) return false;
   return newProjectName.value.length>=4 && 
         newProjectName.value.length<=18 && 
         Boolean(newProjectName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputProject = computed(()=>{
   let _inputCss = "p-1 bg-slate-50 dark:bg-gray-900 border-0 ring-0 ";
   _inputCss +=  _validInputProject.value ?  _validRing : _inValidRing;
   return _inputCss;
});

// Call the function (note `.value` is needed)
/*
const callback = async (_fname, _arguments="") => {
   console.log('callback', _fname);
   function Default() {
     console.log('Hello from Defaultgreet!');
   }
   function Cancel() {
     console.log('Hello from Cancelgreet!');
   }
   function cancel() {
     console.log('Hello from cancelgreet!');
   }
   const functionMap = {
     cancel, Cancel, Default
   };
   const funcName =  _fname;
   // Call the function using its name string
   functionMap[funcName](); // Output: Hello from greet!
}
*/

const userId = ref('uid'+usePage().props.auth.user.id);

const _default = async () => {
   const _path = _set.domain+".userId."+usePage().props.auth.user.id;

   const _defaultPath = { "domain": _set.domain, 
                        "project" : _set.project.title,
                        "environment" : _set.project.environment,
                        "category" : _set.project.category
                  };
   const _payload = {  "model": "Project",
                      "type": "defaultProject",
                      "projectId": 0,
                      "path": _path,
                      "json" : JSON.stringify(_defaultPath),
                      "isActive": 1
                     };
    await _db.setState(_payload);


   //_msgLine = ref({_txt: "", _css:""});
   _msgLine.value = await {_txt: "Set default for : "+_path, _css:"bg-indigo-400 text-white"};
   setTimeout(() => { _msgLine.value = {_txt: "", _css:""}; }, 5000);

}

const _pushButton = async (_fname, _arguments="") => {
   console.log('_pushButton', _fname);

   function Cancel() { _cancel(); }
   function Default() { _default(); }
   const functionMap = {
     Cancel, Default
   };
   const funcName =  _fname;
   // Call the function using its name string
   functionMap[funcName](); // Output: Hello from greet!
}

const _buttons = computed( () => { return [
   { active:0, name:""},   //1
   { active:0, name:""},   //2
   { active:_set.mode.full, name:"Default", css:""}, //3
   { active:0, name:""}, //4
   { active:0, name:"", css:""},  //5
   { active:1, name:"Cancel", css:""},  //6
]});

const _categories = ['primera','segundo','tercera', userId.value ];

const switchCat = async (_a) => { _set.project.category = _categories[_a];}

const _button1 = "m-1 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset min-w-6";

const _switchSelected = "ring-indigo-200 dark:ring-gray-600 text-indigo-400 dark:text-gray-300 bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300";
const _switchUnSelected = "ring-gray-200 dark:ring-gray-600 text-gray-400 dark:text-gray-500 dark:hover:ring-indigo-400 hover:ring-indigo-300 hover:text-gray-400";

// css
const _button = { active: "rounded px-2 py-1 text-sm ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400", 
                  inactive: "rounded px-2 py-1 text-sm ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800" 
               };

const _font = { title:"text-3xl", 
                subtitle:"text-lg", 
                base:"text-sm", 
                compact: "text-xs"
               };

const _color = { accent: "text-indigo-600 dark:text-indigo-300", 
                 active: "text-gray-600 dark:text-gray-400", 
                 inactive: "text-gray-400 dark:text-gray-600", 
                 stats: "text-green-600 dark:text-green-400"
               };

const _label = {  active: "m-1 inline-flex items-center rounded-md bg-indigo-500/10 px-2 py-1 text-xs font-medium text-indigo-800 dark:text-indigo-400 ring-1 ring-inset ring-indigo-600/20", 
                  inactive: "m-1 inline-flex items-center rounded-md bg-gray-500/10 px-2 py-1 text-xs font-medium text-gray-800 dark:text-gray-400 ring-1 ring-inset ring-gray-500/20",
                  hover: "hover:ring-indigo-600 "
               };
const _activeLabel = 'ring-gray-500 text-indigo-700 dark:text-indigo-300 bg-indigo-100';
const _inActiveLabel = 'bg-blue-600/10 ring-gray-500/20 text-gray-800 dark:text-gray-400';
const _icon = " w-4 h-4 mr-2 ";

const _validRing = 'text-green-600';
const _inValidRing = 'text-red-700';

const _theme = computed(() => {
  return _set.dark ? "bg-indigo-950" : "bg-slate-100";
});


var _msgLine = ref({_txt: "", _css:""});
//const _msgLine = ref({_txt: "Whatever msg you want to show here", _css:"bg-indigo-400 text-white"});

</script>

<template>
   <RightSectionLayout :set="_set" @pushButton="_pushButton" :open="open" :buttons="_buttons" :stats="stats">

      <!-- Header -->
      <template #header>
         <div class="my-4" :class="[_font.base]"> 

            <div class="grid grid-cols-6 m-2">
             
               <div class="ml-2" :class="[_color.accent]">
                  <clipboard-document-check-icon class="w-12"/>
               </div>
              
               <div class="col-span-2 ml-2">
                  <div :class="[_font.title, _color.accent]">Project</div>
                  <div class="ml-2" :class="[_color.inactive]">Preferences</div>
               </div>  

               <div class="col-span-3 mt-0 grid grid-cols-3 gap-x-2" :class="[_color.active]">
 

                  <div class="col-span-1 text-right" :class="[_color.inactive, _font.compact]">Domain</div>
                  <div class="col-span-2" :class="[_color.accent, _font.compact]">{{_set.domain}}</div>

                  <div class="col-span-1 text-right -mt-1" :class="[_color.inactive, _font.compact]">Project</div>
                  <div class="col-span-2 -mt-1" :class="[_color.accent, _font.compact]">{{_set.project.title}}</div>

                  <div class="col-span-1 text-right -mt-1" :class="[_color.inactive, _font.compact]">Environment</div>
                  <div class="col-span-2 -mt-1" :class="[_color.accent, _font.compact]">{{_set.project.environment}}</div>

                  <div class="col-span-1 text-right -mt-1" :class="[_color.inactive, _font.compact]">Category</div>
                  <div class="col-span-2 -mt-1" :class="[_color.accent, _font.compact]">{{_set.project.category}}</div>

              
               </div>

            </div>
               <div class="-mt-1 -mb-2  w-full h-5 text-xs text-center " :class="_msgLine._css">{{_msgLine._txt}}</div>
         </div>
      </template>

      <!-- Intro -->
      <template #default>

         <!-- Project Section -->  
         <div class="p-2 h-80 overflow-y-scroll":class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Active Project</div>

            <!-- Select Active Project -->

               <div class="m-1 flex flex-wrap ">
               <div class="m-2" :class="[_font.base, _color.active]">Project </div>
                  <div v-for="_project in _projects" class="">
                     <div>
                        <button type="button" @click="_setProject(_project.title)" class="mt-2" :class="[_actualProject==_project.title ? _label.active : _label.inactive, _label.hover]">{{_project.title}}</button>
                     </div>
                  </div>
               </div>

               <!-- Select Active Environment -->
               <div class="m-1 flex flex-wrap ">
               <div class="m-2" :class="[_font.base, _color.active]">Environment </div>
                  <div v-for="_env in _set.project.validEnvironments">
                     <div>
                        <button type="button" @click="_setEnvironment(_env)" class="mt-2" :class="[_actualEnvironment==_env ? _label.active : _label.inactive, _label.hover]">{{_env}}</button>
                     </div>
                  </div>
               </div>

               <!-- Select Active Category -->
               <div class="m-1 mt-2 flex flex-wrap" v-if="_set.project.id>0">
               <div class="m-2" :class="[_font.base, _color.active]">Category </div>
                   <div @click="switchCat(0)"  :class="[_button1,_set.project.category=='primera' ? _label.active  : _label.inactive,]">00</div>
                   <div @click="switchCat(1)" :class="[_button1,_set.project.category=='segundo' ? _label.active  : _label.inactive,]">01</div>
                   <div @click="switchCat(2)" :class="[_button1,_set.project.category=='tercera' ? _label.active  : _label.inactive,]">02</div>
                   <div @click="switchCat(3)" :class="[_button1,_set.project.category==userId ? _label.active  : _label.inactive,]">{{userId}}</div>
               </div>   

               <!-- Path -->
               <div class="m-1 mt-2 flex flex-wrap" v-if="_set.project.id>0">
                  <div class="m-1" :class="[_font.base, _color.active]">Path </div>
                  <div class="col-span-2 mt-1 ml-2 text-right" :class="[_color.accent, _font.base]">{{_set.domain}}.{{_set.project.title}}.{{_set.project.environment}}.{{_set.project.category}}</div>
               </div>   

         </div>

         <!-- Members Section -->  
         <div class="p-2 h-32" :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">New Project</div>

            <!-- Project Input Section -->
            <div v-if="_set.mode.advanced && !_set.mode.noob">
               <div class="h-6 grid grid-cols-6">
                  <div class="col-span-2"></div>
                  <div>name</div>
               </div>
               <div class="h-8 grid grid-cols-6">
                  <div></div>
                  <div class="text-r col-span-2"><input class="w-32" v-model="newProjectName" placeholder="NewProject" :class="[_inputProject, _font.base]" /></div>           
                  <div><button class="" @click="_insertProject" :disabled="!_validInputProject" type="button" :class="[_validInputProject ? _button.active : _button.inactive]">Create</button></div>
               </div>
               <div class="h-4 grid grid-cols-6 dark:text-gray-600 text-center">
                  <div></div>
                  <div id="status_project"></div>
               </div>
            </div>

      </div>

      </template>

      <template #stats>
      </template>

      <!-- Footer -->
        <template #footer>



      </template>

   </RightSectionLayout>
</template>