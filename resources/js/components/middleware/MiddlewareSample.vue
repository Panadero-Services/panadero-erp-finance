<script setup>
import { ref, onMounted, watch } from 'vue';
import { middlewareManager } from '@/components/middleware/MiddlewareManager';
import MiddlewareArchitectureDiagram from '@/components/middleware/MiddlewareArchitectureDiagram.vue';
import Badges from '@/components/colors/Badges.vue';

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
                'Content-Type': 'application/json',
                'X-Request-ID': 'req-123',
                'Cache-Control': 'max-age=3600',
                'X-Wallet-Address': '0x123...abc',
                'X-Self-Identity': 'verified',
                'X-Self-Listed': 'true',
                'X-Sonar-Identity': 'verified',
                'X-Sonar-Listed': 'true'
            },
            body: {
                action: 'create',
                data: { id: 1, name: 'Test Item' }
            }
        }
    },
    {
        name: 'Invalid Self Authentication',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json',
                'X-Request-ID': 'req-124',
                'X-Wallet-Address': '0x123...abc',
                'X-Self-Identity': 'unverified',
                'X-Self-Listed': 'false',
                'X-Sonar-Identity': 'verified',
                'X-Sonar-Listed': 'true'
            },
            body: {
                action: 'read',
                data: { id: 1 }
            }
        }
    },
    {
        name: 'Invalid Sonar Authentication',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json',
                'X-Request-ID': 'req-125',
                'X-Wallet-Address': '0x123...abc',
                'X-Self-Identity': 'verified',
                'X-Self-Listed': 'true',
                'X-Sonar-Identity': 'unverified',
                'X-Sonar-Listed': 'false'
            },
            body: {
                action: 'delete',
                data: { id: 1 }
            }
        }
    },
    {
        name: 'Missing Wallet Connection',
        request: {
            headers: {
                'Authorization': 'Bearer valid-token',
                'Content-Type': 'application/json',
                'X-Request-ID': 'req-126',
                // Missing wallet and auth headers
            },
            body: {
                action: 'create',
                data: { id: 1 }
            }
        }
    }
]);

const selectedScenario = ref(testScenarios.value[0]);

// Enhanced middleware chain with visual states
const middlewareChain = ref([
    {
        name: 'Logging',
        order: 1,
        color: '#60A5FA', // blue
        active: true,
        status: 'pending',
        test: (request) => {
            // Add safe checks for undefined request/headers
            const headers = request?.headers || {};
            const hasRequestId = headers['X-Request-ID'] !== undefined;
            return {
                isValid: true, // Logging always passes but records
                requestId: headers['X-Request-ID'],
                checks: {
                    'Request Log': true,
                    'Audit Trail': hasRequestId
                }
            };
        }
    },
    {
        name: 'Cache',
        order: 5,
        color: '#34D399', // green
        active: true,
        status: 'pending',
        test: (request) => {
            const headers = request?.headers || {};
            const cacheHit = headers['Cache-Control'] !== 'no-cache';
            const isFresh = true; // Simplified for demo
            return {
                isValid: true, // Cache miss doesn't fail the request
                checks: {
                    'Cache Hit': cacheHit,
                    'Freshness': isFresh
                }
            };
        }
    },
    {
        name: 'Authentication',
        order: 10,
        color: '#F87171', // red
        active: true,
        status: 'pending',
        test: (request) => {
            const headers = request?.headers || {};
            const token = headers.Authorization?.split(' ')[1];
            const isValidToken = token === 'valid-token';
            return {
                isValid: isValidToken,
                checks: {
                    'Session': isValidToken,
                    'CSRF': true,
                    'Tokens': isValidToken,
                    'Access': isValidToken
                }
            };
        }
    },
    {
        name: 'SelfAuthentication',
        order: 11,
        color: '#9333EA', // purple-600
        active: true,
        status: 'pending',
        test: (request) => {
            const headers = request?.headers || {};
            const isWalletConnected = headers['X-Wallet-Address'] !== undefined;
            const hasSelf = headers['X-Self-Identity'] === 'verified';
            const isSelfListed = headers['X-Self-Listed'] === 'true';
            
            return {
                isValid: isWalletConnected && hasSelf && isSelfListed,
                checks: {
                    'WalletConnected': isWalletConnected,
                    'HasSelf': hasSelf,
                    'IsSelfListed': isSelfListed
                }
            };
        }
    },
    {
        name: 'SonarAuthentication',
        order: 12,
        color: '#0EA5E9', // sky-500
        active: true,
        status: 'pending',
        test: (request) => {
            const headers = request?.headers || {};
            const isWalletConnected = headers['X-Wallet-Address'] !== undefined;
            const hasSonar = headers['X-Sonar-Identity'] === 'verified';
            const isSonarListed = headers['X-Sonar-Listed'] === 'true';
            
            return {
                isValid: isWalletConnected && hasSonar && isSonarListed,
                checks: {
                    'WalletConnected': isWalletConnected,
                    'haSonar': hasSonar,
                    'IsSonarListed': isSonarListed
                }
            };
        }
    },
    {
        name: 'Rate Limit',
        order: 15,
        color: '#FBBF24', // yellow
        active: true,
        status: 'pending',
        test: (request) => {
            return {
                isValid: true,
                checks: {
                    'Request Rate': true,
                    'Burst Limit': true,
                    'Cooldown': true
                }
            };
        }
    },
    {
        name: 'Authorization',
        order: 20,
        color: '#A78BFA', // purple
        active: true,
        status: 'pending',
        test: (request) => {
            const body = request?.body || {};
            const action = body.action;
            const allowedActions = {
                'create': ['read', 'write'],
                'read': ['read'],
                'delete': ['admin']
            };
            const userPermissions = ['read', 'write'];
            const hasPermission = allowedActions[action]?.some(p => userPermissions.includes(p)) ?? false;

            return {
                isValid: hasPermission,
                checks: {
                    'Roles': true,
                    'Permissions': hasPermission,
                    'Resources': true,
                    'Actions': true
                }
            };
        }
    },
    {
        name: 'Transform',
        order: 25,
        color: '#EC4899', // pink
        active: true,
        status: 'pending',
        test: (request) => {
            return {
                isValid: true,
                checks: {
                    'Input Transform': true,
                    'Output Transform': true
                }
            };
        }
    },
    {
        name: 'Request',
        order: 30,
        color: '#6EE7B7', // emerald
        active: true,
        status: 'pending',
        test: (request) => {
            const body = request?.body || {};
            const isValidFormat = typeof body.data === 'object';
            const hasRequiredFields = body.action && body.data;

            return {
                isValid: isValidFormat && hasRequiredFields,
                checks: {
                    'Headers': true,
                    'Integrity': true,
                    'Schema': isValidFormat,
                    'Sanitization': hasRequiredFields
                }
            };
        }
    }
]);

