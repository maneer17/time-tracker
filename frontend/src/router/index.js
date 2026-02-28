import About from '@/views/About.vue'
import Home from '@/views/Home.vue'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import AddForm from '@/views/AddForm.vue'
import { authGaurd } from '@/middleware/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "Home",
      component: Home,
      meta: { requiresAuth: true }
    },
    {
      path: "/about",
      name: "About",
      component: About,
      meta: { requiresAuth: true }
    },
    {
      path: "/add",
      name: "Add",
      component: AddForm,
      meta: {requiresAuth: true}
    },
    {
      path: "/login",
      name: "Login",
      component: Login
    },
    {
      path: "/register",
      name: "Register",
      component: Register
    },
  ],
})

router.beforeEach(authGaurd)

export default router