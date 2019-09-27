import moment from 'moment'
import 'moment/locale/es'
moment.locale('es')

Vue.filter('uppercase', value => {
	return value.toUpperCase()
})
Vue.filter('lowercase', value => {
	return value.toLowerCase()
})
Vue.filter('datetime', value => {
	return moment(value).format('LLL')
})