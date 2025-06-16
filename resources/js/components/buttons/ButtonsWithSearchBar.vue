<script setup>
import { ref, provide, computed, onMounted, onUnmounted, watch } from 'vue';


// Search functionality
const searchQuery = ref('');
const isSearching = ref(false);
const searchTimeout = ref(null);
const selectedFields = ref(['title', 'content', 'author']);
const searchFields = [
    { value: 'title', label: 'Title' },
    { value: 'content', label: 'Content' },
    { value: 'author', label: 'Author' }
];

const showFieldPopup = ref(false);
const isInputFocused = ref(false);
const searchInput = ref(null);

// Initialize search from URL parameters and auto-focus
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    const searchFieldsParam = urlParams.get('search_fields');

    if (searchParam) {
        searchQuery.value = searchParam;
    }
    if (searchFieldsParam) {
        selectedFields.value = searchFieldsParam.split(',');
    }

    // Auto-focus search input
    if (searchInput.value) {
        searchInput.value.focus();
    }


    // Add keyboard event listener
    document.addEventListener('keydown', handleKeydown);


    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!target.closest('.field-popup') && !target.closest('.field-button')) {
            closePopup();
        }
    });


});

// Clean up on unmount
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});



// Server-side search using Inertia
const performSearch = () => {
  const query = searchQuery.value.trim();
  
  // Build search parameters
  const searchParams = {
    search: query || undefined,
    search_fields: query && selectedFields.value.length > 0 ? selectedFields.value.join(',') : undefined,
  };
  
  // Remove undefined values
  Object.keys(searchParams).forEach(key => 
    searchParams[key] === undefined && delete searchParams[key]
  );
  
  // Set loading state
  isSearching.value = true;
  
  // Use Inertia router to make the request
  router.get(window.location.pathname, searchParams, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onFinish: () => {
      isSearching.value = false;
    }
  });
};

// Debounced search
const handleSearchInput = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    performSearch();
  }, 400); // 400ms debounce for server requests
};

// Watch for search field changes
watch(selectedFields, () => {
  if (searchQuery.value.trim()) {
    clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
      performSearch();
    }, 200); // Shorter delay for field changes
  }
}, { deep: true });

// Keyboard shortcuts
const handleKeydown = (event) => {
    // Focus search with ⌘K
    if (event.key === 'k' && (event.metaKey || event.ctrlKey) && !event.altKey) {
        event.preventDefault();
        if (searchInput.value) {
            searchInput.value.focus();
            searchInput.value.select();
        }
    }
    // Clear search with Escape
    else if (event.key === 'Escape' && searchQuery.value) {
        event.preventDefault();
        searchQuery.value = '';
        performSearch();
    }
    // Toggle title field with ⌘⌃T
    else if (event.key === 't' && (event.metaKey || event.ctrlKey) && event.altKey) {
        event.preventDefault();
        toggleField('title');
    }
    // Toggle content field with ⌘⌃C
    else if (event.key === 'c' && (event.metaKey || event.ctrlKey) && event.altKey) {
        event.preventDefault();
        toggleField('content');
    }
    // Toggle author field with ⌘⌃A
    else if (event.key === 'a' && (event.metaKey || event.ctrlKey) && event.altKey) {
        event.preventDefault();
        toggleField('author');
    }
};


const closePopup = () => {
    showFieldPopup.value = false;
};

// Handle click outside
onMounted(() => {

});

// Close popup when input is focused
watch(isInputFocused, (focused) => {
    if (focused) {
        closePopup();
    }
});

// Toggle field selection
const toggleField = (field) => {
    const index = selectedFields.value.indexOf(field);
    if (index > -1) {
        selectedFields.value.splice(index, 1);
    } else {
        selectedFields.value.push(field);
    }
};


</script>



