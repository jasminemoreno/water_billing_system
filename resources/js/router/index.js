import { createRouter, createWebHistory } from 'vue-router'

// Admin Pages
import LandingPage from '../LandingPage.vue'
import AdminLogin from '../admin/login.vue'
import AdminSignup from '../admin/signup.vue'
import AdminForgotPassword from '../admin/ForgotPassword.vue'
import AdminDashboard from '../admin/dashboard.vue'
import AdminCustomer from '../admin/customer.vue'
import AdminBill from '../admin/bill.vue'
import AdminPayment from '../admin/payment.vue'
import AdminReport from '../admin/report.vue'
import AdminUser from '../admin/user.vue'
import AdminProfile from '../admin/profile.vue'
import AdminAbout from '../admin/about.vue'
import AdminSetting from '../admin/setting.vue'
// AdminLayout
import AdminLayout from '../layouts/AdminLayout.vue'

// Customer Pages
import CustomerLogin from '../customer/login.vue'
import CustomerDashboard from '../customer/dashboard.vue'
import CusomerForgotPassword from '../customer/ForgotPassword.vue'
import CustomerMybills from '../customer/mybills.vue'
import CustomerPaybills from '../customer/paybills.vue'
import CustomerHistory from '../customer/history.vue'
import CustomerNotification  from '../customer/notification.vue'
import CustomerProfile from '../customer/profile.vue'
import CustomerAbout from '../customer/about.vue'
import CustomerSetting from '../customer/setting.vue'
// CustomerLayout
import CustomerLayout from '../layouts/CustomerLayout.vue'




const routes = [
  { path: '/', name: 'landing', component: LandingPage },

  // Admin Side
  { path: '/admin/login', name: 'admin.login', component: AdminLogin },
  { path: '/admin/signup', name: 'admin.signup', component: AdminSignup },
  { path: '/admin/ForgotPassword', name: 'admin.ForgotPassword', component: AdminForgotPassword},

  { path: '/admin/profile', name: 'admin.profile',component: AdminProfile,},
  { path: '/admin/about', name: 'admin.about', component: AdminAbout,},
  {path: '/admin/setting', name: 'admin.setting', component: AdminSetting,},
  

  // Admin routes wrapped inside AdminLayout
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        path: 'dashboard',        // /admin/dashboard
        name: 'admin.dashboard',
        component: AdminDashboard,},
      {
        path: 'customer',         // /admin/customer
        name: 'admin.customer',
        component: AdminCustomer,
      },
      {
        path: 'bill',
        name: 'admin.bill',
        component: AdminBill,
      },
      {
        path: 'payment',
        name: 'admin.payment',
        component: AdminPayment,
      },
      {
        path: 'report',
        name: 'admin.report',
        component: AdminReport,
      },
      {
        path: 'user',
        name: 'admin.user',
        component: AdminUser,
      },
    ],
  },

  // Customer Side
  {
    path: '/customer/login',
    name: 'customer.login',
    component: CustomerLogin,
  },
  {
    path: '/customer/ForgotPassword',
    name: 'customer.ForgotPassword',
    component: CusomerForgotPassword,
  },
  {
    path: '/customer/profile',
    name: 'customer.profile',
    component: CustomerProfile,
  },
  {
    path: '/customer/about',
    name: 'customer.about',
    component: CustomerAbout,
  },
  {
    path: '/customer/settings',
    name: 'customer.setting',
    component: CustomerSetting,
  },
  {
     path: '/customer',
     component: CustomerLayout,
     children: [
      {
        path: 'dashboard',
        name: 'customer.dashboard',
        component: CustomerDashboard,
      },
      {
        path: 'mybills',
        name: 'customer.mybills',
        component: CustomerMybills,

      },
      {
        path: 'paybills',
        name: 'customer.paybills',
        component: CustomerPaybills,
      },
      {
        path: 'history',
        name: 'customer.history',
        component: CustomerHistory,
      },
      {
        path: 'notifications',
        name: 'customer.notification',
        component: CustomerNotification,
      }
     ]
  }

  
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ---------------------------
// Minimal login/logout protection
// ---------------------------
router.beforeEach((to, from, next) => {
  const isLoggedIn = !!sessionStorage.getItem('authToken') // check sessionStorage token

  // List of protected routes
  const protectedPaths = [
    '/admin/dashboard',
    '/admin/customer',
    '/admin/bill',
    '/admin/payment',
    '/admin/report',
    '/admin/user',
    '/admin/profile',
    '/admin/about',
    '/admin/setting'
  ]

  // Redirect to login if trying to access protected route while logged out
  if (protectedPaths.includes(to.path) && !isLoggedIn) {
    next('/admin/login')
  }
  // Prevent logged-in user from visiting login page again
  else if (to.path === '/admin/login' && isLoggedIn) {
    next('/admin/dashboard')
  } 
  else {
    next()
  }
})

export default router