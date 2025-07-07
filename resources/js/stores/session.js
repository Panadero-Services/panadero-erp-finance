import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

export const useSessionStore = defineStore('session', () => {
    // State
    const sessionLifetime = ref(2 * 60 * 60); // Default 2 hours in seconds
    const checkInterval = ref(30000);          // Check every 30 seconds
    const remainingTime = ref(null);
    const expirationTime = ref(null);
    const isActive = ref(true);
    const checkTimer = ref(null);
    const countdownTimer = ref(null);

    // Computed properties
    const progressPercentage = computed(() => {
        if (remainingTime.value === null) return 100;
        return (remainingTime.value / sessionLifetime.value) * 100;
    });

    const progressBarColor = computed(() => {
        const percentage = progressPercentage.value;
        if (percentage > 60) return 'bg-green-500 dark:bg-green-600';
        if (percentage > 30) return 'bg-yellow-500 dark:bg-yellow-500';
        return 'bg-red-500 dark:bg-red-600';
    });

    const progressTextColor = computed(() => {
        const percentage = progressPercentage.value;
        if (percentage > 60) return 'text-green-500 dark:text-green-600';
        if (percentage > 30) return 'text-yellow-500 dark:text-yellow-500';
        return 'text-red-500 dark:text-red-600';
    });

    const tooltip = computed(() => {
        if (remainingTime.value === null) return "Session: Unknown";
        const minutes = Math.floor(remainingTime.value / 60);
        const seconds = remainingTime.value % 60;
        return `Auto log off: ${minutes}m ${seconds}s`;
    });

    // Actions
    const cleanup = () => {
        if (checkTimer.value) {
            clearInterval(checkTimer.value);
            checkTimer.value = null;
        }
        if (countdownTimer.value) {
            clearInterval(countdownTimer.value);
            countdownTimer.value = null;
        }
    };

    const refreshCsrfToken = async () => {
        try {
            await axios.get('/sanctum/csrf-cookie');
            console.log('Session store: CSRF token refreshed');
        } catch (error) {
            console.error('Session store: CSRF refresh failed:', error);
        }
    };

    const forceLogout = () => {
        console.log('Session store: Force logout initiated');
        cleanup();
        localStorage.clear();
        sessionStorage.clear();
        
        router.visit('/login', { 
            method: 'get',
            preserveState: false,
            preserveScroll: false,
            replace: true
        });
    };

    const checkSessionExpiration = () => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!token) {
            console.log('Session store: No CSRF token found - logging out');
            forceLogout();
            return;
        }
        
        try {
            const currentTime = Date.now();
            const remainingSeconds = Math.max(0, Math.floor((expirationTime.value - currentTime) / 1000));
            remainingTime.value = remainingSeconds;
            
            console.log(`Session store: ${remainingSeconds}s remaining`);
            
            if (currentTime >= expirationTime.value) {
                console.log('Session store: Session expired - forcing logout...');
                forceLogout();
            }
        } catch (error) {
            console.error('Session store: Error checking session expiration:', error);
            forceLogout();
        }
    };

    const updateSessionLifetime = () => {
        const page = usePage();
        const newLifetime = page.props.auth?.user?.session_lifetime;
        
        if (newLifetime) {
            console.log('Session store: Updating session lifetime to', newLifetime);
            sessionLifetime.value = newLifetime;
            expirationTime.value = Date.now() + (newLifetime * 1000);
            remainingTime.value = newLifetime;
        }
    };

    const startSessionTimer = () => {
        cleanup(); // Clear existing timers
        
        if (!isActive.value) {
            console.log('Session store: Session checks are disabled');
            return;
        }

        // Use a single timer for both countdown and session check
        countdownTimer.value = setInterval(() => {
            if (remainingTime.value > 0) {
                remainingTime.value--;
                
                // Only check session every 30 seconds
                if (remainingTime.value % 30 === 0) {
                    checkSessionExpiration();
                }
            } else {
                forceLogout();
            }
        }, 1000);
    };

    const initializeSession = () => {
        console.log('Session store: Initializing session...');
        
        // Update session lifetime first
        updateSessionLifetime();
        
        // Then refresh CSRF token and start timer
        refreshCsrfToken().then(() => {
            startSessionTimer();
            console.log('Session store: Session initialized with lifetime:', sessionLifetime.value, 'check interval:', checkInterval.value);
        });
    };

    const logout = async () => {
        try {
            console.log('LOGOUT: Starting');
            
            try {
                await axios.post('/logout');
                console.log('LOGOUT: Server logout successful');
            } catch (error) {
                console.log('LOGOUT: Server logout failed:', error.message);
            }
            
            cleanup();
            localStorage.clear();
            sessionStorage.clear();
            console.log('LOGOUT: Client cleanup complete');
            
            router.visit('/login', { 
                method: 'get',
                preserveState: false,
                preserveScroll: false,
                replace: true
            });
        } catch (error) {
            console.error('LOGOUT: Fatal error:', error);
            router.visit('/login', { 
                method: 'get',
                preserveState: false,
                preserveScroll: false,
                replace: true
            });
        }
    };

    const updateFromLayout = () => {
        if (remainingTime.value !== null) {
            checkSessionExpiration();
        }
    };

    const stopSessionChecks = () => {
        console.log('Session store: Stopping session checks');
        cleanup();
        isActive.value = false;
    };

    return {
        // State
        sessionLifetime,
        checkInterval,
        remainingTime,
        expirationTime,
        isActive,
        
        // Computed
        progressPercentage,
        progressBarColor,
        progressTextColor,
        tooltip,
        
        // Actions
        logout,
        forceLogout,
        updateSessionLifetime,
        startSessionTimer,
        initializeSession,
        cleanup,
        refreshCsrfToken,
        updateFromLayout,
        stopSessionChecks,
        updateSessionLifetime
    };
}); 