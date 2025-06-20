import axios from 'axios';
window.axios = axios;

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Base configuration
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Request interceptor for XSRF token
axios.interceptors.request.use(config => {
    // Get the latest XSRF token from cookie before each request
    const xsrfToken = getCookie('XSRF-TOKEN');
    if (xsrfToken) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Response interceptor for handling 401 errors
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            console.error('Authentication error:', error);
            
            // Log all cookies individually
            document.cookie.split(';').forEach(cookie => {
                console.log('Cookie:', cookie.trim());
            });
            
            // Check specifically for session cookie
            const sessionCookie = getCookie('i3v1_self_saas_session');
            console.log('Session cookie present:', !!sessionCookie);
            
            console.log('Headers sent:', error.config?.headers);
            console.log('Full error response:', error.response?.data);
            debugger;
        }
        return Promise.reject(error);
    }
);