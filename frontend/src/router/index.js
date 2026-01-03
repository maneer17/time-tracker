import { createRouter, createWebHistory } from 'vue-router'
import TodayEntries from '../views/TodayEntries.vue'
import DateEntries from '../views/DateEntries.vue'
import AddForm from '../views/AddForm.vue'
import HistorySidebar from '../views/HistorySidebar.vue'
import Login from '../views/Login.vue'
import SignUp from '@/views/SignUp.vue'
import { compile } from 'vue'

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
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignUp
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('authToken')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.name === 'login' && token) {
    next('/') 
  } else {
    next()
  }
})

export default router