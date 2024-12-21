<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';

// modules
import { panaderoSim } from "panadero-sim";
const Sim = new panaderoSim('selfSim');
import { panaderoServer } from "panadero-server";
const pServer = new panaderoServer('selfServer');

// components
import move from '@/panaderos/panadero-sim/move.vue';
import ioServer from '@/panaderos/panadero-server/ioserver.vue';
import spaceship from '@/panaderos/panadero-minigames/spaceship.vue';
import othello from '@/panaderos/panadero-minigames/othello.vue';
import breakout from '@/panaderos/panadero-minigames/breakout.vue';
import candycrush from '@/panaderos/panadero-minigames/candycrush.vue';

// templates
import Card from '@/layouts/cards/Card.vue';

import { useGameStore } from '@/stores/game';
let gameS = useGameStore();

const props =defineProps({
    set: Object,
    contract: Object,
    db: Object,
});


// webhooks
onMounted(async ()=> {
    

//    let _cards = await gameS.get('cardScore');
//    console.log(_cards.json);
//    _Cards.value = JSON.parse(_cards.json);



})

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
const _shadow_indigo = "shadow-lg shadow-indigo-300 dark:shadow-indigo-600";
const _shadow_green = "shadow-lg shadow-green-300 dark:shadow-green-600";
const _shadow_red = "shadow-md shadow-red-300 dark:shadow-red-600";
const _shadow_yellow = "shadow-md shadow-yellow-300 dark:shadow-yellow-600";

const _status1=ref("run");
const _status2=ref("");

const optionsIoServer = {
  //id: 1,
  title: 'SC11',
  script: 'i5v21',
  beatMs: 3000,
  beatsPerStep: 1, 
  //stepsPerTurn: 1,
  quitTurn: 2000,
  latencyMs: 1000,
  //comment: 'defaultServer',
  //isActive: 1
};
    
// globals
const _counter=ref(5);
const _activeCard = ref(0);

const _Cards = ref([
    {id:_counter.value, active:1, visible:1, corners:"", status:"idle", comp:{name:"move"}, class:"col-span-1"},
  ]);

const _Modules = ref([
   {title:"Simulation", corners:"tl", module:Sim, components:['move']},
   {title:"MiniGames", corners:"", module:Sim, components:['spaceship', 'othello', 'breakout', 'candycrush']},
   {title:"ioServer", corners:"tr", module:pServer, components:['ioServer']}
]);

const comps = [
  {name:'move', active:1,  class:"col-span-1"}
];

const _killz = (_idx, _id) => {
  console.log('killed', _id);
  _Cards.value = _Cards.value.filter(
    x => x.id !== _id
  );
  console.log(_Cards.value.length);
  // __Cards.value.splice(_id,1);
  // _counter.value++;
}
function removeObjectWithId(arr, id) {
  const objWithIdIndex = arr.findIndex((obj) => obj.id === id);
  arr.splice(objWithIdIndex, 1);
  return arr;
}

const _kill = async (_idx, _id) => {
  console.log('killed', _id);
  _Cards.value[_idx].active = 0;
  _Cards.value[_idx].visible = 0;
  //removeObjectWithId(_Cards.value,_id);
  console.log(_Cards.value.length);
  // __Cards.value.splice(_id,1);
  // _counter.value++;
    let _json =JSON.stringify(_Cards.value);
    let _payload = {"title":"cardScore", "nvalue":_counter.value, "description":"description", "json" :_json };
    await gameS.set('Game',_payload);
}

const _add = async (_compName) => {
  console.log('_compName:' , _compName);
   const _comp = {name:_compName, active:1,  class:"col-span-1"}
  console.log('_add name: ', _comp.name);
  _counter.value++;
  // not good.. id is in fact index
  // need to create 1. unique id + 2. index _idx
   _Cards.value.push({id:_counter.value, active:1, visible:1, corners:"", status:"idle", class:"col-span-1", comp:_comp});

    let _json =JSON.stringify(_Cards.value);

    let _payload = {"title":"cardScore", "nvalue":_counter.value, "description":"description", "json" :_json };
    await gameS.set('Game',_payload);


}

