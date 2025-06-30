import { router } from '@inertiajs/vue3'

export const SessionHandler = {
    checkSession() {
        // Session Validation Section
        const validation = {
            isValid: false,
            details: {
                csrfCheck: {
                    isValid: false,
                    message: 'CSRF token check'
                },
                sessionCheck: {
                    isValid: false,
                    message: 'Session active check'
                }
            },
            error: null
        };

        try {
            // CSRF Token Validation
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            validation.details.csrfCheck.isValid = !!csrfToken;
            
            // Session Active Check
            const hasSession = document.cookie.includes('laravel_session');
            validation.details.sessionCheck.isValid = hasSession;

            // Overall validation status
            validation.isValid = validation.details.csrfCheck.isValid && 
                               validation.details.sessionCheck.isValid;

            if (!validation.isValid) {
                validation.error = 'Session validation failed';
            }
        } catch (error) {
            validation.error = 'Session check error: ' + error.message;
        }

        return validation;
    },

    getSessionHeaders() {
        const session = this.checkSession();
        if (!session.isValid) {
            throw new Error(session.error);
        }

        return {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };
    }
}; 