const testResults = ref([]);
const isLoading = ref(false);
const error = ref(null);
const currentMiddlewareIndex = ref(-1);

// Sleep function for animation delays
const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Modified runTests to reset results first
const runTests = async () => {
    // Reset results before starting
    testResults.value = [];
    isLoading.value = true;
    error.value = null;
    currentMiddlewareIndex.value = -1;
    
    // Reset all middleware statuses
    middlewareChain.value.forEach(m => {
        m.status = 'pending';
    });
    
    try {
        const activeMiddleware = middlewareChain.value.filter(m => m.active);
        const results = [];
        
        // Process each middleware with animation
        for (let i = 0; i < activeMiddleware.length; i++) {
            const middleware = activeMiddleware[i];
            currentMiddlewareIndex.value = i;
            
            // Update status to processing
            middleware.status = 'processing';
            await sleep(1000); // Pause for visual effect
            
            // Run the test
            const result = await middleware.test(selectedScenario.value.request);
            
            // Update status based on result
            middleware.status = result.isValid ? 'success' : 'error';
            
            results.push({
                middleware: middleware.name,
                ...result,
                color: middleware.color
            });
            
            // If test failed, stop the chain
            if (!result.isValid) {
                break;
            }
            
            await sleep(500); // Small pause between middleware
        }
        
        testResults.value = results;
        
    } catch (err) {
        console.error('Middleware test error:', err);
        error.value = err.message;
        testResults.value = [];
    } finally {
        isLoading.value = false;
    }
};

// Watch for scenario changes to auto-run tests
watch(selectedScenario, () => {
    runTests();
});

// Initialize on mount
onMounted(() => {
    // Reset and run tests
    testResults.value = [];
    runTests();
});

// Expose for template
defineExpose({
    runTests,
    testResults,
    isLoading,
    error
});

</script>

