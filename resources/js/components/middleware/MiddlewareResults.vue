// resources/js/components/middleware/MiddlewareResults.vue
<script setup>
import { XCircleIcon, CheckCircleIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    middlewareResults: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div class="mt-4 grid grid-cols-3 gap-4">
        <div v-for="layer in middlewareResults" 
             :key="layer.middleware" 
             class="rounded-lg border p-4">
            <!-- Layer Header -->
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-medium">{{ layer.middleware }}</h4>
                <span :class="[
                    'px-2 py-1 text-xxs rounded-full',
                    layer.result.isValid 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                ]">
                    {{ layer.result.isValid ? 'Valid' : 'Invalid' }}
                </span>
            </div>

            <!-- Dynamic Checks -->
            <template v-if="layer.result.checks">
                <div v-for="(value, key) in layer.result.checks" 
                     :key="key"
                     class="flex items-center justify-between text-xs border-t pt-2">
                    <span class="text-gray-600 dark:text-gray-300">{{ key }}</span>
                    <div class="flex items-center space-x-2">
                        <span :class="value ? 'text-green-600' : 'text-red-600'">
                            {{ value ? ` valid` : ` invalid` }}
                        </span>
                        <XCircleIcon v-if="!value" class="h-4 w-4 text-red-500" />
                        <CheckCircleIcon v-else class="h-4 w-4 text-green-500" />
                    </div>
                </div>
            </template>

            <!-- Special Status Checks -->
            <template v-if="layer.result.session">
                <div class="flex items-center justify-between text-xs border-t pt-2">
                    <span class="text-gray-600 dark:text-gray-300">Session Status</span>
                    <div class="flex items-center space-x-2">
                        <span :class="layer.result.session === 'active' ? 'text-green-600' : 'text-red-600'">
                            {{ layer.result.session === 'active' ? 'Session active' : 'Session expired' }}
                        </span>
                        <XCircleIcon v-if="layer.result.session !== 'active'" class="h-4 w-4 text-red-500" />
                        <CheckCircleIcon v-else class="h-4 w-4 text-green-500" />
            </div>
                </div>
            </template>

            <!-- Token Status -->
            <template v-if="'token' in layer.result">
                <div class="flex items-center justify-between text-xs border-t pt-2">
                    <span class="text-gray-600 dark:text-gray-300">Token Status</span>
                    <div class="flex items-center space-x-2">
                        <span :class="layer.result.token ? 'text-green-600' : 'text-red-600'">
                            {{ layer.result.token ? 'Token valid' : 'Token missing' }}
                        </span>
                        <XCircleIcon v-if="!layer.result.token" class="h-4 w-4 text-red-500" />
                        <CheckCircleIcon v-else class="h-4 w-4 text-green-500" />
                </div>
                </div>
            </template>
        </div>
    </div>
</template>