<script setup>
import { ref,  onMounted, computed } from 'vue'

const props = defineProps({ package: Object });
</script>


<template>
  <nav class="flex items-center justify-center" aria-label="Progress">

    <ol role="list" class="flex items-center ">
      <li v-for="(_step,_idx) in package.stages" :key="_step.name">

        <p class="w-16 text-xxs">
          <span class="">{{ _step.name }}</span>
        </p>
        

        <!-- option 1 step pending -->
        <a v-if="_idx < package.step" :title="_step.result" class="block size-3 rounded-full bg-indigo-500 hover:bg-indigo-900 ml-3 mt-1">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <!-- option2 step active -->
        <a v-else-if="_idx === package.step" :title="_step.result" class="" aria-current="_step">
          <span class="" aria-hidden="true">
            <span class="size-full rounded-full bg-indigo-200" />
          </span>
          <span class=" block size-3 rounded-full bg-indigo-500 ml-3 mt-1" aria-hidden="true" />
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <!-- option3 step finished -->
        <a v-else :title="_step.result" class="block size-3 rounded-full bg-gray-300 hover:bg-gray-500 ml-3 mt-1">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

          <span class="text-xxs text-slate-700 dark:text-gray-400">{{ _step.elapsed }}ms</span>

      </li>
    </ol>

  </nav>
  <nav class="flex items-left justify-start text-xs mt-4" aria-label="Progress">
    <p >
      Step {{ package.step+1 }} of {{ package.stages.length }} -->  {{package.finished}} 
       {{package.stages[package.step].name}}
    </p>
  </nav>

</template>





