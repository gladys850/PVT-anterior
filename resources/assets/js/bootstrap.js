window.Vue = require('vue')
window._ = require('lodash')
window.axios = require('axios').create({
  baseURL: process.env.MIX_APP_URL
})