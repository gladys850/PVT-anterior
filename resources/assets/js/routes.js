import Login from './components/auth/Login'
import Profile from './components/auth/Profile'
import Dashboard from './components/dashboard/Dashboard'

export const routes = [
  {
    name: 'login',
    path: '/login',
    component: Login
  }, {
    name: 'profile',
    path: '/profile',
    component: Profile,
    meta: {
      requiresAuth: true
    }
  }, {
    path: '*',
    redirect: {
      name: 'dashboard'
    },
    meta: {
      requiresAuth: true
    }
  }, {
    path: '/dashboard',
    component: Dashboard,
    name: 'dashboard',
    meta: {
      requiresAuth: true
    }
  }
]