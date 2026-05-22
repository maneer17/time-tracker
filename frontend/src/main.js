import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import Toast from 'vue-toastification'
import PrimeVue from 'primevue/config';
import 'vue-toastification/dist/index.css'
import vue3GoogleLogin from 'vue3-google-login'
import { i18n } from './plugins/i18n'
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import apiClient from './services/api'
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel) => ({
        authorize: (socketId, callback) => {
            apiClient.post('/broadcasting/auth', {
                socket_id: socketId,
                channel_name: channel.name,
            })
            .then(res => callback(null, res.data))
            .catch(err => callback(err))
        }
    })
});
const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(Toast)
app.use(i18n)
app.use(vue3GoogleLogin, {
  clientId: import.meta.env.VITE_CLIENT_ID
})
app.use(PrimeVue);
app.mount('#app')