// resources/js/utils/api.js
import axios from 'axios';

export async function apiRequest(method, url, data = null, config = {}) {
    // Ensure CSRF cookie is set
    await axios.get('/sanctum/csrf-cookie');
    // Make the actual request
    return axios({ method, url, data, ...config });
}