<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  module: {
    type: String,
    required: true
  },
  table: {
    type: String,
    required: true
  },
  buttonText: {
    type: String,
    default: 'New Record'
  },
  placeholder: {
    type: String,
    default: 'Search...'
  },
  searchRoute: {
    type: String,
    required: true
  }
});

const searchQuery = ref('');
const isSearching = ref(false);

// Debounce search
let searchTimeout;
watch(searchQuery, (newValue) => {
  clearTimeout(searchTimeout);
  if (newValue.length > 2 || newValue.length === 0) {
    isSearching.value = true;
    searchTimeout = setTimeout(() => {
      router.get(
        props.searchRoute,
        { search: newValue },
        { preserveState: true, preserveScroll: true }
      );
      isSearching.value = false;
    }, 300);
  }
});

const handleSearch = () => {
  if (searchQuery.value.length > 2 || searchQuery.value.length === 0) {
    isSearching.value = true;
    //router.get(
    //  props.searchRoute,
    //  { search: searchQuery.value },
    //  { preserveState: true, preserveScroll: true }
    //);
    isSearching.value = false;
  }
};

const _button = " mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

</script>

<template>
  <div class="flex items-center justify-between mb-4">
   
    <div class="min-w-64">
      <button @click="isSearching=!isSearching" type="button" :class="_button">Search</button>
    </div>

    <!-- Search Bar -->
    <div class="flex-1 max-w-sm">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
        </div>
        <input
          type="text"
          v-model="searchQuery"
          :placeholder="placeholder"
          class="block w-full pl-10 pr-3 py-0.5 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-0.5 focus:ring-blue-500 focus:border-blue-500 sm:text-xs"
          @keyup.enter="handleSearch"
        />
        <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
        </div>
      </div>
    </div>

    <!-- New Record Button -->
  </div>
</template> 