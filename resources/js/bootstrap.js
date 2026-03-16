// resources/js/bootstrap.js
import axios from 'axios';

// Make axios globally available
window.axios = axios;

// Default headers for Axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Send cookies (required for Sanctum)
window.axios.defaults.withCredentials = true;

// Attach token from localStorage if exists
const token = localStorage.getItem('auth_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Optional: helper to refresh token if needed
window.setAuthToken = (newToken) => {
    localStorage.setItem('auth_token', newToken);
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
};

console.log('Bootstrap.js loaded: Axios ready with CSRF and token.');