<template>
  <v-container fluid class="pl-0 pt-0">
    <v-row justify="center" class="py-0">
      <v-col cols="12" class="py-0">
        <v-row justify="center" class="py-0">
          <v-col cols="12" class="py-0">
            <p style="color:teal"> <b>OBSERVACIONES DEL TRAMITE</b></p>
                  <v-card flat tile>
                  <v-card-text >
                    <v-col cols="12" class="pl-3">
                      <v-data-table
                        :headers="headersObs"
                        :items="observations"
                        :items-per-page="6"
                        class="elevation-1"
                        :options.sync="options"
                        :server-items-length="options.totalItems"
                        :footer-props="{ itemsPerPageOptions: [8, 15, 30, 100] }"
                      >
                      </v-data-table>
                    </v-col>
                  </v-card-text>
                </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>


export default {
  name: "observer-flow",
  data: () => ({
    observation_type: [],
    bus: new Vue(),
    headersHist: [
      {
        text: "Fecha creación",
        class: ["normal", "white--text"],
        align: "left",
        value: "created_at"
      },
      {
        text: "Fecha actualización",
        class: ["normal", "white--text"],
        align: "left",
        value: "update_at"
      },
      {
        text: "Acciones realizadas",
        class: ["normal", "white--text"],
        align: "left",
        value: "accion"
      }
    ],
    headersObs: [
      {
        text: "Usuario",
        class: ["normal", "white--text"],
        align: "left",
        value: "user_id"
      },
      {
        text: "Observación",
        class: ["normal", "white--text"],
        align: "left",
        value: "observation_type_id"
      },
      {
        text: "Mensaje",
        class: ["normal", "white--text"],
        align: "left",
        value: "message"
      },
      {
        text: "Fecha",
        class: ["normal", "white--text"],
        align: "left",
        value: "date"
      },
    
    ],
    record: [],
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['created_at'],
      sortDesc: [false]
    },
  }),
  props: {
    affiliate: {
      type: Object,
      required: true,
    },
    loan: {
      type: Object,
      required: true
    },
    observations: {
      type: Array,
      required: true
    },
    bus1: {
      type: Object,
      required: true
    },
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getRecords(this.loan.id)
      }
    },
  },
  mounted() {
    this.getObservationType()
    this.getRecords(this.loan.id)
  },
  methods: {
  
    async getObservationType() {
      try {
        this.loading = true
        let res = await axios.get(
          `module/${this.$store.getters.module.id}/observation_type`
        )
        this.observation_type = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getRecords(id) {
      try {
        this.loading = true
        let res = await axios.get(`record`, {
          params: {
            loan_id: id,
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
          }
        })
        this.record = res.data.data
        //console.log(this.record)
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
  }
}
</script>