<script setup>
    import {ref, computed} from 'vue';
    import { StopIcon, WrenchIcon, EyeIcon } from '@heroicons/vue/24/solid'
    import {  } from '@heroicons/vue/24/solid'
    
    const props = defineProps({
        cstat: Object, 
        active: Boolean,
        maintenanceMode: Boolean,
        advancedMode: Boolean,
      });

const divColor = computed(() => {
    let aColors = ['ring-zinc-400 hover:bg-zinc-200 ',
        'ring-purple-500 hover:bg-purple-300 ' , 
        'ring-lime-500 hover:bg-lime-300' , 
        'ring-pink-500 hover:bg-pink-300' , 
        'ring-indigo-500 hover:bg-indigo-300' , 
        'ring-green-500 hover:bg-green-300' , 
        'ring-cyan-500 hover:bg-cyan-300' , 
        'ring-amber-500 hover:bg-amber-300' , 
        'ring-orange-600 hover:bg-orange-400' , 
        'ring-gray-500 hover:bg-gray-300' ,  
        'ring-teal-600 hover:bg-teal-400' , 
        'ring-red-700 hover:bg-red-500' ]

    //return `text-${aColors[props.cstat.contractCounter]}` 
    //return `text-[#50d71e]`,
return aColors[props.cstat.contractCounter%12];

})
</script>

<template>
    <span @click="$emit('delete',cstat.contractCounter)"  class="rounded-md px-2 py-2 xl:px-3.5 xl:py-2.5 text-xs xl:text-xs font-semibold text-black dark:text-gray-400 dark:hover:text-black shadow-sm ring-1 ring-inset ml-2 hover:text-black" :class="divColor">
           <span v-if="maintenanceMode" class=" text-gray-400 text-xs">[{{cstat.contractCounter}}]</span> {{ cstat.id}} {{ cstat.title}}
    </span>
</template>