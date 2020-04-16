<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
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
        <Fab v-if="$store.getters.permissions.includes('create-loan')"/>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <List :bus="bus"/>
    </v-card-text>
    <RemoveItem :bus="bus"/>
  </v-card>
</template>

<script>
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import RemoveItem from '@/components/shared/RemoveItem'
import List from '@/components/loan/List'
import Fab from '@/components/loan/Fab'

export default {
  name: "user-index",
  components: {
    Breadcrumbs,
    Fab,
    List,
    RemoveItem
  },
  data: () => ({
    search: '',
    bus: new Vue()
  }),
  beforeMount() {
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Préstamos',
        to: { name: 'loanIndex' }
      }
    ])
  },
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000)
  }
}
</script>