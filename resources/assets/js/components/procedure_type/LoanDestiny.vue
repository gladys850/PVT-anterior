<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Asignación de destinos de pŕestamo</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-card>
        <v-card-text>
          <v-row align="center" no-gutters>
            <v-col cols="12" md="6">
              <v-select
                v-model="selectedProcedure"
                :items="procedures"
                label="Tipo de trámite"
                item-text="name"
                item-value="id"
                :loading="loading"
                prepend-inner-icon="mdi-folder-text"
                class="mx-3"
                dense
                flat
                outlined
                shaped
                solo
              ></v-select>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
      <v-card v-if="selectedProcedure">
        <v-card-title>
          <v-toolbar dense color="grey lighten-3">
            <v-toolbar-title>
              <span>Destinos para préstamos de tipo </span>
              <span class="font-weight-black">{{ procedures.find(o => o.id == selectedProcedure).second_name }}</span>
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
                :class="{ 'mr-5': canUpdate }"
                single-line
                hide-details
                clearable
              ></v-text-field>
            </v-flex>
            <template v-if="canUpdate">
              <AddDestiny :bus="bus"/>
            </template>
          </v-toolbar>
        </v-card-title>
        <v-card-text>
          <v-row
            no-gutters
            class="data-box"
            v-if="destinies.length && !loading"
          >
            <template v-for="(destiniesColumn, index) in chunkedDestinies">
              <v-col :key="index">
                <div
                  v-for="destiny in destiniesColumn"
                  :key="destiny.id"
                  class="my-3"
                >
                  <v-menu
                    offset-y
                    close-on-click
                    close-on-content-click
                    right
                  >
                    <template v-slot:activator="{ on }">
                      <v-chip
                        v-on="on"
                        :color="selectedDestinies.includes(destiny.id) ? 'info' : 'secondary'"
                        dark
                        style="width: 300px; height: 45px;"
                        :outlined="!selectedDestinies.includes(destiny.id)"
                      >
                        <v-avatar left v-if="selectedDestinies.includes(destiny.id)">
                          <v-icon>mdi-checkbox-marked-circle</v-icon>
                        </v-avatar>
                        <div>
                          <div class="subtitle-1 font-weight-black">
                            {{ destiny.name }}
                          </div>
                          <div class="caption font-italic">
                            {{ destiny.description }}
                          </div>
                        </div>
                      </v-chip>
                    </template>
                    <v-list dense color="grey lighten-4">
                      <v-list-item @click="switchDestiny(destiny.id)">
                        <v-list-item-icon>
                          <v-icon :color="selectedDestinies.includes(destiny.id) ? 'danger' : 'success'">
                            {{ selectedDestinies.includes(destiny.id) ? 'mdi-minus' : 'mdi-plus' }}
                          </v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                          <v-list-item-title>
                            {{ selectedDestinies.includes(destiny.id) ? 'Quitar' : 'Agregar' }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item @click="bus.$emit('openDialog', destiny)">
                        <v-list-item-icon>
                          <v-icon color="info">mdi-pencil</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                          <v-list-item-title>Editar</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item @click="bus.$emit('openRemoveDialog', `loan_destiny/${destiny.id}`)">
                        <v-list-item-icon>
                          <v-icon color="error">mdi-close</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                          <v-list-item-title>Eliminar</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </v-col>
            </template>
          </v-row>
          <Loading v-else-if="loading"/>
          <div v-else class="text-center data-box">
            No hay datos disponibles
          </div>
          <div class="text-center">
            <v-pagination
              v-model="options.page"
              :length="options.lastPage"
              :total-visible="8"
              color="secondary"
            ></v-pagination>
          </div>
        </v-card-text>
      </v-card>
    </v-card-text>
    <RemoveItem :bus="bus"/>
  </v-card>
</template>
<script>
import AddDestiny from '@/components/procedure_type/AddDestiny'
import RemoveItem from '@/components/shared/RemoveItem'
import Loading from '@/components/shared/Loading'

export default {
  name: "procedure-type-loan-destiny",
  components: {
    AddDestiny,
    RemoveItem,
    Loading
  },
  data: () => ({
    bus: new Vue(),
    loading: true,
    procedures: [],
    destinies: [],
    selectedProcedure: null,
    selectedDestinies: [],
    search: '',
    options: {
      page: 1,
      lastPage: 1,
      itemsPerPage: 24,
      sortBy: ['name'],
      sortDesc: [false]
    }
  }),
  watch: {
    search: _.debounce(function (newVal, oldVal) {
      if (newVal != oldVal) {
        this.options.page = 1
        this.getDestinies()
      }
    }, 1000),
    options: {
      deep: true,
      handler(newVal, oldVal) {
        this.getDestinies()
      }
    },
    selectedProcedure(newVal, oldVal) {
      if (newVal != oldVal && newVal != null) {
        this.selectedDestinies = []
        this.getProcedureDestinies(newVal)
      }
    }
  },
  computed: {
    chunkedDestinies() {
      return _.chunk(this.destinies, 6)
    },
    canUpdate() {
      return this.$store.getters.permissions.includes('update-setting')
    }
  },
  mounted() {
    this.getDestinies()
    this.getProcedures()
    this.bus.$on('getDestinies', (flush) => {
      this.resetList(flush)
    })
    this.bus.$on('removed', val => {
      this.resetList()
    })
  },
  methods: {
    resetList(flush = false) {
      if (flush) {
        this.search = ''
        this.page = 1
      }
      this.getDestinies()
    },
    async getProcedureDestinies(id) {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${id}/loan_destiny`)
        this.selectedDestinies = res.data.map(o => o['id'])
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getProcedures() {
      try {
        this.loading = true
        let res = await axios.get(`module`, {
          params: {
            name: 'prestamos',
            per_page: 1,
            page: 1
          }
        })
        res = await axios.get(`module/${res.data.data[0].id}/procedure_type`)
        this.procedures = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getDestinies() {
      try {
        this.loading = true
        let res = await axios.get(`loan_destiny`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }
        })
        this.destinies = res.data.data
        delete res.data['data']
        this.options.page = res.data.current_page
        this.options.lastPage = res.data.last_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async switchDestiny(id) {
      try {
        if (this.$store.getters.permissions.includes('update-setting')) {
          this.loading = true
          if (this.selectedDestinies.includes(id)) {
            this.selectedDestinies = this.selectedDestinies.filter(o => o != id)
          } else {
            this.selectedDestinies.push(id)
          }
          let res = await axios.patch(`procedure_type/${this.selectedProcedure}/loan_destiny`, {
            destinies: this.selectedDestinies
          })
          this.selectedDestinies = res.data.map(o => o['id'])
          this.toastr.success('Actualizado correctamente')
        } else {
          this.toastr.warning('No autorizado')
        }
      } catch (e) {
        console.log(e)
        this.selectedDestinies = this.selectedDestinies.filter(o => o != id)
      } finally {
        this.loading = false
      }
    }
  }
};
</script>
<style scoped>
.data-box {
  min-height: 400px;
}
</style>