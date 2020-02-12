<template>
  <v-app-bar
    app
    fixed
    dense
    flat
    clipped-left
    dark
    :color="bar.color"
  >
    <v-app-bar-nav-icon @click.stop="$emit('update:expanded', !expanded)"></v-app-bar-nav-icon>
    <v-toolbar-title>{{ bar.text }}</v-toolbar-title>
    <v-spacer></v-spacer>
    <LoggedUser/>
  </v-app-bar>
</template>

<script>
import LoggedUser from '@/layout/LoggedUser'

export default {
  name: 'app-mainbar',
  components: {
    LoggedUser
  },
  props: {
    expanded: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    bar() {
      if (process.env.NODE_ENV != 'production') {
        return {
          color: `error`,
          text: `PLATAFORMA VIRTUAL DE TRÁMITES (VERSIÓN DE PRUEBA)`
        }
      } else {
        return {
          color: `primary`,
          text: `PLATAFORMA VIRTUAL DE TRÁMITES`
        }
      }
    }
  }
}
</script>
