import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import vue3GoogleLogin from 'vue3-google-login'
import { i18n } from './plugins/i18n'
const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(Toast)
app.use(i18n)
app.use(vue3GoogleLogin, {
  clientId: '790034353136-fdm5vgcqgmjsh6lcp4igrc4khenihdes.apps.googleusercontent.com'
})
app.mount('#app')