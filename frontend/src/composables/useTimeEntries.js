import { ref, toValue, watchEffect } from 'vue' 
import apiClient from '@/services/api'

export function useFetch(url) {
  const data = ref(null)
  const error = ref(null)

  const fetchData = () => {
    apiClient.get(toValue(url))
      .then(function(res) {
        data.value = res.data.data || res.data
      })
      .catch(function(err) {
        error.value = err.message
      })
  }

  watchEffect(() => {
    fetchData()
  })

  return { data, error, fetchData }
}

export function Store(url, formData) {
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
export function deleteEntry(url){
  return apiClient.delete(url)
    .then(response => ({
      success: true,
      data: response.data
    }
  ))
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
export default { Store, useFetch, deleteEntry } 