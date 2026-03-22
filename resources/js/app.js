import { createApp } from "vue"
import App from "./App.vue"
import router from "./router"
import api from "@/api.js"   // Axios instance with withCredentials

api.get('/sanctum/csrf-cookie').then(() => {
  console.log('CSRF cookie ready');
});

const app = createApp(App)

app.use(router)
app.mount("#app")