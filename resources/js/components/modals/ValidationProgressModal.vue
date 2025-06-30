<script setup>
import { ref, computed } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { CheckCircleIcon, XCircleIcon, ClockIcon } from '@heroicons/vue/20/solid';
import { SessionHandler } from '@/utils/sessionHandler';
import { RequestValidator } from '@/utils/requestValidator';
import { PermissionCheck } from '@/utils/permissionCheck';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        default: 'Validation Required'
    },
    description: {
        type: String,
        default: 'Please wait while we validate your request...'
    },
    action: {
        type: String,
        default: 'delete'
    },
    resource: {
        type: String,
        default: 'record'
    },
    url: {
        type: String,
        default: ''
    },
    method: {
        type: String,
        default: 'DELETE'
    },
    data: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'validated', 'failed']);

const validationState = ref({
    isChecking: false,
    error: null,
    currentStep: 0,
    steps: [
        { name: 'Session Validation', status: 'pending', details: {} },
        { name: 'Request Validation', status: 'pending', details: {} },
        { name: 'Permission Check', status: 'pending', details: {} }
    ]
});

const progressPercentage = computed(() => {
    if (!validationState.value.isChecking) return 0;
    const completedSteps = validationState.value.steps.filter(step => step.status === 'completed').length;
    return (completedSteps / validationState.value.steps.length) * 100;
});

const canProceed = computed(() => {
    return validationState.value.steps.every(step => step.status === 'completed');
});

const startValidation = async () => {
    validationState.value.isChecking = true;
    validationState.value.error = null;
    validationState.value.currentStep = 0;
    
    // Reset all steps
    validationState.value.steps.forEach(step => {
        step.status = 'pending';
        step.details = {};
    });

    try {
        // Step 1: Session Validation
        validationState.value.currentStep = 0;
        validationState.value.steps[0].status = 'checking';
        
        const sessionValidation = SessionHandler.checkSession();
        validationState.value.steps[0].status = sessionValidation.isValid ? 'completed' : 'failed';
        validationState.value.steps[0].details = sessionValidation.details;
        
        if (!sessionValidation.isValid) {
            validationState.value.error = sessionValidation.error;
            emit('failed', { step: 'session', error: sessionValidation.error });
            return;
        }

        // Step 2: Request Validation
        validationState.value.currentStep = 1;
        validationState.value.steps[1].status = 'checking';
        
        const requestValidation = RequestValidator.validateRequest({
            url: props.url,
            method: props.method,
            data: props.data
        });
        validationState.value.steps[1].status = requestValidation.isValid ? 'completed' : 'failed';
        validationState.value.steps[1].details = requestValidation.details;
        
        if (!requestValidation.isValid) {
            validationState.value.error = requestValidation.error;
            emit('failed', { step: 'request', error: requestValidation.error });
            return;
        }

        // Step 3: Permission Check
        validationState.value.currentStep = 2;
        validationState.value.steps[2].status = 'checking';
        
        const permissionValidation = await PermissionCheck.validatePermissions({
            action: props.action,
            resource: props.resource
        });
        validationState.value.steps[2].status = permissionValidation.isValid ? 'completed' : 'failed';
        validationState.value.steps[2].details = permissionValidation.details;
        
        if (!permissionValidation.isValid) {
            validationState.value.error = permissionValidation.error;
            emit('failed', { step: 'permission', error: permissionValidation.error });
            return;
        }

        // All validations passed
        emit('validated', {
            session: sessionValidation,
            request: requestValidation,
            permission: permissionValidation
        });
        
    } catch (error) {
        validationState.value.error = error.message;
        validationState.value.steps[validationState.value.currentStep].status = 'failed';
        emit('failed', { step: 'unknown', error: error.message });
    } finally {
        validationState.value.isChecking = false;
    }
};

const handleClose = () => {
    if (!validationState.value.isChecking) {
        emit('close');
    }
};

const getStepIcon = (step) => {
    if (step.status === 'completed') return CheckCircleIcon;
    if (step.status === 'failed') return XCircleIcon;
    if (step.status === 'checking') return ClockIcon;
    return null;
};

const getStepIconClass = (step) => {
    if (step.status === 'completed') return 'text-green-500';
    if (step.status === 'failed') return 'text-red-500';
    if (step.status === 'checking') return 'text-blue-500 animate-spin';
    return 'text-gray-400';
};

// Auto-start validation when modal opens
const startValidationOnOpen = () => {
    if (props.show && !validationState.value.isChecking) {
        startValidation();
    }
};

// Watch for show prop changes
import { watch } from 'vue';
watch(() => props.show, (newVal) => {
    if (newVal) {
        startValidationOnOpen();
    }
});
</script>

<template>
    <TransitionRoot as="template" :show="show">
        <Dialog as="div" class="relative z-10" @close="handleClose">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <DialogTitle as="h3" class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ title }}
                                </DialogTitle>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Progress -->
                        <div v-if="validationState.isChecking" class="mt-6">
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>Validation Progress</span>
                                    <span>{{ Math.round(progressPercentage) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div 
                                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                        :style="{ width: progressPercentage + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Validation Steps -->
                            <div class="space-y-4">
                                <div v-for="(step, index) in validationState.steps" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <component 
                                                :is="getStepIcon(step)" 
                                                v-if="getStepIcon(step)"
                                                class="h-5 w-5"
                                                :class="getStepIconClass(step)"
                                            />
                                            <div class="h-5 w-5 rounded-full border-2 border-gray-300 dark:border-gray-600" v-else></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ step.name }}
                                            </span>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded-full" 
                                              :class="{
                                                  'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300': step.status === 'completed',
                                                  'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300': step.status === 'failed',
                                                  'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300': step.status === 'checking',
                                                  'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300': step.status === 'pending'
                                              }">
                                            {{ step.status }}
                                        </span>
                                    </div>
                                    
                                    <!-- Step Details -->
                                    <div v-if="Object.keys(step.details).length > 0" class="mt-3 ml-8">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <div v-for="(detail, key) in step.details" :key="key" 
                                                 class="flex items-center justify-between text-xs p-2 rounded bg-gray-50 dark:bg-gray-700">
                                                <span class="text-gray-600 dark:text-gray-400">{{ detail.message }}</span>
                                                <span class="font-mono" 
                                                      :class="detail.isValid ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                                    {{ detail.isValid ? 'true' : 'false' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="validationState.error" class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <XCircleIcon class="h-5 w-5 text-red-400" />
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-red-700 dark:text-red-300">
                                        {{ validationState.error }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button
                                type="button"
                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto disabled:opacity-50"
                                :disabled="validationState.isChecking"
                                @click="startValidation"
                            >
                                <svg v-if="validationState.isChecking" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ validationState.isChecking ? 'Validating...' : 'Retry Validation' }}
                            </button>
                            <button
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
                                @click="handleClose"
                                :disabled="validationState.isChecking"
                            >
                                Close
                            </button>
                        </div>
                    </DialogPanel>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template> 