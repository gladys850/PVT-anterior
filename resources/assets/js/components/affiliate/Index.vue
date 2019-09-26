<template>
  <v-container fluid>
    <v-toolbar dense color="tertiary">
      <v-toolbar-title>Afiliados </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-flex xs2>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Buscar"
          single-line
          hide-details
          full-width
          clearable
        ></v-text-field>
      </v-flex>
       <v-btn
          fab
          dark
          x-small
          :to="{name:'affiliateAdd'}"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
    </v-toolbar>
    <List :bus="bus"/>
  </v-container>
</template>
<script>
import Vue from 'vue'
import List from '@/components/affiliate/List'
import Add from '@/components/affiliate/Add'

export default {
  name: "affiliateIndex",
  components: {
    List,
    Add,
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
