import moment from 'moment'
import jwt from 'jsonwebtoken'
import VuexPersistence from 'vuex-persist'
import router from '@/plugins/router'

const vuexLocal = new VuexPersistence({
  key: 'pvt',
  storage: window.localStorage
})

export default {
  state: {
    id: null,
    user: null,
    username: null,
    cityId: null,
    roles: [],
    permissions: [],
    ldapAuth: JSON.parse(process.env.MIX_LDAP_AUTHENTICATION),
    dateNow: moment().format('Y-MM-DD'),
    tokenType: localStorage.getItem('token_type') || null,
    accessToken: localStorage.getItem('access_token') || null,
    tokenExpiration: localStorage.getItem('token_expiration') || null,
    breadcrumbs: []
  },
  getters: {
    ldapAuth(state) {
      return state.ldapAuth
    },
    id(state) {
      return state.id
    },
    user(state) {
      return state.user
    },
    username(state) {
      return state.username
    },
    cityId(state) {
      return state.cityId
    },
    roles(state) {
      return state.roles
    },
    permissions(state) {
      return state.permissions
    },
    dateNow(state) {
      return state.dateNow
    },
    tokenType(state) {
      return state.tokenType
    },
    accessToken(state) {
      return state.accessToken
    },
    tokenExpiration(state) {
      return state.tokenExpiration
    },
    tokenExpired(state) {
      if (state.tokenExpiration) {
        return moment().isAfter(state.tokenExpiration)
      }
    },
    breadcrumbs(state) {
      return state.breadcrumbs
    }
  },
  mutations: {
    logout(state) {
      state.id = null
      state.user = null
      state.username = null
      state.cityId = null
      state.roles = []
      state.permissions = []
      state.tokenType = null
      state.accessToken = null
      state.tokenExpiration = null
    },
    login(state, data) {
      state.id = data.id
      state.user = data.user
      state.username = data.username
      state.cityId = data.city_id
      state.roles = data.roles
      state.permissions = data.permissions
      state.accessToken = data.access_token
      state.tokenType = data.token_type
      state.tokenExpiration = data.exp
      axios.defaults.headers.common['Authorization'] = `${data.token_type} ${data.access_token}`
      router.go({
        name: 'dashboard'
      })
    },
    setDate(state, newValue) {
      state.dateNow = newValue
    },
    refreshToken(state, data) {
      state.accessToken = data.access_token
      state.tokenType = data.token_type
      state.tokenExpiration = moment.unix(jwt.decode(data.access_token).exp).format()
      axios.defaults.headers.common['Authorization'] = `${data.token_type} ${data.access_token}`
    },
    setBreadcrumbs(state, data) {
      state.breadcrumbs = data
    },
    addBreadcrumb(state, index, data) {
      state.breadcrumbs[index] = data
    }
  },
  actions: {
    logout(context) {
      context.commit('logout')
    },
    login({ commit }, data) {
      const payload = jwt.decode(data.access_token)
      commit('login', Object.assign({ ...data, ...payload }, { exp: moment.unix(payload.exp).format() }))
    },
    async refresh({ commit, state }) {
      try {
        const expiration = moment(state.tokenExpiration)
        const now = moment()
        if (now.isAfter(expiration.clone().subtract(JSON.parse(process.env.MIX_JWT_TTL)/10, 'minutes')) && expiration.isAfter(now)) {
          const res = await axios.patch(`auth/${state.id}`)
          commit('refreshToken', res.data)
        }
      } catch (e) {
        console.log(e)
      }
    }
  },
  plugins: [vuexLocal.plugin]
}
