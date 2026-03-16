import axios from "axios";

// Axios instance
const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  headers: { "X-Requested-With": "XMLHttpRequest" },
  withCredentials: true, // important for Sanctum if using cookies (optional for token auth)
});

// Dynamically set token from sessionStorage
export function setToken(token) {
  api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Restore token on page load
const token = sessionStorage.getItem("authToken");
if (token) setToken(token);

export default api;