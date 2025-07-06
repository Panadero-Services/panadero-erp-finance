import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

export class AuthMiddleware {
    constructor() {
        this.checkInterval = 30000; // 30 seconds
        this.timer = null;
        this.isActive = true;
    }

    // Initialize middleware
    init() {
        this.startSessionCheck();
        this.setupAxiosInterceptors();
    }

    // Check authentication status
    checkAuth() {
        const page = usePage();
        const auth = page.props.auth;
        
        return {
            isAuthenticated: !!auth?.user,
            user: auth?.user,
            sessionValid: auth?.session_valid,
            csrfToken: auth?.csrf_token,
            sessionLifetime: auth?.user?.session_lifetime || 7200
        };
    }

    // Start periodic session check
    startSessionCheck() {
        if (this.timer) {
            clearInterval(this.timer);
        }

        this.timer = setInterval(() => {
            if (!this.isActive) return;

            const authStatus = this.checkAuth();
            
            if (!authStatus.isAuthenticated) {
                this.handleLogout('Session expired');
                return;
            }

            // Optional: Check with server
            this.verifySessionWithServer();
        }, this.checkInterval);
    }

    // Verify session with server
    async verifySessionWithServer() {
        try {
            await axios.get('/api/auth/verify-session');
        } catch (error) {
            if (error.response?.status === 401 || error.response?.status === 419) {
                this.handleLogout('Session invalidated by server');
            }
        }
    }

    // Setup axios interceptors for auth errors
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

    // Handle logout
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
        
        // Redirect to login
        window.location.href = '/login';
    }

    // Cleanup
    cleanup() {
        this.isActive = false;
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }
}

// Export singleton instance
export const authMiddleware = new AuthMiddleware(); 