import Login from './components/auth/Login'
import Profile from './components/auth/Profile'
import DashboardIndex from './components/dashboard/Index'
import UserIndex from './components/user/Index'
import RoleIndex from './components/role/Index'

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
      name: 'dashboardIndex'
    },
    meta: {
      requiresAuth: true
    }
  }, {
    path: '/dashboard',
    name: 'dashboardIndex',
    component: DashboardIndex,
    meta: {
      requiresAuth: true
    }
  }, {
    path: '/user',
    name: 'userIndex',
    component: UserIndex,
    meta: {
      requiresAuth: true
    }
  }, {
    path: '/role',
    name: 'roleIndex',
    component: RoleIndex,
    meta: {
      requiresAuth: true
    }
  }
]