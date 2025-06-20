<script setup>
import { ref, provide, computed, onMounted, onUnmounted, nextTick } from 'vue';
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
import ShowRecordCompact from "@/components/modals/ShowRecordCompact.vue";

// Add this import with the other imports
import { Bars3Icon, EllipsisVerticalIcon } from '@heroicons/vue/24/outline';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import Pagination from '@/layouts/Pagination.vue';
import RecordCardDefault from '@/components/cards/RecordCardDefault.vue';
import RecordCardCompact from '@/components/cards/RecordCardCompact.vue';
import RecordCardRows from '@/components/cards/RecordCardRows.vue';
import EditRecordDefault from '@/components/modals/EditRecordDefault.vue';

import Search from '@/components/inputs/Search.vue';
import Button from '@/components/buttons/Button0.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object,
    meta: Object
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

// Add these refs with the other variables
const viewMode = ref('default');
const darkMode = ref(false);

// Add this with the other variables
const viewModes = [
  { id: 'default', label: 'Default' },
  { id: 'compact', label: 'Compact' },
  { id: 'rows', label: 'Rows' }
];



// Add this with the other refs
const showAppearanceOptions = ref(false);
const appearanceBtn = ref(null);
const appearancePopupStyle = ref({ top: '0px', left: '0px' });

// Add this with the other refs
const showActionButtons = ref(true);

