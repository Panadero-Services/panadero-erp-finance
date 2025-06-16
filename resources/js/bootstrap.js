import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Set up Sanctum token
const sanctumToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
if (sanctumToken) {
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(sanctumToken[1]);
}

// Enable credentials
window.axios.defaults.withCredentials = true;

// Add response interceptor for handling 401 errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Store the current URL for redirect after login
            sessionStorage.setItem('intended_url', window.location.href);
            // Redirect to login
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
