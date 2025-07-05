<script setup>
const middlewareChain = [
    {
        name: 'Logging',
        color: '#60A5FA',
    },
    {
        name: 'Cache',
        color: '#34D399',
    },
    {
        name: 'Authentication',
        color: '#F87171',
    },
    {
        name: 'SelfAuthentication',
        color: '#9333EA',
    },
    {
        name: 'SonarAuthentication',
        color: '#0EA5E9',
    },
    {
        name: 'Rate Limit',
        color: '#FBBF24',
    },
    {
        name: 'Authorization',
        color: '#A78BFA',
    },
    {
        name: 'Transform',
        color: '#EC4899',
    },
    {
        name: 'Request',
        color: '#6EE7B7',
    }
];
</script>

<template>
    <div class="flex items-center space-x-6">
        <div v-for="middleware in middlewareChain" 
             :key="middleware.name"
             class="flex items-center text-sm group">
            <div class="w-3 h-3 rounded-full"
                 :style="{ backgroundColor: middleware.color }">
            </div>
            <span class="ml-2 text-gray-700 dark:text-white">
                {{ middleware.name }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.group {
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.2s;
}

.group:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.dark .group:hover {
    background-color: rgba(255, 255, 255, 0.05);
}
</style> 