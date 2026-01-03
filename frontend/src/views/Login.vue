<template>
  <div class="bg-gray-50 min-h-screen pt-12">
    <div class="max-w-md mx-auto bg-white rounded shadow-sm p-6 text-gray-900">
      
      <h2 class="text-xl font-bold mb-4">Login</h2>
      <div v-if="success" class="success">
        {{ success }}
      </div>
      <form
        v-else
        class="flex flex-col space-y-4"
        @submit.prevent="submit"
        novalidate
      >

        <div class="flex flex-col space-y-1">
          <label>Email</label>
          <input type="email" v-model="form.email" />
          <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
        </div>

        <div class="flex flex-col space-y-1">
          <label>Password</label>
          <input type="password" v-model="form.password" />
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
        </div>


        <button :disabled="loading" class="btn">
          {{ loading ? 'Logging in ...' : 'Login' }}
        </button>
      </form>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const loading = ref(false)
const success = ref('')
const errors = ref({})

const form = reactive({
  email: '',
  password: '',
})

const submit = async () => {
  loading.value = true
  errors.value = {}
  success.value = ''

  try {
    const response = await axios.post(
      'http://localhost:8000/api/login',
      form
    )

    success.value = response.data.message

    const token = response.data.token
    localStorage.setItem('authToken', token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    setTimeout(() => {
      router.push('/')
    }, 600)

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    } else {
      console.error(error)
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
input {
  padding: 10px;
  border: 1px solid #ced4da;
  border-radius: 5px;
}

.btn {
  padding: 12px;
  background: #007bff;
  color: white;
  border-radius: 5px;
  font-weight: 600;
  border: none;
  cursor: pointer;
}

.btn:disabled {
  background: #ccc;
}

.error {
  color: #dc3545;
  font-size: 14px;
}

.success {
  background: #d4edda;
  color: #155724;
  padding: 12px;
  border-radius: 5px;
  margin-bottom: 10px;
}
</style>
