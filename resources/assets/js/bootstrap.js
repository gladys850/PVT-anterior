window.Vue = require('vue')
window._ = require('lodash')
window.axios = require('axios').create({
  baseURL: process.env.MIX_APP_URL
})
window.io = require('socket.io-client')

import Echo from 'laravel-echo'
window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: `${process.env.MIX_LARAVEL_ECHO_SERVER_HOST}:${process.env.MIX_LARAVEL_ECHO_SERVER_PORT}`
})
window.Echo.connector.socket.on('connect', () => {
  console.log(`Connected to echo server with ID: ${window.Echo.socketId()}`)
})
window.Echo.connector.socket.on('disconnect', () => {
  console.log('Disconnected from echo server')
})
window.Echo.connector.socket.on('reconnecting', attemptNumber => {
  console.log(`Reconnecting to echo server, intento #${attemptNumber}`)
})