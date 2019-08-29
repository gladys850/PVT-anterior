import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import es from 'vuetify/es5/locale/es'

Vue.use(Vuetify)

export default new Vuetify({
  theme: {
    themes: {
      light: {
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
    }
  },
  lang: {
    locales: { es },
    current: 'es'
  },
  icons: {
    iconfont: 'mdi'
  }
})
