import { defineStore } from 'pinia'
import router from '@/router' 
import apiClient from '@/services/api';

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
      return {success: false}
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
         return {success: false}
      }
    },
    
   async logout() {
     try {
        await apiClient.post('/api/logout');
        this.user = null;
        this.token = null;
        this.isAuthenticated = false;
        localStorage.removeItem('token');
        router.push('/login');
        return { success: true };
        
      } catch (error) {
          return {success: false}
      }
    },
    
    async checkAuth() {
      if (this.token) {
        try {
          const response = await apiClient.get('/api/user');
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