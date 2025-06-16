<script setup lang="ts">
import { ref, provide, computed, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import Pagination from '@/layouts/Pagination.vue';
import RecordCardDefault from '@/components/cards/RecordCardDefault.vue';
import EditRecordDefault from '@/components/modals/EditRecordDefault.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object
});

_set.domainFunction = "home";

// variables
const _module = "home"
const _table = "futures"
const _model = "Future"
const keyIndex = ref(0);

let _poolTimer; 
let _pulse = ref(false);
let editRecordMode= ref(false);

provide('pulse', _pulse);

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

    if (response) {
      Object.keys(response).filter(key => (key !== 'user_id')).forEach(key => {
        _activeRecord.value[key] = response[key];
      });
      
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
   console.log('Loading record:', _nr);
   const record = props.records.data.find(x => x.id === _nr);
   if (!record) {
     console.error('Record not found:', _nr);
     return;
   }
   console.log('Found record:', record);
   _activeRecord.value = {
     ...record,
     form_fields: record.form_fields || {},
     validation_rules: record.validation_rules || {}
   };
   editRecordMode.value = true;
}

const _superSelfAdmin = computed(() => {
  return _set.superSelfAdmins.includes(_set.self);
});

// Card configuration
const cardConfig = {
    title: 'title',
    description: 'description',
    body: 'description',
    flags: ['is_active', 'is_locked'],
    tabs: ['content', 'status', 'author'],
    showUser: true,
    showTags: true
};

// Event handlers
const handleEdit = (id) => {
    _whatever(id);
};

const handleShow = (id) => {
    // Implement show functionality
    console.log('Show record:', id);
};

const handleDelete = (id) => {
    // Implement delete functionality
    console.log('Delete record:', id);
};
</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
      <template #header>
         <pulse v-model="_pulse" :animation="_set.animate"/>

         <div v-if="editRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
            <EditRecordDefault
                :record="_activeRecord"
                :module="_module"
                :table="_table"
                :superSelfAdmin="_superSelfAdmin"
                :db="_db"
                @close="_close"
                @changeRecord="handleRecordChange"
            />
         </div>

      </template>

      <template #intro />

      <template #default>
         <div id="futures" class="w-full min-h-4 min-w-full">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div v-for="record in records.data" :key="record.id" class="text-sm">
                    <RecordCardDefault 
                        :record="record"
                        :module="_module"
                        :table="_table"
                        :db="_db"
                        :set="_set"
                        :config="cardConfig"
                        @edit="handleEdit"
                        @show="handleShow"
                        @delete="handleDelete"
                    />
                </div>
            </div>
            <Pagination :meta="records.meta" />
         </div>
      </template>

      <template #footer>
      </template>
   </AppToolbarLayout>
</template> 