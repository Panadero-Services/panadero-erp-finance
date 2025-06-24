<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { usePage } from '@inertiajs/vue3';
import Badges from '@/components/colors/Badges.vue';

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
            flags: ['is_active', 'is_locked', 'is_featured', 'is_public', 'is_published'],
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

// Enhanced status flags with modern styling
const statusFlags = computed(() => {
    return props.config.flags.map(flag => ({
        key: flag,
        label: flag.replace('is_', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        icon: getIconForFlag(flag),
        active: props.record?.[flag] === 1,
        color: getStatusColor(flag)
    }));
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
        'is_self': 'FingerPrint',
        'is_locked': 'LockClosed',
        'is_featured': 'Fire',
        'is_smart': 'Heart',
        'is_public': 'LockOpen',
        'is_published': 'Tv',
        'is_active': 'CheckCircle'
    };
    return iconMap[flag] || 'QuestionMarkCircle';
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
    return props.meta?.content_fields || props.meta?.getContentFields || ['description', 'body'];
});
</script>

<template>
    <div class="relative block w-full">
        <!-- Circle avatar positioned to overlap top border -->
        <div v-if="config.showUser" 
             class="absolute -top-3.5 left-3 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-800 rounded-full flex items-center justify-center">
            <span class="text-white text-[10px]">{{ record?.item || record?.user?.name?.[0] || 'U' }}</span>
        </div>

        <div class="p-3 h-[300px] bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300">
            <!-- Header with ID and Status -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1 min-w-0">
                    <h3 @click="$emit('show', record?.id)" 
                        class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer truncate">
                        {{ record?.[config.title] }}
                    </h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">
                        {{ formatDistance(record?.updated_at ?? new Date(), new Date()) }} ago
                    </p>
                </div>

                <!-- ID and Status badge stacked -->
                <div class="flex flex-col items-end gap-1">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ record.id }}</span>
                    <Badges 
                        v-if="record.status"
                        :status="record.status"
                        :status-mapping="config.statusMapping"
                        size="xxs"
                        variant="badge"
                    />
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 -mx-3 px-2">
                <button v-for="tab in ['content', 'json', 'links', 'meta']" 
                        :key="tab"
                        @click="activeTab = tab"
                        :class="[
                            'px-2 py-1 text-[11px] font-medium transition-colors duration-200',
                            activeTab === tab 
                                ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                        ]">
                    {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                </button>
            </div>

            <!-- Content area -->
            <div class="mt-2 h-[180px] overflow-y-auto">
                <!-- Content Tab - Two Columns -->
                <div v-if="activeTab === 'content'" class="grid grid-cols-2 gap-2 px-1">
                    <!-- Description at top spanning both columns -->
                    <div v-if="record?.[config.description]" 
                         class="col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-2 px-2 py-1">
                            <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400 shrink-0">
                                Description
                            </span>
                            <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-normal">
                                {{ record[config.description] }}
                            </div>
                        </div>
                    </div>

                    <!-- Other fields -->
                    <template v-for="col in columns" :key="col.key">
                        <div v-if="!['description'].includes(col.key)" 
                             class="bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between px-2 py-1.5">
                                <span class="text-[10px] font-medium text-blue-700 dark:text-blue-400">
                                    {{ col.label }}
                                </span>
                                
                                <!-- Field content inline -->
                                <div class="flex items-center">
                                    <!-- Boolean fields -->
                                    <div v-if="col.formatter === 'boolean'" class="flex items-center">
                                        <div class="w-1.5 h-1.5 rounded-full"
                                             :class="record[col.key] ? 'bg-green-500' : 'bg-red-500'">
                                        </div>
                                        <span class="ml-1 text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ record[col.key] ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Color fields -->
                                    <div v-else-if="col.formatter === 'color'" class="flex items-center">
                                        <div class="w-3 h-3 rounded"
                                             :style="{ backgroundColor: record[col.key] }">
                                        </div>
                                        <span class="ml-1.5 text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ record[col.key] }}
                                        </span>
                                    </div>

                                    <!-- Date fields -->
                                    <div v-else-if="col.key.includes('date') || col.formatter === 'date'">
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ new Date(record[col.key]).toLocaleDateString() }}
                                        </span>
                                    </div>
                                    
                                    <!-- Default text -->
                                    <div v-else class="text-[10px] text-gray-500 dark:text-gray-400">
                                        {{ record[col.key] || '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- JSON Tab -->
                <div v-else-if="activeTab === 'json'" class="space-y-1.5">
                    <template v-for="col in columns" :key="col.key">
                        <div v-if="col.formatter === 'json'" 
                             class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded px-2 py-1.5">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">
                                {{ col.label }}
                            </span>
                            <div class="flex items-center">
                                <span class="px-1.5 py-0.5 text-[9px] rounded bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ Array.isArray(record[col.key]) ? `${record[col.key].length} items` : 'Object' }}
                                </span>
                                <button @click="showJsonDetail(col.key)"
                                        class="ml-2 text-[10px] text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    View
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Links Tab -->
                <div v-else-if="activeTab === 'links'" class="space-y-1.5">
                    <template v-for="(link, index) in record.links" :key="index">
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded px-2 py-1.5">
                            <div class="flex items-center">
                                <span class="text-[10px] font-medium text-gray-700 dark:text-gray-300">
                                    {{ link.link_title }}
                                </span>
                                <span class="ml-2 px-1.5 py-0.5 text-[9px] rounded bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ link.type }}
                                </span>
                            </div>
                            <button @click="navigateToLink(link)"
                                    class="text-[10px] text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                View
                            </button>
                        </div>
                    </template>
                    <div v-if="!record.links?.length" class="text-[10px] text-gray-500 dark:text-gray-400 text-center py-2">
                        No links available
                    </div>
                </div>

                <!-- Meta Tab -->
                <div v-else-if="activeTab === 'meta'" class="grid grid-cols-3 gap-2">
                    <div v-for="flag in statusFlags" 
                         :key="flag.key"
                         class="flex items-center justify-between p-2 rounded"
                         :class="`bg-${flag.color}-50 dark:bg-${flag.color}-900/20`">
                        <span class="text-[10px] font-medium" :class="`text-${flag.color}-700 dark:text-${flag.color}-300`">
                            {{ flag.label }}
                        </span>
                        <CategorySectionIcon 
                            :icon="flag.icon"
                            :activated="flag.active"
                            :error="false"
                            :title="flag.label"
                            class="h-3.5 w-3.5"
                            @click="_updateIcon(record?.id, flag.key, !flag.active)"
                        />
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-2 left-3 flex items-center gap-2">
                <!-- Color indicator only -->
                <div v-if="record.color" class="flex items-center" title="Color">
                    <div class="w-3 h-3 rounded"
                         :style="{ backgroundColor: record.color }">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 