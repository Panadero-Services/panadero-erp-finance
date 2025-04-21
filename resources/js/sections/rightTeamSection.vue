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
  { label: 'Founded', value: '2021' },
  { label: 'Employees', value: '37' },
  { label: 'Countries', value: '12' },
  { label: 'Raised', value: '$25M' },
]


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
      <div>
         <div class="-mt-12 absolute inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-purple-500 rounded-full dark:bg-purple-700 divide-y ">
            <span class="font-bold text-sm text-gray-50 dark:text-gray-300">Teams</span>
         </div>

         <!-- Header -->
         <div class="text-gray-600 dark:text-gray-400 text-xs justify-center"> 
            <div class="grid grid-cols-3 mt-4 pt-3 text-black dark:text-white text-3xl ">
              
               <div class="text-black dark:text-white text-center text-xs mt-1">
                  <div class="ml-4 flex"><users-icon class="w-8"/>
                  <div class="text-indigo-300 text-center m-3">{{_set.domain}}</div>
                  </div>
                  <div class="text-gray-600">Domain</div>
               </div>
              
               <div class="text-black dark:text-white text-center">
                  <div>Teams</div>
                  <div class="text-gray-600 text-xs">Settings Function</div>
               </div>  
               <div class="text-black dark:text-white text-center text-xs mt-3">
                  <div class="text-indigo-300 text-center ">{{_set.domain}}</div>
                  <div class="text-gray-600">Domain</div>
               </div>
            </div>
         </div>


      <div>
         <dl class="mt-16 grid grid-cols-1 gap-x-6 gap-y-12 sm:mt-20 sm:grid-cols-2 sm:gap-y-16 lg:mt-28 lg:grid-cols-4 mr-2">
            <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 border-l border-white/20 pl-6">
               <dd class="text-xl font-semibold tracking-tight text-white text-center">{{ stat.value }}</dd>
               <dt class="text-base text-gray-300 text-center">{{ stat.label }}</dt>
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