<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { usePage } from '@inertiajs/vue3';
import Badges from '@/components/colors/Badges.vue';
import { 
    DocumentCheckIcon,
    GlobeAltIcon,
    StarIcon,
    LockClosedIcon,
    UserIcon,
    LightBulbIcon,
    CheckCircleIcon,
    ArchiveBoxIcon,
    QuestionMarkCircleIcon
} from '@heroicons/vue/24/outline';
import ShowRecordDefault from "@/components/modals/ShowRecordDefault.vue";

const props = defineProps({
    module: String,
    table: String,
    record: Object,
    db: Object,
    set: Object,
    config: {
        type: Object,
        default: () => ({
            title: 'title',
            description: 'description',
            body: 'body',
            status: 'status',
            color: 'color',
            version: 'version',
            icon: 'icon',
            flags: [
                'is_published',
                'is_public',
                'is_featured',
                'is_locked',
                'is_self',
                'is_smart',
                'is_active',
                'is_archived'
            ],
            showUser: true,
            showTags: true,
            columns: [],
            statusMapping: {},
            fieldFormatters: {}
        })
    },
    meta: {
        type: Object,
        default: () => ({})
    }
});

// Add rich field formatters from RecordCardRows
const defaultFieldFormatters = {
    date: (value) => {
        if (!value) return 'N/A';
        return formatDistance(new Date(value), new Date(), { addSuffix: true });
    },
    truncate: (value, length = 40) => {
        if (!value) return 'N/A';
        return value.length > length ? value.substring(0, length) + '...' : value;
    },
    json: (value) => {
        if (!value) return 'N/A';
        if (typeof value === 'object') {
            if (Array.isArray(value)) {
                return value.length === 0 ? '[]' : `[${value.length} items]`;
            }
            const keys = Object.keys(value);
            return keys.length === 0 ? '{}' : `{${keys.length} properties}`;
        }
        return value;
    }
};

// variables
const _iconChangedField = ref('iconChanged');
const _iconChangedId = ref(0);
const _iconChangedValue = ref(false);
const _true = true;
const _false = false;
const activeTab = ref('content');
const showViewModal = ref(false);
const selectedLink = ref(null);

// Process HTML content if needed
const processHtml = (html) => {
    if (!html) return '';
    return html.replace(/<a data-inertia-link href="([^"]+)">([^<]+)<\/a>/g, '<inertia-link href="$1">$2</inertia-link>');
}

const _body = computed(() => {
    if (!props.record?.[props.config.body]) return '';
    return processHtml(props.record[props.config.body]);
});

// Update icon handler
const _updateIcon = async (_id, _field, _value) => {    
    try {
    const i = {
        id: _id,
        field: _field,
        value: _value
    };
    _iconChangedField.value = `${_field} updated`;
    _iconChangedId.value = _id;
    _iconChangedValue.value = _value;
    
        if (!props.db) throw new Error('Database connection not available');
    
    const _response = await props.db.postUpdateIcon(props.table, i);

        // Log action with enhanced data
    const _logData = {
        action: "recordcard.updateicon",
        user_id: usePage().props.auth.user.id || 'no_uid',
        module: props.module, 
        node: 'none',
        team: usePage().props.auth.user.current_team.name || 'no_team',
        project: 'none', 
        content: _response.data,
            json: JSON.stringify(i),
        tags: `${props.module}, ${props.table}`,
        };
        await props.db.logAction(_logData);
    } catch (error) {
        console.error('Error updating icon:', error);
    }
}

// Computed for formatted body
const formattedBody = computed(() => {
    return _body.value.split('\n').join('<br>');
});

// Computed for initials (Author initials - First and Last name)
const initials = computed(() => {
    if (!props.record?.user?.name) return 'U';
    const names = props.record.user.name.trim().split(' ');
    if (names.length >= 2) {
        return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
    }
    return names[0].charAt(0).toUpperCase();
});

