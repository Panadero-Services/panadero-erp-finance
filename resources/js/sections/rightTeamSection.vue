<script setup>
import { ref,  onMounted, computed } from 'vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UsersIcon } from '@heroicons/vue/24/outline'


import UpdateProfileInformationFormAlternative from '@/pages/Profile/Partials/UpdateProfileInformationFormAlternative.vue';

// subsections
import UserProfileSection from '@/sections/user-section/UserProfileSection.vue';
import UserOtherSection from '@/sections/user-section/UserOtherSection.vue';
import UserModeSection from '@/sections/user-section/UserModeSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';
const _db = useDbStore();
const _set = useSettingsStore();

const props = defineProps({
    user: Object
});

const _shadowColor = ref('indigo');

const _cancel = async () => {
  open.value=false;
}
//  'open' command
const open = ref(false)
defineExpose({ open });

// css 
const _hoverColor = 'hover:bg-indigo-500';

const _theme = computed(() => {
  return _set.dark ? "bg-indigo-950" : "bg-slate-100";
});

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

const stats = [
  { label: 'Founded', value: '2025' },
  { label: 'Members', value: '37' },
  { label: 'Projects', value: '12' },
  { label: 'Domains', value: '3' },
]

const _title = "text-3xl"
const _base = "text-xs"
const _main = "text-black dark:text-white";
const _sub = "text-gray-600 dark:text-gray-300 text-xs";
const _index = "text-indigo-600 dark:text-indigo-300";
const _value = "text-green-600 dark:text-green-400";

</script>

<template>
<TransitionRoot as="template" :show="open">
<Dialog class="relative z-10" @close="open=false">
<div class="fixed inset-0 overflow-hidden">
<div class="absolute inset-0 overflow-hidden">
<div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16 ">
<TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
<DialogPanel class="pointer-events-auto w-screen max-w-md">

   <!-- This is the main div -->
   <div class="flex h-full flex-col overflow-y-scroll shadow-2xl shadow-indigo-700 dark:shadow-indigo-200"  :class="_set.dark ? 'dark bg-slate-950 shadow-'+_shadowColor+'-600' : 'light bg-gray-100 shadow-'+_shadowColor+'-200'">
      

      <div class="divide-y divide-slate-200 dark:divide-slate-900" :class="_main">

         <!-- Header -->
         <div class="my-4" :class="_base"> 
            <div class="grid grid-cols-6 m-2">
              
               <div class="ml-2" :class="[_title,_index]">
                  <users-icon class="w-14"/>
               </div>
              
               <div class="col-span-2 ml-2">
                  <div :class="[_title, _index]">Team</div>
                  <div class="ml-2" :class="_sub">Preferences</div>
               </div>  

               <div class="col-span-3 mt-0 grid grid-cols-3 gap-x-2">
                  <div class="text-right" :class="_sub">Domain</div>
                  <div class="col-span-2" :class="_index">{{_set.domain}}</div>
                  <div class="text-right -mt-1" :class="_sub">Team</div>
                  <div class="col-span-2 -mt-1" :class="_index">{{_set.domain}}</div>
                  <div class="text-right -mt-1" :class="_sub">Project</div>
                  <div class=" -mt-1" :class="_index">{{_set.domain}}</div>
               </div>
            </div>
         </div>

         <div class="my-3 h-64" :class="_base"> 
            <div class="grid grid-cols-6 m-3">
               section2
            </div>
         </div>


         <div class="my-3 h-64" :class="_base"> 
            <div class="grid grid-cols-6 m-3">
               section3
            </div>
         </div>

         <div class="my-3 h-64" :class="_base"> 
            <div class="grid grid-cols-6 m-3">
               section4
            </div>
         </div>




         <div class="my-3">
            <dl class="grid grid-cols-1 gap-x-3 sm:grid-cols-2 lg:grid-cols-4 mt-3">
               <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 border-r border-white/20">
                  <dd class="text-xl font-semibold tracking-tight text-center" :class="_value">{{ stat.value }}</dd>
                  <dt class="text-base text-center" >{{ stat.label }}</dt>
               </div>
            </dl>
         </div>


      </div>


</div>

</DialogPanel>
</TransitionChild>
</div>
</div>
</div>
</Dialog>
</TransitionRoot>
</template>