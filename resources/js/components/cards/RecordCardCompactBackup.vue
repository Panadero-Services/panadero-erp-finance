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
    <div class="relative block w-48 h-32 m-1">
        <div class="px-2 py-1 h-full opacity-90 from-gray-700/50 via-transparent rounded-md shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500 bg-white text-gray-600 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300 flex flex-col">
            <div class="flex flex-row items-center mb-0.5 w-full">
                <div v-if="config.showUser" class="absolute -top-1.5 -left-1 h-5 w-5 bg-blue-200 dark:bg-blue-900 flex items-center justify-center rounded-full flex-shrink-0">
                    <div class="text-blue-600 dark:text-blue-300  text-xxxs">{{ initials }}</div>
                </div>
                <div class="flex-1">
                    <h3 @click="$emit('show', record?.id)" class="pl-3 line-clamp-1 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer text-xs">
                        {{ record?.[config.title] }}
                    </h3>
                    <p class="text-xxxs text-gray-500 dark:text-gray-500">Updated {{ formatDistance(record?.updated_at ?? new Date(), new Date()) }} ago</p>
                </div>

            </div>
            <div class="flex-1 w-full overflow-hidden">
                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-0.5 rounded border border-gray-200 dark:border-gray-700 overflow-hidden text-xxs line-clamp-2" 
                    v-html="formattedBody">
                </div>
            </div>


                <div class="flex -ml-3 gap-0">
                    <CategorySectionIcon 
                        v-for="flag in statusFlags"
                        :key="flag.key"
                        :icon="flag.icon"
                        :activated="flag.active"
                        :error="false"
                        :title="flag.label"
                        class="h-3 w-3"
                    />

                    <CategorySectionIcon 
                        v-for="flag in statusFlags"
                        :key="flag.key"
                        :icon="flag.icon"
                        :activated="flag.active"
                        :error="false"
                        :title="flag.label"
                        class="h-3 w-3"
                    />


                </div>




            <div class="flex flex-row justify-between items-center mt-1">
                <div class="text-xxxs text-gray-500 dark:text-gray-500">
                    id: {{ record?.id }}
                    <span v-if="config.showTags && record?.tags" v-for="tag in record?.tags" 
                        :key="tag.id"
                        class="text-xxs bg-indigo-50 text-indigo-700 dark:text-indigo-200 dark:bg-indigo-900 dark:border-indigo-600 py-0.5 px-1 m-0.5 rounded-2xl border border-indigo-300">
                        {{ tag.name }}
                    </span>
                </div>








                <div class="flex space-x-0.5">
                    <button class="rounded p-1 text-xxxs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" 
                        @click="$emit('show', record?.id ?? 0)">Show</button>
                    <button v-if="!(set?.self==='nope')" 
                        class="rounded p-1  text-xxxs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" 
                        @click="$emit('edit', record?.id ?? 0)">Edit</button>
                    <button class="rounded  p-1 text-xxxs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover-text-gray-700 dark:hover:ring-indigo-400" 
                        @click="$emit('delete', record?.id ?? 0)">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template> 