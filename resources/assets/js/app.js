require('./bootstrap')

import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VeeValidate, { Validator } from 'vee-validate'
import { routes } from './routes'
import StoreData from './store'
import AppMain from './components/AppMain'
import es from 'vee-validate/dist/locale/es'

import toastr from 'toastr'
import 'toastr/build/toastr.min.css'
Vue.prototype.toastr = toastr

import print from 'print-js'
import 'vuetify/dist/vuetify.min.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import ess from './es.js'
import Vuetify from 'vuetify'

Vue.use(Vuetify, {
  lang: {
    locales: { ess },
    current: 'ess'
  },
  theme: {
    primary: '#263238',
    secondary: '#455A64',
    tertiary: '#CFD8DC',
    accent: '#8D6E63',
    error: '#DD2C00',
    warning: '#FFAB00',
    info: '#0288D1',
    success: '#43A047',
    danger: '#ff6d00',
    normal: '#757575'
  }
})

Vue.config.productionTip = false
Vue.use(VueRouter)
Vue.use(Vuex)

import moment from 'moment-business-days'
import { log } from 'util'

moment.updateLocale('es', require('moment/locale/es'), {
  workingWeekdays: [1, 2, 3, 4, 5]
})
Vue.use(require('vue-moment'), {
  moment
})

Vue.use(VeeValidate, {
  locale: 'es',
})

const store = new Vuex.Store(StoreData)

const router = new VueRouter({
  routes,
  // hashbang: false,
  mode: 'history',
})

axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Authorization'] = `${store.getters.token.type} ${store.getters.token.value}`

axios.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response) {
    if (error.response.status == 401) {
      store.dispatch('logout')
      router.push('login')
    }
    for (let key in error.response.data.errors) {
      error.response.data.errors[key].forEach(error => {
        toastr.error(error)
      })
    }
  }
  return Promise.reject(error)
})

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const user = store.state.user

  if (requiresAuth && !user) {
    next({
      path: '/login'
    })
  } else if (to.path == '/login' && user) {
    next({
      path: '/'
    })
  } else {
    next()
  }
})

new Vue({
  el: '#app',
  router,
  store,
  components: {
    AppMain
  },
  locale: 'es',
})

Validator.localize('es', es)

if (store.getters.tokenExpired) {
  store.dispatch('logout')
  router.go('login')
}