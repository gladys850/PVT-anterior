<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Registro de actividad</v-toolbar-title>
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
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <List :bus="bus"/>
    </v-card-text>
  </v-card>
</template>

<script>
import List from '@/components/record/List'

export default {
  name: 'record-index',
  components: {
    List
  },
  data: () => ({
    search: '',
    bus: new Vue()
  }),
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000)
  }
}
</script>