<template>
    <div class="flex flex-col lg:flex-row gap-4 mb-4 bg-blue-500">
        <!-- Left container -->
        <div class="flex-shrink-0 pl-2 h-12 bg-green-300 ">
            <button @click="_set.dark=false" type="button" :class="_button">Light</button>
            <button @click="_set.dark=true" type="button" :class="_button">Dark</button>
            <button @click="refreshPage" type="button" :class="_button">Refresh</button>
        </div>   

        <!-- Right container -->
        <div class="flex-grow lg:max-w-5xl flex gap-2 bg-green-400" id="toolbar">
            <div class="w-48 lg:w-64 flex-shrink-0">
                <div class="relative mt-2">
                    <input
                        ref="searchInput"
                        v-model="searchQuery"
                        type="text"
                        :placeholder="`Search in ${selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label || f).join(', ')}...`"
                        class="block w-full rounded-md border-0 pt-1 pb-0.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-1 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-400 sm:text-xs sm:leading-6 bg-white dark:bg-gray-800"
                        @input="handleSearchInput"
                        @focus="isInputFocused = true"
                        @blur="isInputFocused = false"
                    />
                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs text-gray-400 hidden sm:block">
                        <svg v-if="isSearching" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-else class="text-xs">⌘K</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 mt-2">
                <div class="flex items-center space-x-2">
                    <!-- Field Selection Button -->
                    <div class="relative">
                        <button 
                            @click.stop="showFieldPopup = !showFieldPopup"
                            class="field-button px-3 py-1 text-xs rounded-md ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 transition-colors"
                        >
                            Fields: {{ selectedFields.length }}
                        </button>
                        
                        <!-- Field Selection Popup -->
                        <div v-if="showFieldPopup && !isInputFocused" @click.stop class="field-popup absolute top-full mt-2 left-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg p-2 z-50 min-w-[200px]">
                            <div class="space-y-2">
                                <div class="grid grid-cols-3 w-80">
                                    <!-- Field Selection -->
                                    <div class="space-y-0.5 border-r border-gray-200 dark:border-gray-600">
                                        <div class="text-xxs text-gray-500 dark:text-gray-400 space-y-1">
                                            <div>Select: </div>
                                        </div>
                                        <div v-for="field in searchFields" :key="field.value" class="flex items-center px-2 space-y-0.5">
                                            <input 
                                                :id="`field-${field.value}`"
                                                v-model="selectedFields"
                                                :value="field.value"
                                                type="checkbox"
                                                :disabled="isSearching"
                                                class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded disabled:opacity-50"
                                            />
                                            <label 
                                                :for="`field-${field.value}`" 
                                                class="ml-2 text-xxs text-gray-700 dark:text-gray-300 cursor-pointer"
                                            >
                                                {{ field.label }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Keyboard Shortcuts -->
                                    <div class="px-2 space-y-1 ml-2 border-r border-gray-200 dark:border-gray-600">
                                        <div class="text-xxs text-gray-500 dark:text-gray-400 space-y-1">
                                            <div>Toggle fields: </div>
                                            <div><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃T</kbd> Title</div>
                                            <div><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃C</kbd> Content</div>
                                            <div><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃A</kbd> Author</div>
                                        </div>
                                    </div>

                                    <!-- Focus Shortcuts -->
                                    <div class="px-2 space-y-1 ml-2">
                                        <div class="text-xxs text-gray-500 dark:text-gray-400 space-y-1">
                                            <div>Press: </div>
                                            <div><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘K</kbd> to focus</div>
                                            <div><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">Esc</kbd> to clear</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Results Counter -->
            <div v-if="searchQuery.trim()" class="mb-4 px-2">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Found {{ posts.meta.total }} result{{ posts.meta.total !== 1 ? 's' : '' }}
                    <span v-if="searchQuery.trim()">
                        for "<span class="font-medium text-gray-800 dark:text-gray-200">{{ searchQuery }}</span>" 
                        in {{ selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label).join(', ') }}
                    </span>
                    <span class="text-xs ml-2">(searching entire database)</span>
                </p>
            </div>
            
        </div>
    </div>
</template>