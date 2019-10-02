<template>
  <v-container fluid>
    <v-toolbar dense color="tertiary">
      <v-toolbar-title>Afiliados </v-toolbar-title>
      <v-spacer></v-spacer>
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
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn
            fab
            dark
            small
            color="success"
            bottom
            right
            absolute
            v-on="on"
            style="margin-right: -9px;"
            :to="{ name: 'affiliateAdd', params: { id:'new'}}"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>Añadir afiliado</span>
      </v-tooltip>
    </v-toolbar>
    <List :bus="bus"/>
  </v-container>
</template>
<script>
import List from '@/components/affiliate/List'

export default {
  name: "affiliateIndex",
  components: {
    List
  },
  data: () => ({
    search: '',
    bus: new Vue(),
  }),
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
  }
}
</script>
