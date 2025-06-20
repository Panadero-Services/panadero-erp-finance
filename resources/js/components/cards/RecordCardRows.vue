<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { usePage } from '@inertiajs/vue3';

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
    // Status text
    status: (value) => {
        const statusMap = {
            'idle': 'Idle',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'blocked': 'Blocked',
            'review': 'Review'
        };
        return statusMap[value] || value;
    }
};

// Get field formatters (config overrides default)
const fieldFormatters = computed(() => {
    return { ...defaultFieldFormatters, ...props.config.fieldFormatters };
});

// Default columns configuration - can be overridden by config
const defaultColumns = computed(() => {
    // If no custom columns defined, use the record's available fields
    if (props.config.columns && props.config.columns.length > 0) {
        return props.config.columns;
    }

    // Auto-generate columns based on record structure
    const record = props.record || {};
    const columns = [];

    // Always include ID first
    columns.push({
        key: 'id',
        label: 'ID',
        type: 'text',
        width: 'w-16',
        formatter: 'text'
    });

    // Add other common fields
    if (record.item !== undefined) {
        columns.push({
            key: 'item',
            label: 'Item',
            type: 'text',
            width: 'w-32',
            formatter: 'text'
        });
    }

    if (record.title !== undefined) {
        columns.push({
            key: 'title',
            label: 'Title',
            type: 'text',
            width: 'w-48',
            formatter: 'text',
            clickable: true
        });
    }

    if (record.description !== undefined) {
        columns.push({
            key: 'description',
            label: 'Description',
            type: 'text',
            width: 'w-64',
            formatter: 'truncate'
        });
    }

    if (record.color !== undefined) {
        columns.push({
            key: 'color',
            label: 'Color',
            type: 'color',
            width: 'w-24',
            formatter: 'color'
        });
    }

    if (record.version !== undefined) {
        columns.push({
            key: 'version',
            label: 'Version',
            type: 'text',
            width: 'w-24',
            formatter: 'text'
        });
    }

    if (record.icon !== undefined) {
        columns.push({
            key: 'icon',
            label: 'Icon',
            type: 'text',
            width: 'w-24',
            formatter: 'text'
        });
    }

    if (record.status !== undefined) {
        columns.push({
            key: 'status',
            label: 'Status',
            type: 'status',
            width: 'w-32',
            formatter: 'status'
        });
    }

    if (record.user !== undefined) {
        columns.push({
            key: 'user',
            label: 'User',
            type: 'relation',
            width: 'w-32',
            formatter: 'text',
            relationField: 'name'
        });
    }

    if (record.project !== undefined) {
        columns.push({
            key: 'project',
            label: 'Project',
            type: 'relation',
            width: 'w-32',
            formatter: 'text',
            relationField: 'title'
        });
    }

    // Add boolean flags
    if (props.config.flags) {
        props.config.flags.forEach(flag => {
            if (record[flag] !== undefined) {
                columns.push({
                    key: flag,
                    label: flag.replace('is_', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    type: 'boolean',
                    width: 'w-24',
                    formatter: 'boolean'
                });
            }
        });
    }

    // Add timestamps
    if (record.created_at !== undefined) {
        columns.push({
            key: 'created_at',
            label: 'Created',
            type: 'date',
            width: 'w-32',
            formatter: 'date'
        });
    }

    if (record.updated_at !== undefined) {
        columns.push({
            key: 'updated_at',
            label: 'Updated',
            type: 'date',
            width: 'w-32',
            formatter: 'date'
        });
    }

    // Always add actions column last
    columns.push({
        key: 'actions',
        label: 'Actions',
        type: 'actions',
        width: 'w-24',
        formatter: 'actions'
    });

    return columns;
});

// Get column widths (config overrides default)
const columnWidths = computed(() => {
    const defaultWidths = {};
    defaultColumns.value.forEach(col => {
        defaultWidths[col.key] = col.width;
    });
    return { ...defaultWidths, ...props.config.columnWidths };
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
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
        <template v-for="column in defaultColumns" :key="column.key">
            <!-- ID Column -->
            <td v-if="column.key === 'id'" class="py-2 px-2 text-xs text-gray-600 dark:text-gray-300 font-mono">
                {{ formatField(column, getFieldValue(column)) }}
            </td>

            <!-- Text Columns -->
            <td v-else-if="column.type === 'text'" class="py-2 px-2 text-xs text-gray-600 dark:text-gray-300"
                 :class="{ 'text-gray-900 dark:text-white font-medium cursor-pointer hover:text-blue-600 dark:hover:text-blue-400': column.clickable }"
                 @click="column.clickable && $emit('show', record?.id)">
                {{ formatField(column, getFieldValue(column)) }}
            </td>

            <!-- Description/Truncate Columns -->
            <td v-else-if="column.formatter === 'truncate'" class="py-2 px-2 text-xs text-gray-500 dark:text-gray-400">
                {{ formatField(column, getFieldValue(column)) }}
            </td>

            <!-- Color Column -->
            <td v-else-if="column.type === 'color'" class="py-2 px-2 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full border border-gray-300 dark:border-gray-600" 
                         :style="{ backgroundColor: formatField(column, getFieldValue(column)) }"></div>
                    <span class="text-gray-700 dark:text-gray-300">{{ getFieldValue(column) || 'blue' }}</span>
                </div>
            </td>

            <!-- Status Column -->
            <td v-else-if="column.type === 'status'" class="py-2 px-2 text-xs">
                <span :class="[
                    'inline-flex items-center rounded-md px-2 py-1 text-xxs font-medium ring-1 ring-inset',
                    statuses[getFieldValue(column)] || statuses.idle
                ]">
                    {{ formatField(column, getFieldValue(column)) }}
                </span>
            </td>

            <!-- Relation Columns -->
            <td v-else-if="column.type === 'relation'" class="py-2 px-2 text-xs text-gray-600 dark:text-gray-300">
                {{ formatField(column, getFieldValue(column)) }}
            </td>

            <!-- Boolean Columns -->
            <td v-else-if="column.type === 'boolean'" class="py-2 px-2 text-xs">
                <div :class="[
                    'w-3 h-3 rounded-full border border-gray-300 dark:border-gray-600',
                    formatField(column, getFieldValue(column))
                ]"></div>
            </td>

            <!-- Date Columns -->
            <td v-else-if="column.type === 'date'" class="py-2 px-2 text-xs text-gray-500 dark:text-gray-400">
                {{ formatField(column, getFieldValue(column)) }}
            </td>

            <!-- Actions Column -->
            <td v-else-if="column.type === 'actions'" class="py-2 px-2 text-xs">
                <div class="flex items-center gap-x-2">
                    <button 
                        @click="$emit('show', record?.id)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
                        title="Show"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    
                    <button 
                        v-if="!(set?.self==='nope')"
                        @click="$emit('edit', record?.id)"
                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors"
                        title="Edit"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    
                    <button 
                        @click="$emit('delete', record?.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                        title="Delete"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </td>

            <!-- Default fallback -->
            <td v-else class="py-2 px-2 text-xs text-gray-600 dark:text-gray-300">
                {{ formatField(column, getFieldValue(column)) }}
            </td>
        </template>
    </tr>
</template> 