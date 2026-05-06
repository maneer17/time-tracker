import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import en from './locales/en.json'
import ar from './locales/ar.json'
import vue3GoogleLogin from 'vue3-google-login'

const app = createApp(App)

const i18n = createI18n({
    legacy: false,
    locale: 'ar',
    fallbackLocale: 'en',
    messages: { en, ar }
})

app.use(createPinia())
app.use(router)
app.use(Toast)
app.use(i18n)
app.use(vue3GoogleLogin, {
  clientId: '790034353136-fdm5vgcqgmjsh6lcp4igrc4khenihdes.apps.googleusercontent.com'
})
app.mount('#app')