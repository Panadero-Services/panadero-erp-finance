<script setup>
import { ref,  onMounted, computed } from 'vue'


// layout
import RightSectionLayout from '@/sections/layouts/RightSectionLayout.vue';


import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { UsersIcon } from '@heroicons/vue/24/outline'

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

const _members = ref([
   {id: 1, text: "Planning", parent:0},
   {id: 9, text: "Unassigned", parent:1}
]);

const _defaultMembers = [
   {id: 1, text: "Planning", parent:0},
   {id: 2, text: "Development", parent:0},
   {id: 3, text: "Lieuwe", parent:1, unit: "hours/day" },
   {id: 4, text: "Vince", parent:2, unit: "hours/day" },
   {id: 9, text: "Unassigned", parent:2},
];

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

const _insertMember = async () => {
   var _next=1;
   while(_members.value.find(e => e.id==_next)) _next++; 
   _members.value.push( {id: _next, text: newMemberName.value, parent:parentSelected.value, unit: unitSelected.value});
   newMemberName.value = "";
   _members.value.sort((a, b) => a.id - b.id);
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
  { label: 'Members', value: _members.value.length },
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
const newMemberName = ref('Member');
const unitSelected = ref('hours/day');
const parentSelected = ref('NULL');

const _validInputTeam = computed(()=>{
   if (_teams.value.find(e => e.name === newTeamName.value)) return false;
   return newTeamName.value.length>5 && 
         newTeamName.value.length<18 && 
         Boolean(newTeamName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputTeam = computed(()=>{
   let _inputCss = "text-xs p-1 bg-slate-50 dark:bg-slate-950 border-0 ring-1 dark:ring-slate-900 ring-slate-200 focus:ring-indigo-600 ";
   _inputCss +=  _validInputTeam.value ? 'text-green-600 ' : 'text-red-700 ';
   return _inputCss;
});

const _validInputMember = computed(()=>{
   if (_members.value.find(e => e.text === newMemberName.value)) return false;
   return newMemberName.value.length>2 && 
         newMemberName.value.length<16 && 
         Boolean(newMemberName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputMember = computed(()=>{
   let _inputCss = "text-xs p-1 bg-slate-50 dark:bg-slate-950 border-0 ring-1 dark:ring-slate-900 ring-slate-200 focus:ring-indigo-600 ";
   _inputCss +=  _validInputMember.value ? 'text-green-600 ' : 'text-red-700 ';
   return _inputCss;
});


const _removeMember = async (_index) => {
   _members.value.splice(_index,1);
}



const _parentOptions = computed(()=>{
   //let _options = ['NULL'];
   let _options= _members.value.filter(e => e.parent===0);
   return _options;
});

/// move this section to store!!!
const _setProject = async () => {
   if(_set.project.id > 0) {
      const _path = _set.domain+"."+_set.project.title+"."+_set.project.environment;
      let _payload = {  "model": "Team",
                      "type": "team",
                      "path": _path,
                      "projectId": _set.project.id,
                      "json" : JSON.stringify(_members.value),
                      "isActive": 1
                     };
      //console.log(_payload);
      await _db.setState(_payload);
   }
}

const _shuffle = async () => {
   var _next=0;
   // randomize
   _members.value.sort(function() { return .5 - Math.random(); });
   // remove old id
   _members.value.filter(e=>e.parent>0).forEach(f =>f.id=0);
   // set next id
   _members.value.filter(e=>e.parent>0).forEach(function(f) {
      while(_members.value.find(e => e.id==_next)) _next++; 
      f.id=_next;
   });
}


const _save = async () => {
  if(3 > 0){
    const _path = _set.domain+".team."+_actualTeam.value;

//    const jsonMembers =  await _members.value.serialize();
    const _payload = {  "model": "Team",
                      "type": "team",
                      "path": _path,
                      "projectId": 0,
                      "_actualTeam": _actualTeam.value,
                      "json" : JSON.stringify(_members.value),
                      "isActive": 1
                     };
    await _db.setState(_payload);
  }
}

/// move this section also to store!!!
const _load = async () => {
    const _path = _set.domain+".team."+_actualTeam.value;
    const _type = "team";
    const _projectId = 0;
    const _team = await _db.getState(_type, _path, _projectId);
    _members.value = await JSON.parse(_team);

  if(_set.project.id > 0) {
      _set.project.resources = _members.value;
  }
}

const _redRing = computed(()=>{
   let _css= "";
   if (_set.mode.dev) _css = "hover:ring-slate-600 hover:bg-red-700 hover:text-white";
   return _css;
});

const _activeLabel = 'ring-gray-500 text-indigo-700 dark:text-indigo-300';
const _inActiveLabel = 'bg-blue-600/10 ring-gray-500/20 text-gray-800 dark:text-gray-400';
const _sectionTitle = "h-6 text-sm dark:text-gray-400 text-gray-600";

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

         <!-- Teams Section -->  
         <div class="p-2 h-64 " :class="[_base, _main]"> 
            <div :class="_sectionTitle">Teams</div>

            <!-- Teams Input Section -->
            <div v-if="_set.mode.advanced && !_set.mode.noob">
               <div class="h-6 grid grid-cols-6">
                  <div></div>
                  <div>name</div>
               </div>
               <div class="h-8 grid grid-cols-6">
                  <div></div>
                  <div class="text-r col-span-2"><input class="w-32" v-model="newTeamName" placeholder="NewTeam" :class="_inputTeam" /></div>           
                  <div><button class="" @click="_insertTeam" :disabled="!_validInputTeam" type="button" :class="_validInputTeam ? _button : _buttonDisabled">Create</button></div>
               </div>
               <div class="h-4 grid grid-cols-6 dark:text-gray-600 text-center">
                  <div></div>
                  <div id="status_team"></div>
               </div>
            </div>

            <!-- Teams Select Active Team -->
            <div class=" overflow-y-scroll h-32 mt-2">
               <div class="ml-2 flex flex-wrap ">
                  <div v-for="_team in _teams" class="">
                     <div>
                        <button type="button"  @click="_actualTeam=_team.name" class="m-1 inline-flex items-center rounded-md px-2.5 py-1 text-xxs font-medium ring-1 ring-inset dark:hover:ring-indigo-400 hover:ring-black " :class="_actualTeam==_team.name ? _activeLabel : _inActiveLabel">{{_team.name}}</button>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Members Section -->  
         <div class="p-2 h-80 " :class="[_base, _main]"> 
            <div :class="_sectionTitle">Members</div>

            <!-- Teams Input New -->
            <div v-if="_set.mode.advanced && !_set.mode.noob">
               <div class="h-6 grid grid-cols-6 mt-2">
                  <div class="ml-3">units</div>
                  <div class="ml-10 col-span-2">name</div>
                  <div class="ml-8 col-span-2">parent</div>
                  <div></div>
               </div>
               <div class="h-8 grid grid-cols-6">

                 <div class="ml-3">
                     <select class="w-24" v-model="unitSelected" :class="_inputMember">
                       <option>hours/day</option>
                       <option>hours/week</option>
                       <option>hours/month</option>
                     </select>
                  </div> 

                  <div class="text-r col-span-2 ml-10"><input class="w-32" v-model="newMemberName" placeholder="NewMember" maxlength="16" :class="_inputMember" /></div>       
              
                  <div class="ml-8 col-span-2">
                     <select v-model="parentSelected" class="w-28" :class="_inputMember">
                       <option :value="0">null</option>
                       <option v-for="option in _parentOptions"  :value="option.id">{{option.text}}</option>
                     </select>
                  </div>    

                  <div><button class="ml-2" @click="_insertMember" :disabled="!_validInputMember" type="button" :class="_validInputMember ? _button : _buttonDisabled">Insert</button></div>
               </div>

               <div class="h-4 grid grid-cols-6 dark:text-gray-600 text-center">
                  <div></div>
                  <div id="status_member"></div>
               </div>
            </div>

            <div class=" overflow-y-scroll h-48 mt-2">
               <div class="ml-2 flex flex-wrap">
                  <div v-for="(m, _index) in _members">
                     <button @click="_removeMember(_index)" :disabled="!_set.mode.dev" :title="'parent:'+m.parent" type="button" class="m-1 inline-flex items-center rounded-md px-2.5 py-1 text-xxs font-medium text-gray-800 dark:text-gray-400 ring-1 ring-inset bg-slate-600/10" :class="[m.parent==0 ? _activeLabel : _inActiveLabel, _redRing]" >{{m.id}} {{m.text}}</button>
                  </div>
               </div>
            </div>
      </div>

      </template>

      <template #stats>

      </template>

      <!-- Footer -->
      <template #footer>

         <div class="p-2 h-64 " :class="[_base, _main]"> 
            <div :class="_sectionTitle">Actions</div>
            <div class="h-10" :class="_base"> 
               <div class="grid grid-cols-6 gap-2">
                  <div class="col-span-2"></div>
                  <div v-if="!_set.mode.full" class="col-span-2"></div>
                  <button v-if="_set.mode.full" @click="_members=_defaultMembers" type="button" :class="_button">Default</button>
                  <button v-if="_set.mode.full" @click="_members=[]" type="button" :class="_button">Clear</button>
                  <button @click="_load" type="button" :class="_button">Load</button>
                  <button @click="open=false" type="button" :class="_button">Cancel</button>
               </div>
            </div>

            <div class="h-10" :class="_base"> 
               <div v-if="_set.mode.advanced && !_set.mode.noob" class="grid grid-cols-6 gap-2">
                  <div></div>
                  <button v-if="_set.project.id > 0" :disabled="_set.project.id<1" @click="_setProject" type="button" title="Assign current Team to active Project!" class="hover:bg-green-800 bg-indigo-800 text-white" :class="_button">Project</button>
                  <div v-if="_set.project.id < 1"></div>
                  <div class="col-span-2"></div>
                  <button v-if="_set.mode.dev" @click="_shuffle" type="button" :class="_button">Shuffle</button>
                  <div v-if="!_set.mode.dev"></div>
                  <button @click="_save" type="button" :class="_button">Save</button>
               </div>

            </div>
      
            <div class="mt-4" :class="_sectionTitle">UserModes</div>
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