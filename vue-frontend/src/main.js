import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

const i18n = createI18n({
legacy: false,
  locale: 'ar',
  fallbackLocale: 'en',
  messages: {
    en: {
      mesasge: {
        hello: 'hello world',
      },
    },
    ar: {
      message: {
        hello: 'مرحبا يا عالم',
      },
    },
  },
});
app.use(i18n)
app.mount('#app')
