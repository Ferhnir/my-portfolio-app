import { createApp } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import App from './App.vue'
import router from './router'
import './icons'
import './assets/styles/main.css'

const app = createApp(App)
const reloadKey = 'portfolio:view-reload'

app.config.errorHandler = (error, _instance, info) => {
    console.error(error)

    const fatal = info === 'setup function' || info === 'render function' || info === 'component update'

    if (!fatal || sessionStorage.getItem(reloadKey) === location.pathname) {
        return
    }

    sessionStorage.setItem(reloadKey, location.pathname)
    location.assign(location.href)
}

app.component('FontAwesomeIcon', FontAwesomeIcon)
app.use(router)

app.mount('#app')
window.setTimeout(() => sessionStorage.removeItem(reloadKey), 1500)
