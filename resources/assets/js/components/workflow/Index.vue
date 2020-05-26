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
          <v-btn
            v-for="tray in trays"
            :key="tray.name"
            :value="tray.name"
          >
            <v-badge
              :content="tray.count.toString()"
              :color="tray.color"
              class="mr-5 ml-2"
              right
              top
            >
              {{ tray.display_name }}
            </v-badge>
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
            single-line
            hide-details
            clearable
          ></v-text-field>
        </v-flex>
      </v-toolbar>
    </v-card-title>
    <v-tooltip
      left
      content-class="secondary"
    >
      <template v-slot:activator="{ on }">
        <v-btn
          fab
          dark
          fixed
          bottom
          right
          color="warning"
          class="mb-5"
          v-on="on"
          v-show="newLoans.length > 0"
          @click="clearNotification"
        >
          <v-badge
            :content="newLoans.length.toString()"
            right
            top
          >
            <v-icon>mdi-bell-ring</v-icon>
          </v-badge>
        </v-btn>
      </template>
      <v-list
        class="secondary"
        dense
        dark
      >
        <v-subheader>Ver trámites nuevos:</v-subheader>
        <v-list-item-group>
          <v-list-item
            v-for="(loan, index) in newLoans.slice(0, newLoansMax)"
            :key="loan.id"
          >
            <v-list-item-content>
              <v-list-item-title v-text="loan.code">{{index}}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="newLoans.length > newLoansMax">
            <v-list-item-content>
              <v-list-item-title>{{ newLoans.length - newLoansMax }} más ...</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-tooltip>
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
                <v-tab v-for="(procedureType, index) in $store.getters.procedureTypes" :key="procedureType.id">
                  <v-badge
                    :content="procedureTypesCount.hasOwnProperty(index) ? procedureTypesCount[index].toString() : '-'"
                    color="tertiary black--text"
                    right
                    top
                  >
                    {{ procedureType.second_name }}
                  </v-badge>
                </v-tab>
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
          <List :bus="bus" :params="params" @allowFlow="allowFlow = $event" @newLoans="newLoans = $event"/>
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
      newLoans: [],
      newLoansMax: 3,
      allowFlow: false,
      procedureTypesCount: [],
      trays: [
        {
          name: 'received',
          display_name: 'RECIBIDOS',
          count: 0,
          color: 'info'
        }, {
          name: 'validated',
          display_name: 'VALIDADOS',
          count: 0,
          color: 'success'
        }
      ],
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
    if (this.$store.getters.permissions.includes('show-deleted-loan')) this.trays.push({
      name: 'trashed',
      display_name: 'ANULADOS',
      count: 0,
      color: 'error'
    })
    this.filters.traySelected = this.trays[0].name
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Préstamos',
        to: { name: 'loanIndex' }
      }
    ])
  },
  mounted() {
    this.filters.procedureTypeSelected = this.$store.getters.procedureTypes[0]
    this.procedureTypesCount = new Array(this.$store.getters.procedureTypes.length).fill('-')
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
    clearNotification() {
      this.search = ''
      this.filters.traySelected = 'received'
      this.getRoleStatistics()
      this.getProcedureTypeStatistics()
      this.newLoans = []
      this.bus.$emit('added', {})
    },
    getLoans(procedureType) {
      let filters = {
        procedure_type_id: procedureType,
        role_id: this.filters.roleSelected
      }
      switch (this.filters.traySelected) {
        case 'received':
          filters.validated = false
          break
        case 'validated':
          filters.validated = true
          break
        case 'trashed':
          filters.trashed = true
          break
      }
      this.params = filters
      this.getRoleStatistics()
      this.getProcedureTypeStatistics()
    },
    async getRoleStatistics() {
      try {
        let res = await axios.get(`statistic`, {
          params: {
            module: 'prestamos',
            filter: 'role'
          }
        })
        if (this.filters.roleSelected > 0) {
          res = res.data.find(o => o.role_id == this.filters.roleSelected)
          let index
          Object.entries(res.data).forEach(([key, val]) => {
            index = this.trays.findIndex(o => o.name == key)
            if (index !== -1) this.trays[index].count = val
          })
        } else {
          let count = []
          this.trays.forEach(tray => {
            tray.count = res.data.reduce((total, o) => {
              return total + o.data[tray.name]
            }, 0)
          })
        }
      } catch (e) {
        console.log(e)
      }
    },
    async getProcedureTypeStatistics() {
      try {
        let res = await axios.get(`statistic`, {
          params: {
            module: 'prestamos',
            filter: 'procedure_type'
          }
        })
        res.data.forEach((procedure, index) => {
          if (this.filters.roleSelected > 0) {
            this.procedureTypesCount[index] = procedure.data.find(o => o.role_id == this.filters.roleSelected).data[this.filters.traySelected]
            this.$forceUpdate()
          } else {
            this.procedureTypesCount[index] = procedure.total[this.filters.traySelected]
            this.$forceUpdate()
          }
        })
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>