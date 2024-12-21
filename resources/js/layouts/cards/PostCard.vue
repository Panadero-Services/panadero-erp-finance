<script setup>
import { ref } from 'vue';
import { formatDistance } from 'date-fns';

const props = defineProps({
    post: Object,
});

// css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

</script>

<template>

 <div class="relative max-lg:row-start-1">
   <div class="absolute inset-px rounded-lg bg-white dark:bg-gray-950 max-lg:rounded-t-[2rem]" :class="_shadow" />
   <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
     <div class="px-8 pt-8 sm:px-10 sm:pt-10">
       <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950 max-lg:text-center dark:text-gray-50">{{post.id}} {{post.title}}</p>
       <p class="my-0 text-xxs font-medium tracking-tight text-gray-500 max-lg:text-center dark:text-gray-400"> {{formatDistance(post.created_at, new Date())}} ago </p>
       <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center dark:text-gray-300">{{post.body}}</p>
     </div>
     <div class="flex flex-1 items-center justify-center px-8 max-lg:pb-12 max-lg:pt-10 sm:px-10 lg:pb-2 border-t-2 dark:border-gray-700 my-4 text-gray-200">
       <div class="-ml-4 flex flex-wrap items-center justify-between sm:flex-nowrap">
         <div class="ml-4 mt-4 ">
           <div class="flex items-center">
             <div class="flex-shrink-0">
               <img class="h-12 w-12 rounded-full" :src="post.user.profile_photo_url" alt="" />
             </div>
             <div class="ml-4">
               <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-indigo-400">{{post.user.name}}</h3>
               <p class="text-sm text-gray-500 dark:text-slate-300 dark:hover:text-yellow-400">
                 <a href="#">@email:{{post.user.email}}</a>
               </p>
             </div>
           </div>
         </div>
         </div>
     </div>
   </div>
   <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 max-lg:rounded-t-[2rem]" />
 </div>
   
</template>
