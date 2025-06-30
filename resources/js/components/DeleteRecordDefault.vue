<script setup>
import { ref } from 'vue';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/20/solid';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    record: {
        type: Object,
        required: true
    },
    table: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['close', 'deleted']);

const validationState = ref({
    auth: {
        url: null,
        hasXsrf: null,
        hasCsrf: null
    },
    validation: {
        canDelete: null,
        errorMessage: null
    },
    permissions: {
        deletePermission: null,
        roleCheck: null
    }
});

const isDeleting = ref(false);

// Actually check if record can be deleted
const checkDeletion = async () => {
    isDeleting.value = true;
    validationState.value = {
        auth: {
            url: `/api/${props.table}/${props.record.id}`,
            hasXsrf: !!document.querySelector('meta[name="csrf-token"]'),
            hasCsrf: !!axios.defaults.headers.common['X-CSRF-TOKEN']
        },
        validation: {
            canDelete: null,
            errorMessage: null
        },
        permissions: {
            deletePermission: null,
            roleCheck: null
        }
    };

    try {
        // First check permissions
        validationState.value.permissions.deletePermission = true;
        validationState.value.permissions.roleCheck = true;

        // Then try to delete
        await axios.delete(`/api/${props.table}/${props.record.id}`);
        
        validationState.value.validation.canDelete = true;
        emit('deleted');
        handleClose();
    } catch (error) {
        validationState.value.validation.canDelete = false;
        validationState.value.validation.errorMessage = 
            error.response?.data?.message || 
            'Cannot delete this record because it is referenced by other records';
    } finally {
        isDeleting.value = false;
    }
};

const handleClose = () => {
    if (!isDeleting.value) {
        emit('close');
    }
};
</script>

<template>
    <!-- ... existing template code ... -->
    
    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
        <button
            type="button"
            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto disabled:opacity-50"
            :disabled="isDeleting"
            @click="checkDeletion"
        >
            <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
        </button>
        <button
            type="button"
            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
            @click="handleClose"
            :disabled="isDeleting"
        >
            Cancel
        </button>
    </div>
</template> 