<script setup lang="ts">
import { ref, provide, computed, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// modal
import EditPostModal from "@/components/modals/EditPostModal.vue";
import EditRecordModal from "@/components/modals/EditRecordModal.vue";

// sections
import ProjectCard from '@/layouts/cards/ProjectCard.vue';
import Pagination from "@/layouts/Pagination.vue";

// types
//import { ProjectProps } from '@/types/project';
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
    projects: Object
});

_set.domainFunction = "project";

// variables
const _module = "project";
const _table = 'projects';
const _model = 'Project';
const keyIndex = ref(0);

let _poolTimer; 
let _pulse = ref(false);
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
    editRecordMode.value = false;
}

const _activeRecord = ref();

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
  loadRecord(recordData.type, recordData.id);
};

const _whatever = async (_nr) => {
   console.log(_nr);
   _activeRecord.value = props.projects.data.filter(x => x.id ==_nr)[0];
   console.log(_activeRecord.value);
   console.log(_nr);
    editRecordMode.value = true;

   //console.log(editRecordMode.value);
   //editMode.value = true;
}

const _superSelfAdmin = computed(() => {
  return _set.superSelfAdmins.includes(_set.self);
});

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
      <template #header>
         <pulse v-model="_pulse" :animation="_set.animate"/>

         <div v-if="editRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
            <edit-record-modal lng='en' 
                                :key="keyIndex"
                               :record="_activeRecord" 
                               :module="_module" 
                               :table="_table" 
                               @close="_close" 
                               @changeRecord="handleRecordChange"
                               :superSelfAdmin="_superSelfAdmin==1" 
                               :db="_db" />
         </div>

      </template>

      <template #intro />

      <template #default>
         <div id="projects" class="w-full ... min-h-4 min-w-full ">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
              <div v-for="project in projects.data" key="project.id" class="text-sm">
                  <ProjectCard :project="project" :module="_module" :table="_table" @edit="_whatever" :db="_db"/>
              </div>
            </div>
            <Pagination :meta="projects.meta" />
         </div>
      </template>

      <template #footer>
   </template>

   </AppToolbarLayout>
</template>
