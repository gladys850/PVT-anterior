<template>
  <v-container >
    <ValidationObserver ref="observer">
      <v-form>
        <v-card>
        <v-card-text class="ma-0 pb-0">
          <v-container fluid class="ma-0 pb-0">
            <v-row justify="center" class="ma-0 pb-0">
              <v-col cols="12" >
                   <v-layout row wrap>
                    <v-flex xs12 class="px-2">      
                      <fieldset class="pa-3">
                        <center>
                          <v-toolbar-title>DATOS DE LA AMORTIZACION</v-toolbar-title>
                        </center>
                        <v-progress-linear></v-progress-linear>
                <v-container >
                <ValidationObserver ref="observer">
                    <v-form>
                      <template>
                      <v-row>
                        <v-col cols="4" class="ma-0 pb-0">
                          <label><b>CODIGO:</b></label> 
                        {{ payment.code}}
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0">
                          <label><b>FECHA:</b></label>
                        {{ payment.estimated_date}}
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0">
                          <label><b>NUMERO DE CUOTA:</b></label>
                        {{ payment.quota_number}}
                        </v-col>
                        <v-col cols="12" class="ma-0 pb-0">
                        <v-progress-linear></v-progress-linear>
                        <center>
                          <v-toolbar-title>CALCULO DE PAGO ESTIMADO</v-toolbar-title>
                        </center>
                        <v-progress-linear></v-progress-linear>
                        </v-col>
                        <v-col cols="4">
                          <v-row>
                           <v-col cols="12" class="ma-0 pb-0">
                              <h3 class="text-center">PAGADO</h3>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>PAGO A CAPITAL:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              {{payment.capital_payment}}
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES CORRIENTE:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              DIAS: {{payment.estimated_days.current}} <br> TOTAL: {{payment.interest_payment}}
                            </v-col>

                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES PENAL: </b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              DIAS: {{payment.estimated_days.penal}}<br> TOTAL: {{payment.penal_payment}}
                            </v-col>

                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES PENDIENTE:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              {{payment.interest_remaining}}
                            </v-col>

                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES PENAL PENDIENTE:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              {{payment.penal_remaining}}
                            </v-col>
                          </v-row>
                        </v-col>

                        <v-col cols="4">
                          <v-row>
                            <v-col cols="12" class="ma-0 pb-0">
                              <h3 class="text-center">RESTANTE</h3>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES RESTANTE ACUMULADO:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              {{payment.interest_accumulated}}
                            </v-col>

                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>INTERES PENAL RESTANTE ACUMULADO:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              {{payment.penal_accumulated}}
                            </v-col>
                          </v-row>
                        </v-col>

                        <v-col cols="4">
                          <v-row>
                           <v-col cols="12" class="ma-0 pb-0">
                              <h3 class="text-center">RESULTADO</h3>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>CUOTA ESTIMADA:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                            {{payment.estimated_quota}}
                            </v-col> 
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>SALDO ANTERIOR:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                            {{payment.balance}}
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                              <label><b>SALDO ACTUAL:</b></label>
                            </v-col>
                            <v-col cols="6" class="ma-0 pb-0">
                            {{payment.next_balance}}
                            </v-col>
                          </v-row>
                        </v-col>




                    </v-row>
                    </template>
                  </v-form>
                </ValidationObserver>
              </v-container>
                
                      </fieldset>
                    </v-flex>
                  </v-layout>

                  
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "add-payment",
  props: {
    payment: {
      type: Object,
      required: true
    },
    data_payment: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    observation: {},
    tipo_de_pago: [
      {name:"Efectivo",
      id:1
      },
      {name:"Dep. en Cuenta",
      id:2
      },
      {name:"Cheque",
      id:3
      },
      {name:"Garante",
      id:4
      }
    ],
    tipo_de_amortizacion: [
      {name:"Regular",
      id:1
      },
      {name:"Total",
      id:2
      },
    ],
     payment_types:[],
    dates: {
      disbursementDate:{
            formatted: null,
            picker: false
          }
      },
    observation_type: [],
    flow: {},
    valArea: [],
    areas: []
  }),
  
 beforeMount(){
    this.getPaymentTypes()
   
  },


  methods: {

      async getLoan() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_types = res.data
        console.log(this.payment_types+'este es el tipo de desembolso');
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

        async getPaymentTypes() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_types = res.data
        console.log(this.payment_types+'este es el tipo de desembolso');
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>