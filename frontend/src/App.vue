<template>
  <div class="app">
    <!-- Only show sidebar if user is logged in -->
    <div v-if="isLoggedIn" class="sidebar">
      <h2>History</h2>
      <HistorySidebar />
      <router-link to="/add" class="add-btn">Add New Entry</router-link>
      

      <button class="logout-btn" @click="handleLogout" :disabled="loading">
        {{ loading ? 'Logging out...' : 'Logout' }}
      </button>

      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="success" class="success">{{ success }}</div>
    </div>

    <div class="main" :class="{ 'full-width': !isLoggedIn }">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import HistorySidebar from './views/HistorySidebar.vue'

const router = useRouter()
const loading = ref(false)
const success = ref(null)
const error = ref(null)

// Check if user is logged in
const isLoggedIn = computed(() => {
  return !!localStorage.getItem('authToken')
})

const handleLogout = async () => {
  loading.value = true
  success.value = null
  error.value = null

  try {
    await axios.post('http://localhost:8000/api/logout', null, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`
      }
    })

    localStorage.removeItem('authToken')
    delete axios.defaults.headers.common['Authorization']

    success.value = 'Logged out successfully!'
    
    setTimeout(() => {
      router.push('/login')
    }, 500)

  } catch (err) {
    error.value = 'Logout failed. Please try again.'
    console.error(err.response?.data || err.message)
  } finally {
    loading.value = false
  }
}

// Optional: Redirect to login if not authenticated
onMounted(() => {
  if (!isLoggedIn.value && !['/login', '/register'].includes(router.currentRoute.value.path)) {
    router.push('/login')
  }
})
</script>

<style>
.app {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 300px;
  background: #f8f9fa;
  border-right: 1px solid #dee2e6;
  padding: 20px;
}

.main {
  flex: 1;
  padding: 20px;
  transition: width 0.3s;
}

.main.full-width {
  width: 100%;
}

.add-btn {
  display: block;
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: white;
  text-align: center;
  text-decoration: none;
  border-radius: 5px;
  margin-top: 20px;
}

.add-btn:hover {
  background: #0056b3;
}

.logout-btn {
  display: block;
  width: 100%;
  padding: 10px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 5px;
  margin-top: 10px;
  cursor: pointer;
  font-weight: 600;
}

.logout-btn:hover:not(:disabled) {
  background: #b02a37;
}

.logout-btn:disabled {
  background: #f5c6cb;
  cursor: not-allowed;
}

.error {
  margin-top: 10px;
  color: red;
}

.success {
  margin-top: 10px;
  color: green;
}
</style>