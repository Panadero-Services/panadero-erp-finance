<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    middlewareChain: {
        type: Array,
        default: () => [
            {
                name: 'Logging',
                order: 1,
                color: '#60A5FA', // blue
                checks: ['Request Log', 'Audit Trail']
            },
            {
                name: 'Cache',
                order: 5,
                color: '#34D399', // green
                checks: ['Cache Hit', 'Freshness']
            },
            {
                name: 'Authentication',
                order: 10,
                color: '#F87171', // red
                checks: ['Session', 'CSRF', 'Tokens', 'Access']
            },
            {
                name: 'Rate Limit',
                order: 15,
                color: '#FBBF24', // yellow
                checks: ['Request Rate', 'Burst Limit', 'Cooldown']
            },
            {
                name: 'Authorization',
                order: 20,
                color: '#A78BFA', // purple
                checks: ['Roles', 'Permissions', 'Resources', 'Actions']
            },
            {
                name: 'Transform',
                order: 25,
                color: '#EC4899', // pink
                checks: ['Input Transform', 'Output Transform']
            },
            {
                name: 'Request',
                order: 30,
                color: '#6EE7B7', // emerald
                checks: ['Headers', 'Integrity', 'Schema', 'Sanitization']
            }
        ]
    }
});

const selectedMiddleware = ref(null);
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <!-- Title -->
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
            Middleware Architecture Flow
        </h2>

        <!-- Main Flow Diagram -->
        <div class="relative">
            <!-- Middleware Chain -->
            <div class="flex flex-col space-y-4">
                <div v-for="middleware in middlewareChain" 
                     :key="middleware.name"
                     class="relative group"
                     @mouseenter="selectedMiddleware = middleware"
                     @mouseleave="selectedMiddleware = null">
                    
                    <!-- Middleware Box -->
                    <div class="flex items-center">
                        <!-- Order Number -->
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                             :style="{ backgroundColor: middleware.color + '20', color: middleware.color }">
                            {{ middleware.order }}
                        </div>

                        <!-- Middleware Name and Box -->
                        <div class="ml-4 flex-1">
                            <div class="p-4 rounded-lg border-2 transition-all duration-200"
                                 :class="selectedMiddleware?.name === middleware.name ? 'border-opacity-100' : 'border-opacity-50'"
                                 :style="{ borderColor: middleware.color, backgroundColor: middleware.color + '10' }">
                                
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium text-gray-900 dark:text-white">
                                        {{ middleware.name }} Layer
                                    </h3>
                                    
                                    <!-- Validation Checks -->
                                    <div class="flex space-x-2">
                                        <span v-for="check in middleware.checks" 
                                              :key="check"
                                              class="text-xs px-2 py-1 rounded-full"
                                              :style="{ backgroundColor: middleware.color + '20', color: middleware.color }">
                                            {{ check }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Line -->
                    <div v-if="middleware !== middlewareChain[middlewareChain.length - 1]"
                         class="absolute left-4 top-full h-4 w-px"
                         :style="{ backgroundColor: middleware.color }">
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="absolute top-0 right-0 bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md">
                <h4 class="text-sm font-medium mb-2 text-gray-900 dark:text-white">Legend</h4>
                <div class="space-y-2">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="flex items-center text-xs">
                        <div class="w-3 h-3 rounded-full"
                             :style="{ backgroundColor: middleware.color }">
                        </div>
                        <span class="ml-2 text-gray-600 dark:text-gray-300">
                            {{ middleware.name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Panel -->
        <div v-if="selectedMiddleware" 
             class="mt-6 p-4 rounded-lg border-2 transition-all duration-200"
             :style="{ borderColor: selectedMiddleware.color + '50', backgroundColor: selectedMiddleware.color + '05' }">
            <h3 class="font-medium mb-2 text-gray-900 dark:text-white">
                {{ selectedMiddleware.name }} Layer Details
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div v-for="check in selectedMiddleware.checks" 
                     :key="check"
                     class="p-2 rounded bg-white dark:bg-gray-700 shadow-sm">
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ check }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.middleware-chain-line {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    background: linear-gradient(to bottom, transparent, currentColor);
}
</style> 