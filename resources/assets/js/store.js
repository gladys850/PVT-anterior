import moment from 'moment'
import jwt from 'jsonwebtoken'

export default {
  state: {
    id: localStorage.getItem('id') || null,
    user: localStorage.getItem('user') || null,
    roles: localStorage.getItem('roles') || [],
    permissions: localStorage.getItem('permissions') || [],
    ldapAuth: JSON.parse(process.env.MIX_LDAP_AUTHENTICATION),
    dateNow: moment().format('Y-MM-DD'),
    token: {
      type: localStorage.getItem('token_type') || null,
      value: localStorage.getItem('token') || null
    }
  },
  getters: {
    ldapAuth(state) {
      return state.ldapAuth
    },
    id(state) {
      return JSON.parse(state.id)
    },
    user(state) {
      return state.user
    },
    roles(state) {
      return state.roles.split(',')
    },
    permissions(state) {
      return state.permissions.split(',')
    },
    dateNow(state) {
      return state.dateNow
    },
    token(state) {
      return state.token
    },
    tokenExpired(state) {
      let token = jwt.decode(state.token.value)
      if (token) {
        return moment().isAfter(moment.unix(token.exp))
      }
    }
  },
  mutations: {
    'logout': function (state) {
      localStorage.removeItem('id')
      localStorage.removeItem('user')
      localStorage.removeItem('roles')
      localStorage.removeItem('permissions')
      localStorage.removeItem('token')
      localStorage.removeItem('token_type')
      state.id = null
      state.user = null
      state.roles = []
      state.permissions = []
    },
    'login': function (state, data) {
      let payload = jwt.decode(data.token)
      localStorage.setItem('id', payload.id)
      localStorage.setItem('user', payload.user)
      localStorage.setItem('roles', data.roles)
      localStorage.setItem('permissions', data.permissions)
      localStorage.setItem('token', data.token)
      localStorage.setItem('token_type', data.token_type)
      state.id = payload.id
      state.user = payload.user
      state.roles = data.roles
      state.permissions = data.permissions
      state.token = {
        type: data.token_type,
        value: data.token
      }
      axios.defaults.headers.common['Authorization'] = `${data.token_type} ${data.token}`
    },
    'setDate': function(state, newValue) {
      state.dateNow = newValue
    }
  },
  actions: {
    logout(context) {
      context.commit('logout')
    },
  }
}