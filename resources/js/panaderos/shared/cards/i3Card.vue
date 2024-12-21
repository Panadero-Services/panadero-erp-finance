<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
 id: Number
});

const card = defineModel('card');
const buttons = defineModel('buttons');
const emit = defineEmits(['start', 'stop', 'reset', 'kill'])
const active = true;

</script>

<template>
  <div class=" shadow shadow-lg shadow-gray-400 dark:shadow-gray-200 h-40" >
      <div class="h-8 content-start text-center text-slate-800 dark:text-slate-300">{{card.title}}</div>
      <div class="h-4 content-start text-center text-xxxs text-slate-800 dark:text-slate-300">{{card.subTitle}}</div>
      <!-- Content goes here -->
      <div class="h-24 text-center text-5xl text-black dark:text-lime-400 pt-2">{{card.waarde}}</div>
      <div class="h-4 text-xxxs text-black dark:text-slate-200 grid grid-cols-4 bg-slate-200 dark:bg-slate-950 divide-x divide-gray-300 dark:divide-slate-600 ">
         <button v-for="b in buttons" @click="emit(b.command, id)" type="button" :class="['hover:bg-'+b.color+'-400','dark:hover:bg-'+b.color+'-800']">{{b.command.toUpperCase()}}</button>
      </div>
  </div>
</template>


