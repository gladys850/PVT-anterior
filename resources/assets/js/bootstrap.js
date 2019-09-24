window.Vue = require('vue')
window._ = require('lodash')
window.axios = require('axios').create({
  baseURL: process.env.MIX_APP_URL
})

import Echo from 'laravel-echo'
window.io = require('socket.io-client')
window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: '192.168.2.99:6001'
})