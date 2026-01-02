<template>
  <div class="history-sidebar">
    <div v-if="loading">Loading...</div>
    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="dates.length" class="dates">
      <div 
        v-for="date in dates" 
        :key="date" 
        class="date-item"
        @click="goToDate(date)"
      >
        {{ date }}
      </div>
    </div>
    <div v-if="!loading && !dates.length && !error">
      No history
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/api'

const router = useRouter()
const dates = ref([])
const loading = ref(false)
const error = ref(null)

const goToDate = (date) => {
  router.push({ name: 'date-entries', params: { date } })
}

const fetchDates = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await apiClient.get('/api/time-entries/history')
    dates.value = response.data
  } catch (err) {
    error.value = 'Failed to load history'
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchDates)
</script>

<style scoped>
.history-sidebar {
  margin-top: 20px;
}

.dates {
  margin-top: 10px;
}

.date-item {
  padding: 10px;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  margin-bottom: 5px;
  cursor: pointer;
  background: white;
}

.date-item:hover {
  background: #e9ecef;
}

.error {
  color: red;
}
</style>