<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    set: Object,
});

</script>

<template>
  <footer class="bg-indigo-900 text-gray-300">
    <div class="mx-auto max-w-7xl px-3 pb-4 pt-2 sm:pt-2 lg:px-2 lg:pt-4">
      <div class="xl:grid xl:grid-cols-3 xl:gap-4">
        <div class="space-y-2">
          <p class=" ">Main SECTION</p>
        </div>
      </div>
      <div class="mt-2 border-t border-white/10 pt-2 sm:mt-4 lg:mt-6 ">
      </div>
    </div>
  </footer>
</template>