const _start = (_idx, _id) => {
  _Cards.value[_idx].active = 1;
};

const _stop = (_idx, _id) => {
  _Cards.value[_idx].active = 0;
};

const _reset = (_idx, _id) => {
  console.log('reset', _idx, _id);
};

</script>


<template #default>

  <div class="py-0 sm:py-0 lg:py-0 ">
    <div class="mx-auto max-w-2xl px-6 lg:max-w-full lg:px-8">
      <div class=" grid gap-6 sm:mt-16 grid-cols-2 lg:grid-cols-4 xl:grid-cols-6" >

    <!-- card1 -->
       <card v-for="mod in _Modules" height="h-64"  :corners="mod.corners" status="module-active" class="col-span-1">
           <template #title>{{mod.title}}</template>
           <template #default>
             <div class="mt-1">       
                 <p>Module: <b>{{mod.module.name}}</b></p>
                 <p>Date: <b>{{mod.module.date}}</b></p>
                 <p>Version: <b>{{mod.module.version}}</b></p>
                 <p>Author: <b>{{mod.module.author}}</b></p>
                  <!-- <p>Git: <b>{{mod.module.git}}</b></p>-->
                  Components:
                  <p>
                     <span v-for="comp in mod.components" class="border mr-1 rounded-lg p-0.5 px-2 text-xxs border-indigo-300 text-indigo-500 hover:bg-indigo-400 hover:text-white">
                        <button @click="_add(comp)">{{comp}}</button>
                     </span>
                  </p>
               </div>
           </template>
         </card>

      </div>
    </div>
  </div>


<div class="py-1 sm:py-2 lg:py-6">
    <div class="mx-auto max-w-2xl px-6 lg:max-w-max lg:px-8">
      <h2 class="text-center text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">SELF BOTs</h2>
      <p class="mt-2 text-2xl md:text-3xl lg:text-4xl font-semibold tracking-tight text-center text-gray-950 dark:text-gray-200"> ... dashboard to deploy xxx miniApps.......</p>
      <div class=" grid gap-6 sm:mt-8 grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 " >

            <template v-for="(card, _idx) in _Cards" :class="card.comp.class" >
               <card v-if="card.visible" height="h-48 lg:h-64" :id="card.id" :corners="card.corners" :status="card.status" :active="card.active" class="col-span-1" @start="_start(_idx, card.id)" @stop="_stop(_idx, card.id)" @kill="_kill(_idx, card.id)" @reset="_reset(_idx, card.id)">
                  <template #title>
                     {{card.comp.name}}
                     {{card.id}}
                  </template>

                  <template #intro>
                     {{card.status}}
                  </template>

                  <template #default>
                     <div v-if="card.comp.name==='move'" class="mt-2"><component :is='move' :size="51" :interval="500" :active="card.active" @status="(n) => card.status = n" /></div>
                     <div v-if="card.comp.name==='spaceship'" class="-mt-10 -mx-2 bg-slate-950"><component  :is='spaceship' :id="card.id" :callSign="set.self" :activated="card.active" @status="(n) => card.status = n" /></div>
                     <div v-if="card.comp.name==='othello'" class="-mt-7 -mx-2"><component  :is='othello' :id="card.id" :callSign="set.self" :activated="card.active" @status="(n) => card.status = n" /></div>
                     <div v-if="card.comp.name==='breakout'" class="-mt-7 -mx-2 bg-slate-950"><component  :is='breakout' :id="card.id" :callSign="set.self" :activated="card.active" @status="(n) => card.status = n" /></div>
                     <div v-if="card.comp.name==='candycrush'" class="-mt-7 -mx-2 bg-slate-950"><component  :is='candycrush' :id="card.id" :callSign="set.self" :activated="card.active" @status="(n) => card.status = n" /></div>
                     <div v-if="card.comp.name==='ioServer'" class="mt-2"><component :is='ioServer' :code="'SC'+card.id" :options="optionsIoServer" :active="card.active" @status="(n) => card.status = n" /></div>
                  </template>

               </card>
            </template>

         </div>
      </div>
   </div>
</template>

