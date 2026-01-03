<template>
  <div class="login-form">
    <h2>Login</h2>

    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label>Email:</label>
        <input type="email" v-model="formData.email" required />
      </div>
      <div class="form-group">
        <label>Name:</label>
        <input type="text" v-model="formData.name" required />
      </div>      

      <div class="form-group">
        <label>Password:</label>
        <input type="password" v-model="formData.password" required />
      </div>

      <div class="form-group">
        <label>Password Confirmation:</label>
        <input type="password" v-model="formData.password_confirmation" required />
      </div>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Registering...' : 'Sign up' }}
      </button>

      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="success" class="success">{{ success }}</div>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'Login',

  setup() {
    const router = useRouter()

    const formData = ref({
      email: '',
      name: '',
      password: '',
      password_confirmation: ''
    
    })

    const loading = ref(false)
    const error = ref(null)
    const success = ref(null)

    const handleSubmit = async () => {
      loading.value = true
      error.value = null
      success.value = null

      try {
        const response = await axios.post(
          'http://localhost:8000/api/store',
          formData.value
        )

        const token = response.data.token

        localStorage.setItem('authToken', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        success.value = 'Registered  successfully!'

        setTimeout(() => {
          router.push('/')
        })
      } catch (err) {
        if (err.response?.status === 422) {
          const errorData = err.response.data

          if (errorData.errors) {
            const firstError = Object.values(errorData.errors)[0][0]
            error.value = firstError
          } else if (errorData.message) {
            error.value = errorData.message
          } else {
            error.value = 'Invalid credentials'
          }
        } else if (err.response?.status === 401) {
          error.value = 'Invalid email or password'
        } else {
          error.value = 'Login failed. Please try again.'
        }

        console.error('Login error:', err.response?.data || err.message)
      } finally {
        loading.value = false
      }
    }

    return {
      formData,
      loading,
      error,
      success,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.login-form {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  background: white;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da;
  border-radius: 5px;
  font-size: 16px;
}

.form-group input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

button {
  width: 100%;
  padding: 12px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

button:hover:not(:disabled) {
  background: #0056b3;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error {
  margin-top: 15px;
  padding: 12px;
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 5px;
  font-size: 14px;
}

.success {
  margin-top: 15px;
  padding: 12px;
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  border-radius: 5px;
  font-size: 14px;
}
</style>
