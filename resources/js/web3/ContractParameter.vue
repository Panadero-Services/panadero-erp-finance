<script setup>
    import {ref, computed} from 'vue';
    import { StopIcon, WrenchIcon, EyeIcon } from '@heroicons/vue/24/solid'
    
    const props = defineProps({
        stat: Object, 
        statIndex: Number,
        active: Boolean,
        maintenanceMode: Boolean,
        advancedMode: Boolean,
      });

const divClass = computed(() => { 
    let aBorders = [' border-l-zinc-300 ',
        ' border-l-purple-400  dark:border-l-purple-600' , 
        ' border-l-lime-400 dark:border-l-lime-600 ' , 
        ' border-l-pink-400 dark:border-l-pink-600  ' , 
        ' border-l-indigo-400 dark:border-l-indigo-500 ' , 
        ' border-l-green-400 dark:border-l-green-600 ' , 
        ' border-l-cyan-400 dark:border-l-cyan-600 ' , 
        ' border-l-amber-400 dark:border-l-amber-600 ' , 
        ' border-l-orange-500 dark:border-l-orange-600 ' , 
        ' border-l-gray-400 dark:border-l-gray-400 ' , 
        ' border-l-teal-500 dark:border-l-teal-600' , 
        ' border-l-red-600 dark:border-l-red-700 ' ]

    let ring = props.active ? " ring-indigo-600 dark:ring-indigo-400 ring-4 " : " border-2  ";
    let border  = aBorders[props.stat.contractCounter%12];
    return props.stat.columns + ring + border + (props.stat.update ? " bg-gray-100 ": " bg-white"); })

const divColor = computed(() => {
    let aColors = ['text-zinc-300',
        'text-purple-400' , 
        'text-lime-400' , 
        'text-pink-400' , 
        'text-indigo-400' , 
        'text-green-400' , 
        'text-cyan-400' , 
        'text-amber-400' , 
        'text-orange-500' , 
        'text-gray-400' , 
        'text-teal-500' , 
        'text-red-600' ]

    //return `text-${aColors[props.stat.contractCounter]}` 
    //return `text-[#50d71e]`,
    return aColors[props.stat.contractCounter%12];
})

</script>

<template>
    <div class="overflow-hidden rounded-lg mt-1 px-1 py-5 shadow sm:p-6 relative shadow-lg dark:bg-gray-950 dark:border-t-gray-700 dark:border-r-gray-700 dark:border-b-gray-700" :class="divClass">
        <span v-if="advancedMode || maintenanceMode" @click="$emit('delete',stat.contractCounter,stat.line_nr)" class="absolute top-0.5 right-1.5 text-gray-200 dark:text-gray-500 hover:text-red-600 hover:text-lg ">&times;</span>
        <WrenchIcon v-if="advancedMode" @click="$emit('edit',statIndex)" class="absolute top-7 right-1.5 text-gray-200 dark:text-gray-500 h-3 w-3 hover:text-indigo-600" aria-hidden="false"  />
        <EyeIcon v-if="advancedMode" @click="$emit('view',statIndex)" class="absolute top-12 right-1.5 text-gray-200 dark:text-gray-500 h-3 w-3 hover:text-indigo-600" aria-hidden="false"  />
        <StopIcon v-if="advancedMode && stat.is_live && stat.is_active && stat.parameters.length" class="h-3 w-3 -ml-5 -mt-5" aria-hidden="false" :class="divColor">
        </StopIcon>
        <StopIcon v-else aria-hidden="false" class="h-3 w-3 -ml-5 -mt-5 text-gray-50 dark:text-gray-700">
        </StopIcon>

        <div @click="$emit('retrieve',stat.contractCounter, stat.line_nr)">
            <dt class="truncate text-sm font-medium text-gray-600 dark:text-gray-400  -mt-2">{{advancedMode ? stat.web3record_id+" " : ""}}{{stat.line}}</dt>
            <dd class="truncate mt-1 font-semibold tracking-tight text-center" :class="stat.style">{{ stat.value }}</dd>
            <dd v-if="!maintenanceMode"  class="truncate mt-1 -mb-5 font-semibold tracking-tight text-xs text-gray-400 text-center">{{ stat.detail }}</dd>
            <dd v-if="maintenanceMode" class="truncate mt-1 -mb-5 font-semibold tracking-tight text-xs text-gray-500 dark:text-gray-300 text-center">
                id:{{stat.id}} line_nr:{{stat.line_nr}}
                <br />statIndex:{{statIndex}}
                <br />contract:{{stat.contractCounter}}
                <br />{{stat.address}}
                <br />chain: {{stat.chainNr}}
                <br />active:{{stat.is_active}} live:{{stat.is_live}}
            </dd>
        </div>
    </div>
    
  <!--...      
                {{stat.id}}
                {{stat.web3record_id}}
                {{stat.line}}
                {{stat.line_nr}}
                {{stat.parameters}}
                {{stat.abi}}
                {{stat.value}}
                {{stat.int_value}}
                {{stat.columns}}
                {{stat.type}}
                {{stat.style}}
                {{stat.detail}}
                {{stat.updated_at}}
                {{stat.created_at}}
                {{stat.is_active}}
                {{stat.is_live}}
                {{stat.address}}
                {{stat.chainNr}}
                stat.contractAbi
                {{stat.contractCounter}}

    {{cstat}}{ "id": 48, "title": "StakePoolC v0.17c", "contractCounter": 4, "nextNr": 22 }
                
-->
</template>