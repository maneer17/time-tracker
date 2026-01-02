<template>
  <div class="today-entries">
    <h2>Today's Time Entries</h2>
    
    <div v-if="loading">Loading...</div>
    
    <div v-if="error" class="error">{{ error }}</div>
    
    <div v-if="entries.length" class="entries">
      <div v-for="entry in entries" :key="entry.id" class="entry">
        <strong>{{ entry.label }}</strong> 
        From {{ entry.start_time }} to {{ entry.end_time }}
      </div>
    </div>
    
    <div v-if="!loading && !entries.length && !error">
      No entries for today
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import apiClient from '@/services/api'

const entries = ref([])
const loading = ref(false)
const error = ref(null)

const fetchEntries = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await apiClient.get('/api/time-entries')
    entries.value = response.data.data
  } catch (err) {
    error.value = 'Failed to load entries'
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchEntries)
</script>

<style scoped>
.today-entries {
  max-width: 800px;
}

.entries {
  margin-top: 20px;
}

.entry {
  padding: 15px;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  margin-bottom: 10px;
  background: white;
}

.error {
  color: red;
  padding: 10px;
  border: 1px solid red;
  border-radius: 5px;
  background: #ffe6e6;
}
</style>