const showSearchInput = ref(true);





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
const cardConfig = computed(() => ({
    title: 'title',
    description: 'description',
    body: 'description',
    flags: ['is_active', 'is_locked'],
    tabs: ['content', 'status', 'author'],
    showUser: true,
    showTags: true,
    // Dynamic table configuration from model metadata
    columns: props.records?.meta?.table_columns || [],
    statusMapping: props.records?.meta?.status_mapping || {}
}));

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
    if (searchData.query.length > 1 || searchData.query.length === 0) {
        // Build search parameters
        const searchParams = {
            search: searchData.query || undefined,
            search_fields: searchData.query && searchData.fields.length > 0 ? searchData.fields.join(',') : undefined,
            per_page: viewMode.value === 'compact' ? 60 : 10, // Respect current view mode
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

// Add this function
const toggleAppearanceOptions = () => {
    showAppearanceOptions.value = !showAppearanceOptions.value;
};

const positionAppearancePopup = () => {
    const btn = appearanceBtn.value;
    if (btn) {
        const rect = btn.getBoundingClientRect();
        appearancePopupStyle.value = {
            top: `${rect.bottom + window.scrollY + 8}px`,
            left: `${rect.left + window.scrollX}px`
        };
    }
};

const toggleActionButtons = () => {
    showActionButtons.value = !showActionButtons.value;
};

const _frame = computed(()=>{
    return "rounded-md p-1  " ;
});

// Add this function to handle view mode changes with per_page adjustment
const handleViewModeChange = (mode) => {
    viewMode.value = mode;
    
    // Get current URL parameters
    const currentParams = new URLSearchParams(window.location.search);
    const currentPerPage = currentParams.get('per_page');
    
    // Set different per_page based on view mode
    let newPerPage;
    if (mode === 'compact') {
        newPerPage = 60; // 6x more records for compact mode
    } else if (mode === 'rows') {
        newPerPage = 24; // 24 records for rows mode
    } else {
        newPerPage = 10; // Default records for other modes
    }
    
    // Only reload if per_page actually changed
    if (currentPerPage !== newPerPage.toString()) {
        currentParams.set('per_page', newPerPage);
        
        // Use Inertia router to reload with new per_page
        router.get(window.location.pathname, Object.fromEntries(currentParams), {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    }
};

const selectViewMode = (modeId) => {
  handleViewModeChange(modeId);
  showAppearanceOptions.value = false;
};

// Keyboard shortcuts for ⌘4, ⌘5, ⌘6
const handleAppearanceShortcuts = (event) => {
  if ((event.metaKey || event.ctrlKey) && ['4', '5', '6'].includes(event.key)) {
    event.preventDefault();
    const idx = parseInt(event.key, 10) - 4;
    if (viewModes[idx]) {
      selectViewMode(viewModes[idx].id);
    }
  }
};

// Close popover on outside click
const onClickOutside = (e) => {
  if (
    !e.target.closest('.field-popup') &&
    !e.target.closest('.appearance-popover-trigger')
  ) {
    showAppearanceOptions.value = false;
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleAppearanceShortcuts);
  document.addEventListener('click', onClickOutside);
});
onUnmounted(() => {
  document.removeEventListener('keydown', handleAppearanceShortcuts);
  document.removeEventListener('click', onClickOutside);
});
</script>

<template>
    <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">
        <template #header>

    <!-- records.meta 
        <pre>{{ records.meta}}</pre>
        <pre>{{ records.meta.links_table}}</pre>
    -->
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

        <template #intro>

            <div :class="showSearchInput ? 'h-24' : 'h-12'" class="flex w-full overflow-x-auto gap-4 p-1 justify-start">
                <!-- 1. Actions -->
                <div :class="[_frame]" class="flex-shrink-0 w-1/3 min-w-[300px] max-w-[300px] flex gap-2 justify-start items-start text-xs">
                    <!-- Toggle Action Buttons Icon -->
                    <div class="flex items-center m-1">
                        <button
                            @click="toggleActionButtons"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 ml-1"
                            :class="{ 'text-indigo-600 dark:text-indigo-400': showActionButtons }"
                            >
                            <EllipsisVerticalIcon class="h-4 w-4 text-indigo-600 dark:text-indigo-400 hover:text-black dark:hover:text-white" />
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div v-if="showActionButtons" class="flex  gap-1">
                        <Button outline name="day" @click="_set.dark = false" />
                        <Button outline name="night" @click="_set.dark = true" />
                        <Button outline name="insert" @click="_set.dark = true" />
                        <Button outline name="shuffle" @click="_set.dark = true" />
                    </div>
                    <div v-else class="mt-1 -ml-2">
                        <span class="text-gray-400 dark:text-gray-600 text-xs">actions</span>
                    </div>
                </div>

                <!-- 2. Search -->
                <div :class="_frame" class="flex-shrink-0 w-1/3 min-w-[400px] max-w-[400px] flex">
                        <SearchWithPopup
                            :showSearchInput = "showSearchInput"    
                            :searchableColumns="records.meta.searchable_columns"
                            placeholder="Search futures....."
                            buttonText="Search"
                            :searchResults="records"
                            @search="handleSearch"
                            @toggle-search="showSearchInput = !showSearchInput"
                     />
                </div>

                <!-- 3. Appearance -->
                <div :class="_frame" class="flex-shrink-0 w-1/3 min-w-[300px] max-w-[300px] flex items-start justify-start text-xs relative appearance-popover-trigger">
                    <!-- Toggle Appearance Icon (hidden when popup is open) -->
                    <div v-if="!showAppearanceOptions" class="flex items-center">
                        <button
                            @click.stop="toggleAppearanceOptions"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1"
                            ref="appearanceBtn"
                        >
                            <Bars3Icon class="h-4 w-4 text-indigo-600 dark:text-indigo-400 hover:text-black dark:hover:text-white" />


                        </button>
                        <span class="text-gray-400 dark:text-gray-600 text-xs ml-2">{{viewMode}}</span>
                    </div>

                    <!-- Horizontal Popup for Appearance Options (positioned above) -->
                    <div
                        v-if="showAppearanceOptions"
                        class="absolute left-0 z-50 flex bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg space-x-0 "
                        @click.stop
                        style="min-width: 0;"
                    >
                        <button
                            v-for="(mode, index) in viewModes"
                            :key="mode.id"
                            @click="selectViewMode(mode.id)"
                            :class="[
                                'flex items-center justify-center px-3 py-0.5 rounded transition-all duration-150 focus:outline-none text-xs',
                                viewMode === mode.id
                                    ? ' bg-indigo-100 dark:bg-indigo-700 text-indigo-700 dark:text-indigo-200'
                                    : 'bg-transparent text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                            style="min-width: 120px; min-height: 24px;"
                        >
                            <span :class="viewMode==mode.id ? 'dark:text-gray-300 text-gray-800' :'text-gray-500'" class="font-medium text-xs ">{{ mode.label }}</span>
                            <kbd :class="viewMode==mode.id ? 'dark:text-gray-300 text-gray-800' :'text-gray-500'" class="ml-2 px-1">⌘{{ index + 4 }}</kbd>
                        </button>
                    </div>
                </div>
            </div>

        </template>

        <template #default>

            <!-- Main Section : Cards / Rows -->
            <div id="futures" class="w-full min-h-[800px] min-w-full mt-2">

                <!-- Rows View - Table Format -->
                <div v-if="viewMode === 'rows'" class="bg-white dark:bg-gray-900 py-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h2 class="px-4 text-base/7 font-semibold text-gray-900 dark:text-white sm:px-6 lg:px-8">Futures Activity</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-left">
                            <colgroup>
                                <col v-for="column in cardConfig.columns" :key="column.key" :class="column.width || 'w-auto'" />
                                <col class="w-24" /> <!-- Actions column -->
                            </colgroup>
                            <thead class="border-b border-gray-200 dark:border-white/10 text-sm/6 text-gray-900 dark:text-white">
                                <tr>
                                    <th v-for="column in cardConfig.columns" 
                                        :key="column.key" 
                                        scope="col" 
                                        class="py-2 px-2 font-semibold">
                                        {{ column.label }}
                                    </th>
                                    <th scope="col" class="py-2 px-2 font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                <RecordCardRows
                                    v-for="record in records.data"
                                    :key="record.id"
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
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Views (Default and Compact) -->
                <div v-else :class="[
                    viewMode === 'default' ? 'grid md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4' : '',
                    viewMode === 'compact' ? 'flex flex-wrap gap-1' : ''
                ]">
                    <div v-for="record in records.data" :key="record.id" 
                         :class="[
                             viewMode === 'default' ? 'text-sm' : '',
                             viewMode === 'compact' ? 'text-xs' : ''
                         ]">
                        
                        <!-- Default Card View -->
                        <RecordCardDefault 
                            v-if="viewMode === 'default'"
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
                        
                        <!-- Compact Card View -->
                        <RecordCardCompact
                            v-if="viewMode === 'compact'"
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

                <!-- Index Section-->
                <Pagination :meta="records.meta" />
            </div>
        </template>

        <template #footer>
        </template>
    </AppToolbarLayout>
</template>