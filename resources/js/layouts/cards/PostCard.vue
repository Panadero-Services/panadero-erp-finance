<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    module: String,
    table: String,
    post: Object,
    db: Object,
    set: Object
});

// variables
const _iconChangedField = ref('iconChanged');
const _iconChangedId = ref(0);
const _iconChangedValue = ref(false);
const _true = true;
const _false = false;
const activeTab = ref('content');

const processHtml = (html) => {
  return html.replace(/<a data-inertia-link href="([^"]+)">([^<]+)<\/a>/g, '<inertia-link href="$1">$2 click on me</inertia-link>')
}

const _body = processHtml(props.post.body);

let _updateIcon = async (_id, _field, _value) => {    
    console.log('_updateIcon');
    let i={};
    i.id = _id;
    i.field=_field;
    i.value=_value;
    _iconChangedField.value = `${_field} updated`;
    _iconChangedId.value = _id;
    _iconChangedValue.value = _value;
    let _response = await props.db.postUpdateIcon('Post', i);
    console.log(_response);

    // logAction
    const _logData = {
        action: "postcard.updateicon",
        user_id:  usePage().props.auth.user.id || 'no_uid',
        module: props.module, 
        node: 'none',
        team: usePage().props.auth.user.current_team.name || 'no_team', 
        project: 'none', 
        content: _response.data,
        json: '{}',
        //json: JSON.stringify(_response),
        tags: 'content, posts',
    }
      let _response2 = await props.db.logAction(_logData);
      console.log(_response2);
   }

// Computed for formatted body
const formattedBody = computed(() => {
  return _body.replaceAll('.','.<br>');
});

// Computed for initials (Author initials - First and Last name)
const initials = computed(() => {
  if (!props.post.user.name) return 'U';
  const names = props.post.user.name.trim().split(' ');
  if (names.length >= 2) {
    return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
  }
  return names[0].charAt(0).toUpperCase();
});

// Computed for status flags
const statusFlags = computed(() => [
  { key: 'is_self', label: 'Self', icon: 'FingerPrint', active: props.post.is_self == 1 },
  { key: 'is_locked', label: 'Locked', icon: 'LockClosed', active: props.post.is_locked == 1 },
  { key: 'is_featured', label: 'Featured', icon: 'Fire', active: props.post.is_featured == 1 },
  { key: 'is_smart', label: 'Smart', icon: 'Heart', active: props.post.is_smart == 1 },
  { key: 'is_public', label: 'Public', icon: 'LockOpen', active: props.post.is_public == 1 },
  { key: 'is_published', label: 'Published', icon: 'Tv', active: props.post.is_published == 1 }
]);

const emit = defineEmits(['edit','show', 'delete']);
</script>

<template>
  <div class="relative block w-full">
    <div class="p-2 h-[400px] opacity-90 from-gray-700/50 via-transparent rounded-xs shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500 bg-white text-gray-600 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300">
      <div class="flex flex-col h-full min-h-24 p-1 sm:px-6 sm:pt-3 tracking-tight leading-4">
      <!-- Header section -->
    <div class="flex items-center mb-1 w-full">
        <div class="h-12 w-12 bg-blue-200 dark:bg-blue-900 flex items-center justify-center rounded-full mr-4 flex-shrink-0 absolute top-3 -mt-8 left-1">
         <div class="text-blue-600 dark:text-blue-300 font-bold text-base absolute top-2  ">{{ initials }}</div>
       </div>
      <div class="flex-1 text-center sm:text-lg ">
        <h3 v-if="!(set.self=='nope')" @click="$emit('show', post.id)" class="line-clamp-1 font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer ">
          {{ post.title }}
        </h3>
        <h3 v-else class="font-semibold text-gray-900 dark:text-white line-clamp-1">
          {{ post.title }}
        </h3>
        <p class="text-xxxs sm:text-xxs text-gray-500 dark:text-gray-500"> Updated {{ formatDistance(post.updated_at, new Date()) }} ago</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex mb-2 w-full border-b border-gray-200 dark:border-gray-700 grid grid-cols-2">
     <div>
      <button 
        @click="activeTab = 'content'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'content' 
            ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Content
      </button>
      <button 
        @click="activeTab = 'status'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'status' 
            ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Status
      </button>
      <button 
        @click="activeTab = 'author'"
        :class="[
          'px-3 py-2 text-xs font-medium focus:outline-none transition-colors duration-200',
          activeTab === 'author' 
            ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-yellow-300'
        ]"
      >
        Author
      </button>
