import { useAuthStore } from '@/stores/auth'
export function authGaurd (to, from, next){ 
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Signed-out visitors reaching the app root see the landing page; other private pages ask for login.
    next(to.name === 'Home' ? '/welcome' : '/login')
  } else {
    next()
  }
}