<script setup>
import { ref, provide, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useForm, router } from "@inertiajs/vue3";
import axios from 'axios';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { middlewareManager } from '@/components/middleware/MiddlewareManager';
import { middlewareRegistry } from '@/components/middleware/Registry';
import { BaseMiddleware } from '@/components/middleware/BaseMiddleware.js';

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

// Modals impor default
import ShowRecordDefault from "@/components/modals/ShowRecordDefault.vue";
import EditRecordDefault from '@/components/modals/EditRecordDefault.vue';
import DeleteRecordDefault from '@/components/modals/DeleteRecordDefault.vue';

// Add this import with the other imports
import { Bars3Icon, EllipsisVerticalIcon } from '@heroicons/vue/24/outline';

// Add Badges component import
import Badges from '@/components/colors/Badges.vue';

// Add the computed property for development mode
const isDevelopment = computed(() => {
    return import.meta.env.MODE === 'development';
});

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panadero/shared/tools/Pulse.vue';
import Pagination from '@/layouts/Pagination.vue';
import RecordCardDefault from '@/components/cards/RecordCardDefault.vue';
import RecordCardCompact from '@/components/cards/RecordCardCompact.vue';
import RecordCardRows from '@/components/cards/RecordCardRows.vue';

import Search from '@/components/inputs/Search.vue';
import Button from '@/components/buttons/Button0.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    records: Object,
    // Dynamic props
    module: {
        type: String,
        required: true
    },
    table: {
        type: String,
        required: true
    },
    model: {
        type: String,
        required: true
    }
});

_set.domainFunction = props.module;

// Group related variables together
// ================================
// Page Configuration - Now dynamic
const _module = props.module;
const _table = props.table;
const _model = props.model;

// State Management
const viewMode = ref(localStorage.getItem(`${_table}_viewMode`) || 'default');
const perPage = ref(localStorage.getItem(`${_table}_perPage`) || '10');
const showActionButtons = ref(localStorage.getItem(`${_table}_showActionButtons`) !== 'false');
const showSearchInput = ref(localStorage.getItem(`${_table}_showSearchInput`) !== 'false');

// UI State
const showAppearanceOptions = ref(false); 

const appearanceBtn = ref(null);
const appearancePopupStyle = ref({ top: '0px', left: '0px' });
const editRecordMode = ref(false);
const showRecordMode = ref(false);
const _activeRecord = ref(null);
const keyIndex = ref(0);
// Add this with your other refs in DynamicTable.vue
const editMiddlewareResults = ref([]);


// Constants
const viewModes = [
    { id: 'default', label: 'Default' },
    { id: 'compact', label: 'Compact' },
    { id: 'rows', label: 'Rows' },
    { id: 'grid', label: 'Grid' }
];

let _poolTimer; 
let _pulse = ref(false);

const darkMode = ref(false);

provide('pulse', _pulse);

// Add these refs
const recordToDelete = ref(null);
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const authStatus = ref({
    isValid: false,
    token: undefined,
    session: null,
    checks: {}
});

// Simplified middleware status structure matching actual response
const middlewareResults = ref({
    authentication: {
        isValid: false,
        token: undefined,
        session: null,
        checks: {}
    }
});

// Use existing Laravel/Inertia auth check
const debugAuthState = async () => {
    try {
        // Check if we have Inertia page props with auth info
        const page = usePage();
        const auth = page?.props?.auth;
        const jetstream = page?.props?.jetstream;
        
        console.log('Current Auth State:', {
            auth,
            jetstream,
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        });

        return {
            authenticated: !!auth?.user,
            user: auth?.user,
            csrf: true, // CSRF is handled by Inertia automatically
            sessionActive: true // If we can access the page, session is active
        };
    } catch (error) {
        console.error('Auth Debug Error:', error);
        return null;
    }
};

