<script setup>
import { ref, provide, computed, onMounted } from 'vue';
import { useForm, router } from "@inertiajs/vue3";
import axios from 'axios';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import SearchWithPopup from '@/components/inputs/SearchWithPopup.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

// Add ShowRecordDefault import
import ShowRecordDefault from "@/components/modals/ShowRecordDefault.vue";

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import Pagination from '@/layouts/Pagination.vue';
import RecordCardDefault from '@/components/cards/RecordCardDefault.vue';
import EditRecordDefault from '@/components/modals/EditRecordDefault.vue';

import Search from '@/components/inputs/Search.vue';

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
let editRecordMode = ref(false);
let showRecordMode = ref(false);

provide('pulse', _pulse);

// functions
const _loopTimer = async () => {
    _poolTimer =
    setInterval( () => {
        _pulse.value = !_pulse.value;
    }, 1000)
}

const _close = async () => {
    editRecordMode.value = false;
    showRecordMode.value = false;
    _activeRecord.value = null;
}

const _show = async (id) => {
    try {
        console.log('Showing record:', id);
        // Find the record in the current data
        const record = props.records.data.find(x => x.id === id);
        if (!record) {
            console.error('Record not found:', id);
            return;
        }

        // Set the active record with all necessary data
        _activeRecord.value = {
            ...record,
            form_fields: record.form_fields || {},
            validation_rules: record.validation_rules || {},
            links_table: record.links_table || {}
        };

        // Increment keyIndex to force re-render
        keyIndex.value++;
        
        // Show the modal
        showRecordMode.value = true;
        
        console.log('Modal state:', {
            showRecordMode: showRecordMode.value,
            record: _activeRecord.value,
            keyIndex: keyIndex.value
        });
    } catch (error) {
        console.error('Error showing record:', error);
    }
}

const _activeRecord = ref(null);

const loadRecord = async (recordType, recordId) => {
    try {
        const response = await _db.getRecordById(_model, recordId);
        if (response) {
            _activeRecord.value = {
                ...response,
                form_fields: response.form_fields || {},
                validation_rules: response.validation_rules || {},
                links_table: response.links_table || {}
            };
            keyIndex.value++;
        }
    } catch (error) {
        console.error('Error loading record:', error);
    }
};

const handleRecordChange = (recordData) => {
    loadRecord(recordData.type, recordData.id);
};

const _whatever = async (id) => {
    try {
        const record = props.records.data.find(x => x.id === id);
        if (!record) {
            console.error('Record not found:', id);
            return;
        }
        
        _activeRecord.value = {
            ...record,
            form_fields: record.form_fields || {},
            validation_rules: record.validation_rules || {},
            links_table: record.links_table || {}
        };
        
        editRecordMode.value = true;
    } catch (error) {
        console.error('Error in _whatever:', error);
    }
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
    _show(id);
};

const handleDelete = (id) => {
    console.log('Delete record:', id);
};


const handleSearch = (searchData) => {
    if (searchData.query.length > 2 || searchData.query.length === 0) {
        // Build search parameters
        const searchParams = {
            search: searchData.query || undefined,
            search_fields: searchData.query && searchData.fields.length > 0 ? searchData.fields.join(',') : undefined,
        };
        
        // Remove undefined values
        Object.keys(searchParams).forEach(key => 
            searchParams[key] === undefined && delete searchParams[key]
        );
        
        // Use Inertia router to make the request
        router.get(window.location.pathname, searchParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    }
};

const _button = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400";

</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
      <template #header>
         <pulse v-model="_pulse" :animation="_set.animate"/>

         <div v-if="showRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
            <ShowRecordDefault
                :key="keyIndex"
                :record="_activeRecord"
                :module="_module"
                :table="_table"
                @close="_close"
                @changeRecord="handleRecordChange"
                :superSelfAdmin="_superSelfAdmin"
                :db="_db"
            />
         </div>

         <div v-if="editRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
            <EditRecordDefault
                :key="keyIndex"
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

            <div class="flex my-2">
                <SearchWithPopup
                    :searchableColumns="['id','item', 'title', 'description']"
                    placeholder="Search futures....."
                    buttonText="Search"
                    @search="handleSearch"
                />
            </div>

            <!-- Cards Section-->
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