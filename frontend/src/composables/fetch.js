import { ref, watchEffect, toValue } from 'vue'
import apiClient from '@/services/api'
export function useFetch(url) {
  const data = ref(null)
  const error = ref(null)
  const fetchData = ()=>{
          apiClient.get(toValue(url))
          .then(function(res){
              data.value = res.data.data;
              console.log(res.data.data)
          })
          .catch(function(err){
              error.value = err.message
              console.log(err.message)
          })
      }

  watchEffect(() => {
    fetchData()
  })

  return { data, error }
}
export default useFetch