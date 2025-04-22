<script setup>
import { ref,  onMounted, computed } from 'vue'


// layout
import RightSectionLayout from '@/sections/layouts/RightSectionLayout.vue';


import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { UsersIcon } from '@heroicons/vue/24/outline'

import UpdateProfileInformationFormAlternative from '@/pages/Profile/Partials/UpdateProfileInformationFormAlternative.vue';

// subsections
import UserProfileSection from '@/sections/user-section/UserProfileSection.vue';
import UserOtherSection from '@/sections/user-section/UserOtherSection.vue';
import UserModeSection from '@/sections/user-section/UserModeSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';
const _db = useDbStore();
const _set = useSettingsStore();

const props = defineProps({
    user: Object
});

const _teams = ref([]);
const _actualTeam= ref("JaW's Team");

onMounted(async ()=> {
   console.log('lets do it !');
   _teams.value = await _db.get('Team');
});

const _insertTeam = async () => {
   const _model = "Team";
   const _payload = {   "model": "Team",
                        "name" : newTeamName.value, 
                        "user_id" : 12, 
                        "personal_team" : 0
                  };
   const _result = await _db.insertTeam(_model, _payload);
   _teams.value = await _db.get('Team');

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
const _button = "mt-2 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";
const _buttonDisabled = "mt-2 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800 ";

const stats = computed(() => {
return [
  { label: 'Founded', value: '2025' },
  { label: 'Members', value: '37' },
  { label: 'Projects', value: '12' },
  { label: 'Teams', value: _teams.value.length },
]
});

const _title = "text-3xl"
const _base = "text-xs"
const _main = "text-black dark:text-white";
const _sub = "text-gray-600 dark:text-gray-300 text-xs";
const _index = "text-indigo-600 dark:text-indigo-300";
const _value = "text-green-600 dark:text-green-400";

const newTeamName = ref('New');

const _validInputField = computed(()=>{
   if (_teams.value.find(e => e.name === newTeamName.value)) return false;
   return newTeamName.value.length>5 && 
         newTeamName.value.length<18 && 
         Boolean(newTeamName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputField = computed(()=>{
   let _inputCss = "text-xs p-1 bg-slate-50 dark:bg-slate-950 border-0 ring-1 dark:ring-slate-900 ring-slate-200 focus:ring-indigo-600 ";
   _inputCss +=  _validInputField.value ? 'text-green-600 ' : 'text-red-700 ';
   return _inputCss;
});

</script>

<template>
   <RightSectionLayout :set="_set" :open="open">

      <!-- Header -->
      <template #header>
         <div class="my-4" :class="_base"> 
            <div class="grid grid-cols-6 m-2">
              
               <div class="ml-2" :class="[_title,_index]">
                  <users-icon class="w-12"/>
               </div>
              
               <div class="col-span-2 ml-2">
                  <div :class="[_title, _index]">Team</div>
                  <div class="ml-2" :class="_sub">Preferences</div>
               </div>  

               <div class="col-span-3 mt-0 grid grid-cols-3 gap-x-2">
                  <div class="text-right" :class="_sub">Domain</div>
                  <div class="col-span-2" :class="_index">{{_set.domain}}</div>
                  <div class="text-right -mt-1" :class="_sub">Team</div>
                  <div class="col-span-2 -mt-1" :class="_index">{{_actualTeam}}</div>
                  <div class="text-right -mt-1" :class="_sub">Project</div>
                  <div class=" -mt-1" :class="_index">{{_set.domain}}</div>
               </div>
            </div>
         </div>
      </template>

      <!-- Intro -->
      <template #default>

         <div class="p-3 h-64" :class="[_base, _main]"> 
            <!-- Select Active Team -->
            Teams
            <div class="flex flex-wrap">
               <div v-for="_team in _teams" class="">
                  <div>
                     <button @click="_actualTeam=_team.name" type="button" :class="[_button,_actualTeam == _team.name ? 'dark:bg-indigo-600 bg-indigo-200' : '']">{{_team.name}}</button>
                  </div>
               </div>
            </div>

            <!-- Input New Team -->
            <div class="mt-12 ml-2">
                   Input: 
               <div class="block">
                  <input v-model="newTeamName" placeholder="NewTeam" :class="_inputField" />
                  <button @click="_insertTeam" :disabled="!_validInputField" type="button" :class="_validInputField ? _button : _buttonDisabled">Create</button>
               </div>           
            </div>

         </div>

         <div class="my-3 h-64" :class="[_base, _main]"> 
            <div class="grid grid-cols-6 m-3">
               members
            </div>
         </div>

      </template>

      <template #stats>
         <div class="my-3 h-64" :class="[_base, _main]"> 
            <div class="grid grid-cols-6 m-3">
               activities
            </div>
         </div>
      </template>


      <!-- Footer -->
      <template #footer>

         <div class="h-8 mx-3" :class="_base"> 
            <div class="grid grid-cols-6">
                <button @click="_set.dark=false" type="button" :class="_button">Light</button>
                <button @click="_set.dark=true" type="button" :class="_button">Dark</button>
                <div class="col-span-3"></div>
                <button @click="open=false" type="button" :class="_button">Cancel</button>
            </div>
         </div>

         <div class="my-3">
            <dl class="grid grid-cols-1 gap-x-3 sm:grid-cols-2 lg:grid-cols-4 mt-3">
               <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 border-r border-white/20">
                  <dd class="text-xl font-semibold tracking-tight text-center" :class="_value">{{ stat.value }}</dd>
                  <dt class="text-base text-center dark:text-gray-300" >{{ stat.label }}</dt>
               </div>
            </dl>
         </div>
      </template>

   </RightSectionLayout>
</template>