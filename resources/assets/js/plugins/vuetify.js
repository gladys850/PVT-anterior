import Vuetify from 'vuetify'
import es from 'vuetify/es5/locale/es'
import 'vuetify/dist/vuetify.min.css'
import 'roboto-fontface/css/roboto/roboto-fontface.css'
import '@mdi/font/css/materialdesignicons.css'

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
        normal: '#757575',
        teal:'#29617b',
        info_card: '#EDF2F4'
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
