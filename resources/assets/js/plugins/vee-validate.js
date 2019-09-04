import Vue from 'vue'
import VeeValidate, { Validator } from 'vee-validate'
import validateEs from 'vee-validate/dist/locale/es'
Validator.localize('es', validateEs)

Vue.use(VeeValidate, {
  locale: 'es',
})