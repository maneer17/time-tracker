import { ref } from 'vue'
import apiClient from '@/services/api'
export function fetchDates(url) {
  const dates = ref(null)
  const err = ref(null)
  const fetchData = ()=>{
          apiClient.get(url)
          .then(function(res){
              dates.value = res.data;
              console.log(res.data)
          })
          .catch(function(err){
              err.value = err.message
              console.log(err.message)
          })
      }
      fetchData()
  return { dates, err }
}
export default fetchDates