<template>
    <div class="container mx-auto py-4">
        <!-- Main Content: Two Column Layout -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Left Column: Middleware Flow -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex flex-col space-y-4">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="relative"
                         :class="{ 'opacity-50': !middleware.active }">
                        
                        <!-- Middleware Box -->
                        <div class="flex items-center">
                            <!-- Order Number -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                 :style="{ backgroundColor: middleware.color + '20', color: middleware.color }">
                                {{ middleware.order }}
                            </div>

                            <!-- Middleware Name and Box -->
                            <div class="ml-4 flex-1">
                                <div class="p-3 rounded-lg border-2 transition-all duration-200 min-h-[80px]"
                                     :class="middleware.status !== 'pending' ? 'border-opacity-100' : 'border-opacity-50'"
                                     :style="{ borderColor: middleware.color, backgroundColor: middleware.color + '10' }">
                                    
                                    <!-- Header: Name and Status -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 rounded-full"
                                                 :class="{
                                                     'bg-gray-300': middleware.status === 'pending',
                                                     'bg-yellow-400 animate-pulse': middleware.status === 'processing',
                                                     'bg-green-500': middleware.status === 'success',
                                                     'bg-red-500': middleware.status === 'error'
                                                 }">
                                            </div>
                                            <h3 class="font-medium text-gray-900 dark:text-white text-sm">
                                                {{ middleware.name }}
                                            </h3>
                                        </div>
                                        
                                        <!-- Active Toggle -->
                                        <label class="flex items-center space-x-1">
                                            <input type="checkbox" 
                                                   v-model="middleware.active" 
                                                   class="form-checkbox h-3 w-3">
                                            <span class="text-[10px] text-gray-600 dark:text-gray-400">Active</span>
                                        </label>
                                    </div>
                                    
                                    <!-- Validation Checks -->
                                    <div class="flex flex-wrap gap-1.5">
                                        <Badges 
                                            v-for="check in Object.keys(middleware.test({}).checks)" 
                                            :key="check"
                                            status="info"
                                            size="xxs"
                                            variant="pill"
                                            :custom-text="check"
                                            class="px-2.5 py-0.5"
                                        />
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

            <!-- Right Column: Live Results -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex flex-col space-y-4">
                    <div v-for="middleware in middlewareChain" 
                         :key="middleware.name"
                         class="relative">
                        
                        <!-- Middleware Result Box -->
                        <div class="flex items-center">
                            <!-- Order Number -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                 :style="{ backgroundColor: middleware.color + '20', color: middleware.color }">
                                {{ middleware.order }}
                            </div>

                            <!-- Results Box -->
                            <div class="ml-4 flex-1">
                                <div class="p-3 rounded-lg border-2 transition-all duration-200 min-h-[80px]"
                                     :class="{
                                         'opacity-50': !middleware.active,
                                         'border-gray-200 dark:border-gray-700': middleware.status === 'pending',
                                         'border-yellow-400': middleware.status === 'processing',
                                         'border-green-500': middleware.status === 'success',
                                         'border-red-500': middleware.status === 'error'
                                     }">
                                    
                                    <!-- Header: Name and Status -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-xs font-medium text-gray-900 dark:text-white">
                                                {{ middleware.name }}
                                            </h3>
                                            <Badges 
                                                :status="middleware.status"
                                                size="xxs"
                                                variant="pill"
                                                class="px-2.5 py-0.5"
                                            />
                                        </div>
                                    </div>

                                    <!-- Test Results - Horizontal Layout -->
                                    <div v-if="testResults.find(r => r.middleware === middleware.name)?.checks"
                                         class="mt-2 flex flex-wrap gap-1.5">
                                        <Badges 
                                            v-for="(value, check) in testResults.find(r => r.middleware === middleware.name).checks"
                                            :key="check"
                                            :status="value ? 'success' : 'error'"
                                            size="xxs"
                                            variant="pill"
                                            :custom-text="check"
                                            class="px-2.5 py-0.5"
                                        />
                                    </div>
                                    <div v-else class="text-[10px] text-gray-500 dark:text-gray-400 italic">
                                        {{ middleware.status === 'pending' ? 'Waiting...' : 'Processing...' }}
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
        </div>

        <!-- Test Controls Section -->
        <div class="grid grid-cols-3 gap-4 mt-8">
            <!-- Column 1: Test Scenarios & Controls -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow space-y-4">
                <!-- Test Scenarios -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Test Scenarios</h3>
                    <select v-model="selectedScenario" 
                            class="w-full p-2 text-xs border dark:border-gray-700 rounded dark:bg-gray-700 dark:text-white mb-4">
                        <option v-for="scenario in testScenarios" 
                                :key="scenario.name" 
                                :value="scenario">
                            {{ scenario.name }}
                        </option>
                    </select>
                </div>

                <!-- Test Controls -->
                <div>
                    <button 
                        @click="runTests"
                        :disabled="isLoading"
                        class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm disabled:opacity-50"
                    >
                        {{ isLoading ? 'Running Tests...' : 'Run Tests' }}
                    </button>
                </div>
            </div>

            <!-- Column 2: Request Data -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Test Request</h3>
                <div class="space-y-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Headers:</div>
                    <pre class="text-[10px] leading-tight text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 p-2 rounded">{{ JSON.stringify(selectedScenario.request.headers, null, 2) }}</pre>
                    
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-3">Body:</div>
                    <pre class="text-[10px] leading-tight text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 p-2 rounded">{{ JSON.stringify(selectedScenario.request.body, null, 2) }}</pre>
                </div>
            </div>

            <!-- Column 3: Test Response -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Test Response</h3>
                <div v-if="testResults.length > 0" class="space-y-2">
                    <div v-for="result in testResults" :key="result.middleware" class="border-b dark:border-gray-700 pb-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-700 dark:text-gray-300">{{ result.middleware }}</span>
                            <Badges 
                                :status="result.isValid ? 'success' : 'error'"
                                size="xxs"
                                variant="pill"
                                class="px-2.5 py-0.5"
                            />
                        </div>
                    </div>
                </div>
                <div v-else-if="isLoading" class="text-[10px] text-gray-500 dark:text-gray-400 italic">
                    Running tests...
                </div>
                <div v-else class="text-[10px] text-gray-500 dark:text-gray-400 italic">
                    No test results yet
                </div>
            </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mt-4 bg-red-50 dark:bg-red-900/20 p-3 rounded-md">
            <p class="text-xs text-red-600 dark:text-red-400">{{ error }}</p>
        </div>
    </div>
</template> 