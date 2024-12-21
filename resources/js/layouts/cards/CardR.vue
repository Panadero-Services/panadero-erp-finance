<script setup>
import { ref } from 'vue';

const props = defineProps({
    height: String
});

</script>

<template>
        <div class="relative " :class="height">
          <div class="absolute inset-px rounded-xs bg-white dark:bg-gray-950 lg:rounded-r-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] lg:rounded-r-[calc(2rem+1px)]" :class="_shadow">
            <div class="p-8">
              <p class="mt-0 text-lg/7 font-medium tracking-tight text-gray-950 dark:text-gray-100 max-lg:text-center"><slot name="title" /></p>
                <slot name="default" />
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 dark:ring-gray-600 lg:rounded-r-[2rem]" />
        </div>
</template>
