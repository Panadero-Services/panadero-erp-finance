import { BaseMiddleware } from '@/components/middleware/BaseMiddleware';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

export class AuthenticationMiddleware extends BaseMiddleware {
    constructor() {
        super();
        this.name = 'Authentication';
        this.checkInterval = 30000; // 30 seconds
        this.timer = null;
        this.isActive = true;
    }

    getChecks(request) {
        const page = usePage();
        const auth = page.props.auth;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const xsrfToken = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        return {
            userValid: !!auth?.user,
            csrfValid: !!csrf,
            xsrfValid: !!xsrfToken,
            sessionValid: !!auth?.user,
        };
    }

    getAdditionalData(request) {
        const page = usePage();
        const auth = page.props.auth;
        
        return {
            user: auth?.user || null,
            session: auth?.user ? 'active' : 'expired',
            token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
    }

    async handle(request) {
        const checks = this.getChecks(request);
        const additionalData = this.getAdditionalData(request);

        return {
            isValid: Object.values(checks).every(Boolean),
            checks,
            ...additionalData
        };
    }

    // Start periodic session check (LOCAL ONLY - NO SERVER CALLS)
    startSessionCheck() {
        // Don't start timer - session store handles this
        console.log('Session checks handled by session store');
    }

    // REMOVED: verifySessionWithServer method that was causing refresh

    setupAxiosInterceptors() {
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response?.status === 401 || error.response?.status === 419) {
                    this.handleLogout('Authentication error');
                }
                return Promise.reject(error);
            }
        );
    }

    handleLogout(reason = 'Session expired') {
        console.log(`Logging out: ${reason}`);
        
        // Clear timers
        this.cleanup();
        
        // Clear storage
        localStorage.clear();
        sessionStorage.clear();
        
        // Store intended destination
        const currentPath = window.location.pathname;
        if (currentPath !== '/login') {
            sessionStorage.setItem('intendedDestination', currentPath);
        }
        
        // Use Inertia router instead of window.location
        router.visit('/login', { 
            method: 'get',
            preserveState: false,
            preserveScroll: false,
            replace: true
        });
    }

    cleanup() {
        this.isActive = false;
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }
}

export default AuthenticationMiddleware; 