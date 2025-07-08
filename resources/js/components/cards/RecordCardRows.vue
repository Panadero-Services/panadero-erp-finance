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
            fieldFormatters: {},
            columnWidths: {}
        })
    },
    badgesComponent: {
        type: Object,
        default: () => Badges
    },
    statusMapping: {
        type: Object,
        default: () => ({})
    },
    meta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['edit', 'show', 'delete']);

// Default status mapping - can be overridden by config
const defaultStatuses = {
    'idle': 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-gray-500/10 dark:ring-gray-400/20',
    'in_progress': 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-blue-700/10 dark:ring-blue-400/20',
    'completed': 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
    'blocked': 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/10 dark:ring-red-400/20',
    'review': 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20'
};

// Get status mapping (config overrides default)
const statuses = computed(() => {
    return { ...defaultStatuses, ...props.config.statusMapping };
});

// Default field formatters - can be overridden by config
const defaultFieldFormatters = {
    // Date formatter
    date: (value) => {
        if (!value) return 'N/A';
        return formatDistance(new Date(value), new Date(), { addSuffix: true });
    },
    // Text truncator
    truncate: (value, length = 40) => {
        if (!value) return 'N/A';
        return value.length > length ? value.substring(0, length) + '...' : value;
    },
    // Boolean indicator
    boolean: (value) => {
        return value ? 'bg-green-500' : 'bg-red-500';
    },
    // Color indicator
    color: (value) => {
        return value || '#3b82f6';
    },
    // Status text - now dynamic based on statusMapping
    status: (value) => {
        // Use the status mapping from config if available
        const statusMapping = props.config.statusMapping || {};
        const statusKeys = Object.keys(statusMapping);
        
        // If we have status mapping, use the key as display text
        if (statusKeys.length > 0) {
            return value || 'idle';
        }
        
        // Fallback to default mapping
        const statusMap = {
            'idle': 'Idle',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'blocked': 'Blocked',
            'review': 'Review'
        };
        return statusMap[value] || value;
    },
    // JSON formatter - improved
    json: (value) => {
        if (!value) return 'N/A';
        if (typeof value === 'object') {
            // For arrays, show a summary
            if (Array.isArray(value)) {
                if (value.length === 0) return '[]';
                if (value.length === 1) return `[${value[0].name || value[0].title || 'item'}]`;
                return `[${value.length} items]`;
            }
            // For objects, show a summary
            const keys = Object.keys(value);
            if (keys.length === 0) return '{}';
            if (keys.length === 1) return `{${keys[0]}: ${value[keys[0]]}}`;
            return `{${keys.length} properties}`;
        }
        return value;
    }
};

// Get field formatters (config overrides default)
const fieldFormatters = computed(() => {
    return { ...defaultFieldFormatters, ...props.config.fieldFormatters };
});

// Update the columns computed to use record.meta.columns
const columns = computed(() => {
    // First check if we have valid columns configuration
    const metaColumns = props.record?.meta?.columns;
    const configColumns = props.config?.columns;
    
    // Use meta columns if available and valid
    if (Array.isArray(metaColumns) && metaColumns.length > 0) {
        return metaColumns.map(col => {
            // Ensure each column has a key
            if (!col.key) {
                console.warn('Column missing key:', col);
                return { ...col, key: 'unknown' };
            }
            return col;
        });
    }
    
    // Use config columns if available and valid
    if (Array.isArray(configColumns) && configColumns.length > 0) {
        return configColumns.map(col => {
            // Ensure each column has a key
            if (!col.key) {
                console.warn('Column missing key:', col);
                return { ...col, key: 'unknown' };
            }
            return col;
        });
    }
    
    // If no valid columns found, return minimal default set
    console.warn('No valid columns configuration found');
    return [
        { key: 'id', label: 'ID' },
        { key: 'name', label: 'Name' }
    ];
});

// Update the columnWidths computed to use the model's metadata
const columnWidths = computed(() => {
    if (props.meta && props.meta.column_widths) {
        return props.meta.column_widths;
    }
    return {};
});

