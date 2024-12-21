<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
//import { SelfTitanLegends, moduleName } from "self-titanlegends";
import SearchMe from '@/dashboards/self/SearchMe.vue';
import SearchMeAll from '@/dashboards/self/SearchMeAll.vue';
import BurnServer from '@/dashboards/self/BurnServer.vue';
import { Self, moduleName, moduleVersion, moduleGit, resolveName, resolveAllNames } from "panadero-self";;
import TitleLabel from '@/dashboards/self/layouts/TitleLabel.vue';
import ComponentLabel from '@/dashboards/self/layouts/ComponentLabel.vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

// define emits
const emit = defineEmits(['kill', 'wrench']);

// Globals
//const APP_NAME = process.env.APP_NAME;
console.log('test i5v17-panadero-self');
console.log(`moduleName: ${moduleName}`);
console.log(`moduleVersion: ${moduleVersion}`);
//console.log(`moduleGit: ${moduleGit}`);

// call parameters
const props = defineProps({
    contract: Object,
    self: Object,
    set: Object,
    pulse: Boolean
});

// globals
const _title="StakePool";

const Cards = ref([
		//{id: 0, comp:"SearchMeAll", class:"col-span-12"},
		//{id: 1, comp:"SearchMe",  class:"col-span-6 lg:col-span-3 xl:col-span-2"}
	]);
const _activeCard = ref(0);

const _counter=ref(0);

const comps = [
  { name: 'SearchMe', initials: 'SM', href: '', members: 0, bgColor: 'bg-indigo-dark-200', class:"col-span-6 lg:col-span-3 xl:col-span-2 "},
  { name: 'SearchMeAll', initials: 'SmA', href: '#', members: 0, bgColor: 'bg-purple-600', class:"col-span-12 lg:col-span-6 xl:col-span-3" },
  { name: 'BridgeServer', initials: 'T', href: '#', members: 0, bgColor: 'bg-yellow-600', class:"col-span-6 lg:col-span-3 xl:col-span-2 " },
  { name: 'BurnServer', initials: 'BS', href: '#', members: 0, bgColor: 'bg-yellow-600', class:"col-span-12 lg:col-span-6 xl:col-span-3 " },
]

const _kill = (_id) => {
	console.log('killed', _id);
	Cards.value = Cards.value.filter(
		x => x.id !== _id
	);
	console.log(Cards.value.length);
 	// _Cards.value.splice(_id,1);
	// _counter.value++;
}

const _add = (_comp) => {
 	console.log('name: ', _comp.name);
  _counter.value++;
  // not good.. id is in fact index
  // need to create 1. unique id + 2. index _idx
	Cards.value.push({id:_counter.value, comp:_comp});
}

const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-1 md:p-3 lg:p-4 shadow-sm hover:border-gray-400";
//const _mainCard = " relative flex items-center space-x-3 rounded-md border border-gray-300 p-1 sm:p-2 md:p-3 lg:p-4 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400";

const _wrench = (_comp, _obj) => {
  console.log('stakepool says: SOME1 PUSHED WRENCH!');
	emit('wrench', _comp, _obj);
}

</script>

<template>
	<div class="m-2 lg:m-3">

  <div class="grid grid-cols-1 gap-2 lg:gap-3 grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 mt-1 sm:mt-2 lg:mt-3 text-xxs sm:text-xs md-text-sm">
    <title-label title="SELF Domain" detail="Isolated DataSource" makeup="bg-slate-200" icon="bg-purple-700" :mainCard="_mainCard"></title-label>
    <title-label title="SELF Dashboard" detail="The Identity NFT" makeup="bg-slate-100" icon="bg-purple-700"  :mainCard="_mainCard"></title-label>
    <title-label :title="moduleName" :detail="moduleVersion" makeup="bg-slate-100" icon="bg-gradient-to-b from-indigo-500 via-white to-indigo-500"  :mainCard="_mainCard"></title-label>
    <title-label title="JaWsome Orbit" detail="Lead Developer" makeup="bg-slate-100"icon="bg-green-700" :mainCard="_mainCard"></title-label>
  </div>

  <div> 
    <ul role="list" class="mt-2 gap-2 lg:mt-3 lg:gap-3 grid grid-cols-1 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 sm:gap-3 md:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8">
      <li v-for="c in comps" :key="c.name" class="col-span-1 flex rounded-md shadow-sm">
        <component-label :comp="c" @add="_add"> </component-label>
      </li>
    </ul>
  </div>

	<div class=" mt-2 gap-2 lg:mt-3 lg:gap-3 grid grid-cols-12" :id="_counter">	
		<span v-for="(card, _idx) in Cards" :class="card.comp.class">
			<div v-if="card.comp.name==='SearchMe'"><component :is=SearchMe :id="card.id" @kill="_kill" /></div>
			<div v-if="card.comp.name==='SearchMeAll'"><component :is=SearchMeAll :id="card.id" @kill="_kill" /></div>
      <div v-if="card.comp.name==='BurnServer'"><component :is=BurnServer :set="set" :self="self" :comp="card.comp" :id="card.id" :pulse="pulse" @kill="_kill" @wrench="_wrench"/></div>
		</span>
	</div>

	</div>
</template>

