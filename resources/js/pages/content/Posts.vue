<script setup lang="ts">
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
const searchQuery = ref("");
const selectedFields = ref(["title"]); // Default to title only, array for multiple selection
const isSearching = ref(false);
const searchFields = [
  { value: "title", label: "Title" },
  { value: "content", label: "Content" },
  { value: "author", label: "Author" }
];

// Initialize search from URL parameters and auto-focus
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const search = urlParams.get('search');
  const fields = urlParams.get('search_fields');
  
  if (search) {
    searchQuery.value = search;
  }
  if (fields) {
    const fieldsArray = fields.split(',').filter(f => searchFields.some(sf => sf.value === f));
    if (fieldsArray.length > 0) {
      selectedFields.value = fieldsArray;
    }
  }
  
  // Auto-focus search input after page load
  setTimeout(() => {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
      searchInput.focus();
    }
  }, 100); // Small delay to ensure DOM is ready
});

// Server-side search using Inertia
let searchTimeout;
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
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 400); // 400ms debounce for server requests
};

// Watch for search field changes
watch(selectedFields, () => {
  if (searchQuery.value.trim()) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      performSearch();
    }, 200); // Shorter delay for field changes
  }
}, { deep: true });

// Keyboard shortcuts
const handleKeydown = (event) => {
  // Ctrl/Cmd + K to focus search
  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault();
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
      searchInput.focus();
      searchInput.select();
    }
  }
  // Escape to clear search
  if (event.key === 'Escape' && searchQuery.value) {
    searchQuery.value = '';
    performSearch();
  }
};

// Add keyboard event listener
onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

// Clean up on unmount
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
  clearTimeout(searchTimeout);
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


const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

const refreshPage = () => { window.location.reload(); };

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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4"> 
            <div class="pl-2 h-12" >
                <button @click="_set.dark=false" type="button" :class="_button">Light</button>
                <button @click="_set.dark=true" type="button" :class="_button">Dark</button>
                <button @click="refreshPage" type="button" :class="_button">Refresh</button>
            </div>      
            <div class="flex flex-col lg:flex-row gap-4" id="toolbar">
                <div class="lg:w-80">
                    <div class="relative">
                        <input 
                            v-model="searchQuery" 
                            @input="handleSearchInput"
                            name="search" 
                            type="text" 
                            autocomplete="off" 
                            :disabled="isSearching"
                            class="w-full rounded-sm bg-gray-50 dark:bg-slate-950 px-3 py-1.5 pr-12 text-sm text-gray-600 dark:text-gray-300 focus:ring-0 focus:outline-none border border-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-400 disabled:opacity-50 disabled:cursor-not-allowed" 
                            :placeholder="`Search in ${selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label).join(', ')}...`" 
                        />
                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs text-gray-400 hidden sm:block">
                            <svg v-if="isSearching" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-else class="text-xs">⌘K</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                        Press <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs">⌘K</kbd> to focus, <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs">Esc</kbd> to clear
                    </p>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap gap-4 mt-1">
                        <div v-for="field in searchFields" :key="field.value" class="flex items-center">
                            <input 
                                :id="`field-${field.value}`"
                                v-model="selectedFields"
                                :value="field.value"
                                type="checkbox"
                                :disabled="isSearching"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded disabled:opacity-50"
                            />
                            <label 
                                :for="`field-${field.value}`" 
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer"
                            >
                                {{ field.label }}
                            </label>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Searching in: <span class="font-medium text-gray-700 dark:text-gray-300">
                            {{ selectedFields.length > 0 ? selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label).join(', ') : 'None selected' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div id="whatever" class="w-full min-h-4 min-w-full dark:bg-black">
            <!-- Search Results Counter -->
            <div v-if="searchQuery.trim()" class="mb-4 px-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">
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
      </template>

      <template #footer>
   </template>

   </AppToolbarLayout>
</template>