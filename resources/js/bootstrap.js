import axios from 'axios';
window.axios = axios;

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Console suppressor to reduce network request noise
// This runs early and persists across page refreshes
(function() {
    // Store original console methods
    const originalLog = console.log;
    const originalInfo = console.info;
    const originalWarn = console.warn;
    const originalError = console.error;

    // Filter out network request logs
    const filterNetworkLogs = (args) => {
        const message = args.join(' ');
        
        // Filter out common network request patterns
        const networkPatterns = [
            /^GET\s+http/,
            /^POST\s+http/,
            /^PUT\s+http/,
            /^DELETE\s+http/,
            /^PATCH\s+http/,
            /\[HTTP\/\d\.\d\s+\d+\s+\w+\s+\d+ms\]/,
            /\[vite\]\s+connecting/,
            /\[vite\]\s+connected/,
            /ws:\/\/\[::1\]:5173/,
            /http:\/\/\[::1\]:5173/,
            /node_modules\/\.vite\/deps/,
            /resources\/js\/.*\.vue/,
            /resources\/js\/.*\.js/,
            /vendor\/tightenco\/ziggy/
        ];

        return !networkPatterns.some(pattern => pattern.test(message));
    };

    // Override console methods
    console.log = function(...args) {
        if (filterNetworkLogs(args)) {
            originalLog.apply(console, args);
        }
    };

    console.info = function(...args) {
        if (filterNetworkLogs(args)) {
            originalInfo.apply(console, args);
        }
    };

    // Keep warnings and errors as they are important
    console.warn = originalWarn;
    console.error = originalError;

    // Add a method to restore original console behavior if needed
    console.restoreConsole = function() {
        console.log = originalLog;
        console.info = originalInfo;
        console.warn = originalWarn;
        console.error = originalError;
        delete console.restoreConsole;
    };

    // Add a method to temporarily show all logs
    console.showAllLogs = function() {
        console.log = originalLog;
        console.info = originalInfo;
        console.warn = originalWarn;
        console.error = originalError;
    };

    // Add a method to re-enable filtering
    console.enableFiltering = function() {
        console.log = function(...args) {
            if (filterNetworkLogs(args)) {
                originalLog.apply(console, args);
            }
        };
        console.info = function(...args) {
            if (filterNetworkLogs(args)) {
                originalInfo.apply(console, args);
            }
        };
    };
})();

// Base configuration
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let csrfFetched = false;

// Create a separate axios instance for CSRF cookie fetch
const axiosNoIntercept = axios.create();

axios.interceptors.request.use(async config => {
    // Always fetch CSRF cookie for stateful requests
    if (config.method !== 'get' || config.url.includes('/api/')) {
        console.log('Fetching CSRF cookie for:', config.url);
        try {
            await axiosNoIntercept.get('/sanctum/csrf-cookie');
            const xsrfToken = getCookie('XSRF-TOKEN');
            if (xsrfToken) {
                console.log('CSRF token set:', decodeURIComponent(xsrfToken));
            } else {
                console.error('No XSRF-TOKEN cookie found!');
            }
        } catch (error) {
            console.error('Failed to fetch CSRF cookie:', error);
        }
    }
    return config;
}, error => {
    return Promise.reject(error);
});

axios.interceptors.response.use(
    response => {
        console.log('=== RESPONSE SUCCESS ===');
        console.log('Status:', response.status);
        return response;
    },
    error => {
        console.log('=== RESPONSE ERROR ===');
        console.log('Status:', error.response?.status);
        console.log('Data:', error.response?.data);
        console.log('Headers:', error.response?.headers);
        return Promise.reject(error);
    }
);