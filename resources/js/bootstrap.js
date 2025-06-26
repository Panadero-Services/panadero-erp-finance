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
            // Silent handling of auth errors
        }
        if (error.response?.status === 419) {
            // Refresh CSRF token and retry the request
            return axios.get('/sanctum/csrf-cookie')
                .then(() => {
                    // Get the new token
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    // Update axios default header
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                    // Retry the original request
                    const config = error.config;
                    config.headers['X-CSRF-TOKEN'] = token;
                    return axios(config);
                });
        }
        return Promise.reject(error);
    }
);