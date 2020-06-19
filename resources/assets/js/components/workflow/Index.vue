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
          v-if="!track"
        >
          <v-btn
            v-for="tray in trays"
            :key="tray.name"
            :value="tray.name"
          >
            <v-badge
              :content="tray.count.toString()"
              :color="tray.color"
              class="ml-2"
              :class="Number.isInteger(tray.count) ? 'mr-5' : 'mr-6'"
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
        <template v-if="hasTray">
          <v-tooltip
            top
          >
            <template v-slot:activator="{ on }">
              <v-btn
                v-on="on"
                icon
                outlined
                small
                :color="track ? 'info' : 'brown'"
                class="darken-2 ml-4"
                @click="track = !track"
              >
                <v-icon>
                  {{ track ? 'mdi-tray-full' : 'mdi-swap-horizontal' }}
                </v-icon>
              </v-btn>
            </template>
            <span v-if="track">Bandeja de trabajo</span>
            <span v-else>Seguimiento de trámites</span>
          </v-tooltip>
        </template>
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
      <v-row v-if="!track">
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
                    :color="procedureTypeClass(index)"
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
          <List :bus="bus" :tray="filters.traySelected" :procedureModalities="procedureModalities" :options.sync="options" :loans="loans" :totalLoans="totalLoans" :loading="loading" @allowFlow="allowFlow = $event"/>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import List from '@/components/workflow/List'
import Fab from '@/components/workflow/Fab'

export default {
  name: "workflow-index",
  components: {
    Breadcrumbs,
    Fab,
    List
  },
  data() {
    return {
      track: false,
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
      roles: [],
      options: {
        itemsPerPage: 8,
        page: 1,
        sortBy: ['request_date'],
        sortDesc: [true]
      },
      loans: [],
      totalLoans: 0,
      loading: true,
      procedureModalities: []
    }
  },
  computed: {
    singleRol() {
      return this.roles.length <= 1
    },
    hasTray() {
      if (this.procedureModalities.length) {
        return this.$store.getters.permissions.includes('update-loan') && this.$store.getters.permissions.includes('show-all-loan')
      } else {
        return false
      }
    }
  },
  beforeCreate() {
    let self = this
    this.$store.dispatch('selectModule', 'prestamos').then(() => {
      this.getProcedureModalities()
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
    Echo.channel('loan').listen('.flow', (msg) => {
      if (msg.data.role_id == this.filters.roleSelected || this.filters.roleSelected == 0) this.newLoans = msg.data.derived
    })
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Préstamos',
        to: { name: 'flowIndex' }
      }
    ])
  },
  mounted() {
    this.filters.procedureTypeSelected = this.$store.getters.procedureTypes[0]
    this.procedureTypesCount = new Array(this.$store.getters.procedureTypes.length).fill('-')
    this.bus.$on('emitRefreshLoans', val => {
      this.updateLoanList();
    })
  },
  watch: {
    search: _.debounce(function () {
      this.getLoans()
    }, 1000),
    filters: {
      deep: true,
      handler(val) {
        if (val.traySelected != null && val.procedureTypeSelected != null && val.roleSelected != null) {
          let procedureType = this.$store.getters.procedureTypes[this.filters.procedureTypeSelected]
          if (procedureType) this.setFilters(procedureType.id)
        }
      }
    },
    options: {
      deep: true,
      handler(val) {
        this.getLoans()
      }
    },
    track(val) {
      if (val) {
        this.filters.procedureTypeSelected = null
        this.filters.roleSelected = 0
        this.filters.traySelected = 'all'
        this.search = ''
        this.params = {
          role_id: this.filters.roleSelected,
        }
        this.newLoans = []
        this.getLoans()
      } else {
        this.filters.procedureTypeSelected = this.$store.getters.procedureTypes[0]
        this.filters.roleSelected = this.roles[0].id
        this.clearNotification()
      }
    }
  },
  methods: {
    getProcedureModalities() {
      this.$store.getters.procedureTypes.forEach(async (procedureType) => {
        try {
          let res = await axios.get(`procedure_modality`, {
            params: {
              procedure_type_id: procedureType.id,
              page: 1,
              per_page: 100
            }
          })
          this.procedureModalities = this.procedureModalities.concat(res.data.data)
        } catch (e) {
          console.log(e)
        }
      })
    },
    updateLoanList() {
      this.getRoleStatistics()
      this.getProcedureTypeStatistics()
      this.getLoans()
    },
    procedureTypeClass(index) {
      if (this.procedureTypesCount.hasOwnProperty(index)) {
        if (this.procedureTypesCount[index] > 0) return 'tertiary black--text'
      }
      return 'normal black--text'
    },
    clearNotification() {
      this.search = ''
      if (this.track) {
        this.getLoans()
      } else {
        this.filters.traySelected = 'received'
        this.updateLoanList()
      }
      this.newLoans = []
    },
    setFilters(procedureType) {
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
      }
      this.params = filters
      this.updateLoanList()
    },
    async getLoans() {
      try {
        if (!this.$store.getters.permissions.includes('update-loan') && this.$store.getters.permissions.includes('show-all-loan')) {
          this.track = true
        }
        this.loading = true
        let res = await axios.get(`loan`, {
          params: {...{
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }, ...this.params}
        })
        this.loans = res.data.data
        this.totalLoans = res.data.total
        delete res.data['data']
        this.options.page = res.data.current_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
        this.options.totalItems = res.data.total
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getRoleStatistics() {
      try {
        let res = await axios.get(`statistic`, {
          params: {
            module: 'prestamos',
            filter: 'role'
          }
        })
        res = res.data.find(o => o.role_id == this.filters.roleSelected)
        if (res) {
          let index
          Object.entries(res.data).forEach(([key, val]) => {
            index = this.trays.findIndex(o => o.name == key)
            if (index !== -1) this.trays[index].count = val <= 999 ? val : '+999'
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
          let role = procedure.data.find(o => o.role_id == this.filters.roleSelected)
          if (role) {
            this.procedureTypesCount[index] = role.data[this.filters.traySelected]
            if (this.procedureTypesCount[index] > 9999) this.procedureTypesCount[index] = '+999..'
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