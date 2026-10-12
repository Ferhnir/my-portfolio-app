import { createApp } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import App from './App.vue'
import router from './router'
import './icons'
import './assets/styles/main.css'

const app = createApp(App)

app.component('FontAwesomeIcon', FontAwesomeIcon)
app.use(router)

app.mount('#app')
