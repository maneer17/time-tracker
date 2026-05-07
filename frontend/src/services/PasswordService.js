import apiClient from "./api";

const forgotPassword = (email) => apiClient.post('api/passwords/forgot-password', { email })

const resetPassword = (params) =>
    apiClient.post('/api/passwords/reset-password', params);

export default {
    forgotPassword,
    resetPassword,
};