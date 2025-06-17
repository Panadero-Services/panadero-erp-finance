<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Button0 from '@/components/buttons/button0.vue';

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



</script>

<template>
    <div class="flex flex-col lg:flex-row gap-4 mb-4">
      <!-- Left container -->
      <div class="flex-shrink-0 pl-2 h-12 space-x-1">

  <Button0 name="Search" @click="isSearching=!isSearching" />

  <Button0 name="Search" outline />
  <Button0 name="Search" />
  <Button0 variant="primary" >Search</Button0>
  <Button0 variant="primary" outline>Search</Button0>
  <Button0 variant="secondary">Search</Button0>
  <Button0 variant="success" outline>Search</Button0>
  <Button0 variant="danger">Search</Button0>
  <Button0 variant="warning" outline>Search</Button0>
  <Button0 variant="info">Search</Button0>
  <Button0 variant="dark">Search</Button0>




    </div>

    <!-- Search Bar -->
    <div class="flex-1 max-w-sm">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
        </div>
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          :placeholder="placeholder"
          class="block w-full rounded-md border-0 pl-10  pt-1 pb-0.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-1 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-400 sm:text-xs sm:leading-6 bg-white dark:bg-gray-800"
          @keyup.enter="handleSearch"
        />
        <label 
            class="ml-2 text-xxs text-gray-700 dark:text-gray-300 cursor-pointer"
        >
abc        </label>




        <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
        </div>
      </div>
    </div>

    <!-- New Record Button -->
  </div>
</template> 