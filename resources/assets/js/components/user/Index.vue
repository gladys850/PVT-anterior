<template>
  <v-container fluid>
    <v-toolbar dense flat color="tertiary">
      <v-toolbar-title>Usuarios</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn-toggle
        v-model="active"
        active-class="primary white--text"
        mandatory
      >
        <v-btn text :value="true">
          ACTIVOS
        </v-btn>
        <v-btn text :value="false">
          INACTIVOS
        </v-btn>
      </v-btn-toggle>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-flex xs3>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Buscar"
          class="mr-5 pr-5"
          single-line
          hide-details
          clearable
        ></v-text-field>
      </v-flex>
      <Fab :bus="bus"/>
    </v-toolbar>
    <List :bus="bus"/>
    <RemoveItem :bus="bus"/>
  </v-container>
</template>
<script>
import List from '@/components/user/List'
import Fab from '@/components/user/Fab'
import RemoveItem from '@/components/shared/RemoveItem'

export default {
  name: "user-index",
  components: {
    Fab,
    List,
    RemoveItem
  },
  data: () => ({
    search: '',
    bus: new Vue(),
    active: true
  }),
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
    active: function() {
      this.bus.$emit('active', this.active)
    }
  }
}
</script>
