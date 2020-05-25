<template>
  <v-card flat>
    <v-card-title class="pb-0">
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn-toggle
          v-model="filters.traySelected"
          active-class="primary white--text"
          mandatory
        >
          <v-btn v-for="(tray, index) in trays" :key="index" :value="tray">{{ tray }}</v-btn>
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
            single-line
            hide-details
            clearable
          ></v-text-field>
        </v-flex>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-row>
        <v-toolbar flat>
          <v-col :cols="singleRol ? 12 : 10">
              <v-tabs
                v-model="filters.procedureTypeSelected"
                dark
                grow
                center-active
                active-class="secondary"
              >
                <v-tab v-for="procedureType in $store.getters.procedureTypes" :key="procedureType.id">{{ procedureType.second_name }}</v-tab>
              </v-tabs>
          </v-col>
          <v-col cols="2" v-show="!singleRol">
            <v-select
              v-model="filters.roleSelected"
              :items="roles"
              label="Filtro"
              class="pt-3 my-0"
              item-text="display_name"
              item-value="id"
              dense
            ></v-select>
          </v-col>
          <Fab v-show="allowFlow" :bus="bus"/>
        </v-toolbar>
      </v-row>
      <v-row>
        <v-col cols="12">
          <List :bus="bus" :params="params" @allowFlow="allowFlow = $event"/>
        </v-col>
      </v-row>
    </v-card-text>
    <RemoveItem :bus="bus"/>
  </v-card>
</template>

<script>
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import RemoveItem from '@/components/shared/RemoveItem'
import List from '@/components/workflow/List'
import Fab from '@/components/workflow/Fab'

export default {
  name: "workflow-index",
  components: {
    Breadcrumbs,
    Fab,
    List,
    RemoveItem
  },
  data() {
    return {
      search: '',
      bus: new Vue(),
      allowFlow: false,
      trays: ['RECIBIDOS', 'VALIDADOS'],
      filters: {
        traySelected: null,
        procedureTypeSelected: null,
        roleSelected: null
      },
      params: {},
      roles: this.$store.getters.permissions.includes('show-all-loan') ? [{
        id: 0,
        display_name: 'Ver todos'
      }] : []
    }
  },
  computed: {
    singleRol() {
      return !this.$store.getters.permissions.includes('show-all-loan') && this.roles.length <= 1
    }
  },
  beforeCreate() {
    let self = this
    this.$store.dispatch('selectModule', 'prestamos').then(() => {
      this.$store.getters.roles.filter(o => {
        return o.module_id == this.$store.getters.module.id && this.$store.getters.userRoles.includes(o.name)
      }).forEach(function(o, i) {
        if (i == 0) self.filters = {roleSelected: o.id}
        self.roles.push(o)
      })
    })
    this.roles = self.roles
    this.filters = self.filters
  },
  beforeMount() {
    if (this.$store.getters.permissions.includes('show-deleted-loan')) this.trays.push('ANULADOS')
    this.filters.traySelected = this.trays[0]
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Préstamos',
        to: { name: 'loanIndex' }
      }
    ])
  },
  mounted() {
    this.filters.procedureTypeSelected = this.$store.getters.procedureTypes[0]
  },
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
    filters: {
      deep: true,
      handler(val) {
        if (val.traySelected != null && val.procedureTypeSelected != null && val.roleSelected != null) {
          let procedureType = this.$store.getters.procedureTypes[this.filters.procedureTypeSelected]
          if (procedureType) this.getLoans(procedureType.id)
        }
      }
    }
  },
  methods: {
    getLoans(procedureType) {
      let filters = {
        procedure_type_id: procedureType,
        role_id: this.filters.roleSelected
      }
      switch (this.filters.traySelected) {
        case 'RECIBIDOS':
          filters.validated = false
          break
        case 'VALIDADOS':
          filters.validated = true
          break
        case 'ANULADOS':
          filters.trashed = true
          break
      }
      this.params = filters
    }
  }
}
</script>