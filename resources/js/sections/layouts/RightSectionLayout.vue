<script setup>
import { ref,  onMounted, computed } from 'vue'
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'

import UserModeSection from '@/sections/user-section/UserModeSection.vue';


const props = defineProps({
    set: Object,
    open: Boolean,
    buttons: Object, 
    stats: Object
});

// css 
const _shadowColor = ref('indigo');
const _hoverColor = 'hover:bg-indigo-500';

const _theme = computed(() => {
  return props.set.dark ? "bg-slate-950" : "bg-slate-100";
});

// css
const _button = { active: "w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400", 
                  inactive: "w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-300 ring-gray-300 dark:text-gray-800 dark:ring-gray-800" 
               };

const _font = { title:"text-2xl", 
                subtitle:"text-sm", 
                base:"text-xs" 
               };

const _color = { accent: "text-indigo-600 dark:text-indigo-300", 
                 active: "text-gray-600 dark:text-gray-400", 
                 inactive: "text-gray-400 dark:text-gray-600", 
                 stats: "text-green-600 dark:text-green-400"
               };

const _label = {  active: "m-1 inline-flex items-center rounded-md bg-indigo-500/10 px-2 py-1 text-xs font-medium text-indigo-800 dark:text-indigo-400 ring-1 ring-inset ring-indigo-500/20", 
                  inactive: "m-1 inline-flex items-center rounded-md bg-gray-500/10 px-2 py-1 text-xs font-medium text-gray-800 dark:text-gray-400 ring-1 ring-inset ring-gray-500/20",
                  hover: "hover:ring-indigo-600 "
               };
               
const _activeLabel = 'ring-gray-500 text-indigo-700 dark:text-indigo-300 bg-indigo-100';
const _inActiveLabel = 'bg-blue-600/10 ring-gray-500/20 text-gray-800 dark:text-gray-400';
const _icon = " w-4 h-4 mr-2 ";
const _validRing = 'text-green-600';
const _inValidRing = 'text-red-700';

</script>

<template>
   <TransitionRoot as="template" :show="open">
      <Dialog class="relative z-10" @close="open=false">
         <div class="fixed inset-0 overflow-hidden ">
            <div class="absolute inset-0 overflow-hidden ">
               <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16 ">
                  <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                     <DialogPanel class="pointer-events-auto w-screen max-w-md">

                        <!-- This is the main div -->
                        <div class="flex h-full flex-col overflow-y-scroll shadow-2xl shadow-indigo-700 dark:shadow-indigo-200 " :class="[set.dark ? 'dark bg-black shadow-'+_shadowColor+'-600' : 'light bg-gray-100 shadow-'+_shadowColor+'-200', _theme]">
                           <div class="divide-y divide-slate-200 dark:divide-slate-900">
                              <slot name="header"/>
                              <slot name="default"/>
                              <slot name="stats"/>

                              <slot name="footer">

                              <!-- Actions Section -->  
                              <div class="p-2 h-64 " :class="[_font.base, _color.active]"> 
                                 <div :class="[_font.subtitle, _color.inactive]">Actions Footer</div>

                                 <div class="h-10" :class="_font.subtitle"> 
                                    <div class="grid grid-cols-6 gap-2">
                                          <div v-for="but in buttons">
                                             <button v-if="but.active" @click="$emit('pushButton',but.name)" type="button" :class="[_button.active, but.css]">{{but.name}}</button>
                                          </div>
                                    </div>
                                 </div>

                                 <div class="h-10" :class="_font.subtitle"> 
                                    <div v-if="set.mode.advanced && !set.mode.noob" class="grid grid-cols-6 gap-2">
                                        <div class="col-span-6"></div>
                                    </div>

                                 </div>
                              <!-- UserModes -->  
                                 <div class="mt-8" :class="[_font.subtitle, _color.inactive]">UserModes</div>
                                 <user-mode-section :set="set" />
                              </div>

                                 <div class="mt-4">
                                    <div class="mt-4" v-if="set.mode.full">
                                       <dl class="grid grid-cols-1 gap-x-4 sm:grid-cols-2 lg:grid-cols-4 mt-3">
                                          <div v-for="(stat, statIdx) in stats" :key="statIdx" class="flex flex-col-reverse gap-y-3 gap-x-4 border-r border-gray-300 dark:border-gray-700">
                                             <dd class="text-center tracking-tight" :class="[_font.subtitle, _color.stats]">{{ stat.value }}</dd>
                                             <dt class="text-center" :class="_font.base, _color.active" >{{ stat.label }}</dt>
                                          </div>
                                       </dl>
                                    </div>
                                 </div>

                              </slot>

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