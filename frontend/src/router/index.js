import About from '@/views/About.vue'
import NotFound from '@/views/NotFound.vue'
import LandingView from '@/views/LandingView.vue'
import Unsubscribed from '@/views/Unsubscribed.vue'
import HomeView from '@/views/HomeView.vue'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import AddForm from '@/views/AddForm.vue'
import NotificationsList from '@/views/NotificationsList.vue'
import { authGaurd } from '@/middleware/auth'
import CreateChannelForm from '@/views/CreateChannelForm.vue'
import ChannelDashboard from '@/views/ChannelDashboard.vue'
import ChannelsLayout from '@/views/ChannelsLayout.vue'
import ChannelEmpty from '@/views/ChannelEmpty.vue'
import InvitationsView from '@/views/InvitationsView.vue'
import SharedDayDetails from '@/components/channel/shared-days/SharedDayDetails.vue'
import MySharedDays from '@/views/MySharedDays.vue'
import ReportsView from '@/views/ReportsView.vue'
import ResetPassword from '@/views/ResetPassword.vue'
import ForgotPassword from '@/views/ForgotPassword.vue'
import Settings from '@/views/Settings.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "Home",
      component: HomeView,
      meta: { requiresAuth: true, fullWidth: true }
    },
    {
      path: "/welcome",
      name: "Landing",
      component: LandingView,
      meta: { fullWidth: true }
    },
    {
      path: "/unsubscribed",
      name: "Unsubscribed",
      component: Unsubscribed,
      meta: { fullWidth: true }
    },
    {
      path: "/about",
      name: "About",
      component: About,
      meta: { requiresAuth: true, fullWidth: true }
    },
    {
      path: "/settings",
      name: "Settings",
      component: Settings,
      meta: { requiresAuth: true, fullWidth: true }

    },
    {
      path: "/add-entry",
      name: "AddEntry",
      component: AddForm,
      meta: { requiresAuth: true, fullWidth: true }
    },
    {
      path: "/add-channel",
      name: "AddChannel",
      component: CreateChannelForm,
      meta: { requiresAuth: true, fullWidth: true }
    },
    {
      path: "/channels",
      component: ChannelsLayout,
      meta: { requiresAuth: true, fullWidth: true },
      children: [
        { path: "", name: "Channels", component: ChannelEmpty },
        { path: ":id", name: "Channel", component: ChannelDashboard },
      ]
    },
     {
      path: '/my-shared-days',
      name: 'MySharedDays',
      component: MySharedDays,
      meta: { requiresAuth: true, fullWidth: true }

     },
     {
      path: '/channels/:channel_id/shared-days/:id',
      name: 'SharedDay',
      component: SharedDayDetails,
      meta: { requiresAuth: true, fullWidth: true }
     },
     {
      path: '/my-invitations',
      name: 'MyInvitations',
      component: InvitationsView,
      meta: { requiresAuth: true, fullWidth: true }
     },
    {
      path: '/reports',
      name: 'Reports',
      component: ReportsView,
      meta: {requiresAuth: true, fullWidth: true}
    },
    {
      path: "/login",
      name: "Login",
      component: Login,
      meta: { fullWidth: true }
    },
    {
      path: "/register",
      name: "Register",
      component: Register,
      meta: { fullWidth: true }
    },
    {
      path: "/forgot-password",
      name: "ForgotPassword",
      component: ForgotPassword
    },
    {
      path: "/reset-password",
      name: "ResetPassword",
      component: ResetPassword
    },
    {
    path: '/notifications',
    name: 'Notifications',
    component:NotificationsList,
    meta: { requiresAuth: true, fullWidth: true }
},
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFound
    }
  ],
})

router.beforeEach(authGaurd)

export default router