<script setup>
import { ref, provide, computed, onMounted, onUnmounted, watch } from 'vue';
import { useForm, router } from "@inertiajs/vue3";

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// modal
import EditRecordModal from "@/components/modals/EditRecordModal.vue";
import ShowRecordModal from "@/components/modals/ShowRecordModal.vue";

// sections
import PostCard from '@/layouts/cards/PostCard.vue';
import Pagination from "@/layouts/Pagination.vue";

// types
//import { PostProps } from '@/types/post';
// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

import Button0 from '@/components/buttons/button.vue';


const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    posts: Object
});

// variables
const _module = "content";
const _table = 'posts';
const _model = 'Post';
const keyIndex = ref(0);

let _poolTimer; 
let _pulse = ref(false);
let showRecordMode= ref(false);
let editRecordMode= ref(false);

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

    // Start pulse animation
    _loopTimer();

    // Add keyboard event listener
    document.addEventListener('keydown', handleKeydown);
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

// Add keyboard event listener
onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

// Clean up on unmount
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
  clearTimeout(searchTimeout.value);
});

provide(/* key */ 'pulse', /* value */ _pulse);

// functions
const _loopTimer = async () => {
    _poolTimer =
    setInterval( () => {
        _pulse.value = !_pulse.value;
    }, 1000)
}

const _close = async (_nr) => {
    showRecordMode.value = false;
    editRecordMode.value = false;
}

const _activeRecord = ref();

// Store preserved modal settings
const _preservedModalSettings = ref({
  modalSize: null,
  fontSize: null,
  activeTab: null
});

const loadRecord = async (recordType, recordId) => {
  try {
    const response = await _db.getRecordById(_model, recordId);
    console.log(response.title)

    if (response) {
      // Clear existing record
     // _activeRecord.value = {};
      
      // Copy all fields from response to active record
      Object.keys(response).filter(key => (key !== 'user_id')).forEach(key => {
        _activeRecord.value[key] = response[key];
        console.log(key)
      });
      
      // Increment keyIndex to trigger re-render
      keyIndex.value++;
    }
  } catch (error) {
    console.error('Error loading record:', error);
  }
};

const handleRecordChange = (recordData) => {
  // Store preserved settings if provided
  if (recordData.preservedModalSize) {
    _preservedModalSettings.value.modalSize = recordData.preservedModalSize;
  }
  if (recordData.preservedFontSize) {
    _preservedModalSettings.value.fontSize = recordData.preservedFontSize;
  }
  if (recordData.preservedActiveTab) {
    _preservedModalSettings.value.activeTab = recordData.preservedActiveTab;
  }
  
  loadRecord(recordData.type, recordData.id);
};

const _show = async (_nr) => {
   _activeRecord.value = props.posts.data.filter(x => x.id ==_nr)[0];
    showRecordMode.value = true;
}

const _edit = async (_nr) => {
   _activeRecord.value = props.posts.data.filter(x => x.id ==_nr)[0];
    editRecordMode.value = true;
}



const _superSelfAdmin = computed(() => {
  return _set.superSelfAdmins.includes(_set.self);
});

const refreshPage = () => { window.location.reload(); };

// Close popup when clicking outside
const closePopup = () => {
    showFieldPopup.value = false;
};

// Handle click outside
onMounted(() => {
    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!target.closest('.field-popup') && !target.closest('.field-button')) {
            closePopup();
        }
    });
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
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
      <template #header>
         <pulse v-model="_pulse" :animation="_set.animate"/>

         <div v-if="showRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8 dark:bg-gray-800">
            <show-record-modal lng='en' 
                                :key="keyIndex"
                               :record="_activeRecord" 
                               :module="_module" 
                               :table="_table" 
                               @close="_close" 
                               @changeRecord="handleRecordChange"
                               :superSelfAdmin="_superSelfAdmin==1" 
                               :db="_db"
                               :preservedModalSize="_preservedModalSettings.modalSize"
                               :preservedFontSize="_preservedModalSettings.fontSize"
                               :preservedActiveTab="_preservedModalSettings.activeTab" />
         </div>
         <div v-if="editRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8 dark:bg-gray-800">
            <edit-record-modal lng='en' 
                                :key="keyIndex"
                               :record="_activeRecord" 
                               :module="_module" 
                               :table="_table" 
                               @close="_close" 
                               @changeRecord="handleRecordChange"
                               :superSelfAdmin="_superSelfAdmin==1" 
                               :db="_db"
                               :preservedModalSize="_preservedModalSettings.modalSize"
                               :preservedFontSize="_preservedModalSettings.fontSize"
                               :preservedActiveTab="_preservedModalSettings.activeTab" />
         </div>

      </template>

      <template #intro />

      <template #default>
        <div id="posts" class="w-full min-h-4 min-w-full">

            
            <div class="flex flex-col lg:flex-row gap-4 mb-4">
                <!-- Left container -->
                <div class="flex-shrink-0 pl-2 h-12 mt-2">
                    <Button0 name="Light" @click="_set.dark=false" />
                    <Button0 name="Dark" @click="_set.dark=true" />
                    <Button0 name="Refresh" @click="refreshPage" />

                </div>   

                <!-- Right container -->
                <div class="flex-grow lg:max-w-5xl flex gap-2" id="toolbar">
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
                </div>
            </div>

            <div id="whatever" class="w-full min-h-4 min-w-full dark:bg-black">
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

                <!-- Posts Grid -->
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                  <div v-for="post in posts.data" :key="post.id" class="text-sm dark:text-gray-200">
                      <PostCard :post="post" :module="_module" :table="_table" @edit="_edit" @show="_show" :db="_db" :set="_set"/>
                  </div>
                </div>

                <!-- No Results Message -->
                <div v-if="searchQuery.trim() && posts.data.length === 0" class="text-center py-12">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">No posts found</h3>
                        <p class="text-sm">
                            No posts match your search for "<span class="font-medium">{{ searchQuery }}</span>" 
                            in {{ selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label).join(', ') }} across the entire database.
                        </p>
                        <button 
                            @click="searchQuery = ''; performSearch();"
                            class="mt-4 px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                        >
                            Clear Search
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination :meta="posts.meta" class="dark:text-gray-200"/>
             </div>
        </div>
      </template>

      <template #footer>
   </template>

   </AppToolbarLayout>
</template>