</div>
      <div class="justify-end text-right">
            <div class="flex space-x-2 justify-end mt-3 ">
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_self', post.is_self==0), post.is_self = post.is_self == 1 ? 0 : 1" 
                icon="FingerPrint" 
                :activated="post.is_self==1" 
                :error="_false" 
                title="is self"
              />
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_locked', post.is_locked==0), post.is_locked = post.is_locked == 1 ? 0 : 1" 
                icon="LockClosed" 
                :activated="post.is_locked==1" 
                :error="_false" 
                title="is locked"
              />
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_featured', post.is_featured==0), post.is_featured = post.is_featured == 1 ? 0 : 1" 
                icon="Fire" 
                :activated="post.is_featured==1" 
                :error="_false" 
                title="is featured"
              />
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_smart', post.is_smart==0), post.is_smart = post.is_smart == 1 ? 0 : 1" 
                icon="Heart" 
                :activated="post.is_smart==1" 
                :error="_false" 
                title="is smart"
              />
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_public', post.is_public==0), post.is_public = post.is_public == 1 ? 0 : 1" 
                icon="LockOpen" 
                :activated="post.is_public==1" 
                :error="_false" 
                title="is public"
              />
              <CategorySectionIcon 
                @click="_updateIcon(post.id, 'is_published', post.is_published==0), post.is_published = post.is_published == 1 ? 0 : 1" 
                icon="Tv" 
                :activated="post.is_published==1" 
                :error="_false" 
                title="is published"
              />
            </div>

      </div>


    </div>

    <!-- Content area -->
    <div class="flex-1 w-full overflow-auto">
      <!-- Content Tab -->
      <div v-if="activeTab === 'content'" class="space-y-2">
        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto text-xs" 
             v-html="formattedBody">
        </div>
      </div>

      <!-- Status Tab -->
      <div v-else-if="activeTab === 'status'" class="space-y-2">
        <div class="grid sm:grid-cols-2 gap-2">
          <div v-for="(flag, idx) in statusFlags" 
               :key="idx" 
               class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
            <span class="font-medium text-blue-700 dark:text-blue-400 text-xs">{{ flag.label }}:</span>
            <span :class="[
              'text-xs px-2 py-1 rounded',
              flag.active 
                ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' 
                : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400'
            ]">
              {{ flag.active ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>
      </div>

             <!-- Author Tab -->
       <div v-else-if="activeTab === 'author'" class=" grid grid-cols-2 gap-2">
         
         <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
           <div class="flex-1">
             <p class="font-medium text-blue-700 dark:text-blue-400 text-xs -mt-4">{{ post.user.name }}</p>
             <p class="text-xs text-gray-500 dark:text-gray-400">Author</p>
             <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Joined: {{ formatDistance(new Date(post.user.created_at || Date.now()), new Date()) }} ago</p>
           </div>
              
         

         </div>

         <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
           <h4 class="font-medium text-blue-700 dark:text-blue-400 text-xs mb-2">Author Stats</h4>
           <div class="grid grid-cols-2 gap-2 text-xs">
             <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
               <div class="font-bold text-blue-600 dark:text-blue-400">{{ post.user.posts_count || 0 }}</div>
               <div class="text-gray-500 dark:text-gray-400">Posts</div>
             </div>
             <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
               <div class="font-bold text-blue-600 dark:text-blue-400">{{ post.user.id }}</div>
               <div class="text-gray-500 dark:text-gray-400">User ID</div>
             </div>
           </div>
         </div>
         <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700 col-span-2">
                 <img class="h-12 w-12 rounded-full mr-0 " :src="post.user.profile_photo_url"/>
                 <span class="text-xxs ml-2 -mt-2">{{post.user.profile_photo_url}}</span>
       </div>




       </div>
    </div>

        <!-- Footer -->
        <div class="mt-auto">

          <!-- Action buttons exactly like ProviderCard -->
          <div class="flex space-x-1 w-full justify-between mt-3 pt-0 border-t border-gray-200 dark:border-gray-700">
            <div class="mt-3 text-xs text-xs text-gray-500 dark:text-gray-500">
                id: {{ post.id }}         
            </div>
            <div>
              <button class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="$emit('show', post.id)">Show</button>
              <button v-if="!(set.self=='nope')" class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="$emit('edit', post.id)">Edit</button>
              <button class="mt-2.5 mx-1 rounded p-2 w-12 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" @click="$emit('delete', post.id)">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>