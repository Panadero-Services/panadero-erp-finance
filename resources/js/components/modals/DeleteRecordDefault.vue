<script setup>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';
import { XCircleIcon, CheckCircleIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    show: Boolean,
    record: Object,
    authStatus: {
        type: Object,
        default: () => ({
            isValid: false,
            token: undefined,
            session: null,
            checks: {}
        })
    },
    middlewareResults: {
        type: Array,
        default: () => []  // Simple empty array as default
    },
    isDeleting: Boolean
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" class="relative z-10" @close="$emit('close')">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                            <DialogTitle as="h3" class="text-sm font-medium leading-6 text-gray-900">
                                Delete {{ record?.title }}
                            </DialogTitle>

                            <div class="mt-2">
                                <p class="text-xs text-gray-500">
                                    Are you sure you want to delete this record? This action cannot be undone.
                                </p>
                            </div>

                            <!-- Dynamic three columns layout -->
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
                                            <span class="text-gray-600">{{ key }}</span>
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
                                            <span class="text-gray-600">Session Status</span>
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
                                            <span class="text-gray-600">Token Status</span>
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

                            <div class="mt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    class="inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 disabled:opacity-50"
                                    :disabled="isDeleting || !middlewareResults.every(m => m.result.isValid)"
                                    @click="$emit('confirm')"
                                >
                                    {{ isDeleting ? 'Deleting...' : 'Delete' }}
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                    @click="$emit('close')"
                                >
                                    Cancel
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template> 