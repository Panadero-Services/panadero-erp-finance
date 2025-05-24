<script setup>
import { ref } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';

const props = defineProps({
    post: Object,
    db: Object
});

const _iconChangedField = ref('iconChanged');
const _iconChangedId = ref(0);
const _iconChangedValue = ref(false);
const _true = true;
const _false = false;

let _updateIcon = async (_id, _field, _value) => {    
    console.log('_updateIcon');

    let i={};
    i.id = _id;
    i.field=_field;
    i.value=_value;
    _iconChangedField.value = `${_field} updated`;
    _iconChangedId.value = _id;
    _iconChangedValue.value = _value;
    
    let _response = await props.db.postUpdateIcon('Post', i);
    console.log(_response);
   }

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
       <p class="my-0 text-xxs font-medium tracking-tight text-gray-500 max-lg:text-center dark:text-gray-400"> updated: {{formatDistance(post.updated_at, new Date())}} ago </p>
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
        <CategorySectionIcon @click="_updateIcon(post.id, 'self', post.self==0), post.self = post.self == 1 ? 0 : 1 " icon="FingerPrint" :activated="post.self==1" :error="_false" title="is self"/>
        <CategorySectionIcon @click="_updateIcon(post.id, 'locked', post.locked==0), post.locked = post.locked == 1 ? 0 : 1 " icon="LockClosed" :activated="post.locked==1" :error="_false" title="is locked"/>
        <CategorySectionIcon @click="_updateIcon(post.id, 'featured', post.featured==0), post.featured = post.featured == 1 ? 0 : 1 " icon="Fire" :activated="post.featured==1" :error="_false" title="is featured"/>
        <CategorySectionIcon @click="_updateIcon(post.id, 'smart', post.smart==0), post.smart = post.smart == 1 ? 0 : 1 " icon="Heart" :activated="post.smart==1" :error="_false" title="is smart"/>
        <CategorySectionIcon @click="_updateIcon(post.id, 'public', post.public==0), post.public = post.public == 1 ? 0 : 1 " icon="LockOpen" :activated="post.public==1" :error="_false" title="is public"/>
        <CategorySectionIcon @click="_updateIcon(post.id, 'published', post.published==0), post.published = post.published == 1 ? 0 : 1 " icon="Tv" :activated="post.published==1" :error="_false" title="is published"/>
      </div>

     </div>
   </div>
   <div class="pointer-events-none absolute inset-px rounded-sm shadow ring-1 ring-black/5 max-lg:rounded-t-[2rem]" />
 </div>
   
</template>
