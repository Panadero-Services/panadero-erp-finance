import { router } from '@inertiajs/vue3'

export const RequestValidator = {
    validateRequest(requestData = {}) {
        // Validate request method
        const method = requestData.method || 'GET';
        const isValidMethod = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(method.toUpperCase());
        
        // Validate URL format
        const url = requestData.url || '';
        const isValidUrl = url.startsWith('/') || url.startsWith('http');
        
        // Validate data format for POST/PUT/PATCH
        const data = requestData.data;
        const isValidData = !data || (typeof data === 'object' && !Array.isArray(data));

        const validation = {
            isValid: isValidMethod && isValidUrl && isValidData,
            details: {
                methodCheck: {
                    isValid: isValidMethod,
                    message: isValidMethod ? `Method ${method} is valid` : `Invalid method: ${method}`
                },
                urlCheck: {
                    isValid: isValidUrl,
                    message: isValidUrl ? 'URL format is valid' : 'Invalid URL format'
                },
                dataCheck: {
                    isValid: isValidData,
                    message: isValidData ? 'Data format is valid' : 'Invalid data format'
                }
            },
            error: null
        };

        if (!validation.isValid) {
            validation.error = 'Request validation failed';
        }

        return validation;
    },

    handleInvalidRequest() {
        if (window.$toast) {
            window.$toast.warning('Request validation failed.');
        }
        return false;
    }
};

// Extend Inertia router to include validation
const originalDelete = router.delete;
router.delete = function(url, options = {}) {
    const validation = RequestValidator.validateRequest({ url, method: 'DELETE' });
    
    if (!validation.isValid) {
        if (window.$toast) {
            window.$toast.error('Delete failed - Please try again', {
                timeout: 5000  // Keep the message visible for 5 seconds
            });
        }
        return Promise.reject(new Error(validation.error));
    }

    // Add CSRF token to headers if not present
    options.headers = {
        ...options.headers,
        'X-CSRF-TOKEN': validation.token,
        'X-Requested-With': 'XMLHttpRequest'
    };

    return originalDelete.call(this, url, options);
}; 