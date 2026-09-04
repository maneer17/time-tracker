import { useToast } from 'vue-toastification';
import ERROR_MESSAGES from '../config/customErrors';
import { i18n } from '@/plugins/i18n';
const toast = useToast();
import axios from 'axios';
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Accept-Language': i18n.global.locale.value,
    'X-Organization-Id': 23
  }
});

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
        let message = ERROR_MESSAGES.GENERIC_ERROR;

        if (error.response) {
            const { status, data } = error.response;

            // if blob response, parse it as JSON first
            if (data instanceof Blob && data.type === 'application/json') {
                const text = await data.text()
                const parsed = JSON.parse(text)
                message = parsed.message ?? ERROR_MESSAGES.GENERIC_ERROR
            } else if (data?.message) {
                message = data.message
            } else {
                message = ERROR_MESSAGES[status] ?? ERROR_MESSAGES.GENERIC_ERROR
            }
        } else {
            message = ERROR_MESSAGES.NETWORK_ERROR
        }

        toast.error(message)
        return Promise.reject(new Error(message))
    }
);



export default apiClient;
