<script setup lang="ts">
import { ref, provide, computed, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

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

const filter = ref("");

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
        <div class="grid grid-cols-2"> 
            <div class="pl-2 h-12" >
                <button @click="_set.dark=false" type="button" :class="_button">Light</button>
                <button @click="_set.dark=true" type="button" :class="_button">Dark</button>
                <button @click="refreshPage" type="button" :class="_button">Refresh</button>
            </div>      
            <div class="" id="toolbar">
                <input v-model="filter" name="filter" type="filter" autocomplete="filter" required="" 
                class="mt-2 min-w-0 flex-auto rounded-sm bg-gray-50 dark:bg-slate-950 px-3.5 py-0.5 text-base text-gray-600 sm:text-sm/6 focus:ring-0" placeholder="Search" />
            </div>
        </div>

        <div id="whatever" class="w-full min-h-4 min-w-full dark:bg-black">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
              <div v-for="post in posts.data" key="post.id" class="text-sm dark:text-gray-200">
                  <PostCard :post="post" :module="_module" :table="_table" @edit="_edit" @show="_show" :db="_db" :set="_set"/>
              </div>
            </div>
            <Pagination :meta="posts.meta" class="dark:text-gray-200"/>
         </div>
      </template>

      <template #footer>
   </template>

   </AppToolbarLayout>
</template>