<template>
  <div class="add-form">
    <h2>Add New Time Entry</h2>
    
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label>Label:</label>
        <input type="text" v-model="formData.label" required>
      </div>
      
      <div class="form-group">
        <label>Start Time:</label>
        <input type="time" v-model="formData.start_time" required>
      </div>
      
      <div class="form-group">
        <label>End Time:</label>
        <input type="time" v-model="formData.end_time" required>
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Adding...' : 'Add Entry' }}
      </button>
      
      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="success" class="success">{{ success }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/api'

const router = useRouter()
const formData = ref({
  label: '',
  start_time: '',
  end_time: ''
})
const loading = ref(false)
const error = ref(null)
const success = ref(null)

const handleSubmit = async () => {
  loading.value = true
  error.value = null
  success.value = null
  
  try {
    const response = await apiClient.post('/api/time-entries', formData.value)
    
    success.value = 'Entry added successfully!'
    formData.value = { label: '', start_time: '', end_time: '' }
    
    setTimeout(() => {
      router.push('/')
    }, 1000)
    
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = 'Failed to add entry'
    }
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.add-form {
  max-width: 500px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #dee2e6;
  border-radius: 5px;
}

button {
  padding: 10px 20px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error {
  color: red;
  margin-top: 10px;
  padding: 10px;
  border: 1px solid red;
  border-radius: 5px;
  background: #ffe6e6;
}

.success {
  color: green;
  margin-top: 10px;
  padding: 10px;
  border: 1px solid green;
  border-radius: 5px;
  background: #e6ffe6;
}
</style>