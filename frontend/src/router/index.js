import { createRouter, createWebHistory } from 'vue-router'
import TodayEntries from '../components/TodayEntries.vue'
import DateEntries from '../components/DateEntries.vue'
import AddForm from '../components/AddForm.vue'
import HistorySidebar from '../components/HistorySidebar.vue'
import Login from '../components/Login.vue'

const routes = [
  {
    path: '/',
    name: 'today',
    component: TodayEntries,
    meta: { requiresAuth: true }
  },
  {
    path: '/date/:date',
    name: 'date-entries',
    component: DateEntries,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/add',
    name: 'add',
    component: AddForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: Login
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard to protect routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('authToken')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.name === 'login' && token) {
    next('/') // Redirect to home if already logged in
  } else {
    next()
  }
})

export default router