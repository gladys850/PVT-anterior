// Vue instance
require('./bootstrap')
window.Vue = require('vue')
Vue.config.productionTip = false
import AppMain from './components/AppMain'

// Validator
import VeeValidate, { Validator } from 'vee-validate'
import validateEs from 'vee-validate/dist/locale/es'
Validator.localize('es', validateEs)
Vue.use(VeeValidate, {
  locale: 'es',
})

// Vuetify
import vuetify from './plugins/vuetify';
import 'roboto-fontface/css/roboto/roboto-fontface.css'
import '@mdi/font/css/materialdesignicons.css'

// Locale
import VueI18n from 'vue-i18n'
const i18n = new VueI18n({
  locale: 'es'
})

// Router
import VueRouter from 'vue-router'
import { routes } from './routes'
const router = new VueRouter({
  routes,
  // hashbang: false,
  mode: 'history',
})
Vue.use(VueRouter)

// Vuex
import Vuex from 'vuex'
import StoreData from './store'
const store = new Vuex.Store(StoreData)

// Moment
import moment from 'moment-business-days'
moment.updateLocale('es', require('moment/locale/es'), {
  workingWeekdays: [1, 2, 3, 4, 5]
})
Vue.use(require('vue-moment'), {
  moment
})

// JWT
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
if (store.getters.tokenExpired) {
  store.dispatch('logout')
  router.go('login')
}

new Vue({
  router,
  i18n,
  store,
  components: { AppMain },
  vuetify,
  render: h => h(AppMain)
}).$mount('#app')