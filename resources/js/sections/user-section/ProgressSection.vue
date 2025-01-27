<script setup>
import { ref,  onMounted, computed } from 'vue'

const props = defineProps({ package: Object });


</script>


<template>
  <nav class="flex items-center justify-center" aria-label="Progress">

    <ol role="list" class="flex items-center ">
      <li v-for="(_step,_idx) in package.stages" :key="_step.name">

        <p class="w-16 text-xxxs">
          <span class="">{{ _step.name }}</span>
        </p>

        <!-- option 1 step pending -->
        <a v-if="_idx < package.step" class="block size-2.5 rounded-full bg-indigo-600 hover:bg-indigo-900 ml-3">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <!-- option2 step active -->
        <a v-else-if="_idx === package.step" class="" aria-current="_step">
          <span class="" aria-hidden="true">
            <span class="size-full rounded-full bg-indigo-200" />
          </span>
          <span class=" block size-2.5 rounded-full bg-indigo-600 ml-3" aria-hidden="true" />
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <!-- option3 step finished -->
        <a v-else class="block size-2.5 rounded-full bg-gray-200 hover:bg-gray-400 ml-3">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

          <span class="text-xxxs text-slate-700 dark:text-gray-400 ">{{ _step.elapsed }}ms</span>

      </li>
    </ol>

  </nav>
  <nav class="flex items-center justify-center text-xs mt-4" aria-label="Progress">
    <p class="">
      Step {{ package.step+1 }} of {{ package.stages.length }} -->  {{package.finished}} 
       {{package.stages[package.step].name}}
    </p>
  </nav>

</template>





