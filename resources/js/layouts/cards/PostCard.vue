<script setup>
import { ref } from 'vue';
import { formatDistance } from 'date-fns';

import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';


const props = defineProps({
    post: Object,
});

// css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

</script>

<template>

 <div class="relative max-lg:row-start-1">

   <div class="absolute inset-px rounded-sm bg-white dark:bg-gray-950 max-lg:rounded-t-[2rem]" :class="_shadow" />
   <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
     <div class="px-8 pt-4 sm:px-10 sm:pt-6">
       <p @click="$emit('whatever',post.id)" class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950 max-lg:text-center dark:text-gray-50">{{post.id}} {{post.title}}</p>
       <p class="my-0 text-xxs font-medium tracking-tight text-gray-500 max-lg:text-center dark:text-gray-400"> created: {{formatDistance(post.created_at, new Date())}} ago :  updated: {{formatDistance(post.updated_at, new Date())}} ago </p>
       <p class="mt-4 text-sm/6 text-gray-600 max-lg:text-center dark:text-gray-300 mb-3">{{post.body}}</p>
     </div>

     <div class="grid grid-cols-2 border-t-2 dark:border-gray-700 my-3 mx-6">
       <div class="flex flex-1 justify-start text-gray-200">
         <div class=" flex flex-wrap items-center justify-between sm:flex-nowrap">
           <div class="ml-0 mt-4 ">
             <div class="flex items-center">
               <div class="flex-shrink-0">
                 <img class="h-12 w-12 rounded-full" :src="post.user.profile_photo_url" alt="" />
               </div>
               <div class="ml-2">
                 <h3 class="text-base font-semibold text-gray-900 dark:text-indigo-400">{{post.user.name}}</h3>
                 <p class="text-sm text-gray-500 dark:text-slate-300 dark:hover:text-yellow-400">
  <!-- form                  <a href="#">@email:{{post.user.id}}</a>-->
                 </p>
               </div>
             </div>
           </div>
           </div>
       </div>

      <div class="flex space-x-2 mt-2 justify-end mr-3">  
        <CategorySectionIcon icon="FingerPrint" :activated="1" :error="0" title="self"/>
        <CategorySectionIcon icon="Wrench" :activated="1" :error="0" title="self"/>
        <CategorySectionIcon icon="ArrowPath" :activated="1" :error="0" title="self"/>
        <CategorySectionIcon icon="CloudArrowUp" :activated="1" :error="0" title="self"/>
        <CategorySectionIcon icon="LockClosed" :activated="1" :error="0" title="self"/>
        <CategorySectionIcon icon="User" :activated="0" :error="0" title="self"/>
        <CategorySectionIcon icon="Heart" :activated="0" :error="0" title="self"/>
        <CategorySectionIcon icon="Fire" :activated="0" :error="0" title="self"/>
        <CategorySectionIcon icon="Tv" :activated="0" :error="0" title="self"/>
      </div>
     </div>
   </div>
   <div class="pointer-events-none absolute inset-px rounded-sm shadow ring-1 ring-black/5 max-lg:rounded-t-[2rem]" />
 </div>
   
</template>
