<template>
  <v-container fluid>
    <v-toolbar dense color="tertiary">
      <v-toolbar-title>Usuarios</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn-toggle
        v-model="status"
        active-class="primary white--text"
        mandatory
      >
        <v-btn text value="active">
          ACTIVOS
        </v-btn>
        <v-btn text value="inactive">
          INACTIVOS
        </v-btn>
      </v-btn-toggle>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-flex xs2>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Buscar"
          single-line
          hide-details
          clearable
        ></v-text-field>
      </v-flex>
    </v-toolbar>
    <v-card>
      <v-card-text>
        <List :bus="bus"/>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
import Vue from 'vue'
import List from '@/components/user/List'

export default {
  name: "userIndex",
  components: {
    List
  },
  data: () => ({
    search: '',
    bus: new Vue(),
    status: 'active'
  }),
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
    status: function() {
      this.bus.$emit('status', this.status)
    }
  }
}
</script>
