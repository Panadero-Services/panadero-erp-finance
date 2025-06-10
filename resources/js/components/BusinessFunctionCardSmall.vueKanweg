<template>
  <div class="relative">
    <div class="absolute top-2 right-2">
      <button @click="togglePopup" class="text-gray-500 hover:text-gray-700">
        <Bars3Icon class="h-5 w-5" />
      </button>
      <div v-if="showPopup" class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md z-10">
        <div v-for="option in f.options" :key="option.name" class="p-2 hover:bg-gray-100">
          <a :href="option.url" class="text-sm text-gray-700">{{ option.name }}</a>
        </div>
      </div>
    </div>
    <div class="p-4 border rounded-lg shadow-sm">
      <h3 class="text-lg font-semibold">{{ f.title }}</h3>
      <p class="text-sm text-gray-600">{{ f.description }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Bars3Icon } from '@heroicons/vue/24/outline';

const props = defineProps({
  f: {
    type: Object,
    required: true,
  },
});

const showPopup = ref(false);

const togglePopup = () => {
  showPopup.value = !showPopup.value;
};
</script> 