<script setup>
import { ref,  onMounted, computed } from 'vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { UsersIcon } from '@heroicons/vue/24/outline'
import UserModeSection from '@/sections/user-section/UserModeSection.vue';
import RightSectionLayout from '@/sections/layouts/RightSectionLayout.vue';
import logo from "@/layouts/logos/self2.png";

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

//const _actualTeam= ref("JaW's Team");
const _actualTeam= ref(usePage().props.auth.user.current_team.name);
const _teams = ref([]);
const newTeamName = ref('New');
const newMemberName = ref('Member');
const unitSelected = ref('hours/day');
const parentSelected = ref('NULL');

//  'open' command
const open = ref(false)
defineExpose({ open });

const stats = computed(() => {
return [
  { label: 'Founded', value: '2025' },
  { label: 'Members', value: _members.value.length },
  { label: 'Projects', value: '12' },
  { label: 'Teams', value: _teams.value.length },
]
});

const _parentOptions = computed(()=>{
   //let _options = ['NULL'];
   let _options= _members.value.filter(e => e.parent===0);
   return _options;
});


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

const _cancel = async () => {
  open.value=false;
}

const _removeMember = async (_idx) => {
   _members.value.splice(_idx,1);
}


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

// css
const _button = { active: "rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400", 
                  inactive: "rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800" 
               };

