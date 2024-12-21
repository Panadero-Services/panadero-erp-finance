<script setup>
import { ref } from 'vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';
// define emits
const emit = defineEmits(['add']);

// call parameters
const props = defineProps({
  comp: Object
});

//@click="cards.push({id:_counter, comp:c.name}), _counter++" 
</script>

<template>
  <div @click="$emit('add',comp)" :class="[comp.bgColor, 'flex w-8 sm:w-10 md:w-10 lg:w-12 flex-shrink-0 items-center justify-center rounded-l-md text-xxxs sm:text-xxs md:text-xs font-medium text-white']">
    {{ comp.initials }}
  </div>
  <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
    <div class="flex-1 truncate px-2 py-1 text-xxs sm:text-xs">
      <a :href="comp.href" class="font-medium text-gray-900 hover:text-gray-600">{{ comp.name }}</a>
      <p class="text-xxs sm:text-xs text-gray-500">{{ comp.members }} Members</p>
    </div>
    <div class="flex-shrink-0 pr-2">
      <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-transparent bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <span class="sr-only">Open options</span>
        <EllipsisVerticalIcon class="ml-4 h-5 w-5" aria-hidden="true" />
      </button>
    </div>
  </div>
</template>