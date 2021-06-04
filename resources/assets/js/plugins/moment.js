import Vue from 'vue'
import moment from 'moment-business-days'

moment.updateLocale('es', require('moment/locale/es'), {
  workingWeekdays: [1, 2, 3, 4, 5]
})
Vue.use(require('vue-moment'), {
  moment
})