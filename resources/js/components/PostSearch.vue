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


const emit = defineEmits(['update:modelValue', 'update:postTitle'])

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
    <div class="flex items-center bg-white dark:bg-gray-700 rounded-md ">
      <input
         :value="selectedPost ? selectedPost[props.label] : searchQuery"
        :placeholder="`Search ${props.table}...`"
        class=" w-full pl-3 pr-10 py-2 text-xs border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 "
        @focus="showDropdown = true"
        @blur="delayedHideDropdown"
        @input="handleInput"
        @click="clickInput"
      />
      <button
        v-if="selectedPost"
        @click="clearSelection"
        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-500"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div
      v-if="showDropdown && filteredPosts.length > 0"
      class="absolute z-10 w-max-w mt-0 bg-white dark:bg-gray-700 rounded-md max-h-80 overflow-auto"
    >

      <div
        v-for="post in filteredPosts"
        :key="post.id"
        @click="selectPost(post)"
        class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-xs"
        :class="{ 'bg-gray-100 dark:bg-gray-600': selectedPost?.id === post.id }"
      >
        {{ post[label] }}
      </div>
    </div>
  </div>
</template>
