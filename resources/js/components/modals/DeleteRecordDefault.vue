<script setup>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';
import MiddlewareResults from '@/components/middleware/MiddlewareResults.vue';

const props = defineProps({
    show: Boolean,
    record: Object,
    middlewareResults: {
        type: Array,
        default: () => []
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

                            <!-- Use the middleware results component -->
                            <MiddlewareResults :middleware-results="middlewareResults" />

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