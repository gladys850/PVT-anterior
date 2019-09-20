import Vue from 'vue'

Vue.filter('uppercase', value => {
	return value.toUpperCase()
})

Vue.filter('lowercase', value => {
	return value.toLowerCase()
})