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

// Request interceptor for XSRF token - USE ONLY COOKIE TOKEN
axios.interceptors.request.use(config => {
    // Get the latest XSRF token from cookie before each request
    const xsrfToken = getCookie('XSRF-TOKEN');
    if (xsrfToken) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
        
        // Update meta tag with fresh token
        const metaTag = document.head.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', decodeURIComponent(xsrfToken));
        }
    }
    return config;
}, error => {
    return Promise.reject(error);
});