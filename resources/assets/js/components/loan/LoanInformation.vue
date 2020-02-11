<template>
  <v-container fluid >
    <v-card>
    <v-row justify="center">
      <v-col cols="12"  >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="4" class="py-0">
              Modalidad Prestamo
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              Monto Solicitado
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              Plazo Meses
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              <v-select
                dense
                v-model="loanTypeSelected"
                data-vv-name="modalities"
                :onchange="Onchange()"
                :items="modalities"
                item-text="name"
                item-value="id"
              ></v-select>
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              <v-text-field
                dense
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('monto')"
                data-vv-name="monto"
                v-model="monto"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-0" >
              <v-text-field
                dense
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('plazo')"
                data-vv-name="plazo"
                v-model="plazo"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-col>
    </v-row>
    </v-card>
  </v-container>
</template>
<script>
import { Validator } from 'vee-validate'
  export default {
  inject: ['$validator'],
  name: "loan-information",
  data: () => ({
    modalities: [],
    loanTypeSelected:null,
    monto:null,
    plazo:null,
    interval:[],

  }),
  beforeMount() {
    this.getProcedureType();
    this.getLoanIntervals()
  },
  methods: {
    async getProcedureType() {
      try {
        let resp = await axios.get(`module`,{
          params: {
            name: 'prestamos',
            sortBy: ['name'],
            sortDesc: ['false'],
            per_page: 10,
            page: 1
            }
        })
        this.modulo= resp.data.data[0].id
        let res = await axios.get(`module/${this.modulo}/procedure_type`)
        this.modalities = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    Onchange(){
      for (this.i = 0; this.i< this.interval.length; this.i++) {
        if(this.loanTypeSelected==this.interval[this.i].procedure_type_id)
        {
          this.monto= this.interval[this.i].maximum_amount,
          this.plazo= this.interval[this.i].maximum_term
        }
      }
    },
    async getLoanIntervals() {
      try {
        let res = await axios.get(`loan_interval`)
        this.interval = res.data
       } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>