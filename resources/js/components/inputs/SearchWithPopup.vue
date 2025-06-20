<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { MagnifyingGlassIcon, FunnelIcon } from '@heroicons/vue/24/outline';
import Button0 from '@/components/buttons/button0.vue';

const props = defineProps({
    searchableColumns: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Search...'
    },
    buttonText: {
        type: String,
        default: 'Search'
    },
    searchResults: {
        type: Object,
        default: () => ({ meta: { total: 0 } })
    },
    showSearchInput: {
        type:Boolean,
        default: false
    }
});

const emit = defineEmits(['search', 'toggle-search']);

const searchQuery = ref('');
const selectedFields = ref([]);
const showFieldPopup = ref(false);
const isInputFocused = ref(false);
const isSearching = ref(false);

// Convert searchable columns to field objects
const searchFields = computed(() => {
    return props.searchableColumns.map(column => ({
        value: column,
        label: column.charAt(0).toUpperCase() + column.slice(1).replace(/_/g, ' ')
    }));
});

// Debounce search
let searchTimeout;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    if (searchQuery.value.length > 1 || searchQuery.value.length === 0) {
        isSearching.value = true;
        searchTimeout = setTimeout(() => {
            emit('search', {
                query: searchQuery.value,
                fields: selectedFields.value
            });
            isSearching.value = false;
        }, 300);
    }
};

// Watch for search query changes
watch(searchQuery, debouncedSearch);

// Handle search button click
const handleSearch = () => {
    if (searchQuery.value.length > 1 || searchQuery.value.length === 0) {
        isSearching.value = true;
        emit('search', {
            query: searchQuery.value,
            fields: selectedFields.value
        });
        isSearching.value = false;
    }
};

// Toggle field popup
const toggleFieldPopup = () => {
    showFieldPopup.value = !showFieldPopup.value;
};

// Toggle search input
const toggleSearchInput = () => {
    emit('toggle-search');
};

// Get shortcut key based on index
const getShortcutKey = (index) => {
    return `⌘${index + 1}`;
};

// Get placeholder text based on selected fields
const placeholderText = computed(() => {
    if (selectedFields.value.length === 0) {
        return props.placeholder;
    }
    const fieldLabels = selectedFields.value.map(field => 
        searchFields.value.find(sf => sf.value === field)?.label || field
    );
    return `Search in ${fieldLabels.join(', ')}...`;
});

// Handle keyboard shortcuts
const handleKeydown = (event) => {
    // Cmd+K to focus
    if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
        event.preventDefault();
        document.getElementById('search-input')?.focus();
    }
    
    // Esc to clear
    if (event.key === 'Escape') {
        searchQuery.value = '';
        showFieldPopup.value = false;
    }
    
    // Dynamic field toggles (⌘1, ⌘2, etc.)
    for (let i = 0; i < Math.min(props.searchableColumns.length, 9); i++) {
        if ((event.metaKey || event.ctrlKey) && event.key === String(i + 1)) {
            event.preventDefault();
            if (props.searchableColumns[i]) {
                toggleField(props.searchableColumns[i]);
            }
        }
    }
};

// Toggle specific field
const toggleField = (fieldName) => {
    const index = selectedFields.value.indexOf(fieldName);
    if (index > -1) {
        selectedFields.value.splice(index, 1);
    } else {
        selectedFields.value.push(fieldName);
    }
};

// Handle click outside to close popup
const handleClickOutside = (event) => {
    if (!event.target.closest('.search-container')) {
        showFieldPopup.value = false;
    }
};

// Add positioning logic for teleported popup
const popupPosition = ref({ top: 0, left: 0 });

const updatePopupPosition = () => {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        const rect = searchInput.getBoundingClientRect();
        popupPosition.value = {
            top: rect.bottom + window.scrollY + 8, // 8px gap
            left: rect.right - 320 // 320px is the popup width (w-80)
        };
    }
};

// Watch for popup visibility changes
watch(showFieldPopup, (newVal) => {
    if (newVal) {
        nextTick(() => {
            updatePopupPosition();
        });
    }
});

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    document.addEventListener('click', handleClickOutside);
    
    // Initialize with all fields selected
    selectedFields.value = [...props.searchableColumns];
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="search-container relative justify-start mx-2">
        <div class="flex flex-col lg:flex-row gap-4 mb-2">
            <!-- Toggle Search Icon -->
            <div class="flex items-center">
                <button
                    @click="toggleSearchInput"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1"
                    :class="{ 'text-indigo-600 dark:text-indigo-400': showSearchInput }"
                >
                    <MagnifyingGlassIcon class="h-4 w-4" />
                </button>
            </div>

            <!-- Search Bar -->
            <div v-if="showSearchInput" class="flex-1 max-w-[200px]">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        id="search-input"
                        type="text"
                        v-model="searchQuery"
                        :placeholder="placeholderText"
                        class="block w-full rounded-md border-0 pl-10 pr-10 pt-1 pb-0.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-1 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-400 sm:text-xs sm:leading-6 bg-white dark:bg-gray-800"
                        @keyup.enter="handleSearch"
                        @focus="isInputFocused = true"
                        @blur="isInputFocused = false"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center space-x-2">
                        <!-- Field Selection Button -->
                        <button
                            @click="toggleFieldPopup"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            :class="{ 'text-indigo-600 dark:text-indigo-400': showFieldPopup }"
                        >
                            <FunnelIcon class="h-4 w-4" />
                        </button>
                        <!-- Loading Spinner -->
                        <div v-if="isSearching" class="animate-spin rounded-full h-4 w-4 border-b border-blue-500"></div>
                    </div>
                </div>
            </div>
            <div v-else>
                <span class="text-gray-400 dark:text-gray-600 text-xs -ml-2">{{searchQuery || 'search'}}</span>
            </div>


        </div>

        <!-- Search Results Counter -->
        <div v-if="searchQuery.trim() && showSearchInput" class="">
            <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap overflow-hidden">
                Found {{ searchResults.meta.total }} result{{ searchResults.meta.total !== 1 ? 's' : '' }}
                <span v-if="searchQuery.trim()">
                    for "<span class="font-medium text-gray-800 dark:text-gray-200">{{ searchQuery }}</span>" 
                    in {{ selectedFields.map(f => searchFields.find(sf => sf.value === f)?.label).join(', ') }}
                </span>
                <span class="text-xs ml-2">(searching entire database)</span>
            </p>
        </div>

        <!-- Field Selection Popup -->
        <div v-if="showFieldPopup && !isInputFocused && showSearchInput" @click.stop class="field-popup fixed bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg p-2 z-[9999] min-w-[200px] overflow-visible">
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
                            <div v-for="(field, index) in searchFields.slice(0, 3)" :key="field.value">
                                <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">
                                    {{ getShortcutKey(index) }}
                                </kbd> 
                                {{ field.label }}
                            </div>
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
</template> 