// Enhanced status flags with modern styling - now using meta.boolean_fields
const statusFlags = computed(() => {
    // Use meta.boolean_fields instead of hardcoded flags
    const booleanFields = props.meta?.boolean_fields || [];
    
    return booleanFields.map(flag => {
        const value = props.record?.[flag];
        
        return {
            key: flag,
            label: flag.replace('is_', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
            icon: getIconForFlag(flag),
            active: value === true || value === 1 || value === '1',
            color: getStatusColor(flag)
        };
    });
});

// Add modern color mapping for flags
const getStatusColor = (flag) => {
    const colorMap = {
        'is_active': 'emerald',
        'is_locked': 'amber',
        'is_featured': 'purple',
        'is_smart': 'blue',
        'is_public': 'green',
        'is_published': 'indigo'
    };
    return colorMap[flag] || 'gray';
};

// Helper function to get icon for flag
const getIconForFlag = (flag) => {
    const iconMap = {
        'is_published': 'CloudArrowUp',
        'is_public': 'CloudArrowUp',
        'is_featured': 'Fire',
        'is_locked': 'LockClosed',
        'is_self': 'User',
        'is_smart': 'FingerPrint',
        'is_active': 'LockOpen',
        'is_archived': 'ArrowPath'
    };
    return iconMap[flag] || 'User';
};

const emit = defineEmits(['edit', 'show', 'delete', 'show-json', 'navigate-link']);

// Add dynamic columns support
const columns = computed(() => {
    return props.record?.meta?.columns || props.config.columns || [];
});

const showJsonDetail = (key) => {
    // Emit event to show JSON detail in a modal or similar
    emit('show-json', { key, value: props.record[key] });
};

const navigateToLink = (link) => {
    // Emit event to handle link navigation
    emit('navigate-link', link);
};

// Computed property for content fields
const contentFields = computed(() => {
    return props.meta?.content_fields || ['description', 'body'];
});

// Get the main content field (first element from content_fields)
const mainContentField = computed(() => {
    return contentFields.value[0] || 'description';
});

// Add this method to handle viewing a link
const viewLink = (link) => {
    selectedLink.value = link;
    showViewModal.value = true;
};

// Add this method to handle closing the modal
const closeViewModal = () => {
    showViewModal.value = false;
    selectedLink.value = null;
};

const submit = async () => {
    if (!isFormValid.value) {
        console.log('Form validation failed');
        return;
    }
    // ... rest of submit function
};

// Add copy to clipboard function
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        // You could add a toast notification here
        console.log('Copied to clipboard');
    } catch (err) {
        // Fallback for older browsers or when clipboard API is not available
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            console.log('Copied to clipboard (fallback)');
        } catch (fallbackErr) {
            console.error('Failed to copy: ', fallbackErr);
        }
        document.body.removeChild(textArea);
    }
};
</script>

