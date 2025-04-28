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
const _actualProject= ref("ActualProject");
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

const _setProject = (_title) => {
    if(!( usePage().props.auth.user == null)) {
        // toDo !! retrieve from db
        _set.project.id = _title=='none' ? 0 : 1;
        _set.project.title = _title;
        _set.project.environment = "master";
        _set.project.validEnvironments = ["master", "alternative", "sandbox"];
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



// css
const _button = { active: "rounded px-2 py-1 text-sm ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400", 
                  inactive: "rounded px-2 py-1 text-sm ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800" 
               };

const _font = { title:"text-3xl", 
                subtitle:"text-lg", 
                base:"text-sm" 
               };

const _color = { accent: "text-indigo-600 dark:text-indigo-300", 
                 active: "text-gray-600 dark:text-gray-400", 
                 inactive: "text-gray-400 dark:text-gray-600", 
                 stats: "text-green-600 dark:text-green-400"
               };

const _label = {  active: "m-1 inline-flex items-center rounded-md bg-indigo-500/10 px-2 py-1 text-xs font-medium text-indigo-800 dark:text-indigo-400 ring-1 ring-inset ring-indigo-500/20", 
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

const callback = async (_parameter) => {
   console.log('callback', _parameter);
   open.value=false;
}

</script>

<template>
   <RightSectionLayout :set="_set" :open="open" @some-event="callback">

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
                  <div class="text-right">Domain</div>
                  <div class="col-span-2" :class="[_color.accent]">{{_set.domain}}</div>
                  <div class="text-right -mt-1">{Project}</div>
                  <div class="col-span-2 -mt-1" :class="[_color.accent]">{{_actualProject}}</div>
                  <div class="text-right -mt-1">Project</div>
                  <div class=" -mt-1" :class="[_color.accent]">{{_set.domain}}</div>
               </div>

            </div>
         </div>
      </template>

      <!-- Intro -->
      <template #default>

         <!-- Project Section -->  
         <div class="p-2 h-64 " :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Projects</div>

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

            <!-- Projects Select Active Project -->
            <div class=" overflow-y-scroll h-32 mt-2">
               <div class="ml-2 flex flex-wrap ">
                  <div v-for="_project in _projects" class="">
                     <div>
                        <button type="button" @click="_setProject(_project.title)" class="mt-4" :class="[_actualProject==_project.tite ? _label.active : _label.inactive, _label.hover]">{{_project.title}}</button>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Members Section -->  
         <div class="p-2 h-8" :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Members</div>
      </div>

      </template>

      <template #stats>
      </template>

      <!-- Footer -->
        <template #footer>



      </template>

   </RightSectionLayout>
</template>