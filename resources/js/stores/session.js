import { defineStore } from 'pinia';
import axios from 'axios';

export const useSessionStore = defineStore('session', {
  state: () => ({
    sessionLifetime: 2 * 60 * 60, // 2 hours in seconds
    checkInterval: 30000, // Check every 30 seconds
    remainingTime: null,
    expirationTime: null,
    isActive: true,
    checkTimer: null,
    countdownTimer: null
  }),
  
  getters: {
    progressPercentage: (state) => {
      if (state.remainingTime === null) return 100;
      return (state.remainingTime / state.sessionLifetime) * 100;
    },
    
    progressBarColor: (state) => {
      const percentage = state.progressPercentage;
      if (percentage > 60) return 'bg-green-500 dark:bg-green-600';
      if (percentage > 30) return 'bg-yellow-500 dark:bg-yellow-500';
      return 'bg-red-500 dark:bg-red-600';
    },
    
    progressTextColor: (state) => {
      const percentage = state.progressPercentage;
      if (percentage > 60) return 'text-green-500 dark:text-green-600';
      if (percentage > 30) return 'text-yellow-500 dark:text-yellow-500';
      return 'text-red-500 dark:text-red-600';
    },
    
    tooltip: (state) => {
      if (state.remainingTime === null) return "Session: Unknown";
      const minutes = Math.floor(state.remainingTime / 60);
      const seconds = state.remainingTime % 60;
      return `Auto log off: ${minutes}m ${seconds}s`;
    }
  },
  
  actions: {
    // Central logout function
    async logout() {
      try {
        console.log('LOGOUT: Starting');
        
        // Check initial state
        const initialToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('LOGOUT: Initial CSRF token:', initialToken ? 'exists' : 'missing');
        
        localStorage.clear();
        sessionStorage.clear();
        this.cleanup();
        console.log('LOGOUT: Storage cleared');
        
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Refresh CSRF token
        console.log('LOGOUT: Refreshing CSRF token');
        await axios.get('/sanctum/csrf-cookie');
        
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Check new token
        const newToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('LOGOUT: New CSRF token:', newToken ? 'exists' : 'missing');
        
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Try logout
        console.log('LOGOUT: Attempting logout POST');
        await axios.post('/logout');
        console.log('LOGOUT: Success');
        
        window.location.href = '/login';
      } catch (error) {
        console.log('LOGOUT: Failed with status:', error.response?.status);
        console.log('LOGOUT: Error message:', error.response?.data?.message);
        window.location.href = '/login';
      }
    },

    // Force logout without server call
    forceLogout() {
      console.log('Session store: Force logout initiated');
      this.cleanup();
      localStorage.clear();
      sessionStorage.clear();
      window.location.href = '/login';
    },

    updateSessionLifetime(lifetime) {
      this.sessionLifetime = lifetime;
      this.expirationTime = Date.now() + (lifetime * 1000);
      this.remainingTime = lifetime;
    },
    
    updateCheckInterval(interval) {
      this.checkInterval = interval;
      
      // Clear existing timer and set new one
      if (this.checkTimer) {
        clearInterval(this.checkTimer);
      }
      this.checkTimer = setInterval(() => {
        this.checkSessionExpiration();
      }, interval);
    },
    
    checkSessionExpiration() {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      if (!token) {
        console.log('Session store: No CSRF token found - logging out');
        this.forceLogout();
        return;
      }
      
      try {
        const currentTime = Date.now();
        const remainingSeconds = Math.max(0, Math.floor((this.expirationTime - currentTime) / 1000));
        this.remainingTime = remainingSeconds;
        
        console.log(`Session store: ${remainingSeconds}s remaining`);
        
        if (currentTime >= this.expirationTime || (this.expirationTime - currentTime) < 30000) {
          console.log('Session store: Session expired - forcing logout...');
          this.forceLogout();
        }
      } catch (error) {
        console.error('Session store: Error checking session expiration:', error);
        this.forceLogout();
      }
    },
    
    startSessionTimer() {
      // Clear existing timers
      if (this.checkTimer) {
        clearInterval(this.checkTimer);
      }
      if (this.countdownTimer) {
        clearInterval(this.countdownTimer);
      }
      
      // Update countdown every second
      this.countdownTimer = setInterval(() => {
        if (this.remainingTime > 0) {
          this.remainingTime--;
        } else {
          this.forceLogout();
        }
      }, 1000);
      
      // Check session validity more frequently
      this.checkTimer = setInterval(() => {
        this.checkSessionExpiration();
      }, this.checkInterval);
    },
    
    initializeSession() {
      console.log('Session store: Initializing session...');
      
      // Refresh CSRF token first
      this.refreshCsrfToken().then(() => {
        // Get session lifetime from server or use default
        const serverSessionLifetime = document.querySelector('meta[name="session-lifetime"]')?.getAttribute('content');
        if (serverSessionLifetime) {
          this.updateSessionLifetime(parseInt(serverSessionLifetime));
        } else {
          // Default to 2 hours
          this.updateSessionLifetime(2 * 60 * 60);
        }

        // Get check interval from server or use default
        const serverCheckInterval = document.querySelector('meta[name="session-check-interval"]')?.getAttribute('content');
        if (serverCheckInterval) {
          this.updateCheckInterval(parseInt(serverCheckInterval));
        } else {
          // Default to 30 seconds for more frequent checks
          this.updateCheckInterval(30000);
        }

        // Check session immediately
        this.checkSessionExpiration();
        
        // Start timers
        this.startSessionTimer();
        
        console.log('Session store: Session initialized with lifetime:', this.sessionLifetime, 'check interval:', this.checkInterval);
      });
    },
    
    cleanup() {
      if (this.checkTimer) {
        clearInterval(this.checkTimer);
      }
      if (this.countdownTimer) {
        clearInterval(this.countdownTimer);
      }
    },
    
    refreshCsrfToken() {
      return axios.get('/sanctum/csrf-cookie')
        .then(() => {
          console.log('Session store: CSRF token refreshed');
        })
        .catch(error => {
          console.error('Session store: CSRF refresh failed:', error);
        });
    }
  }
}); 