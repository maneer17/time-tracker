import { useToast } from 'vue-toastification';
import ERROR_MESSAGES from '../config/customErrors';
const toast = useToast();
import axios from 'axios';
const apiClient = axios.create({
  baseURL: 'http://localhost:8000',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        let message = ERROR_MESSAGES.GENERIC_ERROR;

        if (error.response) {
            const { status, data } = error.response;

            if (data && data.message) {
                message = data.message;
            } else {
                message = ERROR_MESSAGES[status] || ERROR_MESSAGES.GENERIC_ERROR;
            }

            if (status === 401) {
                // handle 401 here e.g. redirect to login
            }
        } else {
            // only set NETWORK_ERROR if there's no response at all
            message = ERROR_MESSAGES.NETWORK_ERROR;
        }

        toast.error(message);
        return Promise.reject(new Error(message));
    }
);



export default apiClient;
