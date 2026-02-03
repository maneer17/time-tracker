import { defineStore } from 'pinia'
import axios from 'axios'
import router from '@/router' // IMPORT ROUTER
import apiClient from '@/services/api';
const instance = axios.create({
  baseURL: 'http://localhost:8000',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export const useAuthStore = defineStore('authStore', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: !!localStorage.getItem('token')
  }),
  actions: {
 async login(credentials) {
  try {
    const response = await apiClient.post('/api/login', credentials)
    const data = response.data

    this.token = data.token
    this.user = data.user
    this.isAuthenticated = true
    localStorage.setItem('token', data.token)

    router.push({name: 'Home'})
    return { success: true }

  } catch (error) { 
    if (error.response) {
      const errorData = error.response.data
      return { 
        success: false, 
        errors: errorData 
      }
    } else {
      return { 
        success: false, 
        errors: { message: 'Network error. Please try again.' } 
      }
    }
  }
}
,
    
    async register(credentials) {
      try {
        const response = await apiClient.post('/api/register', credentials);
        const data = response.data;
        
        this.token = data.token;
        this.user = data.user;
        this.isAuthenticated = true;
        localStorage.setItem('token', data.token);
        router.push({name: 'Home'})
        return { success: true };
        
      } catch (error) {
        if (error.response) {
          console.log(error.response)
          return { 
            success: false, 
            errors: error.response.data 
          };
        } else {
          return { 
            success: false, 
            errors: { message: 'Network error. Please try again.' } 
          };
        }
      }
    },
    
   async logout() {
      this.user = null;
      this.token = null;
      this.isAuthenticated = false;
      localStorage.removeItem('token');
      router.push('/login');
    },
    
    async checkAuth() {
      if (this.token) {
        try {
          const response = await apiClient.get('/api/user', {
            headers: { Authorization: `Bearer ${this.token}` }
          });
          this.user = response.data;
          this.isAuthenticated = true;
        } catch (error) {
          this.user = null;
          this.token = null;
          this.isAuthenticated = false;
          localStorage.removeItem('token');
        }
      }
    }
  }
});