<template>
    <div class="block w-full">
        <div class="p-3 h-[340px] bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
            <!-- Header with Title -->
            <div class="flex flex-col mb-2">
                <!-- Row 1: Title with avatar -->
                <div class="relative flex items-center mb-1">
                    <div v-if="config.showUser" 
                         class="absolute -left-6 -top-8 h-11 w-11 bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-800 rounded-full flex items-center justify-center">
                        <span class="text-white text-[11px]">{{ record?.item || record?.user?.name?.[0] || 'U' }}</span>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer truncate ml-8">
                        {{ record?.[config.title] }}
                    </h3>
                </div>

                <!-- Row 2: Icons and Updated time -->
                <div class="flex justify-between items-center mb-2">
                    <!-- Updated time aligned with title -->
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 ml-8 -mt-3">
                        {{ formatDistance(record?.updated_at ?? new Date(), new Date()) }} ago
                    </p>
                    <!-- Icons on right - only show if there are boolean fields -->
                    <div v-if="meta?.boolean_fields?.length" class="flex gap-1 items-center pr-2 mt-0.5">
                        <CategorySectionIcon 
                            v-for="flag in statusFlags"
                            :key="flag.key"
                            :icon="flag.icon"
                            :activated="flag.active"
                            :error="false"
                            :title="flag.label"
                        />
                    </div>
                </div>

                <!-- Row 3: Tabs - keep border only for selected tab -->
                <div class="flex -mx-3 px-2 justify-between items-center -mt-0.5">
                    <div class="flex ml-[10px]">
                        <button v-for="tab in ['content', 'record', 'meta', 'links']" 
                                :key="tab"
                                @click="activeTab = tab"
                                :class="[
                                    'px-1 py-1 text-[11px] font-medium transition-colors duration-200 whitespace-nowrap',
                                    activeTab === tab 
                                        ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                ]"
                        >
                            {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                            <span v-if="tab === 'links' && record.links?.length">({{ record.links.length }})</span>
                        </button>
                    </div>
                    
                    <!-- Status Badge -->
                    <div v-if="record?.status" class="pr-2">
                        <Badges 
                            :status="record.status"
                            size="xxs"
                            variant="badge"
                        />
                    </div>
                </div>


                
            </div>

            <!-- Content area -->
            <div class="flex-1 overflow-y-auto bg-white dark:bg-transparent -mx-3 px-3 pt-1">
                <!-- Content tab -->
                <div v-if="activeTab === 'content'" class="space-y-2 px-1">
                    <!-- Main content field (full width) -->
                    <div v-if="record?.[mainContentField] && meta?.content_fields?.includes(mainContentField)" 
                         class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-2 px-2 py-1">
                            <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400 shrink-0">
                                {{ mainContentField.charAt(0).toUpperCase() + mainContentField.slice(1) }}
                            </span>
                            <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-normal">
                                {{ record[mainContentField] }}
                            </div>
                        </div>
                    </div>

                    <!-- Other content fields -->
                    <div v-if="meta?.content_fields?.length > 1" class="grid grid-cols-2 gap-2">
                        <template v-for="fieldName in meta.content_fields" :key="fieldName">
                            <div v-if="fieldName !== mainContentField" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between px-2 py-1.5">
                                    <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                        {{ fieldName.charAt(0).toUpperCase() + fieldName.slice(1) }}
                                    </span>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 ml-[5px] leading-tight">
                                        <!-- Boolean field formatting -->
                                        <div v-if="meta?.boolean_fields?.includes(fieldName)" class="flex items-center">
                                            <div class="w-1.5 h-1.5 rounded-full"
                                                 :class="record[fieldName] ? 'bg-green-500' : 'bg-red-500'">
                                            </div>
                                            <span class="ml-1">
                                                {{ record[fieldName] ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                        <!-- Object field formatting -->
                                        <template v-else-if="typeof record[fieldName] === 'object' && record[fieldName] !== null">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(record[fieldName]) ? `${record[fieldName].length} items` : `${Object.keys(record[fieldName]).length} properties` }}
                                            </span>
                                        </template>
                                        <!-- Regular field formatting -->
                                        <template v-else>
                                            {{ record[fieldName] !== null && record[fieldName] !== undefined ? record[fieldName] : '-' }}
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Object/Array fields (full width) -->
                    <div v-if="meta?.content_fields?.length > 1" class="space-y-1.5">
                        <template v-for="fieldName in meta.content_fields" :key="fieldName">
                            <div v-if="fieldName !== mainContentField && record?.[fieldName] && typeof record[fieldName] === 'object' && !(Array.isArray(record[fieldName]) && record[fieldName].length <= 3)" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="px-2 py-1.5">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                            {{ fieldName.charAt(0).toUpperCase() + fieldName.slice(1) }}
                                        </div>
                                        <button 
                                            @click="copyToClipboard(JSON.stringify(record[fieldName], null, 2))"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                            title="Copy JSON content"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(record[fieldName]) ? `${record[fieldName].length} items` : `${Object.keys(record[fieldName]).length} properties` }}
                                            </span>
                                            <pre class="whitespace-pre-wrap">{{ JSON.stringify(record[fieldName], null, 2) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Record tab -->
                <div v-else-if="activeTab === 'record'" class="space-y-2 px-1">
                    <!-- Main record field (first field from record) - only if it's an object/array -->
                    <div v-if="record && Object.keys(record).length > 0 && typeof record[Object.keys(record)[0]] === 'object' && record[Object.keys(record)[0]] !== null" 
                         class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-2 px-2 py-1">
                            <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400 shrink-0">
                                {{ Object.keys(record)[0].charAt(0).toUpperCase() + Object.keys(record)[0].slice(1) }}
                            </span>
                            <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-normal">
                                {{ record[Object.keys(record)[0]] || '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- All record fields in grid (including first field if not object/array) -->
                    <div v-if="record && Object.keys(record).length > 0" class="grid grid-cols-2 gap-2">
                        <template v-for="(value, fieldName) in record" :key="fieldName">
                            <div v-if="typeof value !== 'object' || value === null || (Array.isArray(value) && value.length <= 3)" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between px-2 py-1.5">
                                    <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                        {{ fieldName.charAt(0).toUpperCase() + fieldName.slice(1) }}
                                    </span>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 ml-[5px] leading-tight">
                                        <!-- Boolean field formatting -->
                                        <div v-if="meta?.boolean_fields?.includes(fieldName)" class="flex items-center">
                                            <div class="w-1.5 h-1.5 rounded-full"
                                                 :class="value ? 'bg-green-500' : 'bg-red-500'">
                                            </div>
                                            <span class="ml-1">
                                                {{ value ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                        <!-- Object field formatting -->
                                        <template v-else-if="typeof value === 'object' && value !== null">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(value) ? `${value.length} items` : `${Object.keys(value).length} properties` }}
                                            </span>
                                        </template>
                                        <!-- Regular field formatting -->
                                        <template v-else>
                                            {{ value !== null && value !== undefined ? value : '-' }}
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Object/Array fields (full width) -->
                    <div v-if="record && Object.keys(record).length > 0" class="space-y-1.5">
                        <template v-for="(value, fieldName) in record" :key="fieldName">
                            <div v-if="typeof value === 'object' && value !== null && !(Array.isArray(value) && value.length <= 3)" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="px-2 py-1.5">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                            {{ fieldName.charAt(0).toUpperCase() + fieldName.slice(1) }}
                                        </div>
                                        <button 
                                            @click="copyToClipboard(JSON.stringify(value, null, 2))"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                            title="Copy JSON content"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(value) ? `${value.length} items` : `${Object.keys(value).length} properties` }}
                                            </span>
                                            <pre class="whitespace-pre-wrap">{{ JSON.stringify(value, null, 2) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Meta tab -->
                <div v-else-if="activeTab === 'meta'" class="space-y-3">
                    <!-- Part 1: Non-JSON/Array fields in 2 columns -->
                    <div v-if="meta" class="grid grid-cols-2 gap-2">
                        <template v-for="(value, key) in meta" :key="key">
                            <div v-if="typeof value !== 'object' || (Array.isArray(value) && value.length <= 3)" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between px-2 py-1.5">
                                    <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                        {{ key }}
                                    </span>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 ml-[5px] leading-tight">
                                        <template v-if="typeof value === 'object'">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(value) ? `${value.length} items` : `${Object.keys(value).length} properties` }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            {{ value }}
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Part 2: JSON/Array fields in 1 column -->
                    <div v-if="meta" class="space-y-1.5">
                        <template v-for="(value, key) in meta" :key="key">
                            <div v-if="typeof value === 'object' && !(Array.isArray(value) && value.length <= 3)" 
                                 class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="px-2 py-1.5">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                            {{ key }}
                                        </div>
                                        <button 
                                            @click="copyToClipboard(JSON.stringify(value, null, 2))"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                            title="Copy JSON content"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ Array.isArray(value) ? `${value.length} items` : `${Object.keys(value).length} properties` }}
                                            </span>
                                            <pre class="whitespace-pre-wrap">{{ JSON.stringify(value, null, 2) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div v-if="!meta" class="text-[10px] text-gray-500 dark:text-gray-400 text-center py-2">
                        No meta data available
                    </div>
                </div>

                <!-- Links tab -->
                <div v-else-if="activeTab === 'links'" class="p-1">
                    <div v-if="record.links?.length" class="grid grid-cols-2 gap-2">
                        <template v-for="(link, index) in record.links" :key="index">
                            <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                                <div class="h-6 w-6 bg-green-200 dark:bg-green-900 flex items-center justify-center rounded-full flex-shrink-0">
                                    <span class="text-green-600 dark:text-green-300 font-bold text-[10px]">{{ link.link_id }}</span>
                                </div>
                                <div class="min-w-0 flex-1 ml-2">
                                    <p 
                                        @click="viewLink(link)"
                                        class="font-medium text-blue-700 dark:text-blue-400 truncate text-[10px] hover:text-blue-800 dark:hover:text-blue-300 cursor-pointer"
                                    >
                                        {{ link.link_title }}
                                    </p>
                                    <p class="text-gray-500 dark:text-gray-400 text-[9px]">
                                        {{ (link.type || 'unknown').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div v-else class="text-[10px] text-gray-500 dark:text-gray-400 text-center py-2">
                        No links available
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="h-12 -mx-3 -mb-3 px-3 flex items-center justify-between bg-white dark:bg-gray-800 rounded-b-lg">
                <!-- Left side with Color icon and ID -->
                <div class="flex items-center gap-2">
                    <!-- Color indicator -->
                    <div v-if="record.color" class="flex items-center" title="Color">
                        <div class="w-2.5 h-2.5 rounded-sm"
                             :style="{ backgroundColor: record.color }">
                        </div>
                    </div>
                    <span class="text-xxs text-gray-500 dark:text-gray-400">id: {{ record.id }}</span>
                </div>
                
                <!-- Right side - Action buttons -->
                <div class="flex items-center gap-1">
                    <button 
                        @click="$emit('show', record?.id)"
                        class="w-12 rounded px-1.5 py-0.5 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400"
                    >
                        Show
                    </button>
                    <button 
                        @click="$emit('edit', record?.id)"
                        class="w-12 rounded px-1.5 py-0.5 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400"
                    >
                        Edit
                    </button>
                    <button 
                        @click="$emit('delete', record?.id)"
                        class="w-12 rounded px-1.5 py-0.5 text-[10px] ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template> 