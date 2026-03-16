import { createApp } from "vue"
import App from "./App.vue"
import router from "./router"
import api from "@/api.js"   // only import api, no setAuthToken

const app = createApp(App)

// restore token after refresh
const token = sessionStorage.getItem("admin_token")
if (token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

app.use(router)
app.mount("#app")