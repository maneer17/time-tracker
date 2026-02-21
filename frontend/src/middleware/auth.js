import { useAuthStore } from '@/stores/auth'
export function authGaurd (to, from, next){ 
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
}