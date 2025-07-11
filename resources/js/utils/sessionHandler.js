import { router } from '@inertiajs/vue3'

export const SessionHandler = {
    checkSession() {
        // Check XSRF token in cookies
        const hasXsrf = document.cookie.includes('XSRF-TOKEN');
        const xsrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
        
        // Check CSRF token in meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const hasCsrf = !!csrfToken;

        // Detailed validation result
        const validation = {
            isValid: hasXsrf && hasCsrf,
            token: csrfToken,
            xsrfToken: xsrfToken,
            details: {
                xsrfCheck: {
                    isValid: hasXsrf,
                    message: hasXsrf ? 'XSRF token found' : 'XSRF token missing'
                },
                csrfCheck: {
                    isValid: hasCsrf,
                    message: hasCsrf ? 'CSRF token found' : 'CSRF token missing'
                }
            },
            error: null
        };

        if (!validation.isValid) {
            validation.error = 'Authentication required - missing tokens';
        }

        return validation;
    },

    // Helper method to add session headers to any request
    getSessionHeaders() {
        const session = this.checkSession();
        if (!session.isValid) {
            console.error('Session check failed:', session);
            throw new Error(session.error);
        }

        return {
            'X-CSRF-TOKEN': session.token,
            'X-XSRF-TOKEN': session.xsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };
    },

    // Generic method to make authenticated requests
    async makeRequest(url, method = 'GET', data = null) {
        const session = this.checkSession();
        if (!session.isValid) {
            if (window.$toast) {
                window.$toast.error(session.error);
            }
            throw new Error(session.error);
        }

        const options = {
            method: method,
            headers: this.getSessionHeaders(),
            credentials: 'include'
        };

        if (data && ['POST', 'PUT', 'PATCH'].includes(method.toUpperCase())) {
            options.body = JSON.stringify(data);
            options.headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            if (window.$toast) {
                window.$toast.error('Request failed: ' + error.message);
            }
            throw error;
        }
    },

    refreshSession() {
        // You can implement session refresh logic here
        return fetch('/api/refresh-session', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Session refresh failed:', error);
            return { isValid: false, error: 'Failed to refresh session' };
        });
    }
};

// Move the working delete handler here
const originalDelete = router.delete;
router.delete = function(url, options = {}) {
    const validation = SessionHandler.checkSession();
    
    if (!validation.isValid) {
        if (window.$toast) {
            window.$toast.error('Delete failed - Please try again', {
                timeout: 5000
            });
        }
        return Promise.reject(new Error(validation.error));
    }

    options.headers = {
        ...options.headers,
        'X-CSRF-TOKEN': validation.token,
        'X-Requested-With': 'XMLHttpRequest'
    };

    return originalDelete.call(this, url, options);
}; 