const _font = { title:"text-2xl", 
                subtitle:"text-sm", 
                base:"text-xs" 
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

const _validInputTeam = computed(()=>{
   if (_teams.value.find(e => e.name.toLowerCase() === newTeamName.value.toLowerCase())) return false;
   return newTeamName.value.length>5 && 
         newTeamName.value.length<18 && 
         Boolean(newTeamName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _validInputMember = computed(()=>{
   if (_members.value.find(e => e.text.toLowerCase() === newMemberName.value.toLowerCase())) return false;
   return newMemberName.value.length>2 && 
         newMemberName.value.length<16 && 
         Boolean(newMemberName.value.match(/^[A-Za-z0-9]*$/)) ;
});

const _inputTeam = computed(()=>{
   let _inputCss = "p-1 bg-slate-50 dark:bg-gray-900 border-0 ring-0 ";
   _inputCss +=  _validInputTeam.value ?  _validRing : _inValidRing;
   return _inputCss;
});

const _inputMember = computed(()=>{
   let _inputCss = "p-1 bg-slate-50 dark:bg-gray-900 border-0 ring-0 ";
   _inputCss +=  _validInputMember.value  ?  _validRing : _inValidRing;
   return _inputCss;
});

</script>

<template>
   <RightSectionLayout :set="_set" :open="open">

      <!-- Header -->
      <template #header>
         <div class="my-4" :class="[_font.base]"> 
            <div class="grid grid-cols-6 m-2">
              
               <div class="ml-2" :class="[_color.accent]">
                  <users-icon class="w-12"/>
               </div>
              
               <div class="col-span-2 ml-2">
                  <div :class="[_font.title, _color.accent]">Team</div>
                  <div class="ml-2" :class="[_color.inactive]">Preferences</div>
               </div>  

               <div class="col-span-3 mt-0 grid grid-cols-3 gap-x-2" :class="[_color.active]">
                  <div class="text-right">Domain</div>
                  <div class="col-span-2" :class="[_color.accent]">{{_set.domain}}</div>
                  <div class="text-right -mt-1">Team</div>
                  <div class="col-span-2 -mt-1" :class="[_color.accent]">{{_actualTeam}}</div>
                  <div class="text-right -mt-1">Project</div>
                  <div class=" -mt-1" :class="[_color.accent]">{{_set.domain}}</div>
               </div>
            </div>
         </div>
      </template>

      <!-- Intro -->
      <template #default>

         <!-- Teams Section -->  
         <div class="p-2 h-64 " :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Teams</div>

            <!-- Teams Input Section -->
            <div v-if="_set.mode.advanced && !_set.mode.noob">
               <div class="h-6 grid grid-cols-6">
                  <div class="col-span-2"></div>
                  <div>name</div>
               </div>
               <div class="h-8 grid grid-cols-6">
                  <div class="col-span-2"></div>
                  <div class="text-r col-span-2"><input class="w-32" v-model="newTeamName" placeholder="NewTeam" :class="[_inputTeam, _font.base]" /></div>           
                  <div><button class="" @click="_insertTeam" :disabled="!_validInputTeam" type="button" :class="[_validInputTeam ? _button.active : _button.inactive]">Create</button></div>
               </div>
               <div class="h-4 grid grid-cols-6 text-center">
                  <div></div>
                  <div id="status_team"></div>
               </div>
            </div>

            <!-- Teams Select Active Team -->
            <div class=" overflow-y-scroll h-32 mt-2">
               <div class="ml-2 flex flex-wrap ">
                  <div v-for="_team in _teams" class="">
                     <div>
                        <button type="button" @click="_actualTeam=_team.name" class="mt-4" :class="[_actualTeam==_team.name ? _label.active : _label.inactive, _label.hover]">{{_team.name}}</button>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Members Section -->  
         <div class="p-2 h-80" :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Members</div>


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
                     <select class="w-24" v-model="unitSelected" :class="[_inputMember, _font.base]">
                       <option>hours/day</option>
                       <option>hours/week</option>
                       <option>hours/month</option>
                     </select>
                  </div> 

                  <div class="text-r col-span-2 ml-10"><input class="w-32" v-model="newMemberName" placeholder="NewMember" maxlength="16" :class="[_inputMember, _font.base]" /></div>       
              
                  <div class="ml-8 col-span-2">
                     <select v-model="parentSelected" class="w-28" :class="[_inputMember, _font.base]">
                       <option :value="0">null</option>
                       <option v-for="option in _parentOptions"  :value="option.id">{{option.text}}</option>
                     </select>
                  </div>    

                  <div><button class="ml-2" @click="_insertMember" :disabled="!_validInputMember" type="button" :class="[_validInputMember ? _button.active : _button.inactive]">Insert</button></div>
               </div>

               <div class="h-4 grid grid-cols-6 text-center">
                  <div></div>
                  <div id="status_member"></div>
               </div>
            </div>

            <div class=" overflow-y-scroll h-48 mt-2">
               <div class="ml-2 flex flex-wrap">
                  <div v-for="(m, idx) in _members">
                        <button type="button"  @click="_removeMember(idx)"  :disabled="!_set.mode.dev" :title="'parent:'+m.parent"  :class="[m.parent==0 ? _label.active : _label.inactive, _redRing]">
                        <span v-if="m.text==_set.self " ><img class="m-0.5 hover:rotate-45 dark:brightness-200" :class="_icon" :src="logo"/></span>
                        {{m.id}} {{m.text}}
                     </button>
                  </div>
               </div>
            </div>
      </div>

      </template>

      <template #stats>

      </template>

      <!-- Footer -->
      <template #footer>

         <!-- Actions Section -->  
         <div class="p-2 h-64 " :class="[_font.base, _color.active]"> 
            <div :class="[_font.subtitle, _color.inactive]">Actions</div>

            <div class="h-10" :class="_font.sub"> 
               <div class="grid grid-cols-6 gap-2">
                  <div class="col-span-2"></div>
                  <div v-if="!_set.mode.full" class="col-span-2"></div>
                  <button v-if="_set.mode.full" @click="_members=_defaultMembers" type="button" :class="_button.active">Default</button>
                  <button v-if="_set.mode.full" @click="_members=[]" type="button" :class="_button.active">Clear</button>
                  <button @click="_load" type="button" :class="_button.active">Load</button>
                  <button @click="open=false" type="button" :class="_button.active">Cancel</button>
               </div>
            </div>

            <div class="h-10" :class="_font.sub"> 
               <div v-if="_set.mode.advanced && !_set.mode.noob" class="grid grid-cols-6 gap-2">
                  <div></div>
                  <button v-if="_set.project.id > 0" :disabled="_set.project.id<1" @click="_setProject" type="button" title="Assign current Team to active Project!" class="hover:bg-green-800 bg-indigo-800 text-white" :class="_button.active">Project</button>
                  <div v-if="_set.project.id < 1"></div>
                  <div class="col-span-2"></div>
                  <button v-if="_set.mode.dev" @click="_shuffle" type="button" :class="_button.active">Shuffle</button>
                  <div v-if="!_set.mode.dev"></div>
                  <button @click="_save" type="button" :class="_button.active">Save</button>
               </div>

            </div>
         <!-- UserModes -->  
            <div class="mt-8" :class="[_font.subtitle, _color.inactive]">UserModes</div>
            <user-mode-section :set="_set" />

         </div>

         <div class="mt-4">
            <div class="mt-4" v-if="_set.mode.full">
               <dl class="grid grid-cols-1 gap-x-4 sm:grid-cols-2 lg:grid-cols-4 mt-3">
                  <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 gap-x-4 border-r border-gray-300 dark:border-gray-700">
                     <dd class="text-center tracking-tight " :class="[_font.subtitle, _color.stats]">{{ stat.value }}</dd>
                     <dt class="text-center" :class="_font.base, _color.active" >{{ stat.label }}</dt>
                  </div>
               </dl>
            </div>
         </div>

      </template>

   </RightSectionLayout>
</template>