<script setup>
import { ref, computed } from 'vue';
import { moduleName, moduleVersion, resolveName } from "panadero-self";
import SmallLine from '@/components/SmallLine.vue';

// call parameters
const props = defineProps({
	id: Number
});

const _title = ref("SearchMe");
const _detail = ref("");
const _status = ref("idle");
const _makeup = ref(" bg-white dark:bg-black ");
const _oneLine = true;

const _button1 = async (_input) => {
	_input = _input.toLowerCase().trim();
	_detail.value = "";
	try {
	  	_detail.value = await resolveName(_input);
	  	_status.value = "success";
	} catch{
	  	_status.value = "error";
	}
}

const _label="text-gray-800 dark:text-gray-300";
const _value="text-xs text-green-800 dark:text-green-300";
//const _filter= "bg-indigo-50 dark:bg-indigo-dark-700 dark:text-white focus:bg-white focus:dark:bg-gray-900 border-0 dark:border-gray-600 focus:border-indigo-100 w-24 lg:w-32 text-xxxs sm:text-xxs md:text-xs px-0.5 ";

import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

//const _titleColor="bg-indigo-dark-300 text-white ";
const _idColor="text-indigo-dark-300 ";
const _mainCard = "relative flex items-start rounded-md border border-slate-300 sm:p-0 md:p-1 lg:p-1 hover:border-slate-400 ";
const _icon = computed(()=>{
	if(_status.value==="success") return "bg-indigo-600 hover:bg-green-600 m-1";
	if(_status.value==="error") return "bg-red-600 hover:bg-green-600 m-1";
	return "bg-slate-300 hover:bg-green-600 m-1";
});
</script>

<template>
    <small-line :id="id" :title="_title" :detail="_detail" :makeup="_makeup" :icon="_icon" :mainCard="_mainCard" :oneLine="_oneLine" @button1="_button1" @kill="$emit('kill',id)" ></small-line>

</template>