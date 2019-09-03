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
    <div class="flex-grow-1"></div>
    <v-menu
      bottom
      offset-y
    >
      <template v-slot:activator="{ on }">
        <v-btn v-on="on">
          <v-icon>mdi-account</v-icon>
          {{ $store.getters.user }}
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item :to="{ name: 'profile' }">
          <v-list-item-icon class="ml-0 mr-2">
            <v-icon>mdi-account-box</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Perfil</v-list-item-title>
        </v-list-item>
        <v-list-item link @click.stop="logout()">
          <v-list-item-icon class="ml-0 mr-2">
            <v-icon>mdi-lock</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Cerrar sesión</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>
<script>
export default {
  name: 'app-mainbar',
  props: {
    expanded: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    bar() {
      if (process.env.NODE_ENV == 'production') {
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
  },
  methods: {
    logout() {
      this.$store.dispatch("logout")
      this.$router.go("login")
    }
  }
}
</script>