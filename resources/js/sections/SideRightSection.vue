<script setup>
import { ref,  onMounted, computed } from 'vue'


// layout
import RightSectionLayout from '@/sections/layouts/RightSectionLayout.vue';


import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline'

//import UpdateProfileInformationFormAlternative from '@/pages/Profile/Partials/UpdateProfileInformationFormAlternative.vue';

// subsections
//import UserProfileSection from '@/sections/user-section/UserProfileSection.vue';
//import UserOtherSection from '@/sections/user-section/UserOtherSection.vue';
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


onMounted(async ()=> {
   console.log('lets do it !');
   _projects.value = await _db.get('Project');
});

const _insertProject = async () => {
   const _model = "Project";
   const _payload = {   "model": "Project",
                        "name" : newProjectName.value, 
                        "user_id" : 12, 
                        "personal_team" : 0
                  };
   const _result = await _db.insertProject(_model, _payload);
   _projects.value = await _db.get('Project');
}

const _shadowColor = ref('indigo');

const _cancel = async () => {
  open.value=false;
}
//  'open' command
const open = ref(false)
defineExpose({ open });

// css 
const _hoverColor = 'hover:bg-indigo-500';

const _theme = computed(() => {
  return _set.dark ? "bg-indigo-950" : "bg-slate-100";
});

//const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";
const _button = "rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
const _buttonDisabled = "rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800 ";

const stats = computed(() => {
return [
  { label: 'Founded', value: '2025' },
  { label: 'Members', value: 0 },
  { label: 'Teams', value: '12' },
  { label: 'Projects', value: _projects.value.length },
]
});

const _title = "text-3xl"
const _base = "text-xs"
const _main = "text-black dark:text-white";
const _sub = "text-gray-600 dark:text-gray-300 text-xs";
const _index = "text-indigo-600 dark:text-indigo-300";
const _value = "text-green-600 dark:text-green-400";

const newProjectName = ref('New');

const _validInputProject = computed(()=>{
   //if (_projects.value.find(e => e.name === newProjectName.value)) return false;
   return newProjectName.value.length>5 && 
         newProjectName.value.length<18 && 
         Boolean(newProjectName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputProject = computed(()=>{
   let _inputCss = "text-xs p-1 bg-slate-50 dark:bg-slate-950 border-0 ring-1 dark:ring-slate-900 ring-slate-200 focus:ring-indigo-600 ";
   _inputCss +=  _validInputProject.value ? 'text-green-600 ' : 'text-red-700 ';
   return _inputCss;
});

const _activeLabel = 'ring-gray-500 text-indigo-700 dark:text-indigo-300';
const _inActiveLabel = 'bg-slate-600/10 ring-gray-500/20 text-gray-800 dark:text-gray-400';
const _sectionTitle = "h-6 text-sm dark:text-gray-400 text-gray-600";

</script>

<template>
   <RightSectionLayout :set="_set" :open="open">

      <!-- Header -->
      <template #header>
         <div class="my-4" :class="_base"> 
            <div class="grid grid-cols-6 m-2">
              
               <div class="ml-2" :class="[_title,_index]">
                  <clipboard-document-check-icon class="w-12"/>
               </div>
              
               <div class="col-span-2 ml-2">
                  <div :class="[_title, _index]">Project</div>
                  <div class="ml-2" :class="_sub">Preferences</div>
               </div>  

               <div class="col-span-3 mt-0 grid grid-cols-3 gap-x-2">
                  <div class="text-right" :class="_sub">Domain</div>
                  <div class="col-span-2" :class="_index">{{_set.domain}}</div>
                  <div class="text-right -mt-1" :class="_sub">Project</div>
                  <div class="col-span-2 -mt-1" :class="_index">{{_actualProject}}</div>
                  <div class="text-right -mt-1" :class="_sub">Project</div>
                  <div class=" -mt-1" :class="_index">{{_set.domain}}</div>
               </div>
            </div>
         </div>
      </template>

      <!-- Intro -->
      <template #default>

         <!-- Project Section -->  
         <div class="p-2 h-64 " :class="[_base, _main]"> 
            <div :class="_sectionTitle">Projects</div>

            <!-- Project Input Section -->
            <div v-if="_set.mode.advanced && !_set.mode.noob">
               <div class="h-6 grid grid-cols-6">
                  <div></div>
                  <div>name</div>
               </div>
               <div class="h-8 grid grid-cols-6">
                  <div></div>
                  <div class="text-r col-span-2"><input class="w-32" v-model="newProjectName" placeholder="NewProject" :class="_inputProject" /></div>           
                  <div><button class="" @click="_insertProject" :disabled="!_validInputProject" type="button" :class="_validInputProject ? _button : _buttonDisabled">Create</button></div>
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
                        <button type="button"  @click="_actualProject=_project.name" class="m-1 inline-flex items-center rounded-md px-2.5 py-1 text-xxs font-medium ring-1 ring-inset dark:hover:ring-indigo-400 hover:ring-black " :class="_actualProject==_project.name ? _activeLabel : _inActiveLabel">{{_project.name}}</button>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Members Section -->  
         <div class="p-2 h-80 " :class="[_base, _main]"> 
            <div :class="_sectionTitle">Members</div>


      </div>

      </template>

      <template #stats>
      </template>

      <!-- Footer -->
      <template #footer>

         <div >
            <div class="mt-2 ml-2" :class="_sectionTitle">Actions</div>

            <div class="h-10" :class="_base"> 
               <div class="grid grid-cols-6 gap-2">
                  <div class="col-span-5"></div>
                  <button @click="open=false" type="button" :class="_button">Cancel</button>
               </div>
            </div>

            <div class="h-10" :class="_base"> 
               <div v-if="_set.mode.advanced && !_set.mode.noob" class="grid grid-cols-6 gap-2">
                  <div class="col-span-5"></div>
                  <div></div>
               </div>

            </div>
      
            <div class="mt-4 ml-2" :class="_sectionTitle">UserModes</div>
            <user-mode-section :set="_set" />

         </div>

         <div class="mt-8">
            <div class="mt-4" v-if="_set.mode.full">
               <dl class="grid grid-cols-1 gap-x-4 sm:grid-cols-2 lg:grid-cols-4 mt-3">
                  <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 gap-x-4 border-r border-gray-300 dark:border-gray-700">
                     <dd class="text-base text-center tracking-tight " :class="_value">{{ stat.value }}</dd>
                     <dt class="text-center" :class="_sectionTitle" >{{ stat.label }}</dt>
                  </div>
               </dl>
            </div>
         </div>

      </template>

   </RightSectionLayout>
</template>