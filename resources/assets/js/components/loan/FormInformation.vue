<template>
  <v-container fluid >
    <v-card>
    <v-row justify="center">
      <v-col cols="12"  >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="2" class="py-0">
              <label> Tipo de Deposito:</label>
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                dense
                v-model="loanTypeSelected"
                data-vv-name="modalities"
                :items="payment_types"
                item-text="name"
                item-value="id"
              ></v-select>
            </v-col>
             <v-col cols="12" md="2" class="py-0">
              <label> Nro de Cuenta:</label>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                dense
                outlined
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('cuenta')"
                data-vv-name="cuenta"
                v-model="cuenta"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="2" class="py-0">
              <label> Destino del Prestamo:</label>
            </v-col>
            <v-col cols="12" md="9"  >
              <v-text-field
                dense
                outlined
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('destino')"
                data-vv-name="destino"
                v-model="destino"
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
import Ballots from '@/components/loan/Ballots'
  export default {
  inject: ['$validator'],
  name: "loan-information",
  data: () => ({
    cuenta:null,
    destino:null,
    loanTypeSelected:null,
    payment_types:[{
      id:1,
      name:'Efectivo'
    },
    {
      id:2,
      name:'Deposito Bancario'
    }

    ]

  }),
  props: {
    datos: {
      type: Array,
      required: true
    },
  },
  beforeMount() {
    this.getLoanIntervals()
  },
  methods: {
    async getPaymentTypes() {
      try {
        let res = await axios.get(`loan_interval`)
        this.payment_types = res.data
       } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>