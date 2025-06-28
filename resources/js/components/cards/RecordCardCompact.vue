<script setup>
import { ref, computed } from 'vue';
import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';
import { usePage } from '@inertiajs/vue3';
import Badges from '@/components/colors/Badges.vue';
import { 
    EyeIcon,
    PencilIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    module: String,
    table: String,
    record: Object,
    db: Object,
    set: Object,
    meta: Object,
    config: {
        type: Object,
        default: () => ({
            title: 'title',
            item: 'item',
            version: 'version',
            color: 'color'
        })
    }
});

const emit = defineEmits(['edit', 'show', 'delete']);

const defaultFlags = [
    'is_published',
    'is_public',
    'is_featured',
    'is_locked',
    'is_self',
    'is_smart',
    'is_active',
    'is_archived'
];

const statusFlags = computed(() => {
    return Object.keys(props.record)
        .filter(key => key.startsWith('is_'))
        .map(flag => ({
            key: flag,
            icon: getIconForFlag(flag),
            active: props.record[flag] === true || props.record[flag] === 1
        }));
});

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
    return iconMap[flag] || 'QuestionMarkCircle';
};

// Add icon mapping for buttons
const buttonIcons = {
    show: 'Eye',
    edit: 'Pencil',
    delete: 'Trash'
};

// Update button styles to be more compact and square for icons
const _button = {
    base: "rounded px-0.5 py-0.5 text-[8px] ring-1 ring-inset transition-colors duration-200 flex items-center justify-center",
    default: "text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400",
    width: "w-6" // Reduced width since we're using icons
};

// Update computed property for truncated title to 32 chars
const truncatedTitle = computed(() => {
    const title = props.record?.[props.config.title] || '';
    return title.length > 32 ? title.slice(0, 32) + '...' : title;
});

// Add this computed property after the other computed properties
const initials = computed(() => {
    // First try to get initials from the item if it exists
    if (props.record?.[props.config.item]) {
        return props.record[props.config.item].charAt(0).toUpperCase();
    }
    
    // If no item, try to get from user name if it exists
    if (props.record?.user?.name) {
        const names = props.record.user.name.trim().split(' ');
        if (names.length >= 2) {
            return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
        }
        return names[0].charAt(0).toUpperCase();
    }
    
    // Default fallback
    return 'U';
});

const getRandomChar = () => {
    const chars = '@#$%&*!?=+';
    return chars[Math.floor(Math.random() * chars.length)];
};

const formatTitle = (title) => {
    if (!title) return 'U';
    const noSpaces = title.replace(/\s+/g, getRandomChar);
    return noSpaces.substring(0, 3) + '-' + noSpaces.slice(-2);
};
</script>

<template>
    <div class="relative block w-80 h-[64px] m-1 ">
        <!-- Main Card Container -->
        <div class="h-full flex rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden ">
          








            <!-- Left Avatar - Full Height -->
            <div class="w-6 min-w-[1.5rem] flex items-center justify-center px-0 bg-gradient-to-br from-blue-500 to-indigo-600 via-indigo-700 dark:from-blue-600 dark:to-indigo-800">
                <span class="text-[9px] font-medium text-blue-200 transform -rotate-90 uppercase whitespace-nowrap px-0">
                    {{ record.item || (record?.[config.title] && formatTitle(record[config.title])) || 'U' }}
                </span>
            </div>

            <!-- Right Content -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
                <!-- Top Row: Title with strict 80% width and single line clamp -->
                <div class="h-[25px] flex items-center px-3 mt-1">
                    <div class="w-4/5 flex">






                        <h3 class="text-xs font-medium text-gray-900 dark:text-white line-clamp-1 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" 
                            @click="$emit('show', record?.id ?? 0)">                            


    

                            {{ record?.[config.title] }}

                        </h3>
                    </div>
                    <div class="w-1/5 flex-none text-xxxs text-right text-gray-900 dark:text-white ">
                        {{ record?.version || 'id' + record?.id}}
                    </div>

                </div>


                <!-- Middle Row: Title with strict 80% width and single line clamp -->
                <div class="h-[10px] flex items-center px-3 flex">
                    
                    <div class="w-2/5 ">





                        <span  class="text-xxxs text-gray-500 dark:text-gray-400 ">
                        {{ formatDistance(record?.updated_at ?? new Date(), new Date()) }} ago
                        </span>
                    </div>

                </div>






                <!-- Bottom Row: Completely Independent -->
                <div class="h-[25px] flex items-center px-3 mt-2">
                    <!-- Left Side: Icons -->
                    <div class="flex-1 min-w-0 overflow-hidden">
                        <div class="flex items-center space-x-1 translate-y-[1px]">
                            <CategorySectionIcon 
                                v-for="flag in statusFlags"
                                :key="flag.key"
                                :icon="flag.icon"
                                :activated="record[flag.key]"
                                :error="false"
                                size="2.5"
                            />
                        </div>
                    </div>




                 <!-- Status Badge -->
                        <div v-if="record?.status" class="w-1/5 scale-75 -mt-2">
                            <Badges 
                                :status="record.status"
                                size="xxs"
                                variant="badge"
                            />
                        </div>  

                        <!-- Color indicator -->
                        <div v-else-if="record?.color" class="scale-75 -mt-2" title="Color">
                            <div class="w-2.5 h-2.5 rounded-sm"
                                 :style="{ backgroundColor: record.color }">
                            </div>
                        </div>


                    <!-- Right Side: Action Icons -->
                    <div class="flex-none flex items-center space-x-1 w-1/5 justify-end -mr-0.5">
                        <CategorySectionIcon 
                            icon="Eye"
                            :activated="true"
                            :error="false"
                                size="2.5"
                            @click="$emit('show', record?.id ?? 0)"
                        />
                        <CategorySectionIcon 
                            v-if="!(set?.self==='nope')"
                            icon="PencilSquare"
                            :activated="true"
                            :error="false"
                            size="2.5"
                            activatedColor="text-green-600 dark:text-green-400"
                            @click="$emit('edit', record?.id ?? 0)"
                        />
                        <CategorySectionIcon 
                            icon="Trash"
                            :activated="true"
                            :error="false"
                            size="2.5"
                            activatedColor="text-red-600 dark:text-red-400"
                            @click="$emit('delete', record?.id ?? 0)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 