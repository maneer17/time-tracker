import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    loading: false,
    errors: null
  }),

  getters: {
    isLoggedIn: (state) => !!state.token
  },

  actions: {
    async register(data) {
      this.loading = true
      this.errors = null

      try {
        const response = await axios.post(
          'http://localhost:8000/api/store',
          data
        )

        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('token', this.token)
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`

      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors
        }
      } finally {
        this.loading = false
      }
    },

    async login(data) {
      this.loading = true
      this.errors = null

      try {
        const response = await axios.post(
          'http://localhost:8000/api/login',
          data
        )

   
        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('token', this.token)
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`
     

      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors
        }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('http://localhost:8000/api/logout')
      } catch (e) {
        // ignore
      }

      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common.Authorization
    }
  }
})
