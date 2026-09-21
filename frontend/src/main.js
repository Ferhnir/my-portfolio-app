import { createApp } from 'vue'
import App from './App.vue'
import router from './router' // 💡 Ensure this import line is correct
import './assets/styles/main.css'

const app = createApp(App)

app.use(router) // 💡 CRITICAL: This activates the global router context injectors

app.mount('#app')