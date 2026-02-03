import apiClient from '@/services/api'

function Store(url, formData) {
    return apiClient.post(url, formData)
        .then(response => ({
            success: true,
            data: response.data
        }))
        .catch(error => {
            if (error.response?.status === 422 && error.response?.data?.errors) {
                return {
                    success: false,
                    errors: error.response.data.errors
                }
            }
            return {
                success: false,
                errors: error.response?.data || { message: 'Network error' }
            }
        })
}

export default Store