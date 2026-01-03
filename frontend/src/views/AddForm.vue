<template>
  <div class="container">
    <h2>Create Time Entry</h2>

    <!-- Success Message -->
    <p v-if="success" class="success">
      {{ success }}
    </p>

    <!-- General Error -->
    <p v-if="errors.general" class="error">
      {{ errors.general[0] }}
    </p>

    <form @submit.prevent="handleSubmit">
      <div class="field">
        <label>Label</label>
        <input
          type="text"
          v-model="formData.label"
          :disabled="loading"
        />
        <small v-if="errors.label" class="error">
          {{ errors.label[0] }}
        </small>
      </div>

      <div class="field">
        <label>Start Time</label>
        <input
          type="time"
          v-model="formData.start_time"
          :disabled="loading"
        />
        <small v-if="errors.start_time" class="error">
          {{ errors.start_time[0] }}
        </small>
      </div>

      <div class="field">
        <label>End Time</label>
        <input
          type="time"
          v-model="formData.end_time"
          :disabled="loading"
        />
        <small v-if="errors.end_time" class="error">
          {{ errors.end_time[0] }}
        </small>
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Saving...' : 'Save' }}
      </button>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/api'

export default {
  name: 'CreateTimeEntry',

  setup() {
    const router = useRouter()

    const loading = ref(false)
    const success = ref(null)
    const errors = ref({})

    const formData = ref({
      label: '',
      start_time: '',
      end_time: ''
    })

    const handleSubmit = async () => {
      loading.value = true
      errors.value = {}
      success.value = null

      try {
        await apiClient.post('/api/time-entries', formData.value)

        success.value = 'Entry added successfully!'
        formData.value = {
          label: '',
          start_time: '',
          end_time: ''
        }

        router.push('/')
      } catch (err) {
        if (err.response && err.response.status === 422) {
          errors.value = err.response.data.errors
        } else {
          errors.value = {
            general: ['Something went wrong. Please try again.']
          }
        }
      } finally {
        loading.value = false
      }
    }

    return {
      formData,
      handleSubmit,
      loading,
      success,
      errors
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 400px;
}

.field {
  margin-bottom: 12px;
}

.error {
  color: red;
  font-size: 0.85rem;
}

.success {
  color: green;
  font-size: 0.9rem;
}

button {
  margin-top: 10px;
}
</style>
