import About from '@/views/About.vue'
import HomeView from '@/views/HomeView.vue'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import AddForm from '@/views/AddForm.vue'
import { authGaurd } from '@/middleware/auth'
import CreateChannelForm from '@/views/CreateChannelForm.vue'
import ChannelDashboard from '@/views/ChannelDashboard.vue'
import ChannelList from '@/views/ChannelList.vue'
import InvitationsView from '@/views/InvitationsView.vue'
import SharedDayDetails from '@/components/channel/shared-days/SharedDayDetails.vue'
import MySharedDays from '@/views/MySharedDays.vue'
import ReportsView from '@/views/ReportsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "Home",
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: "/about",
      name: "About",
      component: About,
      meta: { requiresAuth: true }
    },
    {
      path: "/add-entry",
      name: "AddEntry",
      component: AddForm,
      meta: {requiresAuth: true}
    },
    {
      path: "/channels",
      name: "Channels",
      component: ChannelList,
      meta: {requiresAuth: true}
    },
    {
      path: "/add-channel",
      name: "AddChannel",
      component: CreateChannelForm,
      meta: {requiresAuth: true}
    },
      {
    path: '/channels/:id',
    name: 'Channel',
    component: ChannelDashboard,
    meta: {requiresAuth: true}
     },
     {
      path: '/my-shared-days',
      name: 'MySharedDays',
      component: MySharedDays,
      meta: {requiresAuth: true}

     },
     {
      path: '/channels/:channel_id/shared-days/:id',
      name: 'SharedDay',
      component: SharedDayDetails,
      meta: {requiresAuth: true}
     },
     {
      path: '/my-invitations',
      name: 'MyInvitations',
      component: InvitationsView,
      meta: {requiresAuth: true}
     },
    {
      path: '/reports',
      name: 'Reports',
      component: ReportsView,
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