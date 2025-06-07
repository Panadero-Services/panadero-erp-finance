<script setup>
import { ref, computed, watch, getCurrentInstance } from 'vue';
import { useDbStore } from '@/stores/db';

const props = defineProps({
  modelValue: { type: String, default: '' },
  model: { type: String, required: true },
  label: { type: String, required: true },
  filter: { type: String, default: '' },
  table: { type: String, default: 'posts' },
  titleColumn: { type: String, default: 'title' },
  idColumn: { type: String, default: 'id' }
});

const emit = defineEmits(['update:modelValue', 'update:linkTitle']);

const db = useDbStore();
const searchQuery = ref('');
const searchResults = ref([]);
const selectedPost = ref(null);
const showDropdown = ref(false);

// -------------------- Computed --------------------
const filteredPosts = computed(() => {
  if (!searchQuery.value) return [];
  return searchResults.value.filter(item =>
    item[props.label]?.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// -------------------- Methods --------------------
const handleInput = async (event) => {
  searchQuery.value = event.target.value;
  selectedPost.value = null;
  showDropdown.value = true;
  
  // Only search if there's a query
  if (searchQuery.value.trim()) {
    await searchPosts();
  }
};

const clickInput = () => {
  showDropdown.value = true;
  // Trigger search if we have a query but no results
  if (searchQuery.value.trim() && searchResults.value.length === 0) {
    searchPosts();
  }
};

const selectPost = (post) => {
  selectedPost.value = post;
  emit('update:modelValue', post.id.toString());
  emit('update:linkTitle', post[props.label]);
  searchQuery.value = post[props.label];
  showDropdown.value = false;

  const parent = getCurrentInstance();
  parent?.emit('update:post-title', post[props.label]);
};

const clearSelection = () => {
  selectedPost.value = null;
  emit('update:modelValue', '');
  emit('update:linkTitle', '');
  searchQuery.value = '';
  showDropdown.value = false; // Changed to false to avoid immediate re-opening
};

const delayedHideDropdown = () => {
  setTimeout(() => {
    showDropdown.value = false;
  }, 200); // Increased delay to prevent conflicts with click events
};

const searchPosts = async () => {
  try {
    const labels = await db.getLabels(props.model, props.label, props.filter);
    searchResults.value = labels.map((label, index) => ({
      id: props.filter || index + 1,
      [props.label]: label
    }));
  } catch (error) {
    console.error(`Error fetching ${props.model} labels:`, error);
  }
};

// Initialize component if modelValue is provided
const initializeFromModelValue = async () => {
  if (props.modelValue) {
    try {
      // Search for the post with the given ID
      await searchPosts();
      const foundPost = searchResults.value.find(post => post.id.toString() === props.modelValue.toString());
      if (foundPost) {
        selectedPost.value = foundPost;
        searchQuery.value = foundPost[props.label];
      }
    } catch (error) {
      console.error('Error initializing PostSearch:', error);
    }
  }
};

// -------------------- Watchers --------------------
// Removed the automatic search on query change since we handle it in handleInput

watch(selectedPost, (newPost) => {
  if (newPost) {
    emit('update:modelValue', newPost.id.toString());
    emit('update:linkTitle', newPost[props.label]);
  }
});

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue && (!selectedPost.value || selectedPost.value.id.toString() !== newValue.toString())) {
    initializeFromModelValue();
  } else if (!newValue) {
    // Clear selection if modelValue becomes empty
    selectedPost.value = null;
    searchQuery.value = '';
  }
}, { immediate: true });

// -------------------- Classes --------------------
const inputClasses = "w-full pl-3 pr-10 py-2 text-xs rounded-md border focus:outline-none focus:ring-1 bg-white border-gray-300 text-gray-700 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-600";

const buttonClasses = "absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-300";

const dropdownClasses = "absolute z-10 mt-1 w-full max-h-80 overflow-auto bg-white dark:bg-gray-700 shadow-lg rounded-md border border-gray-200 dark:border-gray-600";

const itemClasses = (isSelected) =>
  `px-3 py-2 cursor-pointer text-xs ${isSelected ? 'bg-gray-100 dark:bg-gray-600' : 'hover:bg-gray-100 dark:hover:bg-gray-600'}`;
</script>

<template>
  <div class="relative block w-full">
    <div class="flex items-center bg-white dark:bg-gray-700 rounded-md">
      <input
        v-model="searchQuery"
        :placeholder="`Search ${props.table}...`"
        :class="inputClasses"
        @input="handleInput"
        @focus="clickInput"
        @blur="delayedHideDropdown"
      />
      <button v-if="selectedPost" @click="clearSelection" :class="buttonClasses">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div v-if="showDropdown && filteredPosts.length" :class="dropdownClasses">
      <div
        v-for="post in filteredPosts"
        :key="post.id"
        @mousedown.prevent="selectPost(post)"
        :class="itemClasses(selectedPost?.id === post.id)"
      >
        {{ post[props.label] }}
      </div>
    </div>
  </div>
</template>

