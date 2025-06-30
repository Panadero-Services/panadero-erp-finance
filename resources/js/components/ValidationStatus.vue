<script setup>
import { ref } from 'vue';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/20/solid';

const validationStatus = ref({
    session: {
        title: 'Session Validation',
        checks: {
            csrf: { name: 'CSRF Token', valid: false },
            active: { name: 'Session Active', valid: false }
        }
    },
    request: {
        title: 'Request Validation',
        checks: {
            xsrf: { name: 'XSRF Token', valid: false },
            headers: { name: 'Headers', valid: false }
        }
    },
    permission: {
        title: 'Permission Checks',
        checks: {
            auth: { name: 'Authentication', valid: false },
            access: { name: 'Authorization', valid: false }
        }
    }
});

const getStatusClass = (value) => {
    if (value === null) return 'text-gray-500';
    return value ? 'text-green-500' : 'text-red-500';
};
</script>

<template>
    <div class="grid grid-cols-3 gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Session Validation -->
        <div class="border-r dark:border-gray-700 pr-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                {{ validationStatus.session.title }}
            </h3>
            <div class="space-y-2">
                <div v-for="(check, key) in validationStatus.session.checks" 
                     :key="key"
                     class="flex items-center text-xs">
                    <CheckCircleIcon v-if="check.valid" 
                        class="h-4 w-4 text-green-500 mr-1" />
                    <XCircleIcon v-else 
                        class="h-4 w-4 text-red-500 mr-1" />
                    {{ check.name }}
                </div>
            </div>
        </div>

        <!-- Request Validation -->
        <div class="border-r dark:border-gray-700 px-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                {{ validationStatus.request.title }}
            </h3>
            <div class="space-y-2">
                <div v-for="(check, key) in validationStatus.request.checks" 
                     :key="key"
                     class="flex items-center text-xs">
                    <CheckCircleIcon v-if="check.valid" 
                        class="h-4 w-4 text-green-500 mr-1" />
                    <XCircleIcon v-else 
                        class="h-4 w-4 text-red-500 mr-1" />
                    {{ check.name }}
                </div>
            </div>
        </div>

        <!-- Permission Checks -->
        <div class="pl-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                {{ validationStatus.permission.title }}
            </h3>
            <div class="space-y-2">
                <div v-for="(check, key) in validationStatus.permission.checks" 
                     :key="key"
                     class="flex items-center text-xs">
                    <CheckCircleIcon v-if="check.valid" 
                        class="h-4 w-4 text-green-500 mr-1" />
                    <XCircleIcon v-else 
                        class="h-4 w-4 text-red-500 mr-1" />
                    {{ check.name }}
                </div>
            </div>
        </div>
    </div>
</template> 