const checkMiddleware = async (record) => {
    try {
        // Get current auth state
        const currentAuth = await debugAuthState();
        console.log('Current Auth State:', currentAuth);

        const request = {
            method: 'DELETE',
            path: `/api/${props.table}/${record.id}`,
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=(.*)/)?.[1],
                'X-Requested-With': 'XMLHttpRequest'
            },
            context: {
                hasAccess: _set.hasAccess,
                permissions: _set.permissions,
                roles: _set.roles || [],
                requiredPermissions: ['delete']
            }
        };

        const results = await middlewareManager.processRequest(request);
        console.log('Middleware Results:', results);

        // Update middleware results following the consistent pattern
        middlewareResults.value = results.map(result => ({
            middleware: result.middleware,
            result: {
                isValid: result.result.isValid,
                checks: result.result.checks,
                ...result.result.getAdditionalData ? result.result.getAdditionalData() : {}
            }
        }));

        showDeleteDialog.value = true;
    } catch (error) {
        console.error('Error in middleware check:', error);
    }
};

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
            ...record
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

const loadRecord = async (recordType, recordId) => {
    try {
        const response = await _db.getRecordById(_model, recordId);
        if (response) {
            _activeRecord.value = {
                ...response
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

        // Add middleware check before showing edit modal
        const request = {
            method: 'PUT',
            path: `/api/${props.table}/${id}`,
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=(.*)/)?.[1],
                'X-Requested-With': 'XMLHttpRequest'
            },
            context: {
                hasAccess: _set.hasAccess,
                permissions: _set.permissions,
                roles: _set.roles || [],
                requiredPermissions: ['edit']
            }
        };

        // Process middleware
        const results = await middlewareManager.processRequest(request);
        console.log('Edit middleware results:', results);

        // Update middleware results with safe access to properties
        editMiddlewareResults.value = results.map(result => ({
            middleware: result.middleware,
            result: {
                isValid: result.result?.isValid || false,
                checks: result.result?.checks || {},
                ...(result.result?.validatedData ? { validatedData: result.result.validatedData } : {}),
                ...(result.result?.user ? { user: result.result.user } : {}),
                ...(result.result?.session ? { session: result.result.session } : {}),
                ...(result.result?.token ? { token: result.result.token } : {}),
                ...(result.result?.permissions ? { permissions: result.result.permissions } : {}),
                ...(result.result?.roles ? { roles: result.result.roles } : {})
            }
        }));
        
        _activeRecord.value = {
            ...record
        };
        
        editRecordMode.value = true;
    } catch (error) {
        console.error('Error in _whatever:', error);
    }
}

const _superSelfAdmin = computed(() => {
    return _set.superSelfAdmins.includes(_set.self);
});

