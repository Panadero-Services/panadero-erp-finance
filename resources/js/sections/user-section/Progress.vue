<script setup>
import { ref,  onMounted, computed } from 'vue'

const props = defineProps({ package: Object });

const _steps = [
  { name: 'prepare', href: '#', status: 'current' },
  { name: 'validate-1', href: '#', status: 'upcoming' },
  { name: 'request', href: '#', status: 'upcoming' },
  { name: 'response', href: '#', status: 'upcoming' },
  { name: 'validate-2', href: '#', status: 'upcoming' },
  { name: 'finished', href: '#', status: 'upcoming' },
]

</script>


<template>
  <nav class="flex items-center justify-center" aria-label="Progress">
    <p class="text-sm font-medium">Step {{ package.step+1 }} of {{ _steps.length }} {{package.finished}} </p>
    

    <ol role="list" class="ml-8 flex items-center space-x-5">
      <li v-for="(_step,_idx) in _steps" :key="_step.name">

        <a v-if="_idx < package.step" :href="_step.href" class="block size-2.5 rounded-full bg-indigo-600 hover:bg-indigo-900">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <a v-else-if="_idx === package.step" :href="_step.href" class="relative flex items-center justify-center" aria-current="_step">
          <span class="absolute flex size-5 p-px" aria-hidden="true">
            <span class="size-full rounded-full bg-indigo-200" />
          </span>
          <span class="relative block size-2.5 rounded-full bg-indigo-600" aria-hidden="true" />
          <span class="sr-only">{{ _step.name }}</span>
        </a>

        <a v-else :href="_step.href" class="block size-2.5 rounded-full bg-gray-200 hover:bg-gray-400">
          <span class="sr-only">{{ _step.name }}</span>
        </a>

      </li>
    </ol>

  </nav>
  <nav class="flex items-center justify-center text-xs" aria-label="Progress">
    {{_steps[package.step].name}}
  </nav>

</template>