// Format field value based on formatter
const formatField = (column, value) => {
    const formatter = fieldFormatters.value[column.formatter];
    if (formatter) {
        return formatter(value, column.formatterOptions);
    }
    return value || 'N/A';
};

// Get field value (handles nested relations)
const getFieldValue = (column) => {
    const record = props.record || {};
    
    if (column.type === 'relation' && column.relationField) {
        return record[column.key]?.[column.relationField];
    }
    
    return record[column.key];
};
</script>

<template>
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
        <td v-for="column in columns" 
            :key="column.key || 'unknown'" 
            :class="[
                'py-2 px-2 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap',
                column.width, // The width class from column definition
                'max-w-0' // Force td to respect width
            ]"
        >
            <!-- Inner divs should fill the td width -->
            <div class="overflow-hidden text-ellipsis">
                <!-- Status fields -->
                <div v-if="column.formatter === 'status' || column.key === 'status'" class="w-full">
                    <Badges 
                        :status="record[column.key]?.toString() || record[column.key]" 
                        :status-mapping="statusMapping"
                        size="xxs"
                        variant="badge"
                    />
                </div>
                
                <!-- Boolean fields - small circle with border -->
                <div v-else-if="column.formatter === 'boolean' || typeof record[column.key] === 'boolean'" class="w-full">
                    <div 
                        class="w-2 h-2 rounded-full border"
                        :class="record[column.key] 
                            ? 'bg-green-500 border-green-600 dark:border-green-400' 
                            : 'bg-red-500 border-red-600 dark:border-red-400'"
                    ></div>
                </div>
                
                <!-- Color fields - using Badges component -->
                <div v-else-if="column.key === 'color' || column.formatter === 'color'" class="w-full">
                    <Badges 
                        :status="record[column.key] || '#3b82f6'"
                        variant="color"
                        size="xxs"
                    />
                </div>
                
                <!-- JSON fields -->
                <div v-else-if="column.formatter === 'json' || typeof record[column.key] === 'object'" class="w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ Array.isArray(record[column.key]) ? `${record[column.key].length} items` : 'Object' }}
                    </span>
                </div>
                
                <!-- Date fields -->
                <div v-else-if="column.formatter === 'date' || column.key.includes('_at') || column.key.includes('date')" class="w-full">
                    <span class="text-xs text-gray-600 dark:text-gray-300">
                        {{ new Date(record[column.key]).toLocaleDateString() }}
                    </span>
                </div>
                
                <!-- Default text fields - skip actions column -->
                <div v-else-if="column.key !== 'actions'" class="text-xs text-gray-900 dark:text-gray-100 w-full overflow-hidden text-ellipsis">
                    {{ record[column.key] || '-' }}
                </div>
            </div>
        </td>
        
        <!-- Action buttons column -->
        <td class="py-2 px-2 text-sm" :class="columns.find(c => c.key === 'actions')?.width || 'w-24'">
            <div class="flex items-center gap-1.5">
                <!-- Show button -->
                <button 
                    @click="$emit('show', record.id)" 
                    class="h-5 w-5 flex items-center justify-center rounded-full transition-all duration-150
                           bg-blue-50 hover:bg-blue-100 
                           dark:bg-blue-900/20 dark:hover:bg-blue-800/50
                           group"
                >
                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>

                <!-- Edit button -->
                <button 
                    @click="$emit('edit', record.id)" 
                    class="h-5 w-5 flex items-center justify-center rounded-full transition-all duration-150
                           bg-emerald-50 hover:bg-emerald-100
                           dark:bg-emerald-900/20 dark:hover:bg-emerald-800/50
                           group"
                >
                    <svg 
                        class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>

                <!-- Delete button -->
                <button 
                    @click="$emit('delete', record.id)" 
                    class="h-5 w-5 flex items-center justify-center rounded-full transition-all duration-150
                           bg-red-50 hover:bg-red-100
                           dark:bg-red-900/20 dark:hover:bg-red-800/50
                           group"
                >
                    <svg 
                        class="w-3.5 h-3.5 text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
</template> 