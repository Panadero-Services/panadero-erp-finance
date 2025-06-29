export const SessionHandler = {
    checkSession() {
        // Add debugging
        console.log('Checking session cookies:', {
            session: document.cookie.includes('i3v1_self_saas_session='),
            xsrf: document.cookie.includes('XSRF-TOKEN=')
        });

        // Get all required session elements
        const sessionCookie = document.cookie.includes('i3v1_self_saas_session=');
        const xsrfToken = document.cookie.includes('XSRF-TOKEN=');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Debug tokens
        console.log('CSRF Token from meta:', csrfToken);
        
        if (!sessionCookie) {
            return {
                isValid: false,
                error: 'Session cookie is missing',
                details: { sessionCookie, xsrfToken, csrfToken }
            };
        }

        if (!xsrfToken) {
            return {
                isValid: false,
                error: 'XSRF token is missing',
                details: { sessionCookie, xsrfToken, csrfToken }
            };
        }

        if (!csrfToken) {
            return {
                isValid: false,
                error: 'CSRF token is missing',
                details: { sessionCookie, xsrfToken, csrfToken }
            };
        }

        return {
            isValid: true,
            csrfToken: csrfToken,
            details: { sessionCookie, xsrfToken, csrfToken }
        };
    },

    // Helper method to add session headers to any request
    getSessionHeaders() {
        const session = this.checkSession();
        if (!session.isValid) {
            console.error('Session check failed:', session);
            throw new Error(session.error);
        }

        return {
            'X-CSRF-TOKEN': session.csrfToken,
            'X-XSRF-TOKEN': document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1],
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