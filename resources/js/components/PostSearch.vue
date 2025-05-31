<script setup>
import { ref, watch, computed, getCurrentInstance } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useDbStore } from '@/stores/db';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  model: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  filter: {
    type: String,
    default: ''
  },
  table: {
    type: String,
    default: 'posts'
  },
  titleColumn: {
    type: String,
    default: 'title'
  },
  idColumn: {
    type: String,
    default: 'id'
  }
})

// titleColum and label are double defined!!

const emit = defineEmits(['update:modelValue', 'update:linkTitle'])

const handleInput = (event) => {
  console.log('handleInput')
  searchQuery.value = event.target.value
  selectedPost.value = null
  showDropdown.value = true
}

const clckInput = () => {
  console.log('clickInput');
}

const searchQuery = ref('')
const searchResults = ref([])
const selectedPost = ref(null)
const showDropdown = ref(false)

//const db = usePage().props._db
const db = useDbStore();

const filteredPosts = computed(() => {
  if (!searchQuery.value) return []
  
  return searchResults.value.filter(item => 
    item[props.label]?.toLowerCase()?.includes(searchQuery.value.toLowerCase())
  )
})

const searchPosts = async () => {
  try {
    // Get labels using the new getLabels method
    const labels = await db.getLabels(props.model, props.label, props.filter)
    
    // Create objects with id and label
    searchResults.value = labels.map((label, index) => ({
      id: props.filter ? props.filter : index + 1,
      [props.label]: label
    }))
  } catch (error) {
    console.error(`Error fetching ${props.model} labels:`, error)
  }
}

watch(searchQuery, async (newQuery) => {
  if (newQuery) {
    await searchPosts()
  }
})

watch(selectedPost, (newPost) => {
  if (newPost) {
    emit('update:modelValue', newPost.id)
    emit('update:postTitle', newPost[props.label])
    //emit('update:postTitle', 'titetel') this works!!
  }
})

const selectPost = (post) => {
  selectedPost.value = post

console.log(post)
console.log(post)
console.log(post)
console.log(post)

  emit('update:modelValue', post.id)
  emit('update:postTitle', post[props.label])
  searchQuery.value = post[props.label]
  showDropdown.value = false
  // Update the link's post_title in the parent component
  const parent = getCurrentInstance()
  if (parent) {
    parent.emit('update:post-title', post[props.label])
  }
}

const _input = {
  base: "w-full pl-3 pr-10 py-2 text-xs rounded-md focus:outline-none focus:ring-1",
  light: "bg-white border-gray-300 text-gray-700 focus:ring-indigo-500",
  dark: "dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-600"
};

const _container = {
  base: "flex items-center",
  light: "bg-white",
  dark: "dark:bg-gray-700"
};

const _dropdown = {
  base: "absolute z-10 w-max-w mt-0 rounded-md max-h-80 overflow-auto shadow-lg",
  light: "bg-white",
  dark: "dark:bg-gray-700"
};

const _item = {
  base: "px-3 py-2 cursor-pointer text-xs",
  hover: "hover:bg-gray-100 dark:hover:bg-gray-600",
  selected: "bg-gray-100 dark:bg-gray-600"
};

const _button = {
  base: "absolute right-2 top-1/2 -translate-y-1/2",
  light: "text-gray-400 hover:text-gray-500",
  dark: "dark:text-gray-500 dark:hover:text-gray-300"
};

const clearSelection = () => {
  console.log('clearSelection')
  selectedPost.value = null
  emit('update:modelValue', '')
  emit('update:postTitle', '')
  searchQuery.value = ''
  showDropdown.value = true
}

const delayedHideDropdown = () => {
  window.setTimeout(() => {
    showDropdown.value = false
  }, 150)
}

</script>

<template>
  <div class="relative block w-full" >
    <div :class="[_container.base, _container.light, _container.dark]">
      <input
         :value="selectedPost ? selectedPost[props.label] : searchQuery"
        :placeholder="`Search ${props.table}...`"
        :class="[_input.base, _input.light, _input.dark]"
        @focus="showDropdown = true"
        @blur="delayedHideDropdown"
        @input="handleInput"
        @click="clickInput"
      />
      <button
        v-if="selectedPost"
        @click="clearSelection"
        :class="[_button.base, _button.light, _button.dark]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div
      v-if="showDropdown && filteredPosts.length > 0"
      :class="[_dropdown.base, _dropdown.light, _dropdown.dark]"
    >

      <div
        v-for="post in filteredPosts"
        :key="post.id"
        @click="selectPost(post)"
        :class="[
          _item.base,
          _item.hover,
          { [_item.selected]: selectedPost?.id === post.id }
        ]"
      >
        {{ post[props.label] }}
      </div>
    </div>
  </div>
</template>
