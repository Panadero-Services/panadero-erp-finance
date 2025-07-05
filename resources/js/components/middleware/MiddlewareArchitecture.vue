<script setup>
import { ref } from 'vue';

const middlewareChain = ref([
    {
        name: 'Logging',
        order: 1,
        color: '#60A5FA', // blue
        checks: ['Request Log', 'Audit Trail'],
        description: 'Records all incoming requests and system activities',
        responsibilities: [
            'Request logging - Records incoming HTTP requests',
            'Audit trails - Tracks user actions and system changes',
            'Error logging - Captures system and runtime errors',
            'Performance metrics - Monitors response times and system load'
        ]
    },
    {
        name: 'Cache',
        order: 5,
        color: '#34D399', // green
        checks: ['Cache Hit', 'Freshness'],
        description: 'Manages data caching and retrieval for improved performance',
        responsibilities: [
            'Response caching - Stores frequently accessed data',
            'Cache validation - Ensures data freshness',
            'Cache invalidation - Removes stale data',
            'Cache optimization - Manages cache size and performance'
        ]
    },
    {
        name: 'Authentication',
        order: 10,
        color: '#F87171', // red
        checks: ['Session', 'CSRF', 'Tokens', 'Access'],
        description: 'Verifies user identity and session validity',
        responsibilities: [
            'Session validation - Checks active user sessions',
            'Token verification - Validates CSRF and API tokens',
            'Identity confirmation - Verifies user credentials',
            'Access control - Manages login state and session security'
        ]
    },
    {
        name: 'SelfAuthentication',
        order: 11,
        color: '#9333EA', // purple-600
        checks: ['WalletConnected', 'HasSelf', 'IsSelfListed'],
        description: 'Validates self-identity through wallet and listing status',
        responsibilities: [
            'Wallet verification - Checks wallet connection status',
            'Self identity - Validates self identity verification',
            'Listing validation - Confirms self listing status',
            'Identity management - Handles self identity lifecycle'
        ]
    },
    {
        name: 'SonarAuthentication',
        order: 12,
        color: '#0EA5E9', // sky-500
        checks: ['WalletConnected', 'hasSonar', 'IsSonarListed'],
        description: 'Validates Sonar identity through wallet and listing status',
        responsibilities: [
            'Wallet verification - Checks wallet connection status',
            'Sonar identity - Validates Sonar identity verification',
            'Listing validation - Confirms Sonar listing status',
            'Identity management - Handles Sonar identity lifecycle'
        ]
    },
    {
        name: 'Rate Limit',
        order: 15,
        color: '#FBBF24', // yellow
        checks: ['Request Rate', 'Burst Limit', 'Cooldown'],
        description: 'Controls request frequency to prevent abuse',
        responsibilities: [
            'Request throttling - Limits request frequency',
            'Burst protection - Prevents request flooding',
            'Rate monitoring - Tracks API usage',
            'Quota management - Enforces usage limits'
        ]
    },
    {
        name: 'Authorization',
        order: 20,
        color: '#A78BFA', // purple
        checks: ['Roles', 'Permissions', 'Resources', 'Actions'],
        description: 'Controls access rights and permissions',
        responsibilities: [
            'Role verification - Checks user roles and capabilities',
            'Permission control - Manages access rights',
            'Resource protection - Controls resource access',
            'Action validation - Verifies allowed operations'
        ]
    },
    {
        name: 'Transform',
        order: 25,
        color: '#EC4899', // pink
        checks: ['Input Transform', 'Output Transform'],
        description: 'Handles data transformation and formatting',
        responsibilities: [
            'Input processing - Formats incoming data',
            'Output formatting - Structures response data',
            'Data normalization - Standardizes data formats',
            'Content negotiation - Handles different data types'
        ]
    },
    {
        name: 'Request',
        order: 30,
        color: '#6EE7B7', // emerald
        checks: ['Headers', 'Integrity', 'Schema', 'Sanitization'],
        description: 'Validates and sanitizes request data',
        responsibilities: [
            'Header validation - Checks HTTP headers',
            'Data integrity - Ensures data completeness',
            'Schema validation - Verifies data structure',
            'Input sanitization - Cleanses incoming data'
        ]
    }
]);

