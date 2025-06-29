import { router } from '@inertiajs/vue3'

export const RequestValidator = {
    validateRequest() {
        // Check CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            return {
                isValid: false,
                error: 'CSRF token is missing'
            };
        }

        // Check XSRF token
        const xsrfToken = document.cookie.includes('XSRF-TOKEN=');
        if (!xsrfToken) {
            return {
                isValid: false,
                error: 'XSRF token is missing'
            };
        }

        return {
            isValid: true,
            token
        };
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
    const validation = RequestValidator.validateRequest();
    
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