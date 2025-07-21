import axios from 'axios';
window.axios = axios;

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// COMPLETELY SILENCE ALL CONSOLE MESSAGES
console.log = () => {};
console.warn = () => {};
console.error = () => {};
console.info = () => {};

// Base configuration
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let csrfFetched = false;

// Create a separate axios instance for CSRF cookie fetch
const axiosNoIntercept = axios.create();

axios.interceptors.request.use(async config => {
    // Only fetch CSRF cookie for non-GET requests or API requests, and only once
    if ((config.method !== 'get' || config.url.includes('/api/')) && !csrfFetched) {
        try {
            await axiosNoIntercept.get('/sanctum/csrf-cookie');
            const xsrfToken = getCookie('XSRF-TOKEN');
            if (xsrfToken) {
                csrfFetched = true;
            }
        } catch (error) {
            // Silent fail
        }
    }
    return config;
}, error => {
    return Promise.reject(error);
});

axios.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        return Promise.reject(error);
    }
);