const selectedMiddleware = ref(middlewareChain.value[0]); // Default to first item
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <!-- Header with Title and Legend -->
        <div class="flex justify-between items-start mb-8">
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Middleware Architecture
            </h2>

            <!-- Legend -->
            <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md">
                <h4 class="text-sm font-medium mb-3 text-gray-900 dark:text-white">Legend</h4>
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="flex items-center text-xs group">
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

        <div class="flex max-w-7xl mx-auto">
            <!-- Left Column -->
            <div class="w-[520px] border-r dark:border-gray-700 pr-6 flex-shrink-0">
                <div class="flex flex-col space-y-4">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="relative group cursor-pointer"
                         :class="{'ring-2 ring-offset-2': selectedMiddleware?.name === middleware.name}"
                         @click="selectedMiddleware = middleware">
                        
                        <!-- Middleware Box -->
                        <div class="flex items-center">
                            <!-- Order Number -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium flex-shrink-0"
                                 :style="{ backgroundColor: middleware.color + '20', color: middleware.color }">
                                {{ middleware.order }}
                            </div>

                            <!-- Middleware Name -->
                            <div class="ml-4 flex-1 min-w-0">
                                <div class="p-3 rounded-lg border-2 transition-all duration-200"
                                     :class="selectedMiddleware?.name === middleware.name ? 'border-opacity-100 bg-gray-50 dark:bg-gray-700' : 'border-opacity-50'"
                                     :style="{ borderColor: middleware.color }">
                                    
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ middleware.name }}
                                        </h4>
                                        
                                        <!-- Compact Badges -->
                                        <div class="flex space-x-1 flex-shrink-0 ml-2">
                                            <span v-for="check in middleware.checks.slice(0, 2)" 
                                                  :key="check"
                                                  class="text-xs px-2 py-0.5 rounded-full text-gray-900 dark:text-white whitespace-nowrap"
                                                  :style="{ backgroundColor: middleware.color + '20' }">
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
            </div>

            <!-- Right Column -->
            <div class="w-[760px] pl-6 flex-shrink-0">
                <div class="sticky top-4">
                    <div v-if="selectedMiddleware" 
                         class="p-6 rounded-lg border-2 transition-all duration-200"
                         :style="{ borderColor: selectedMiddleware.color + '50', backgroundColor: selectedMiddleware.color + '05' }">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ selectedMiddleware.name }} Layer
                            </h3>
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    Order:
                                </span>
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-medium"
                                      :style="{ backgroundColor: selectedMiddleware.color + '20', color: selectedMiddleware.color }">
                                    {{ selectedMiddleware.order }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 dark:text-white mb-6">
                            {{ selectedMiddleware.description }}
                        </p>

                        <!-- Validation Checks -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                Validation Checks
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="check in selectedMiddleware.checks" 
                                      :key="check"
                                      class="text-sm px-3 py-1 rounded-full text-gray-900 dark:text-white"
                                      :style="{ backgroundColor: selectedMiddleware.color + '20' }">
                                    {{ check }}
                                </span>
                            </div>
                        </div>

                        <!-- Responsibilities -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                Responsibilities
                            </h4>
                            <div class="space-y-2">
                                <div v-for="responsibility in selectedMiddleware.responsibilities" 
                                     :key="responsibility"
                                     class="flex items-start space-x-2 p-2 rounded bg-white dark:bg-gray-700">
                                    <div class="w-1.5 h-1.5 mt-1.5 rounded-full flex-shrink-0"
                                         :style="{ backgroundColor: selectedMiddleware.color }">
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-white">
                                        {{ responsibility }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Placeholder when no middleware is selected -->
                    <div v-else class="h-full flex items-center justify-center text-gray-500 dark:text-white">
                        Select a middleware to view details
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.sticky {
    position: sticky;
    top: 1rem;
}
</style> 