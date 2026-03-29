import axios from 'axios';

const api = axios.create({
    baseURL: '/api', // Matches your api.php prefix
    withCredentials: true,
    withXSRFToken: true,
});

// Response interceptor to handle session timeouts
api.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 419) {
            // If session expired, try to refresh CSRF and retry once
            await axios.get('/sanctum/csrf-cookie');
            return api(error.config);
        }
        return Promise.reject(error);
    }
);

export default api;