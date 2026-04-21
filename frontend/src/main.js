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
app.mount('#app')