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
            showTags: true
        })
    }
});

const emit = defineEmits(['edit', 'show', 'delete']);

// Process HTML content if needed
const processHtml = (html) => {
    if (!html) return '';
    return html.replace(/<a data-inertia-link href="([^"]+)">([^<]+)<\/a>/g, '<inertia-link href="$1">$2 click on me</inertia-link>');
}

const _body = computed(() => {
    if (!props.record?.[props.config.body]) return '';
    return processHtml(props.record[props.config.body]);
});

const formattedBody = computed(() => {
    return _body.value.replaceAll('.', '.<br>');
});

const initials = computed(() => {
    if (!props.record?.user?.name) return 'U';
    const names = props.record.user.name.trim().split(' ');
    if (names.length >= 2) {
        return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
    }
    return names[0].charAt(0).toUpperCase();
});

const statusFlags = computed(() => {
    return props.config.flags.map(flag => ({
        key: flag,
        label: flag.replace('is_', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        icon: getIconForFlag(flag),
        active: props.record?.[flag] === 1
    }));
});

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
</script>

<template>
    <div class="relative block w-52 h-36 m-1">
        <div class="h-full bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700 overflow-hidden group">
            
            <!-- Header with Icon and Title -->
            <div class="flex items-center p-3 pb-2">
                <!-- Icon Circle -->
                <div class="flex-shrink-0 mr-2">
                    <div class="w-6 h-6 absolute -top-2 left-0 bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-500 dark:to-indigo-600 rounded-full items-center justify-center shadow-md">
                        <span class="text-blue-100 text-bold text-center text-xxs p-1.5">{{ initials }}</span>
                    </div>
                </div>
                
                <!-- Title -->
                <div class="flex-1 min-w-0">
                    <h3 @click="$emit('show', record?.id)" 
                        class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer line-clamp-1 group-hover:underline transition-colors">
                        {{ record?.[config.title] }}
                    </h3>
                    <p class="text-xxxs text-gray-500 dark:text-gray-500">Updated {{ formatDistance(record?.updated_at ?? new Date(), new Date()) }} ago</p>
                </div>
            </div>

            <!-- Content Preview - moved down 1px -->
            <div class="px-3 pb-2 mt-0.5">
                <div class="text-xxs text-gray-600 dark:text-gray-300 line-clamp-2 leading-tight">
                    {{ record?.[config.body]?.substring(0, 80) }}{{ record?.[config.body]?.length > 80 ? '...' : '' }}
                </div>
            </div>

            <!-- Boolean Icons above footer -->
            <div class="absolute bottom-8 left-2 right-2 flex justify-end">
                <div class="flex space-x-1">
                    <CategorySectionIcon 
                        v-for="flag in statusFlags.slice(0, 2)"
                        :key="flag.key"
                        :icon="flag.icon"
                        :activated="flag.active"
                        :error="false"
                        :title="flag.label"
                        class="h-3 w-3"
                    />
                </div>
            </div>

            <!-- Footer with ID and Action Buttons -->
            <div class="absolute bottom-2 left-2 right-2 flex justify-between items-center">
                <!-- ID on the left -->
                <div class="text-xxs text-gray-500 dark:text-gray-400 font-mono">
                    #{{ record?.id }}
                </div>

                <!-- Action Buttons on the right - Always visible but gray, colored on hover -->
                <div class="flex space-x-1">
                    <button class="w-6 h-6 bg-gray-400/20 group-hover:bg-blue-500 dark:bg-gray-500/30 dark:group-hover:bg-blue-600 rounded-full flex items-center justify-center group-hover:text-white text-xxs shadow-md hover:shadow-lg transition-all duration-200" 
                        @click="$emit('show', record?.id ?? 0)"
                        title="Show">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    
                    <button v-if="!(set?.self==='nope')" 
                        class="w-6 h-6 bg-gray-400/20 group-hover:bg-green-500 dark:bg-gray-500/30 dark:group-hover:bg-green-600 rounded-full flex items-center justify-center group-hover:text-white text-xxs shadow-md hover:shadow-lg transition-all duration-200" 
                        @click="$emit('edit', record?.id ?? 0)"
                        title="Edit">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    
                    <button class="w-6 h-6 bg-gray-400/20 group-hover:bg-red-500 dark:bg-gray-500/30 dark:group-hover:bg-red-600 rounded-full flex items-center justify-center group-hover:text-white text-xxs shadow-md hover:shadow-lg transition-all duration-200" 
                        @click="$emit('delete', record?.id ?? 0)"
                        title="Delete">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        </div>
    </div>
</template> 