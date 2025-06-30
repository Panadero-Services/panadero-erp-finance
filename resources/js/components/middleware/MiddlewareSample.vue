<script setup>
import { ref } from 'vue';

// Keep original test request
const testRequest = ref({
    headers: {
        'Authorization': 'Bearer test-token',
        'Content-Type': 'application/json'
    },
    body: {
        action: 'test-action',
        data: 'test-data'
    }
});

// Add test scenarios while keeping original functionality
const testScenarios = ref([
    {
        name: 'Valid Request',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json'
            },
            body: {
                action: 'create',
                data: { id: 1, name: 'Test Item' }
            }
        }
    },
    {
        name: 'Invalid Token',
        request: {
            headers: {
                'Authorization': 'Bearer expired-token',
                'Content-Type': 'application/json'
            },
            body: {
                action: 'read',
                data: { id: 1 }
            }
        }
    },
    {
        name: 'Missing Permissions',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json'
            },
            body: {
                action: 'delete',
                data: { id: 1 }
            }
        }
    },
    {
        name: 'Invalid Data Format',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json'
            },
            body: {
                action: 'create',
                data: 'invalid-json-data'
            }
        }
    }
]);

const selectedScenario = ref(testScenarios.value[0]);

// Keep original middleware chain with enhanced test functions
const middlewareChain = ref([
    {
        name: 'Authentication',
        order: 1,
        color: '#F87171', // red
        active: true,
        test: (request) => {
            const token = request.headers.Authorization?.split(' ')[1];
            const isValidToken = token === 'valid-token';
            
            return {
                isValid: isValidToken,
                token: request.headers.Authorization,
                session: isValidToken ? 'active' : 'expired',
                checks: {
                    tokenValid: isValidToken,
                    sessionActive: isValidToken
                }
            };
        }
    },
    {
        name: 'Authorization',
        order: 2,
        color: '#60A5FA', // blue
        active: true,
        test: (request) => {
            const action = request.body.action;
            const allowedActions = {
                'create': ['read', 'write'],
                'read': ['read'],
                'delete': ['admin']
            };
            const userPermissions = ['read', 'write'];
            const hasPermission = allowedActions[action]?.some(p => userPermissions.includes(p)) ?? false;

            return {
                isValid: hasPermission,
                permissions: userPermissions,
                checks: {
                    hasAccess: true,
                    hasPermissions: hasPermission,
                    requiredPermissions: allowedActions[action] || []
                }
            };
        }
    },
    {
        name: 'Request Validation',
        order: 3,
        color: '#34D399', // green
        active: true,
        test: (request) => {
            const isValidFormat = typeof request.body.data === 'object';
            const hasRequiredFields = request.body.action && request.body.data;

            return {
                isValid: isValidFormat && hasRequiredFields,
                validatedData: request.body,
                checks: {
                    dataFormat: isValidFormat,
                    requiredFields: hasRequiredFields
                }
            };
        }
    }
]);

// Keep original test results tracking
const testResults = ref([]);

// Enhanced runTests function that works with both original and scenario testing
const runTests = () => {
    testResults.value = [];
    let currentRequest = selectedScenario.value ? { ...selectedScenario.value.request } : { ...testRequest.value };
    
    for (const middleware of middlewareChain.value) {
        if (middleware.active) {
            const result = middleware.test(currentRequest);
            testResults.value.push({
                middleware: middleware.name,
                result: result,
                timestamp: new Date()
            });
            
            // Stop chain if validation fails
            if (!result.isValid) {
                break;
            }
            
            // Update request for next middleware
            currentRequest = { ...currentRequest, ...result };
        }
    }
};
</script>

<template>
    <div class="container mx-auto py-4">
        <div class="space-y-4">
            <!-- Top section in 3 columns -->
            <div class="grid grid-cols-3 gap-4">
                <!-- Test Scenario Selection -->
                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Test Scenarios</h3>
                    <select v-model="selectedScenario" 
                            class="w-full p-2 text-xs border dark:border-gray-700 rounded dark:bg-gray-700 dark:text-white">
                        <option v-for="scenario in testScenarios" 
                                :key="scenario.name" 
                                :value="scenario">
                            {{ scenario.name }}
                        </option>
                    </select>
                </div>

                <!-- Request Data -->
                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Request Data</h3>
                    <pre class="text-[10px] leading-tight text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 p-2 rounded overflow-auto max-h-[120px]">{{ JSON.stringify(selectedScenario.request, null, 1) }}</pre>
                </div>

                <!-- Test Controls -->
                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Test Controls</h3>
                    <button 
                        @click="runTests"
                        class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                    >
                        Run Tests
                    </button>
                </div>
            </div>

            <!-- Middleware Chain Status -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Middleware Chain</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="p-3 border dark:border-gray-700 rounded text-xs"
                         :style="{ borderLeftColor: middleware.color, borderLeftWidth: '4px' }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div :class="middleware.active ? 'bg-green-500' : 'bg-gray-400'"
                                     class="w-2 h-2 rounded-full"></div>
                                <span class="text-gray-900 dark:text-white text-sm font-medium">{{ middleware.name }}</span>
                            </div>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       v-model="middleware.active" 
                                       class="form-checkbox h-4 w-4">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Active</span>
                            </label>
                        </div>
                        <div class="mt-2 text-gray-500 dark:text-gray-400">
                            <div class="text-xs">Order: {{ middleware.order }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div v-if="testResults.length" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Test Results</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div v-for="(result, index) in testResults" 
                         :key="index"
                         class="p-3 border dark:border-gray-700 rounded text-xs"
                         :class="{'border-red-500': !result.result.isValid}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ result.middleware }}</h4>
                            <span :class="result.result.isValid ? 'text-green-500' : 'text-red-500'"
                                  class="text-xs font-medium">
                                {{ result.result.isValid ? '✓ Passed' : '✗ Failed' }}
                            </span>
                        </div>
                        <div class="space-y-1">
                            <div v-for="(value, key) in result.result.checks" 
                                 :key="key"
                                 class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <span class="text-gray-600 dark:text-gray-400">{{ key }}</span>
                                <span :class="value ? 'text-green-500' : 'text-red-500'">
                                    {{ value ? '✓' : '✗' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-gray-500 dark:text-gray-400">
                            <div v-if="result.result.permissions" class="truncate">
                                Permissions: {{ result.result.permissions.join(', ') }}
                            </div>
                            <div v-if="result.result.session" class="truncate">
                                Session: {{ result.result.session }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 