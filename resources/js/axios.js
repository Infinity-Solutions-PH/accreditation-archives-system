import axios from 'axios';

// Grab CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

const api = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token ? token.content : '',
    },
    withCredentials: true,
});

export default api;