import Vue from 'vue'
import Snack from '@/components/shared/Snack'

export default function(message, color) {
  let SnackClass = Vue.extend(Snack)
  let instance = new SnackClass({
    propsData: {
      display: true,
      message: message,
      color: color
    }
  })
  instance.$mount()
  document.getElementById('app').appendChild(instance.$el)
}