// Card configuration - Now dynamic
const cardConfig = computed(() => ({
    title: props.records?.meta?.title_column || 'title',
    description: 'description',
    body: 'description',
    // Get flags from meta data's boolean_fields
    flags: props.records?.meta?.boolean_fields || [
        'is_published',
        'is_public',
        'is_featured',
        'is_locked',
        'is_self',
        'is_smart',
        'is_active',
        'is_archived'
    ],
    tabs: ['content', 'status', 'author'],
    showUser: true,
    showTags: true,
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

// Add the generic logging function
const logDelete = (type, index, record) => {
  console.log(`Deleting ${type}:`, {
    id: record.id,
    title: record.title || record.name || record.id,
    record: record,
    timestamp: new Date().toISOString()
  });
};

// Update the handleDelete function with more logging
const handleDelete = async (id) => {
    console.log('DynamicTable: handleDelete called with id:', id);
    
    const request = {
        method: 'DELETE',
        path: `/api/${props.table}/${id}`,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        context: {
            isMetaMask: _set.isMetaMask,
            hasAccess: _set.hasAccess,
            permissions: _set.permissions || ['canDo'],
            selfName: !(_set.self=='nope'),
            // ... any other store data needed
        }
    };

    // Get results directly as array
    const results = await middlewareManager.processRequest(request);
    console.log('DynamicTable: Middleware results:', results);

    if (!Array.isArray(results)) {
        console.error('Invalid middleware results:', results);
        return;
    }

    recordToDelete.value = props.records.data.find(r => r.id === id);
    middlewareResults.value = results;
    showDeleteDialog.value = true;
};

// this routine uses middleware and is accessed by handleSearchNew
const processRequest = async (request) => {
    try {
        const { success, results, error } = await middlewareManager.processRequest(request);
        
        if (!success) {
            console.error('Middleware chain failed:', error || results);
            return false;
        }

        // Log middleware results if needed
        results.forEach(result => {
            console.log(`${result.middleware}:`, result.result);
        });

        // Get the final processed request (last result in chain)
        const finalResult = results[results.length - 1];
        return finalResult ? { ...request, ...finalResult.result } : false;
    } catch (error) {
        console.error('Process request error:', error);
        return false;
    }
};

const handleSearchNew = async (searchData) => {
    if (searchData.query.length > 1 || searchData.query.length === 0) {
        const request = {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            },
            body: {
                action: 'search',
                data: {
                    query: searchData.query,
                    fields: searchData.fields,
                    per_page: perPage.value
                }
            }
        };

        const processedRequest = await processRequest(request);
        if (processedRequest) {
            // Build search parameters from processed request
            const searchParams = {
                search: processedRequest.body.data.query || undefined,
                search_fields: processedRequest.body.data.fields?.join(','),
                per_page: processedRequest.body.data.per_page
            };

            // Remove undefined values
            Object.keys(searchParams).forEach(key => 
                searchParams[key] === undefined && delete searchParams[key]
            );

            // Make the request
            router.get(window.location.pathname, searchParams, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        }
    }
};

const handleSearch = (searchData) => {
    if (searchData.query.length > 1 || searchData.query.length === 0) {
       // Build search parameters
        const searchParams = {
            search: searchData.query || undefined,
            search_fields: searchData.query && searchData.fields.length > 0 ? searchData.fields.join(',') : undefined,
            per_page: perPage.value // Use stored perPage value
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

// Update the toggleActionButtons function
const toggleActionButtons = () => {
    showActionButtons.value = !showActionButtons.value;
    localStorage.setItem(`${_table}_showActionButtons`, showActionButtons.value.toString());
};

// Update the toggleSearchInput function to handle the toggle properly
const toggleSearchInput = () => {
    showSearchInput.value = !showSearchInput.value;
    localStorage.setItem(`${_table}_showSearchInput`, showSearchInput.value.toString());
};

const _frame = computed(()=>{
    return "rounded-md p-1  " ;
});

// Update the handleViewModeChange function
const handleViewModeChange = (mode) => {
    try {
        viewMode.value = mode;
        localStorage.setItem(`${_table}_viewMode`, mode);
        
        // Set different per_page based on view mode
        const perPageMap = {
            'compact': 60,
            'grid': 48,
            'rows': 24,
            'default': 10
        };
        
        const newPerPage = perPageMap[mode] || 10;
        perPage.value = newPerPage.toString();
        localStorage.setItem(`${_table}_perPage`, newPerPage.toString());
        
        // Update URL if needed
        const currentParams = new URLSearchParams(window.location.search);
        const currentPerPage = currentParams.get('per_page');
        
        if (currentPerPage !== newPerPage.toString()) {
            currentParams.set('per_page', newPerPage);
            router.get(window.location.pathname, Object.fromEntries(currentParams), {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        }
    } catch (error) {
        console.error('Error changing view mode:', error);
    }
};

// Update the selectViewMode function
const selectViewMode = (modeId) => {
    handleViewModeChange(modeId);
    showAppearanceOptions.value = false;
};

// Keyboard shortcuts for ⌘4, ⌘5, ⌘6, ⌘7
const handleAppearanceShortcuts = (event) => {
  if ((event.metaKey || event.ctrlKey) && ['4', '5', '6', '7'].includes(event.key)) {
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

// Add this function to sync localStorage with URL on page load
const syncPerPageFromStorage = () => {
    const storedPerPage = localStorage.getItem(`${_table}_perPage`);
    const currentPerPage = new URLSearchParams(window.location.search).get('per_page');
    
    // If we have a stored perPage and it's different from URL, update URL
    if (storedPerPage && currentPerPage !== storedPerPage) {
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.set('per_page', storedPerPage);
        
        router.get(window.location.pathname, Object.fromEntries(currentParams), {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    }
};

onMounted(() => {
    console.log('DynamicTable: Component mounted');
    console.log('DynamicTable: Middleware manager state:', middlewareManager);
    configureMiddleware();
    // Sync perPage from localStorage on page load
    syncPerPageFromStorage();
    
    document.addEventListener('keydown', handleAppearanceShortcuts);
    document.addEventListener('click', onClickOutside);
});
onUnmounted(() => {
  document.removeEventListener('keydown', handleAppearanceShortcuts);
  document.removeEventListener('click', onClickOutside);
});

// You can also use the exposed function directly
const { getStatusColor } = Badges;

// Update the confirmDelete function
const confirmDelete = async () => {
    if (!recordToDelete.value) return;
    
    try {
        isDeleting.value = true;
        await fetch(`/api/${props.table}/${recordToDelete.value.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
        });
        
        showDeleteDialog.value = false;
        recordToDelete.value = null;
        router.reload({ preserveScroll: true });
    } catch (error) {
        console.error('Delete Error:', error);
    } finally {
        isDeleting.value = false;
    }
};

const handleClose = () => {
    showDeleteDialog.value = false;
    recordToDelete.value = null;
    isDeleting.value = false;
    // Reset auth status
    authStatus.value = {
        isValid: false,
        token: undefined,
        session: null,
        checks: {}
    };
};

// Example usage in any component
async function fetchData() {
    try {
        const data = await SessionHandler.makeRequest('/api/your-endpoint');
        console.log(data);
    } catch (error) {
        console.error('Request failed:', error);
    }
}

async function deleteItem(id) {
    try {
        await SessionHandler.makeRequest(`/api/items/${id}`, 'DELETE');
        if (window.$toast) {
            window.$toast.success('Item deleted successfully');
        }
    } catch (error) {
        console.error('Delete failed:', error);
    }
}

// Update the configureMiddleware function
const configureMiddleware = () => {
    try {
        // Example: Configure middleware based on user roles or permissions
        const userPermissions = _set.permissions || [];
        
        if (userPermissions.includes('admin')) {
            middlewareManager.setMiddlewareActive('Authorization', false); // Skip auth for admins
        }

        // Example: Add request logging for development
        if (isDevelopment.value) {
            class LoggingMiddleware extends BaseMiddleware {
                async handle(request) {
                    console.log('Request:', request);
                    return request;
                }
            }
            
            // Register the logging middleware
            middlewareManager.registerMiddleware('Logging', new LoggingMiddleware());
        }
    } catch (error) {
        console.error('Error configuring middleware:', error);
    }
};

// Middleware chain monitoring
const middlewareChainStatus = computed(() => {
    const chain = middlewareManager.getMiddlewareChain();
    return chain ? chain.map(middleware => ({
        name: middleware.name || 'Unknown',
        status: middleware.status || 'pending',
        type: middleware.type || 'middleware'
    })) : [];
});

// Add method to toggle middleware
const toggleMiddleware = (name) => {
    const middleware = middlewareChainStatus.value.find(m => m.name === name);
    if (middleware) {
        middlewareManager.setMiddlewareActive(name, !middleware.active);
    }
};
</script>

<template>
    <AppToolbarLayout :title="page?.title || 'Dynamic Table'" :baseSections="baseSections || {}" :set="_set" :contract="_contract" :page="page">
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
                    :meta="props.records.meta"
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
                    :meta="props.records.meta"
                    :middleware-results="editMiddlewareResults"
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
                            :placeholder="`Search ${table}....`"
                            buttonText="Search"
                            :searchResults="records"
                            @search="handleSearch"
                            @toggle-search="toggleSearchInput"
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
            <div :id="table" class="w-full min-h-[800px] min-w-full mt-2 px-6">
                <!-- Rows View - Table Format -->
                <div v-if="viewMode === 'rows'  || viewMode === 'grid'" class="bg-white dark:bg-gray-900 py-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h2 class="px-4 text-base/7 font-semibold text-gray-900 dark:text-white sm:px-6 lg:px-8">{{ table }} - table</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-left">
                            <colgroup>
                                <col v-for="column in cardConfig.columns" :key="column.key" :class="column.width || 'w-auto'" />
                            </colgroup>
                            <thead class="border-b border-gray-200 dark:border-white/10 text-sm/6 text-gray-900 dark:text-white">
                                <tr>
                                    <th v-for="column in cardConfig.columns" 
                                        :key="column.key" 
                                        scope="col" 
                                        class="py-2 px-2 font-semibold">
                                        {{ column.label }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                <RecordCardRows
                                    v-for="record in records.data"
                                    :key="record.id"
                                    :record="record"
                                    :meta="records.meta"
                                    :module="_module"
                                    :table="_table"
                                    :db="_db"
                                    :set="_set"
                                    :config="cardConfig"
                                    :badges-component="Badges"
                                    :status-mapping="records.meta.status_mapping"
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
                    viewMode === 'default' ? 'grid md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6' : '',
                    viewMode === 'compact' ? 'flex flex-wrap gap-1' : '',
                    viewMode === 'grid' ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 2xl:grid-cols-12 gap-2' : ''
                ]">
                    <div v-for="record in records.data" :key="record.id" 
                         :class="[
                             viewMode === 'default' ? 'text-sm' : '',
                             viewMode === 'compact' ? 'text-xs' : '',
                             viewMode === 'grid' ? 'text-xs' : ''
                         ]">
                        
                        <!-- Default Card View -->
                        <RecordCardDefault 
                            v-if="viewMode === 'default'"
                            :record="record"
                            :meta="records.meta"
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
                            :meta="records.meta"
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

    <!-- Add this debugging info -->
    <div v-if="isDevelopment" class="hidden">
        Debug Info:
        showDeleteDialog: {{ showDeleteDialog }}
        recordToDelete: {{ recordToDelete?.id }}
        isDeleting: {{ isDeleting }}
    </div>

    <!-- Update the modal component -->
    <DeleteRecordDefault
        v-if="showDeleteDialog && middlewareResults"
        :show="showDeleteDialog"
        :record="recordToDelete"
        :middleware-results="middlewareResults"
        :is-deleting="false"
        @close="handleClose"
        @confirm="confirmDelete"
    />

    <!-- Update the middleware status indicators section -->
    <div v-if="isDevelopment" 
         class="fixed bottom-4 right-4 flex flex-col space-y-2 bg-gray-100 border border-gray-300 dark:bg-gray-800 p-2 rounded-lg shadow-lg">
        <h3 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Middleware Chain</h3>
        <div class="flex flex-col space-y-1">
            <div v-for="status in middlewareChainStatus" 
                 :key="status.name"
                 @click="toggleMiddleware(status.name)"
                 class="cursor-pointer flex items-center justify-between px-2 py-1 rounded text-xs"
                 :class="[
                    'border',
                    status.active ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20'
                 ]">
                <div class="flex items-center space-x-2">
                    <div :class="[
                        'w-2 h-2 rounded-full',
                        status.active ? 'bg-green-500' : 'bg-red-500'
                    ]"></div>
                    <span class="text-gray-700 dark:text-gray-300">{{ status.name }}</span>
                </div>
            </div>
            
        </div>
    </div>
</template> 