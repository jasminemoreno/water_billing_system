import axios from "axios";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  headers: { "X-Requested-With": "XMLHttpRequest" },
  withCredentials: true,
});

// Set token for customer authentication
export function setToken(token) {
  api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// On page load, set token if present
const token = sessionStorage.getItem("customerToken");
if (token) setToken(token);

export default api;