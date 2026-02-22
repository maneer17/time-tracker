// src/composables/useApi.js
import { ref } from 'vue';

const useApi = (apiFunc, immediate = false) => {
  const data = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const request = async (...args) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await apiFunc(...args);
      data.value = result.data?.data ?? result.data;  // Make sure this matches your API response structure
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  };

  // Auto-fetch if immediate is true
  if (immediate) {
    request();
  }

  return { data, loading, error, request };
};

export default useApi;