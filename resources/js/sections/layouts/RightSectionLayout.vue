<script setup>
import { ref,  onMounted, computed } from 'vue'
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'

const props = defineProps({
    set: Object,
    open: Boolean
});

// css 
const _shadowColor = ref('indigo');
const _hoverColor = 'hover:bg-indigo-500';

const _theme = computed(() => {
  return props.set.dark ? "bg-slate-950" : "bg-slate-100";
});
</script>

<template>
   <TransitionRoot as="template" :show="open">
      <Dialog class="relative z-10" @close="open=false">
         <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden" :class="_theme">
               <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16 ">
                  <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                     <DialogPanel class="pointer-events-auto w-screen max-w-md">

                        <!-- This is the main div -->
                        <div class="flex h-full flex-col overflow-y-scroll shadow-2xl shadow-indigo-700 dark:shadow-indigo-200" :class="set.dark ? 'dark bg-black shadow-'+_shadowColor+'-600' : 'light bg-gray-100 shadow-'+_shadowColor+'-200'">
                           <div class="divide-y divide-slate-200 dark:divide-slate-900" :class="_theme">
                              <slot name="header"/>
                              <slot name="default"/>
                              <slot name="stats"/>
                              <slot name="footer"/>
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