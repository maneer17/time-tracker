import { useToast } from 'vue-toastification';
import { i18n } from '@/plugins/i18n';
const toast = useToast();
import axios from 'axios';
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Accept-Language': i18n.global.locale.value
  }
});

// Registered from main.js (after Pinia and the router exist) so this module
// doesn't have to import the auth store and create a circular dependency.
let onUnauthorized = null
export const setUnauthorizedHandler = (handler) => {
    onUnauthorized = handler
}

// Endpoints where a 401 means "wrong credentials", not "your session expired".
const AUTH_ENDPOINTS = ['/api/login', '/api/register', '/api/auth/google', '/api/passwords/']
const isAuthEndpoint = (url = '') => {
    const path = '/' + String(url).replace(/^\/+/, '')
    return AUTH_ENDPOINTS.some((endpoint) => path.startsWith(endpoint))
}

let handlingExpiredSession = false
const isExpiredSession = (error) =>
    error.response?.status === 401 &&
    !isAuthEndpoint(error.config?.url) &&
    (!!localStorage.getItem('token') || handlingExpiredSession)

const handleExpiredSession = () => {
    if (handlingExpiredSession) return // several in-flight requests can 401 together
    handlingExpiredSession = true
    toast.error(i18n.global.t('message.session_expired'))
    onUnauthorized?.()
    setTimeout(() => { handlingExpiredSession = false }, 1000)
}

apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    config.headers['Accept-Language'] = i18n.global.locale.value
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (isExpiredSession(error)) {
            handleExpiredSession()
            return Promise.reject(new Error(i18n.global.t('message.session_expired')))
        }

        const t = (key) => i18n.global.t(key)
        let message = t('errors.generic');

        if (error.response) {
            const { status, data } = error.response;

            // if blob response, parse it as JSON first
            if (data instanceof Blob && data.type === 'application/json') {
                const text = await data.text()
                const parsed = JSON.parse(text)
                message = parsed.message ?? t('errors.generic')
            } else if (status === 404) {
                message = t('errors.not_found') // never show "No query results for model [...]"
            } else if (status >= 500) {
                message = t('errors.server')
            } else if (status === 403 && (!data?.message || data.message === 'This action is unauthorized.')) {
                message = t('errors.forbidden')
            } else if (data?.message) {
                message = data.message
            } else {
                message = t('errors.generic')
            }
        } else {
            message = t('errors.network')
        }

        toast.error(message)
        return Promise.reject(new Error(message))
    }
);



export default apiClient;
