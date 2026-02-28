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
      data.value = result.data?.data ?? result.data;
    } catch (err) {
      error.value = err.response?.data?.errors ?? err.message;
    } finally {
      loading.value = false;
    }
  };

  if (immediate) {
    request();
  }

  return { data, loading, error, request };
};

export default useApi;