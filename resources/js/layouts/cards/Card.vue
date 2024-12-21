<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    height: String,
    corners: String, 
    status: String, 
    active: Number
});

const emit = defineEmits(['start', 'stop', 'reset', 'kill'])


const _shadow = computed(() => {
   if(props.status=="pause") return " bg-white dark:bg-slate-950 shadow-lg shadow-yellow-300 dark:shadow-yellow-600";
   if(props.status=="error") return "bg-white dark:bg-slate-950 shadow-lg shadow-red-300 dark:shadow-red-600";
   if(props.status=="run") return "bg-white dark:bg-slate-950 shadow-lg shadow-green-300 dark:shadow-green-600";
   if(props.status=="active") return "bg-white dark:bg-slate-950 shadow-md shadow-indigo-300 dark:shadow-indigo-600";
   if(props.status=="module-active") return "bg-slate-50 dark:bg-slate-950 shadow-md shadow-indigo-300 dark:shadow-indigo-600";
   if(props.status=="module") return "bg-slate-50 dark:bg-slate-800 shadow-md shadow-gray-300 dark:shadow-slate-600";
   return "bg-white dark:bg-slate-950 shadow-lg shadow-gray-300 dark:shadow-slate-600";
});

const _corners = computed(() => {
   if(props.corners=="tr") return { main: " lg:rounded-tr-[2rem] ", shadow: " lg:rounded-tr-[calc(2rem+1px)] "};
   if(props.corners=="tl") return { main: " lg:rounded-tl-[2rem] ", shadow: " lg:rounded-tl-[calc(2rem+1px)] "};
   if(props.corners=="bl") return { main: " lg:rounded-bl-[2rem] ", shadow: " lg:rounded-bl-[calc(2rem+1px)] "};
   if(props.corners=="br") return { main: " lg:rounded-br-[2rem] ", shadow: " lg:rounded-br-[calc(2rem+1px)] "};
   if(props.corners=="l") return { main: " lg:rounded-l-[2rem] ", shadow: " lg:rounded-l-[calc(2rem+1px)] "};
   if(props.corners=="r") return { main: " lg:rounded-r-[2rem] ", shadow: " lg:rounded-r-[calc(2rem+1px)] "};
   if(props.corners=="all") return { main: " lg:rounded-[2rem] ", shadow: " lg:rounded-[calc(2rem+1px)] "};
   return "";
});

const _bg = computed(() => {

});



</script>

<template>
   <div class="relative  motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500" :class="height">
      <div class=" absolute inset-px rounded-xs bg-gray-100 dark:bg-gray-950 " :class="_corners.main" />

      <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)]" :class="_shadow+_corners.shadow">
         <div class="divide-y divide-gray-50 dark:divide-gray-900 overflow-hidden rounded-lg shadow">

            <div class=" px-3 lg:px-4 py-1 h-8 text-sm text-slate-800 dark:text-white bg-slate-100 dark:bg-slate-900">
               <slot name="title" />
            </div>


            <div class=" px-3 lg:px-4 h-4 text-xxs text-slate-500 dark:text-white ">
               <slot name="intro" />
            </div>

            <div class=" px-3 lg:px-4 h-40">
               <!-- Content goes here -->
               <p class="mt-0 text-lg/7 font-medium tracking-tight text-gray-950 dark:text-gray-100 max-lg:text-center text-sm lg:text-base">
               </p>
               <p class="text-xxs lg:text-xs">
                  <slot name="default" />
               </p>
            </div>

            <div class=" mt-6 h-6  text-xxxs text-slate-500 dark:text-slate-200 grid grid-cols-4 bg-slate-100 dark:bg-slate-900 divide-x">

               <button @click="emit('start')" type="button" class="hover:bg-slate-300 dark:hover:bg-slate-600" :class="active==1 ? 'text-green-600' : ''" >START</button>
               <button @click="emit('stop')" type="button" class="hover:bg-slate-300 dark:hover:bg-slate-600" :class="active==0 ? 'text-red-600' : ''">STOP</button>
               <button @click="emit('reset')" type="button" class="hover:bg-slate-300 dark:hover:bg-slate-600">RESET</button>
               <button @click="emit('kill')" type="button" class="hover:bg-slate-300 dark:hover:bg-slate-600">KILL</button>

               <!-- Content goes here -->
               <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
            </div>
         </div>
      </div>

      <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 dark:ring-gray-600" :class="_corners.main" />
   </div>

</template>
