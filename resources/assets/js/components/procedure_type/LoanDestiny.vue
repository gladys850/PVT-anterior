<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Asignación de destinos de pŕestamo</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-card>
        <v-card-title>
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
        </v-card-title>
        <v-card-text v-if="selectedProcedure">
          <div class="title">
            <span>Destinos para préstamos de tipo </span>
            <span class="font-weight-black">{{ procedures.find(o => o.id == selectedProcedure).second_name }}</span>
          </div>
          <v-row no-gutters>
            <template v-for="(destiniesColumn, index) in chunkedDestinies">
              <v-col :key="index">
                <div
                  v-for="destiny in destiniesColumn"
                  :key="destiny.id"
                  class="my-3"
                >
                  <v-hover v-slot:default="{ hover }">
                    <v-chip
                      :class="hover ? 'elevation-4' : 'elevation-0'"
                      :color="selectedDestinies.includes(destiny.id) ? 'info' : 'secondary'"
                      dark
                      style="width: 280px;"
                      :outlined="!selectedDestinies.includes(destiny.id)"
                      @click.stop="switchDestiny(destiny.id)"
                    >
                      <v-avatar left v-if="selectedDestinies.includes(destiny.id)">
                        <v-icon>mdi-checkbox-marked-circle</v-icon>
                      </v-avatar>
                      {{ destiny.name }}
                    </v-chip>
                  </v-hover>
                </div>
              </v-col>
            </template>
          </v-row>
        </v-card-text>
      </v-card>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "procedure-type-loan-destiny",
  data: () => ({
    loading: true,
    procedures: [],
    destinies: [],
    selectedProcedure: null,
    selectedDestinies: []
  }),
  watch: {
    selectedProcedure(newVal, oldVal) {
      if (newVal != oldVal && newVal != null) {
        this.selectedDestinies = []
        this.getProcedureDestinies(newVal)
      }
    }
  },
  computed: {
    chunkedDestinies() {
      return _.chunk(this.destinies, 8)
    }
  },
  mounted() {
    this.getDestinies()
    this.getProcedures()
  },
  methods: {
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
        let res = await axios.get(`loan_destiny`)
        this.destinies = res.data.data
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
